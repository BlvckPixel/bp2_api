<?php
// app/Http/Requests/SendCustomEmailRequest.php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SendCustomEmailRequest extends FormRequest
{
    public function rules()
    {
        return [
            'emailArray' => 'required|array',
            'emailArray.*' => 'email',
            'subject' => 'required|string',
            'content' => 'required|string',
        ];
    }
}