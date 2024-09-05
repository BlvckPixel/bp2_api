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
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric',
            'features' => 'required|array',
        ]);

        $package = Package::create($request->all());

        return response()->json($package, 201);
    }

    public function show($id)
    {
        $package = Package::findOrFail($id);

        return response()->json($package);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'price' => 'sometimes|required|numeric',
            'features' => 'sometimes|required|array',
        ]);

        $package = Package::findOrFail($id);
        $package->update($request->all());

        return response()->json($package);
    }

    public function destroy($id)
    {
        $package = Package::findOrFail($id);
        $package->delete();

        return response()->json(null, 204);
    }
}
