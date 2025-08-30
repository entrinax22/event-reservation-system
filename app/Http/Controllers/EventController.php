<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class EventController extends Controller
{
    public function index(){
        return Inertia::render('admin/tables/eventsTable');
    }

    public function create(){
        return Inertia::render('admin/events/create');
    }

    public function store(Request $request)
    {
        try{
            $validated = $request->validate([
                'event_name' => 'required|string|max:255',
                'event_description' => 'required|string|max:255',
            ]);


            $user = Event::create([
                'event_name' => $validated['event_name'],
                'event_description' => $validated['event_description'],
            ]);

            return response()->json([
                'result' => true,
                'message' => 'Event created successfully.',
            ]);
        }catch (\Exception $e) {
            return response()->json([
                'result' => false,
                'message' => 'An error occurred while creating the event.',
            ], 500);
        }
    }

    public function list(Request $request)
    {
        try {
            $search = $request->search;
            $events = Event::query();

            if ($search) {
                $events->where(function ($q) use ($search) {
                    $q->where('event_name', 'like', '%' . $search . '%')
                      ->orWhere('event_description', 'like', '%' . $search . '%');
                });
            }

            $events->orderBy('event_id', 'asc');
            $events = $events->paginate(10);

            // Map the collection to return only necessary fields
            $data = $events->getCollection()->map(function ($event) {
                return [
                    'event_id' => $event->event_id,
                    'event_name' => $event->event_name,
                    'event_description' => $event->event_description,
                ];
            });

            return response()->json([
                'result' => true,
                'message' => 'Events retrieved successfully.',
                'data' => $data,
                'pagination' => [
                    'current_page' => $events->currentPage(),
                    'last_page' => $events->lastPage(),
                    'per_page' => $events->perPage(),
                    'total' => $events->total(),
                ],
            ]);
        } catch (\Exception $e) {
            \Log::error('Error retrieving events: ' . $e->getMessage());

            return response()->json([
                'result' => false,
                'message' => 'An error occurred while retrieving the events.',
            ], 500);
        }
    }

    
    public function update(Request $request)
    {
        try {
            $validated = $request->validate([
                'event_id' => 'required|exists:events,event_id',
                'event_name' => 'required|string|max:255',
                'event_description' => 'required|string|max:255',
            ]);

            $event = Event::findOrFail($request->event_id);

            $event->update($validated);

            return response()->json([
                'result'  => true,
                'message' => 'User updated successfully.',
                'event' => $event
            ], 200);

        } catch (ValidationException $e) {
            return response()->json([
                'result'  => false,
                'message' => 'Validation failed.',
                'errors'  => $e->errors(),
            ], 422);

        } catch (\Exception $e) {
            return response()->json([
                'result'  => false,
                'message' => 'An error occurred while updating the event.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    public function destroy(Request $request)
    {
        try {
            $validated = $request->validate([
                'event_id' => 'required|exists:events,event_id',
            ]);

            $event = Event::findOrFail($validated['event_id']); 

            $event->delete();

            return response()->json([
                'result'  => true,
                'message' => 'Event deleted successfully.',
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'result'  => false,
                'message' => 'An error occurred while deleting the event.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }
}
