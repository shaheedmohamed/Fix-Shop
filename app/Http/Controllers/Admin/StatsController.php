<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Illuminate\Http\Request;

class StatsController extends Controller
{
    public function kpis()
    {
        $totalUsers = DB::table('users')->count();
        $totalVisits = DB::table('visits')->count();
        $dailyLogins = DB::table('login_logs')
            ->where('success', true)
            ->whereDate('created_at', Carbon::today())
            ->count();

        return response()->json([
            'users' => $totalUsers,
            'visits' => $totalVisits,
            'dailyLogins' => $dailyLogins,
        ]);
    }

    public function usersGrowth()
    {
        $start = Carbon::now()->startOfMonth()->subMonths(11);
        $rows = DB::table('users')
            ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as ym, COUNT(*) as c")
            ->where('created_at', '>=', $start)
            ->groupBy('ym')
            ->orderBy('ym')
            ->get();

        $map = collect();
        $cursor = $start->copy();
        for ($i=0; $i<12; $i++) { $map[$cursor->format('Y-m')] = 0; $cursor->addMonth(); }
        foreach ($rows as $r) { $map[$r->ym] = (int) $r->c; }

        // Convert to cumulative growth
        $labels = [];
        $data = [];
        $sum = 0;
        foreach ($map as $ym => $c) {
            $labels[] = Carbon::createFromFormat('Y-m', $ym)->format('M');
            $sum += $c; $data[] = $sum;
        }

        return response()->json(['labels' => $labels, 'data' => $data]);
    }

    public function visitsTrend()
    {
        $start = Carbon::now()->subDays(29)->startOfDay();
        $rows = DB::table('visits')
            ->selectRaw("DATE(created_at) as d, COUNT(*) as c")
            ->where('created_at', '>=', $start)
            ->groupBy('d')
            ->orderBy('d')
            ->get();

        $map = collect();
        for ($i=0; $i<30; $i++) { $map[$start->copy()->addDays($i)->toDateString()] = 0; }
        foreach ($rows as $r) { $map[$r->d] = (int) $r->c; }

        $labels = []; $data = [];
        foreach ($map as $date => $c) { $labels[] = Carbon::parse($date)->format('d M'); $data[] = $c; }

        return response()->json(['labels' => $labels, 'data' => $data]);
    }

    public function dailyLogins()
    {
        $start = Carbon::now()->startOfWeek();
        $rows = DB::table('login_logs')
            ->selectRaw("DATE(created_at) as d, COUNT(*) as c")
            ->where('success', true)
            ->where('created_at', '>=', $start)
            ->groupBy('d')
            ->orderBy('d')
            ->get();

        $map = collect();
        for ($i=0; $i<7; $i++) { $map[$start->copy()->addDays($i)->toDateString()] = 0; }
        foreach ($rows as $r) { $map[$r->d] = (int) $r->c; }

        $labels = []; $data = [];
        foreach ($map as $date => $c) { $labels[] = Carbon::parse($date)->format('D'); $data[] = $c; }

        return response()->json(['labels' => $labels, 'data' => $data]);
    }

    public function sources()
    {
        $start = Carbon::now()->subDays(29)->startOfDay();
        $rows = DB::table('visits')
            ->selectRaw("COALESCE(NULLIF(referer,''), 'Direct') as src, COUNT(*) as c")
            ->where('created_at', '>=', $start)
            ->groupBy('src')
            ->orderByDesc('c')
            ->limit(6)
            ->get();

        $labels = $rows->pluck('src');
        $data = $rows->pluck('c');

        return response()->json(['labels' => $labels, 'data' => $data]);
    }
}
