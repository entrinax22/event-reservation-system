<?php

namespace App\Http\Controllers;

use App\Models\User;
use Inertia\Inertia;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
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
                    'must_change_password' => $user->must_change_password
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

            // Generate random password
            $randomPassword = Str::random(10);

            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'role' => $validated['role'],
                'password' => Hash::make($randomPassword),
                'contact_information' => $validated['contact_information'],
                'must_change_password' => true,
            ]);

            // // Optionally: Send password via email
            // Mail::to($user->email)->send(new \App\Mail\SendInitialPassword($user, $randomPassword));

            return response()->json([
                'result' => true,
                'message' => 'User created successfully.',
                'password_temporary' => $randomPassword
            ]);
        }catch (\Exception $e) {
            return response()->json([
                'result' => false,
                'message' => 'An error occurred while creating the users.',
            ], 500);
        }
    }

    public function showChangePasswordForm()
    {
        return Inertia::render('auth/change-password');
    }

    public function update(Request $request)
    {
        $request->validate([
            'password' => ['required','confirmed','min:8'],
        ]);

        $user = $request->user();
        $user->update([
            'password' => Hash::make($request->password),
            'must_change_password' => false,
        ]);

        return redirect()->route('home')->with('success', 'Password changed successfully.');
    }

}
