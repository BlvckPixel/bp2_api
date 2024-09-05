<?php

namespace App\Http\Controllers;

use App\Models\Blvckbox;
use Illuminate\Http\Request;


class BlvckboxController extends Controller
{
    public function index()
    {
        $blvckboxes = Blvckbox::all();
        return response()->json($blvckboxes);
    }

    public function show($id)
    {
        $blvckbox = Blvckbox::find($id);
        if (!$blvckbox) {
            return response()->json(['message' => 'Blvckbox not found'], 404);
        }
        return response()->json($blvckbox);
    }

}
