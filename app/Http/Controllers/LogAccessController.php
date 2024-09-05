<?php

namespace App\Http\Controllers;

use App\Models\UserAccessLog;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LogAccessController extends Controller
{
    public function index()
    {
        $now = Carbon::now();
        $last180Days = $now->copy()->subDays(180);
    
        $accessData = UserAccessLog::select(DB::raw('DATE(accessed_at) as date'), DB::raw('count(*) as count'))
            ->where('accessed_at', '>=', $last180Days)
            ->groupBy(DB::raw('DATE(accessed_at)'))
            ->orderBy('date', 'asc')
            ->get();
    
        $formattedData = $accessData->map(function ($item) {
            return [
                'date' => $item->date,
                'count' => $item->count,
            ];
        });
    
        return response()->json([
            'accessData' => $formattedData,
        ]);
    }

    public function logAccess(Request $request) {
        if (Auth::check()) {
            UserAccessLog::create([
                'user_id' => Auth::id(),
                'accessed_at' => now(),
                'ip_address' => $request->ip(),
                'user_agent' => $request->header('User-Agent'),
            ]);
        }

        return response()->json(['message' => 'Access logged successfully.']);
    }

    
    public function revenueStats()
    {
        $now = Carbon::now();
        $last24Hours = $now->copy()->subHours(24);
        $last360Days = $now->copy()->subDays(360);
    
        // Query revenue data for the last 24 hours
        $revenue24Hours = DB::table('payments')
            ->select(DB::raw('DATE(paid_at) as date'), DB::raw('SUM(amount) as total_revenue'))
            ->where('paid_at', '>=', $last24Hours)
            ->groupBy(DB::raw('DATE(paid_at)'))
            ->orderBy('date', 'asc')
            ->get();
    
        // Query revenue data for the last 360 days
        $revenue360Days = DB::table('payments')
            ->select(DB::raw('DATE(paid_at) as date'), DB::raw('SUM(amount) as total_revenue'))
            ->where('paid_at', '>=', $last360Days)
            ->groupBy(DB::raw('DATE(paid_at)'))
            ->orderBy('date', 'asc')
            ->get();
    
        // Format revenue data for the last 24 hours
        $formattedRevenue24Hours = $revenue24Hours->map(function ($item) {
            return [
                'date' => $item->date,
                'total_revenue' => $item->total_revenue,
            ];
        });
    
        // Format revenue data for the last 360 days
        $formattedRevenue360Days = $revenue360Days->map(function ($item) {
            return [
                'date' => $item->date,
                'total_revenue' => $item->total_revenue,
            ];
        });
    
        return response()->json([
            'revenue24Hours' => $formattedRevenue24Hours,
            'revenue360Days' => $formattedRevenue360Days,
        ]);
    }
    
    
}
