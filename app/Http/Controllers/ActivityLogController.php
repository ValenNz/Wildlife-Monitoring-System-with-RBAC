<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Carbon\Carbon;

class ActivityLogController extends Controller
{
    /**
     * Display a listing of activity logs.
     */
    public function index(Request $request)
    {
        $query = DB::table('system_logs')
            ->select('id', 'level', 'message', 'context', 'created_at')
            ->orderBy('created_at', 'desc');

        if ($request->filled('level')) {
            $query->where('level', $request->level);
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('message', 'LIKE', "%{$request->search}%")
                  ->orWhere('context', 'LIKE', "%{$request->search}%");
            });
        }

        $logs = $query->paginate(20);
        $totalLogs = DB::table('system_logs')->count();
        $todayLogs = DB::table('system_logs')
            ->where('created_at', '>=', Carbon::today())
            ->count();

        return view('activity-logs.index', compact('logs', 'totalLogs', 'todayLogs'));
    }

    /**
     * Display the specified log.
     */
    public function show($id)
    {
        $log = DB::table('system_logs')
            ->where('id', $id)
            ->first();

        if (!$log) {
            return redirect()->route('activity-logs.index')
                ->with('error', 'Log not found');
        }

        return view('activity-logs.show', compact('log'));
    }

    /**
     * Display logs by specific user (placeholder - no user_id in system_logs)
     */
    public function byUser($userId)
    {
        // Karena system_logs tidak punya kolom user_id,
        // kita kembalikan semua log dengan pesan peringatan
        $logs = DB::table('system_logs')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('activity-logs.index', [
            'logs' => $logs,
            'totalLogs' => DB::table('system_logs')->count(),
            'todayLogs' => DB::table('system_logs')->where('created_at', '>=', Carbon::today())->count(),
            'warning' => 'User filtering not available - system_logs has no user_id column'
        ]);
    }

    /**
     * Export logs to CSV
     */
    public function export(Request $request)
    {
        $query = DB::table('system_logs')
            ->select('id', 'level', 'message', 'context', 'created_at');

        if ($request->filled('level')) {
            $query->where('level', $request->level);
        }

        $logs = $query->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="system_logs_' . now()->format('Y-m-d') . '.csv"',
        ];

        $callback = function() use ($logs) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['ID', 'Level', 'Message', 'Context', 'Created At']);

            foreach ($logs as $log) {
                fputcsv($file, [
                    $log->id,
                    $log->level,
                    $log->message,
                    $log->context ?? '',
                    $log->created_at
                ]);
            }

            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }

    /**
     * Clear all logs (use with caution!)
     */
    public function clear(Request $request)
    {
        DB::table('system_logs')->truncate();
        return redirect()->route('activity-logs.index')
            ->with('success', 'All system logs have been cleared!');
    }
}
