<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Event;
use Illuminate\Http\Request;
use App\Models\ReservedEvent;
use App\Models\ReservedMaterial;

class AdminDashboardController extends Controller
{
    public function dashboard()
    {
        try {
            // Stats
            $totalBookings = ReservedEvent::count();
            $pendingRequests = ReservedEvent::where('status', 'pending')->count();
            $revenue = ReservedEvent::where('status', 'completed')->sum('total_cost');
            $totalUsers = User::count();

            // Recent bookings (latest 5)
            $recentBookings = ReservedEvent::with('event', 'user', 'materials')
                ->latest()
                ->take(5)
                ->get()
                ->map(function($re) {
                    return [
                        'date' => $re->event_date,
                        'end_date' => $re->event_end_date,
                        'client' => $re->user?->name ?? 'N/A',
                        'type' => $re->event?->event_name ?? 'N/A',
                        'status' => $re->status,
                        'total_cost' => $re->total_cost,
                        'downpayment_amount' => $re->downpayment_amount,
                        'event_notes' => $re->event_notes,
                        'materials' => $re->materials->map(fn($m) => [
                            'name' => $m->material_name,
                            'description' => $m->material_description
                        ])
                    ];
                });

            // Monthly bookings for chart
            $monthlyBookings = ReservedEvent::selectRaw("MONTH(event_date) as month_num, COUNT(*) as count")
                ->whereYear('event_date', now()->year)
                ->groupBy('month_num')
                ->orderBy('month_num')
                ->get()
                ->map(fn($item) => [
                    'month' => date('F', mktime(0, 0, 0, $item->month_num, 1)),
                    'count' => $item->count
                ]);

            // Popular events (most booked)
            $popularEvents = ReservedEvent::select('event_id')
                ->selectRaw('COUNT(*) as bookings_count')
                ->groupBy('event_id')
                ->with('event')
                ->get()
                ->map(fn($re) => [
                    'event_name' => $re->event?->event_name ?? 'N/A',
                    'bookings_count' => $re->bookings_count
                ]);

            // Popular materials (most used)
            $popularMaterials = ReservedMaterial::select('material_id')
                ->selectRaw('COUNT(*) as used_count')
                ->groupBy('material_id')
                ->with('material')
                ->get()
                ->map(fn($rm) => [
                    'material_name' => $rm->material?->material_name ?? 'N/A',
                    'used_count' => $rm->used_count
                ]);

            return response()->json([
                'result' => true,
                'stats' => [
                    'totalBookings' => $totalBookings,
                    'pendingRequests' => $pendingRequests,
                    'revenue' => $revenue,
                    'totalUsers' => $totalUsers
                ],
                'recentBookings' => $recentBookings,
                'monthlyBookings' => $monthlyBookings,
                'popularEvents' => $popularEvents,
                'popularMaterials' => $popularMaterials,
                'reservedEvents' => $recentBookings // or all reserved events if you prefer
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'result' => false,
                'message' => 'An error occurred while retrieving the dashboard data.',
                'error' => $e->getMessage()
            ], 500);
        }
    }



    public function generatePDFData()
    {
        try {
            // Fetch all reserved events with related event, user, and materials
            $reservedEvents = ReservedEvent::with(['event', 'user', 'materials.material'])->orderBy('event_date', 'desc')->get();

            // Transform data for front-end PDF
            $eventData = $reservedEvents->map(function($re) {
                return [
                    'date' => $re->event_date,
                    'end_date' => $re->event_end_date,
                    'client' => $re->user->name ?? 'N/A',
                    'type' => $re->event->event_name ?? 'N/A',
                    'status' => $re->status,
                    'materials' => $re->materials->map(function($m){
                        return [
                            'name' => $m->material->material_name,
                            'description' => $m->material->material_description,
                        ];
                    }),
                    'total_cost' => $re->total_cost,
                    'downpayment_amount' => $re->downpayment_amount,
                    'event_notes' => $re->event_notes,
                ];
            });

            // Compute summary stats
            $totalRevenue = $reservedEvents->sum('total_cost');
            $totalBookings = $reservedEvents->count();
            $totalUsers = $reservedEvents->pluck('user_id')->unique()->count();
            $pendingRequests = $reservedEvents->where('status', 'pending')->count();

            // Popular events (top 5 by bookings)
            $popularEvents = $reservedEvents->groupBy('event_id')
                ->map(function($group) {
                    return [
                        'event_name' => $group->first()->event->event_name ?? 'N/A',
                        'bookings_count' => $group->count(),
                    ];
                })
                ->sortByDesc('bookings_count')
                ->take(5)
                ->values();

            $materialCounts = [];
            foreach ($reservedEvents as $re) {
                foreach ($re->materials as $rm) {
                    $materialName = $rm->material?->material_name ?? 'Unknown Material';
                    if (!isset($materialCounts[$materialName])) {
                        $materialCounts[$materialName] = 0;
                    }
                    $materialCounts[$materialName]++;
                }
            }

            $popularMaterials = collect($materialCounts)
                ->map(function($count, $name) { return ['material_name' => $name, 'used_count' => $count]; })
                ->sortByDesc('used_count')
                ->take(5)
                ->values();

            return response()->json([
                'result' => true,
                'stats' => [
                    'totalRevenue' => $totalRevenue,
                    'totalBookings' => $totalBookings,
                    'totalUsers' => $totalUsers,
                    'pendingRequests' => $pendingRequests,
                ],
                'popularEvents' => $popularEvents,
                'popularMaterials' => $popularMaterials,
                'reservedEvents' => $eventData
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'result' => false,
                'message' => 'Failed to fetch PDF data.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

}
