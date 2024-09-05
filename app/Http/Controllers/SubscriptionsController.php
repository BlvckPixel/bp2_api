<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Package;

class SubscriptionsController extends Controller
{
    public function index()
    {
        return Package::all();
    }
}
