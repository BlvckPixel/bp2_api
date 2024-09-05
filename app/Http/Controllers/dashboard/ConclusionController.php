<?php

namespace App\Http\Controllers\dashboard;
use App\Http\Controllers\Controller;
use App\Models\Blvckbox;
use App\Models\Conclusion;
use Illuminate\Http\Request;

class ConclusionController extends Controller
{
    public function storeOrUpdate(Request $request, $slug)
    {
        $validated = $request->validate([
            'section' => 'required|string',
            'background_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $blvckbox = Blvckbox::where('slug', $slug)->firstOrFail();

        $editorial = Conclusion::updateOrCreate(
            ['blvckbox_id' => $blvckbox->id],
            [
                'section' => $validated['section'],
                'background_image' => $this->uploadImage($request)
            ]
        );

        return response()->json($editorial, 200);
    }

    public function getEditorial($slug)
    {
        $blvckbox = Blvckbox::where('slug', $slug)->firstOrFail();
        $editorial = Conclusion::where('blvckbox_id', $blvckbox->id)->first();

        return response()->json($editorial, 200);
    }

    private function uploadImage(Request $request)
    {
        if ($request->hasFile('background_image')) {
            return $request->file('background_image')->store('background_images', 'public');
        }
        return null;
    }
}
