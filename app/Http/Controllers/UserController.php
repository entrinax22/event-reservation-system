<?php

namespace App\Http\Controllers;

use App\Models\User;
use Inertia\Inertia;
use App\Mail\OtpMail;
use App\Models\UserOtp;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\ReservedEvent;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class UserController extends Controller
{
    public function index(){
        return Inertia::render('admin/tables/usersTable');
    }

    public function list(Request $request)
    {
        try {
            $search = $request->search;
            $users = User::query();

            if ($search) {
                $users->where(function ($q) use ($search) {
                    $q->where('name', 'like', '%' . $search . '%')
                      ->orWhere('email', 'like', '%' . $search . '%')
                      ->orWhere('role', 'like', '%' . $search . '%');
                });
            }

            $users->orderBy('user_id', 'asc');
            $users = $users->paginate(10);

            // Map the collection to return only necessary fields
            $data = $users->getCollection()->map(function ($user) {
                return [
                    'user_id' => $user->user_id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role,
                    // add more fields here if needed
                ];
            });

            return response()->json([
                'result' => true,
                'message' => 'Users retrieved successfully.',
                'data' => $data,
                'pagination' => [
                    'current_page' => $users->currentPage(),
                    'last_page' => $users->lastPage(),
                    'per_page' => $users->perPage(),
                    'total' => $users->total(),
                ],
            ]);
        } catch (\Exception $e) {
            \Log::error('Error retrieving users: ' . $e->getMessage());

            return response()->json([
                'result' => false,
                'message' => 'An error occurred while retrieving the users.',
            ], 500);
        }
    }

    public function create(){
        return Inertia::render('admin/users/create');
    }

    
    public function store(Request $request)
    {
        try{
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'role' => 'required|string',
                'contact_information' => 'required|string'
            ]);


            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'role' => $validated['role'],
                'contact_information' => $validated['contact_information'],
            ]);

            return response()->json([
                'result' => true,
                'message' => 'User created successfully.',
            ]);
        }catch (\Exception $e) {
            return response()->json([
                'result' => false,
                'message' => 'An error occurred while creating the users.',
            ], 500);
        }
    }

    public function update(Request $request)
    {
        try {
            $validated = $request->validate([
                'user_id' => 'required|exists:users,user_id',
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email,' . $request->user_id . ',user_id',
                'role' => 'required|string|in:admin,user', 
                'contact_information' => 'required|string|max:255'
            ]);

            $user = User::findOrFail($request->user_id);

            $user->update($validated);

            return response()->json([
                'result'  => true,
                'message' => 'User updated successfully.',
                'user' => $user
            ], 200);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'result'  => false,
                'message' => 'Validation failed.',
                'errors'  => $e->errors(),
            ], 422);

        } catch (\Exception $e) {
            return response()->json([
                'result'  => false,
                'message' => 'An error occurred while updating the user.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    public function destroy(Request $request)
    {
        try {
            $validated = $request->validate([
                'user_id' => 'required|exists:users,user_id',
            ]);

            $user = User::findOrFail($validated['user_id']); // use array syntax

            $user->delete();

            return response()->json([
                'result'  => true,
                'message' => 'User deleted successfully.',
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'result'  => false,
                'message' => 'An error occurred while deleting the user.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    public function selectList(){
        try {
            $users = User::select('user_id', 'name')->get();

            return response()->json([
                'result'  => true,
                'message' => 'Users list retrieved successfully.',
                'data' => $users
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'result'  => false,
                'message' => 'An error occurred while retrieving the user list.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    public function userData(){
        try{
            $user = auth()->user();
            $reservations = ReservedEvent::where('user_id', $user->user_id)->count();
            $pending = ReservedEvent::where('user_id', $user->user_id)
                ->where('status', 'pending')
                ->count();
            $completed = ReservedEvent::where('user_id', $user->user_id)
                ->where('status', 'completed')
                ->count();
            return response()->json([
                'result'  => true,
                'message' => 'User data retrieved successfully.',
                'data' => [
                    'user_id' => $user->user_id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role,
                    'contact_information' => $user->contact_information ?? null,
                    'photo_url' =>$user->photo_url ? asset('storage/' . $user->photo_url) : null,
                    'address' => $user->address ?? null
                ],
                'stats' => [
                    'total_reservations' => $reservations,
                    'pending_reservations' => $pending,
                    'completed_reservations' => $completed,
                ]
            ], 200);
        }catch(\Exception $e){
            return response()->json([
                'result'  => false,
                'message' => 'An error occurred while retrieving user data.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    public function updateProfile(Request $request)
    {
        try {
            $user = auth()->user();

            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email,' . $user->user_id . ',user_id',
                'contact_information' => 'nullable|string|max:255',
                'password' => 'nullable|string|min:8|confirmed',
                'photo_url' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
                'address' => 'nullable|string|max:500',
            ]);

            $user->name = $validated['name'];
            $user->email = $validated['email'];
            $user->contact_information = $validated['contact_information'] ?? $user->contact_information;
            
            if (!empty($validated['password'])) {
                $user->password = Hash::make($validated['password']);
            }

            if($request->hasFile('photo_url')){
                $file = $request->file('photo_url');
                $path = $file->store('user_photos', 'public');
                $user->photo_url = $path;
            }

            $user->address = $validated['address'] ?? $user->address;
            $user->save();

            return response()->json([
                'result'  => true,
                'message' => 'Profile updated successfully.',
            ], 200);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'result'  => false,
                'message' => 'Validation failed.',
                'errors'  => $e->errors(),
            ], 422);

        } catch (\Exception $e) {
            return response()->json([
                'result'  => false,
                'message' => 'An error occurred while updating the profile.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    public function sendOtpViaEmail()
    {
        try {
            $user = auth()->user();
            if (!$user) {
                return response()->json([
                    'result' => false,
                    'message' => 'User not authenticated.'
                ], 401);
            }
            // Generate 6-digit numeric OTP
            $otpCode = rand(100000, 999999);

            // Store OTP to DB (delete previous OTPs)
            UserOtp::where('user_id', $user->user_id)->delete();

            UserOtp::create([
                'user_id' => $user->user_id,
                'otp' => $otpCode,
                'expires_at' => Carbon::now()->addMinutes(5)
            ]);

            // Send email
            Mail::to($user->email)->send(new OtpMail($otpCode));

            return response()->json([
                'result' => true,
                'message' => 'OTP has been sent to your email.'
            ], 200);

        }catch(\Illuminate\Validation\ValidationException $e){
            return response()->json([
                'result' => false,
                'message' => 'Validation failed.',
                'errors' => $e->errors()
            ], 422);

        }catch (\Exception $e) {
            return response()->json([
                'result' => false,
                'message' => 'Failed to send OTP.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'otp' => 'required',
            'password' => 'required|confirmed|min:6'
        ]);

        $user = auth()->user();

        $record = UserOtp::where('user_id', $user->user_id)
                        ->where('otp', $request->otp)
                        ->where('expires_at', '>', now())
                        ->first();

        if (!$record) {
            return response()->json([
                'result' => false,
                'message' => 'Invalid or expired OTP.'
            ]);
        }

        // Update password
        $user->update([
            'password' => bcrypt($request->password)
        ]);

        // Delete used OTP
        $record->delete();

        return response()->json([
            'result' => true,
            'message' => 'Password changed successfully.'
        ]);
    }

    public function sendOtp(Request $request)
    {
        try{
            $user = User::where('email', $request->email)->first();
            if(!$user){
                return response()->json([
                    'result' => false,
                    'message' => 'Email not found.'
                ], 404);
            }

            // Generate 6-digit numeric OTP
            $otpCode = rand(100000, 999999);

            // Store OTP to DB (delete previous OTPs)
            UserOtp::where('user_id', $user->user_id)->delete();

            UserOtp::create([
                'user_id' => $user->user_id,
                'otp' => $otpCode,
                'expires_at' => Carbon::now()->addMinutes(5)
            ]);

            // Send email
            Mail::to($user->email)->send(new OtpMail($otpCode));

            return response()->json([
                'result' => true,
                'message' => 'OTP has been sent to your email.'
            ], 200);
        }catch(\Illuminate\Validation\ValidationException $e){
            return response()->json([
                'result' => false,
                'message' => 'Validation failed.',
                'errors' => $e->errors()
            ], 422);
        }catch (\Exception $e) {
            return response()->json([
                'result' => false,
                'message' => 'Failed to send OTP.',
                'error' => $e->getMessage()
            ], 500);
        }   
    }

    public function verifyOtpForReset(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required',
            'password' => 'required|confirmed|min:6'
        ]);

        $user = User::where('email', $request->email)->first();
        if(!$user){
            return response()->json([
                'result' => false,
                'message' => 'Email not found.'
            ], 404);
        }

        $record = UserOtp::where('user_id', $user->user_id)
                        ->where('otp', $request->otp)
                        ->where('expires_at', '>', now())
                        ->first();

        if (!$record) {
            return response()->json([
                'result' => false,
                'message' => 'Invalid or expired OTP.'
            ]);
        }

        // Update password
        $user->update([
            'password' => bcrypt($request->password)
        ]);

        // Delete used OTP
        $record->delete();

        return response()->json([
            'result' => true,
            'message' => 'Password changed successfully.'
        ]);
    }
}
