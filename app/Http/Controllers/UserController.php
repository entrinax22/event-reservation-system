<?php

namespace App\Http\Controllers;

use App\Models\User;
use Inertia\Inertia;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

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

}
