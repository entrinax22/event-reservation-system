<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Models\ReservedEvent;
use App\Models\ReservedMaterial;

class ReservedEventController extends Controller
{
    public function index(){
        return Inertia::render('admin/tables/reservedEventsTable');
    }

    public function create(){
        return Inertia::render('admin/reservedEvents/create');
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'user_id'            => 'required|exists:users,user_id',
                'event_date'         => 'required|date',
                'event_end_date'     => 'required|date|after_or_equal:event_date',
                'event_id'           => 'required|exists:events,event_id',
                'materials'          => 'nullable|array',
                'materials.*.material_id' => 'required|exists:materials,material_id',
                'total_cost'         => 'required|numeric|min:0',
                'downpayment_amount' => 'nullable|numeric|min:0',
                'event_notes'        => 'nullable|string',
                'status'             => 'required|string|in:pending,accepted,downpayment_update,completed,cancelled',
            ]);

            $reservation = ReservedEvent::create([
                'user_id'            => $validated['user_id'],
                'event_date'         => $validated['event_date'],
                'event_end_date'     => $validated['event_end_date'],
                'event_id'           => $validated['event_id'],
                'total_cost'         => $validated['total_cost'],
                'event_notes'        => $validated['event_notes'] ?? null,
                'downpayment_amount' => $validated['downpayment_amount'],
                'status'             => $validated['status'],
            ]);

            if (!empty($validated['materials'])) {
                foreach ($validated['materials'] as $material) {
                    ReservedMaterial::create([
                        'reserved_event_id' => $reservation->reserved_event_id,
                        'material_id'       => $material['material_id'],
                    ]);
                }
            }

            return response()->json([
                'result'  => true,
                'message' => 'Reservation created successfully.',
                'data'    => $reservation
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'result'  => false,
                'message' => 'An error occurred while creating the reservation.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }


    public function list(Request $request)
    {
        try {
            $search = $request->search;

            $query = ReservedEvent::with(['event', 'materials','user']);

            if ($search) {
                $query->where(function ($q) use ($search) {
                    // Search event name/date
                    $q->whereHas('event', function ($qe) use ($search) {
                        $qe->where('event_name', 'like', '%' . $search . '%')
                        ->orWhere('event_date', 'like', '%' . $search . '%');
                    })
                    // Search materials
                    ->orWhereHas('materials', function ($qm) use ($search) {
                        $qm->where('material_name', 'like', '%' . $search . '%');
                    });
                });
            }

            $reservedEvents = $query->orderBy('reserved_event_id', 'asc')->paginate(10);

            $data = $reservedEvents->getCollection()->map(function ($re) {
                return [
                    'reserved_event_id' => $re->reserved_event_id,
                    'event_id' => $re->event_id,
                    'user_id' => $re->user_id,
                    'user_name' => $re->user?->name,
                    'event_name' => $re->event?->event_name,
                    'event_notes' => $re->event_notes,
                    'event_date' => $re->event_date,
                    'event_end_date' => $re->event_end_date,
                    'total_cost' => (float) $re->total_cost,
                    'downpayment_amount' => (float) $re->downpayment_amount,
                    'materials' => $re->materials->map(function ($m) {
                        return [
                            'material_id' => $m->material_id,
                            'material_name' => $m->material_name,
                            'material_description' => $m->material_description,
                        ];
                    }),
                    'status' => $re->status,
                ];
            });

            return response()->json([
                'result' => true,
                'message' => 'Reserved events retrieved successfully.',
                'data' => $data,
                'pagination' => [
                    'current_page' => $reservedEvents->currentPage(),
                    'last_page' => $reservedEvents->lastPage(),
                    'per_page' => $reservedEvents->perPage(),
                    'total' => $reservedEvents->total(),
                ],
            ]);
        } catch (\Exception $e) {
            \Log::error('Error retrieving reserved events: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'result' => false,
                'message' => 'An error occurred while retrieving the reserved events.',
                'error' => $e->getMessage(), 
            ], 500);
        }
    }
    public function update(Request $request)
    {
        try {
            $validated = $request->validate([
                'reserved_event_id' => 'required|integer|exists:reserved_events,reserved_event_id',
                'user_id'           => 'nullable|exists:users,user_id',
                'event_date'        => 'nullable|date',
                'event_end_date'    => 'nullable|date|after_or_equal:event_date',
                'event_id'          => 'nullable|exists:events,event_id',
                'materials'         => 'nullable|array',
                'materials.*.material_id' => 'required|exists:materials,material_id',
                'total_cost'        => 'nullable|numeric|min:0',
                'downpayment_amount'=> 'nullable|numeric|min:0',
                'event_notes'       => 'nullable|string',
                'status'            => 'required|string|in:pending,accepted,downpayment_update,completed,cancelled',
            ]);

            // Find the reserved event
            $reservedEvent = ReservedEvent::findOrFail($validated['reserved_event_id']);

            // Prepare update data
            $updateData = collect($validated)->except(['reserved_event_id', 'materials'])->toArray();

            // Convert numeric fields
            if (isset($updateData['total_cost'])) {
                $updateData['total_cost'] = (float) $updateData['total_cost'];
            }
            if (isset($updateData['downpayment_amount'])) {
                $updateData['downpayment_amount'] = (float) $updateData['downpayment_amount'];
            }

            // Downpayment check
            if (isset($updateData['downpayment_amount'], $updateData['total_cost']) &&
                $updateData['downpayment_amount'] > $updateData['total_cost']) {
                return response()->json([
                    'result' => false,
                    'message' => 'Downpayment cannot be greater than total cost.',
                    'errors' => ['downpayment_amount' => ['Downpayment cannot exceed total cost.']],
                ], 422);
            }

            // Update the reserved event
            $reservedEvent->update($updateData);

            // Handle materials: remove old and add new (like store method)
            if (isset($validated['materials'])) {
                // Remove existing pivot records
                ReservedMaterial::where('reserved_event_id', $reservedEvent->reserved_event_id)->delete();

                // Insert new ones
                foreach ($validated['materials'] as $material) {
                    ReservedMaterial::create([
                        'reserved_event_id' => $reservedEvent->reserved_event_id,
                        'material_id'       => $material['material_id'],
                    ]);
                }
            }

            // Reload with relationships
            $reservedEvent->load('materials');

            return response()->json([
                'result'  => true,
                'message' => 'Reserved event updated successfully.',
                'data'    => $reservedEvent,
            ], 200);

        } catch (ValidationException $e) {
            return response()->json([
                'result' => false,
                'message' => 'Validation failed.',
                'errors' => $e->errors(),
            ], 422);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'result' => false,
                'message' => 'Reserved event not found.',
            ], 404);

        } catch (\Exception $e) {
            return response()->json([
                'result' => false,
                'message' => 'An error occurred while updating the reserved event.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }


    public function destroy(Request $request)
    {
        try {
            $validated = $request->validate([
                'reserved_event_id' => 'required|exists:reserved_events,reserved_event_id',
            ]);

            $reservedEvent = ReservedEvent::with('materials')
                ->findOrFail($validated['reserved_event_id']);

            $reservedEvent->materials()->detach();

            $reservedEvent->delete();

            return response()->json([
                'result'  => true,
                'message' => 'Reserved event and its materials deleted successfully.',
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'result'  => false,
                'message' => 'An error occurred while deleting the reserved event.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

}
