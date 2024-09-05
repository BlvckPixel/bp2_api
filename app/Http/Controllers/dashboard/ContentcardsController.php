<?php

namespace App\Http\Controllers\dashboard;

use App\Http\Controllers\Controller;
use App\Models\Contentcard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ContentcardsController extends Controller
{
    public function index(Request $request)
    {
        $blvckboxId = $request->query('blvckbox_id');
        
        if ($blvckboxId) {
            $contentCards = ContentCard::where('blvckbox_id', $blvckboxId)->get();
        } else {
            $contentCards = ContentCard::all();
        }

        return response()->json($contentCards);
    }
    public function show($slug)
    {
        $contentCard = ContentCard::where('slug', $slug)->firstOrFail();
        return response()->json($contentCard);
    }
    

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'blvckbox_id' => 'required|exists:blvckboxes,id',
            'slug' => 'required|string|max:255|unique:contentcards',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);
    
        $imageUrl = null;
    
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('contentcard', 'public');
            $imageUrl = Storage::url($imagePath);
        }
    
        $contentCard = ContentCard::create([
            'title' => $request->title,
            'description' => $request->description,
            'blvckbox_id' => $request->blvckbox_id,
            'slug' => $request->slug,
            'background' => $imageUrl,
        ]);
    
        return response()->json($contentCard, 201);
    }
    

    public function update(Request $request, $slug)
    {
        $request->validate([
            'title' => 'string|max:255',
            'description' => 'string',
            'blvckbox_id' => 'exists:blvckboxes,id',
            'slug' => 'string|max:255|unique:contentcards,slug,' . $slug . ',slug',
            'background' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
    
        $contentCard = ContentCard::where('slug', $slug)->firstOrFail();
    
        if ($request->hasFile('background')) {
            $file = $request->file('background');
            $filePath = $file->store('contentcard', 'public');
            $backgroundUrl = Storage::url($filePath);
    
            if ($contentCard->background) {
                Storage::disk('public')->delete(parse_url($contentCard->background, PHP_URL_PATH));
            }
        } else {
            $backgroundUrl = $contentCard->background;
        }
    
        $contentCard->update([
            'title' => $request->title,
            'description' => $request->description,
            'blvckbox_id' => $request->blvckbox_id,
            'slug' => $request->slug,
            'background' => $backgroundUrl,
        ]);
    
        return response()->json($contentCard);
    }
    


    public function destroy($id)
    {
        $contentCard = ContentCard::findOrFail($id);
        $contentCard->delete();

        return response()->json(null, 204);
    }
}
