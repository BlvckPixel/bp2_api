<?php

namespace App\Http\Controllers\dashboard;

use App\Http\Controllers\Controller;
use App\Models\Blvckcard;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function getStats()
    {
        $blvckcardsCount = Blvckcard::count();
        $usersCount = User::count();

        return response()->json([
            'blvckcards' => $blvckcardsCount,
            'users' => $usersCount,
            'subscribers' => '0',
        ]);
    }
}
