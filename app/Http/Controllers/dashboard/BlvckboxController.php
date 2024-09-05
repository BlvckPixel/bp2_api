<?php

namespace App\Http\Controllers\dashboard;

use App\Http\Controllers\Controller;
use App\Models\Blvckbox;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\File;
use Carbon\Carbon;

class BlvckboxController extends Controller
{

    public function index()
    {
        $blvckboxes = Blvckbox::all();
        return response()->json($blvckboxes);
    }

    // public function create(Request $request)
    // {
    //     $validator = Validator::make($request->all(), [
    //         'title' => 'required|string|max:255',
    //         'slug' => 'required|string|max:255|unique:blvckboxes',
    //         'subtitle' => 'nullable|string|max:255',
    //         'description' => 'nullable|string',
    //         'date' => 'nullable|date',
    //         'background' => 'nullable|string|max:7',
    //     ]);

    //     if ($validator->fails()) {
    //         return response()->json(['errors' => $validator->errors()], 422);
    //     }

    //     $blvckbox = Blvckbox::create($request->all());

    //     return response()->json([
    //         'message' => 'Blvckbox created successfully!',
    //         'data' => $blvckbox
    //     ], 201);
    // }


    public function store(Request $request)
    {

        $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:blvckboxes',
            'subtitle' => 'nullable|string|max:255',
            'date' => 'nullable|date',
            'background' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
    
        $backgroundImageUrl = null;
        if ($request->hasFile('background')) {
            $backgroundImage = $request->file('background');
            $filePath = $backgroundImage->store('blvckbox', 'public');
            $backgroundImageUrl = Storage::url($filePath);
        }
    
        $date = $request->date ?? Carbon::now();
    
        $blvckbox = Blvckbox::create([
            'title' => $request->title,
            'slug' => $request->slug,
            'subtitle' => $request->subtitle,
            'date' => $date,
            'background' => $backgroundImageUrl,
        ]);
    
        return response()->json($blvckbox, 201);
    }

    public function show($slug)
    {
        try {
            $blvckbox = Blvckbox::where('slug', $slug)->firstOrFail();
            return response()->json($blvckbox, 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Blvckbox not found.'], 404);
        }
    }

    public function update(Request $request, $slug)
    {
        $blvckbox = Blvckbox::where('slug', $slug)->first();
        if (!$blvckbox) {
            return response()->json(['message' => 'Blvckbox not found'], 404);
        }
    
        $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:blvckboxes,slug,' . $blvckbox->id,
            'subtitle' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'date' => 'nullable|date',
            'background' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);
    
        if ($request->hasFile('background')) {
            $backgroundImage = $request->file('background');
            $filePath = $backgroundImage->store('blvckbox', 'public');
            $backgroundImageUrl = Storage::url($filePath);
    
            $blvckbox->background = $backgroundImageUrl;
        }
    
        $blvckbox->update($request->except('background'));
        return response()->json($blvckbox);
    }
    

    public function destroy($id)
    {
        $blvckbox = Blvckbox::find($id);
        if (!$blvckbox) {
            return response()->json(['message' => 'Blvckbox not found'], 404);
        }

        $blvckbox->delete();
        return response()->json(['message' => 'Blvckbox deleted successfully']);
    }
    
}
