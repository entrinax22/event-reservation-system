<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Material;
use Illuminate\Http\Request;

class MaterialController extends Controller
{
    public function index(){
        return Inertia::render('admin/tables/materialsTable');
    }

    public function create(){
        return Inertia::render('admin/materials/create');
    }

    public function store(Request $request)
    {
        try{
            $validated = $request->validate([
                'material_name' => 'required|string|max:255',
                'material_description' => 'required|string|max:255',
                'material_quantity' => 'required|integer|min:0',
                'status' => 'required|in:inactive,active',
            ]);


            $user = Material::create([
                'material_name' => $validated['material_name'],
                'material_description' => $validated['material_description'],
                'material_quantity' => $validated['material_quantity'],
                'status' => $validated['status'],
            ]);

            return response()->json([
                'result' => true,
                'message' => 'Material created successfully.',
            ]);
        }catch (\Exception $e) {
            return response()->json([
                'result' => false,
                'message' => 'An error occurred while creating the material.',
            ], 500);
        }
    }

    public function list(Request $request)
    {
        try {
            $search = $request->search;
            $materials = Material::query();

            if ($search) {
                $materials->where(function ($q) use ($search) {
                    $q->where('material_name', 'like', '%' . $search . '%')
                      ->orWhere('material_description', 'like', '%' . $search . '%');
                });
            }

            $materials->orderBy('material_id', 'asc');
            $materials = $materials->paginate(10);

            // Map the collection to return only necessary fields
            $data = $materials->getCollection()->map(function ($material) {
                return [
                    'material_id' => $material->material_id,
                    'material_name' => $material->material_name,
                    'material_description' => $material->material_description,
                    'material_quantity' => $material->material_quantity,
                    'status' => $material->status,
                ];
            });

            return response()->json([
                'result' => true,
                'message' => 'Materials retrieved successfully.',
                'data' => $data,
                'pagination' => [
                    'current_page' => $materials->currentPage(),
                    'last_page' => $materials->lastPage(),
                    'per_page' => $materials->perPage(),
                    'total' => $materials->total(),
                ],
            ]);
        } catch (\Exception $e) {
            \Log::error('Error retrieving material: ' . $e->getMessage());

            return response()->json([
                'result' => false,
                'message' => 'An error occurred while retrieving the materials.',
            ], 500);
        }
    }
    
    public function update(Request $request)
    {
        try {
            $validated = $request->validate([
                'material_id' => 'required',
                'material_name' => 'required|string|max:255',
                'material_description' => 'required|string|max:255',
                'material_quantity' => 'required|integer|min:0',
                'status' => 'required|in:inactive,active',
            ]);

            $material = Material::findOrFail($request->material_id);

            $material->update($validated);

            return response()->json([
                'result'  => true,
                'message' => 'Material updated successfully.',
                'material' => $material
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
                'message' => 'An error occurred while updating the material.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    public function destroy(Request $request)
    {
        try {
            $validated = $request->validate([
                'material_id' => 'required|exists:materials,material_id',
            ]);

            $event = Material::findOrFail($validated['material_id']); 

            $event->delete();

            return response()->json([
                'result'  => true,
                'message' => 'Material deleted successfully.',
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'result'  => false,
                'message' => 'An error occurred while deleting the material.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    public function selectList(){
        try {
            $materials = Material::select('material_id', 'material_name', 'material_quantity')->where('status', 'active')->get();

            return response()->json([
                'result'  => true,
                'message' => 'Materials list retrieved successfully.',
                'data' => $materials
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'result'  => false,
                'message' => 'An error occurred while retrieving the materials list.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }
}
