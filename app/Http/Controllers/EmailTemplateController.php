<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

use App\Models\EmailTemplate;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class EmailTemplateController extends Controller
{
    /**
     * Get all email templates.
     */
    public function index()
    {
        $templates = EmailTemplate::all();
        return response()->json([
            'success' => true,
            'data' => $templates,
        ], Response::HTTP_OK);
    }

    /**
     * Get a specific email template by ID.
     */
    public function show($id)
    {
        $template = EmailTemplate::find($id);

        if (!$template) {
            return response()->json([
                'success' => false,
                'message' => 'Email template not found.',
            ], Response::HTTP_NOT_FOUND);
        }

        return response()->json([
            'success' => true,
            'data' => $template,
        ], Response::HTTP_OK);
    }

    /**
     * Create a new email template.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:email_templatese,name',
            'subject' => 'required|string',
            'body' => 'required',
        ]);

        $template = EmailTemplate::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Email template created successfully.',
            'data' => $template,
        ], Response::HTTP_CREATED);
    }

    /**
     * Update an existing email template.
     */
    public function update(Request $request, $id)
    {
        $template = EmailTemplate::find($id);

        if (!$template) {
            return response()->json([
                'success' => false,
                'message' => 'Email template not found.',
            ], Response::HTTP_NOT_FOUND);
        }

        $validated = $request->validate([
            'name' => 'sometimes|string|unique:email_templatese,name,' . $id,
            'subject' => 'sometimes|string',
            'body' => 'sometimes',
        ]);

        $template->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Email template updated successfully.',
            'data' => $template,
        ], Response::HTTP_OK);
    }

    /**
     * Delete an email template.
     */
    public function destroy($id)
    {
        $template = EmailTemplate::find($id);

        if (!$template) {
            return response()->json([
                'success' => false,
                'message' => 'Email template not found.',
            ], Response::HTTP_NOT_FOUND);
        }

        $template->delete();

        return response()->json([
            'success' => true,
            'message' => 'Email template deleted successfully.',
        ], Response::HTTP_OK);
    }
}

