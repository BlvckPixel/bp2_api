<?php


namespace App\Http\Controllers;

use App\Models\Emails;
use Illuminate\Http\Request;

class EmailTempController extends Controller
{
    public function index()
    {
        $emails = Emails::all();
        return response()->json($emails);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string',
            'content' => 'required|string', // Validate as a string
        ]);

        $email = Emails::create($request->all());

        return response()->json($email, 201); // 201 Created
    }

    public function show(Emails $email)
    {
        return response()->json($email);
    }

    public function update(Request $request, Emails $email)
    {
        $request->validate([
            'title' => 'required|string',
            'content' => 'required|string', // Validate as a string
        ]);

        $email->update($request->all());

        return response()->json($email);
    }

    public function destroy(Emails $email)
    {
        $email->delete();
        return response()->json(null, 204); // 204 No Content
    }
}