<?php

namespace App\Http\Controllers;

use App\Models\User;
use Inertia\Inertia;
use App\Models\Material;
use Illuminate\Http\Request;
use App\Models\ReservedEvent;
use App\Mail\BookingUpdateMail;
use App\Models\ReservedMaterial;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\BookingConfirmationMail;
use Illuminate\Validation\ValidationException;
use App\Notifications\CancelReservationNotification;
use App\Notifications\ReservationCreatedNotification;
use App\Notifications\AdminNewReservationNotification;
use App\Notifications\NewUpdateReservationNotification;
use Illuminate\Database\Eloquent\ModelNotFoundException;

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
                'materials.*.quantity'    => 'required|integer|min:1',
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
                foreach ($validated['materials'] as $item) {

                    $material = Material::where('material_id', $item['material_id'])->first();

                    if ($material->material_quantity < $item['quantity']) {
                        return response()->json([
                            'result' => false,
                            'message' => 'Requested quantity for ' . $material->material_name . ' exceeds available stock.',
                        ], 400);
                    }else{
                        $material->material_quantity -= $item['quantity'];
                        $material->save();
                    }

                    ReservedMaterial::create([
                        'reserved_event_id' => $reservation->reserved_event_id,
                        'material_id'       => $item['material_id'],
                        'material_quantity' => $item['quantity'],
                    ]);
                }
            }

            $reservation->load(['user', 'event', 'materials.material']);

            $user = $reservation->user;
            $user->notify(new ReservationCreatedNotification($reservation));

            $admins = User::where('role', 'admin')->get();
            foreach($admins as $admin){
                $admin->notify(new AdminNewReservationNotification($reservation));
            }
            
            if ($user && $user->email) {
                try {
                    
                    Mail::to($user->email)->send(new BookingConfirmationMail($reservation));
                } catch (\Exception $mailError) {
                    Log::error('Mail sending failed: ' . $mailError->getMessage());
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

            $query = ReservedEvent::with(['event', 'materials.material','user']);

            if ($search) {
                $query->where(function ($q) use ($search) {
                    // Search event name/date
                    $q->whereHas('event', function ($qe) use ($search) {
                        $qe->where('event_name', 'like', '%' . $search . '%')
                        ->orWhere('event_date', 'like', '%' . $search . '%');
                    })
                    // Search materials (nested relationship)
                    ->orWhereHas('materials.material', function ($qm) use ($search) {
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
                    'event_name' => $re->event_name ?? $re->event?->event_name,
                    'event_notes' => $re->event_notes,
                    'event_date' => $re->event_date,
                    'event_end_date' => $re->event_end_date,
                    'total_cost' => (float) $re->total_cost,
                    'downpayment_amount' => (float) $re->downpayment_amount,
                    'materials' => $re->materials->map(function ($rm) {
                        return [
                            'material_id'          => $rm->material?->material_id,
                            'material_name'        => $rm->material?->material_name,
                            'material_description' => $rm->material?->material_description,
                            'material_quantity'    => $rm->material_quantity,
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
            Log::error('Error retrieving reserved events: ' . $e->getMessage(), [
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
                'event_name'        => 'nullable|string|max:255',
                'materials'         => 'nullable|array',
                'materials.*.material_id' => 'required|exists:materials,material_id',
                'materials.*.quantity'    => 'required|integer|min:1',
                'total_cost'        => 'nullable|numeric|min:0',
                'downpayment_amount'=> 'nullable|numeric|min:0',
                'event_notes'       => 'nullable|string',
                'status'            => 'required|string|in:pending,accepted,downpayment_update,completed,cancelled',
            ]);

            $reservedEvent = ReservedEvent::findOrFail($validated['reserved_event_id']);

            // 1. Capture the original status BEFORE updating
            $originalStatus = $reservedEvent->status;

            // Update main fields
            $updateData = collect($validated)->except(['reserved_event_id', 'materials'])->toArray();
            if (isset($updateData['total_cost'])) $updateData['total_cost'] = (float) $updateData['total_cost'];
            if (isset($updateData['downpayment_amount'])) $updateData['downpayment_amount'] = (float) $updateData['downpayment_amount'];

            // Downpayment validation
            if (isset($updateData['downpayment_amount'], $updateData['total_cost']) &&
                $updateData['downpayment_amount'] > $updateData['total_cost']) {
                return response()->json([
                    'result' => false,
                    'message' => 'Downpayment cannot be greater than total cost.',
                    'errors' => ['downpayment_amount' => ['Downpayment cannot exceed total cost.']],
                ], 422);
            }

            $reservedEvent->update($updateData);

            // 2. CHECK: If status changed to 'completed', restore stock
            if ($validated['status'] === 'completed' && $originalStatus !== 'completed') {
                
                // Loop through existing reserved materials
                foreach ($reservedEvent->materials as $reservedMaterial) {
                    $material = Material::find($reservedMaterial->material_id);
                    
                    if ($material) {
                        // Access the pivot quantity and add it back to main stock
                        $qtyToRestore = $reservedMaterial->material_quantity;
                        $material->increment('material_quantity', $qtyToRestore);
                    }
                }
                
                // Note: We do NOT process $validated['materials'] here. 
                // If an event is completed, we assume the items used are being returned. 
                // We keep the ReservedMaterial records for history.

            } 
            // 3. ELSE: Handle normal material updates (Only if NOT completing)
            elseif (isset($validated['materials'])) {

                // Restore old stock first (Standard update logic)
                foreach ($reservedEvent->materials as $oldMaterial) {
                    $material = Material::find($oldMaterial->material_id);
                    if ($material) {
                        $material->material_quantity += $oldMaterial->material_quantity;
                        $material->save();
                    }
                }

                ReservedMaterial::where('reserved_event_id', $reservedEvent->reserved_event_id)->delete();

                // Add new materials and adjust stock
                foreach ($validated['materials'] as $item) {
                    $material = Material::find($item['material_id']);
                    if (!$material) continue;

                    if ($material->material_quantity < $item['quantity']) {
                        return response()->json([
                            'result' => false,
                            'message' => 'Requested quantity for ' . $material->material_name . ' exceeds available stock.',
                        ], 400);
                    }

                    // Deduct new stock
                    $material->material_quantity -= $item['quantity'];
                    $material->save();

                    // Save pivot
                    ReservedMaterial::create([
                        'reserved_event_id' => $reservedEvent->reserved_event_id,
                        'material_id'       => $item['material_id'],
                        'material_quantity' => $item['quantity'],
                    ]);
                }
            }

            // Reload relationships
            $reservedEvent->load('materials', 'user');
            
            // Notifications
            $user = $reservedEvent->user;
            if ($user) {
                $message = 'Your reservation has been updated.';
                
                // Customize message for completion
                if ($validated['status'] === 'completed') {
                    $message = 'Your event has been marked as completed. Thank you!';
                }

                $user->notify(new NewUpdateReservationNotification($reservedEvent, $message));

                if ($user->email) {
                    try {
                        Mail::to($user->email)->send(new BookingUpdateMail($reservedEvent));
                    } catch (\Exception $mailError) {
                        Log::error('Mail sending failed: ' . $mailError->getMessage());
                    }
                }
            }

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

            $reservedEvent->materials->each(function ($reservedMaterial) {
                $material = Material::find($reservedMaterial->material_id);
                if ($material) {
                    $material->material_quantity += $reservedMaterial->material_quantity;
                    $material->save();
                }
            });

            $reservedEvent->materials()->delete();

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

    public function reservedOnline()
    {
        try {
            $validated = request()->validate([
                'event_id'                => 'nullable|exists:events,event_id',
                'event_name'              => 'required_without:event_id|string|max:255',
                'event_date'              => 'required|date',
                'event_end_date'          => 'required|date|after_or_equal:event_date',
                'materials'               => 'nullable|array',
                'materials.*.material_id' => 'nullable|exists:materials,material_id',
                'materials.*.quantity'    => 'nullable|integer|min:1',
                'event_notes'             => 'nullable|string',
            ]);

            if (!array_key_exists('event_id', $validated)) {
                $validated['event_id'] = null;
            }

            $user = Auth::user();
            if (!$user) {
                return response()->json([
                    'result' => false,
                    'message' => 'User not authenticated.',
                ], 401);
            }

            $start = $validated['event_date'];
            $end   = $validated['event_end_date'];

            $existingBookings = ReservedEvent::where(function ($q) use ($start, $end) {
                    $q->whereBetween('event_date', [$start, $end])
                    ->orWhereBetween('event_end_date', [$start, $end])
                    ->orWhere(function ($query) use ($start, $end) {
                        $query->where('event_date', '<=', $start)
                                ->where('event_end_date', '>=', $end);
                    });
                })
                ->whereIn('status', ['pending', 'accepted'])
                ->count();

            if ($existingBookings >= 2) {
                return response()->json([
                    'result' => false,
                    'message' => 'Booking unavailable — there are already 2 or more booked events on the selected date range.',
                ], 409);
            }

            $reservation = ReservedEvent::create([
                'user_id'       => $user->user_id,
                'event_date'    => $validated['event_date'],
                'event_end_date'=> $validated['event_end_date'],
                'event_id'      => $validated['event_id'] ?? null,
                'event_name'    => $validated['event_name'] ?? null,
                'event_notes'   => $validated['event_notes'] ?? null,
                'status'        => 'pending',
            ]);

            
            if (!empty($validated['materials'])) {
                foreach ($validated['materials'] as $item) {

                    if (empty($item['material_id'])) {
                        continue;
                    }

                    $material = Material::where('material_id', $item['material_id'])->first();

                    if (!$material) {
                        continue; 
                    }

                    if ($material->material_quantity < $item['quantity']) {
                        return response()->json([
                            'result' => false,
                            'message' => 'Requested quantity for ' . $material->material_name . ' exceeds available stock.',
                        ], 400);
                    }

                    $material->material_quantity -= $item['quantity'];
                    $material->save();

                    ReservedMaterial::create([
                        'reserved_event_id' => $reservation->reserved_event_id,
                        'material_id'       => $item['material_id'],
                        'material_quantity' => $item['quantity'],
                    ]);
                }
            }

            // STORE NOTIFICATION
            $user->notify(new ReservationCreatedNotification($reservation));

            $admins = User::where('role', 'admin')->get();
            foreach($admins as $admin){
                $admin->notify(new AdminNewReservationNotification($reservation));
            }
            
            // SEND EMAIL
            if ($user->email) {
                try {
                    Mail::to($user->email)->send(new BookingConfirmationMail($reservation));
                } catch (\Exception $mailError) {
                    Log::error('Mail sending failed: ' . $mailError->getMessage());
                }
            }

            return response()->json([
                'result'  => true,
                'message' => 'Event booked successfully. Please wait for admin confirmation.',
                'data'    => $reservation,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'result'  => false,
                'message' => 'An error occurred while booking the event.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }


    public function eventsList(){
        try{
            $statuses = ['pending', 'accepted', 'downpayment_update', 'completed'];

            $reservedEvents = ReservedEvent::with(['event', 'user'])
                ->whereIn('status', $statuses)
                ->orderBy('event_date', 'asc')
                ->get();

            $data = $reservedEvents->map(function ($re) {
                return [
                    'user' => [
                        'user_id' => $re->user?->user_id,
                        'name'    => $re->user?->name,
                        'email'   => $re->user?->email,
                    ],
                    'reserved_event_id' => $re->reserved_event_id,
                    'event_id' => $re->event_id,
                    'event_name' => $re->event?->event_name ?? $re->event_name,
                    'event_date' => $re->event_date,
                    'event_end_date' => $re->event_end_date,
                    'status' => $re->status,
                ];
            });
            return response()->json([
                'result' => true,
                'message' => 'Events retrieved successfully.',
                'data' => $data,
            ]);
        }catch (\Exception $e) {
            return response()->json([
                'result'  => false,
                'message' => 'An error occurred while fetching the events list.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    public function myReservations(Request $request)
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return response()->json([
                    'result' => false,
                    'message' => 'User not authenticated.',
                ], 401);
            }

            // 1. Get the raw collection from the database
            $reservationsData = ReservedEvent::with(['event', 'materials.material'])
                ->where('user_id', $user->user_id)
                ->orderBy('reserved_event_id', 'desc')
                ->get();

            // 2. Map through the collection to format EACH item
            $formattedReservations = $reservationsData->map(function ($reservation) {
                return [
                    'reserved_event_id'  => $reservation->reserved_event_id,
                    'event_id'           => $reservation->event_id,
                    'event_name'         => $reservation->event ? $reservation->event->event_name : $reservation->event_name,
                    'event_date'         => $reservation->event_date,
                    'event_end_date'     => $reservation->event_end_date,
                    'total_cost'         => (float) $reservation->total_cost,
                    'downpayment_amount' => (float) $reservation->downpayment_amount,
                    'status'             => $reservation->status,
                    'materials'          => $reservation->materials->map(function ($rm) {
                        return [
                            'material_id'          => $rm->material?->material_id,
                            'material_name'        => $rm->material?->material_name ?? null,
                            'material_description' => $rm->material?->material_description,
                            'material_quantity'    => $rm->material_quantity,
                        ];
                    }),
                ];
            });

            return response()->json([
                'result'  => true,
                'message' => 'User reservations retrieved successfully.',
                'data'    => $formattedReservations, // Return the formatted list
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'result'  => false,
                'message' => 'An error occurred while fetching user reservations.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    public function cancelReservation(Request $request)
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return response()->json([
                    'result' => false,
                    'message' => 'User not authenticated.',
                ], 401);
            }

            $validated = $request->validate([
                'reserved_event_id' => 'required|exists:reserved_events,reserved_event_id',
            ]);

            $reservation = ReservedEvent::where('reserved_event_id', $validated['reserved_event_id'])
                ->where('user_id', $user->user_id)
                ->first();
            $reservation->load('materials');

            $reservation->materials->each(function ($reservedMaterial) {
                $material = Material::find($reservedMaterial->material_id);
                if ($material) {
                    $material->material_quantity += $reservedMaterial->material_quantity;
                    $material->save();
                }
            });

            if (!$reservation) {
                return response()->json([
                    'result' => false,
                    'message' => 'Reservation not found for this user.',
                ], 404);
            }

            $reservation->status = 'cancelled';
            $reservation->save();

            $user = $reservation->user;

            $user->notify(new CancelReservationNotification($reservation));
            
            return response()->json([
                'result' => true,
                'message' => 'Reservation cancelled successfully.',
                'data' => $reservation,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'result'  => false,
                'message' => 'An error occurred while cancelling the reservation.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    public function reservedEventData($reserved_event_id){
        try{
            $reservation = ReservedEvent::where('reserved_event_id', $reserved_event_id)->firstOrFail();

            $reservation->load(['materials','materials.material','user', 'event']);
            
            return response()->json([
                'result' => true,
                'data' => $reservation
            ]);
        }catch(\Exception $e){
            return response()->json([
                'result'  => false,
                'message' => 'An error occurred while fetching the reservation.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }
}
