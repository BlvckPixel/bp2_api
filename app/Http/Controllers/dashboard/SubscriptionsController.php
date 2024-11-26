<?php

namespace App\Http\Controllers\dashboard;

use App\Http\Controllers\Controller;
use App\Models\Package;
use Illuminate\Http\Request;

class SubscriptionsController extends Controller
{
    public function index()
    {
        return Package::all();
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric',
            'features' => 'required|array',
            'stripe_price_id' => 'required|string',
        ]);

        \Log::info('Creating package with data: ', $validatedData);

        $package = Package::create($validatedData);

        return response()->json($package, 201);
    }

    public function show($id)
    {
        $package = Package::findOrFail($id);

        return response()->json($package);
    }

    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'price' => 'sometimes|required|numeric',
            'features' => 'sometimes|required|array',
            'stripe_price_id' => 'required|string',
        ]);

        \Log::info('Updating package with data: ', $validatedData);

        $package = Package::findOrFail($id);
        $package->update($validatedData);

        return response()->json($package);
    }

    public function destroy($id)
    {
        $package = Package::findOrFail($id);
        $package->delete();

        return response()->json(null, 204);
    }
}
