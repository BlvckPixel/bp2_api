<?php

namespace App\Http\Controllers\dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Blvckcard;
use App\Models\BlvckcardImage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class BlvckcardsController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
    
        $blvckcards = Blvckcard::with('images')->where('user_id', $user->id)->get();
    
        return response()->json($blvckcards);
    }

    public function store(Request $request)
    {
        // Validation
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:100',
            'slug' => 'required|string|max:100|unique:blvckcards',
            'description' => 'required|string|max:2000',
            'teaserdescription' => 'required|string|max:1000',
            'metakeywords' => 'required|string',
            'date' => 'required|date',
            'images.*' => 'nullable|mimes:jpeg,png,jpg,gif|max:20480',
            'videos.*' => 'nullable|mimes:mp4,avi,mov,flv|max:20480',
            'blvckbox_id' => 'required|exists:blvckboxes,id',
            'contentcard_id' => 'nullable|exists:contentcards,id',
        ]);
    
        if ($validator->fails()) {
            Log::error('Validation failed:', $validator->errors()->toArray());
            return response()->json(['errors' => $validator->errors()], 422);
        }
    
        // Creating Blvckcard
        $user = Auth::user();


        $blvckcard = Blvckcard::create([
            'title' => $request->title,
            'slug' => $request->slug,
            'description' => $request->description,
            'teaser_description' => $request->teaserdescription,
            'meta_keywords' => $request->metakeywords,
            'date' => $request->date,
            'blvckbox_id' => $request->blvckbox_id,
            'contentcard_id' => $request->contentcard_id,
            'user_id' => $user->id,
        ]);
    
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                $filePath = $file->store('blvckcards', 'public');
                $fileUrl = Storage::url($filePath);
        
                BlvckcardImage::create([
                    'blvckcard_id' => $blvckcard->id,
                    'image_path' => $fileUrl,
                    'type' => 'image',
                ]);
            }
        }
    
        if ($request->hasFile('videos')) {
            foreach ($request->file('videos') as $file) {
                $fileName = uniqid() . '_' . $file->getClientOriginalName();
                $filePath = $file->storeAs('public/blvckcards/videos', $fileName);
    
                BlvckcardImage::create([
                    'blvckcard_id' => $blvckcard->id,
                    'image_path' => 'storage/blvckcards/videos/' . $fileName,
                    'type' => 'video',
                ]);
            }
        }
    
        return response()->json($blvckcard, 201);
    }
    

    public function show($slug)
    {
        $blvckcard = Blvckcard::with('images')->where('slug', $slug)->firstOrFail();
        return response()->json($blvckcard);
    }    

    public function update(Request $request, $slug)
{
    $blvckcard = Blvckcard::where('slug', $slug)->firstOrFail();
    
    $validator = Validator::make($request->all(), [
        'title' => 'required|string|max:100',
        'slug' => 'required|string|max:100|unique:blvckcards,slug,' . $blvckcard->id,
        'description' => 'required|string|max:2000',
        'teaserdescription' => 'required|string|max:1000',
        'metakeywords' => 'required|string|max:255',
        'date' => 'required|date',
        'images.*' => 'nullable|mimes:jpeg,png,jpg,gif|max:20480',
        'videos.*' => 'nullable|mimes:mp4,avi,mov,flv|max:20480',
        'blvckbox_id' => 'required|exists:blvckboxes,id',
        'contentcard_id' => 'nullable|exists:contentcards,id',
    ]);
    
    if ($validator->fails()) {
        Log::error('Validation failed:', $validator->errors()->toArray());
        return response()->json(['errors' => $validator->errors()], 422);
    }
    
    $blvckcard->update($validator->validated());
    
    if ($request->hasFile('images')) {
        foreach ($request->file('images') as $image) {
            $filePath = $image->store('blvckcards', 'public');
            $fileUrl = Storage::url($filePath);
            
            $blvckcard->images()->create([
                'image_path' => $fileUrl,
            ]);
        }
    }
    
    $blvckcard->load('images');
    
    return response()->json($blvckcard);
}

    



    public function destroy($id)
    {
        $blvckcard = Blvckcard::findOrFail($id);
        $blvckcard->delete();
        return response()->json(['message' => 'Blvckcard deleted successfully']);
    }


    public function deleteImage($id)
{
    $image = BlvckcardImage::findOrFail($id);

    if (Storage::exists($image->image_path)) {
        Storage::delete($image->image_path);
    }

    $image->delete();

    return response()->json(['message' => 'Image deleted successfully.']);
}

}
