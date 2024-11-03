<?php

namespace App\Http\Controllers;

use App\Models\Blvckbox;
use App\Models\Conclusion;
use Illuminate\Http\Request;
use App\Models\Contentcard;
use App\Models\Editorial;

class ContentcardsController extends Controller
{
    public function index($slug)
    {
        $blvckbox = Blvckbox::where('slug', $slug)->firstOrFail();

        $contentcards = Contentcard::where('blvckbox_id', $blvckbox->id)->get();

        $editorial = Editorial::where('blvckbox_id', $blvckbox->id)->first();

        $conclusion = Conclusion::where('blvckbox_id', $blvckbox->id)->first();


        return response()->json([
            'contentcards' => $contentcards,
            'editorial' => $editorial,
            'conclusion' => $conclusion,
        ]);
    }

}
