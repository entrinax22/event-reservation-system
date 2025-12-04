<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Payment;
use Illuminate\Http\Request;
use App\Models\ReservedEvent;

class PaymentsController extends Controller
{
    public function index(){
        return Inertia::render('admin/tables/paymentsTable');
    }

    public function create(){
        return Inertia::render('admin/payments/create');
    }

    public function list(Request $request){
        try{
            $search = $request->search;

            $query = Payment::with(['user', 'reservedEvent', 'reservedEvent.event']);

            if($search){
                $query->whereHas('user', function($q) use ($search){
                    $q->where('name', 'like', '%' . $search . '%');
                })->orWhereHas('reservedEvent.event', function($q) use ($search){
                    $q->where('event_name', 'like', '%' . $search . '%');
                })->orWhere('reference_number', 'like', '%' . $search . '%');
            }
            $payments = $query->orderBy('payment_id', 'desc')->paginate(10);

            $data = $payments->getCollection()->map(function($payment){
                return [
                    'payment_id' => $payment->payment_id,
                    'user_name' => $payment->user->name ?? null,
                    'event_name' => $payment->reservedEvent->event->event_name ?? null,
                    'amount' => $payment->amount,
                    'reference_number' => $payment->reference_number,
                    'payment_proof' => $payment->payment_proof ? asset('storage/' . $payment->payment_proof) : null,
                    'payment_date' => $payment->created_at->toDateString(),
                    'status' => $payment->status,
                ];
            });

            return response()->json([
                'result' => true,
                'data' => $data,
                'pagination' => [
                    'current_page' => $payments->currentPage(),
                    'last_page' => $payments->lastPage(),
                    'per_page' => $payments->perPage(),
                    'total' => $payments->total(),
                ],
                'message' => 'Payments fetched successfully.'
            ]);
        }catch(\Exception $e){
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch payments: ' . $e->getMessage()
            ]);
        }
    }

    public function store(Request $request){
        try{
            $validated = $request->validate([
                'reserved_event_id' => 'required|numeric',
                'amount' => 'required|numeric|min:0',
                'reference_number' => 'required|string|max:255|unique:payments,reference_number',
                'payment_proof' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            ]);

            $user = Auth()->user();

            $payment = new Payment();
            $payment->user_id = $user->user_id;
            $payment->reserved_event_id = $validated['reserved_event_id'];
            $payment->amount = $validated['amount'];
            $payment->reference_number = $validated['reference_number'];
            if($request->hasFile('payment_proof')){
                $file = $request->file('payment_proof');
                $path = $file->store('payment_proofs', 'public');
                $payment->payment_proof = $path;
            }
            $payment->save();

            return response()->json([
                'result' => true,
                'message' => 'Payment stored successfully.'
            ]);
        }catch(\Exception $e){
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to store payment: ' . $e->getMessage()
            ]);
        }
    }

    

    public function update(Request $request){
        try{
            $validated = $request->validate([
                'payment_id' => 'required|numeric|exists:payments,payment_id',
                'status' => 'required|string|in:pending,completed,failed',
            ]);

            $payment = Payment::findOrFail($validated['payment_id']);
            $payment->status = $validated['status'];
            $payment->save();

            if($validated['status'] === 'completed'){
                ReservedEvent::where('reserved_event_id', $payment->reserved_event_id)
                    ->update(['status' => 'accepted']);
            }
            
            return response()->json([
                'result' => true,
                'message' => 'Payment updated successfully.'
            ]);
        }catch(\Exception $e){
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update payment: ' . $e->getMessage()
            ]);
        }
    }

    public function destroy(Request $request){
        try{
            $payment_id = $request->input('payment_id');
            $payment = Payment::findOrFail($payment_id);
            $payment->delete();

            return response()->json([
                'result' => true,
                'message' => 'Payment deleted successfully.'
            ]);
        }catch(\Exception $e){
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to delete payment: ' . $e->getMessage()
            ]);
        }
    }

    public function payOnline(Request $request){
        try{
            $validated = $request->validate([
                'reserved_event_id' => 'required|numeric',
                'amount' => 'required|numeric|min:0',
                'reference_number' => 'required|string|max:255|unique:payments,reference_number',
                'payment_proof' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            ]);

            $user = Auth()->user();

            $payment = new Payment();
            $payment->user_id = $user->user_id;
            $payment->reserved_event_id = $validated['reserved_event_id'];
            $payment->amount = $validated['amount'];
            $payment->reference_number = $validated['reference_number'];
            if($request->hasFile('payment_proof')){
                $file = $request->file('payment_proof');
                $path = $file->store('payment_proofs', 'public');
                $payment->payment_proof = $path;
            }
            $payment->save();

            return response()->json([
                'result' => true,
                'message' => 'Payment stored successfully.'
            ]);
        }catch(\Exception $e){
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to store payment: ' . $e->getMessage()
            ]);
        }
    }
}
