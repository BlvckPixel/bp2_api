<?php

namespace App\Http\Controllers;

use App\Models\Blvckbox;
use App\Models\Blvckcard;
use App\Models\Contentcard;
use Illuminate\Http\Request;

class BlvckcardsController extends Controller
{
    public function index($slug)
    {
        $contentcard = Contentcard::where('slug', $slug)->firstOrFail();
        $contentcards = Blvckcard::with('images')
            ->where('contentcard_id', $contentcard->id)
            ->paginate(5);
        return response()->json($contentcards);
    }
    
    public function show($slug)
    {
        $blvckcard = Blvckcard::with('images')
            ->where('slug', $slug)
            ->firstOrFail();
        
        return response()->json($blvckcard);
    }
}
