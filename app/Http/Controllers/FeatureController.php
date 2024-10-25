<?php

// app/Http/Controllers/FeatureController.php

namespace App\Http\Controllers;

use App\Models\Feature;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class FeatureController extends Controller
{
    /**
     * Display a listing of the features.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $features = Feature::all();
        
        return response()->json([
            'status' => 'success',
            'data' => $features
        ]);
    }

    /**
     * Store a newly created feature.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:features'
        ]);

        $feature = Feature::create([
            'name' => $request->name
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Feature created successfully',
            'data' => $feature
        ], 201);
    }

    /**
     * Display the specified feature.
     *
     * @param Feature $feature
     * @return JsonResponse
     */
    public function show(Feature $feature): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'data' => $feature
        ]);
    }

    /**
     * Update the specified feature.
     *
     * @param Request $request
     * @param Feature $feature
     * @return JsonResponse
     */
    public function update(Request $request, Feature $feature): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:features,name,' . $feature->id
        ]);

        $feature->update([
            'name' => $request->name
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Feature updated successfully',
            'data' => $feature
        ]);
    }

    /**
     * Remove the specified feature.
     *
     * @param Feature $feature
     * @return JsonResponse
     */
    public function destroy(Feature $feature): JsonResponse
    {
        $feature->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Feature deleted successfully'
        ]);
    }
}