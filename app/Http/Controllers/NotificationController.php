<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class NotificationController extends Controller
{
    /**
     * Display all notifications with pagination and filters
     *
     * Matches: notifications/index.blade.php
     */
    public function index(Request $request)
    {
        // Get filter parameters
        $type = $request->get('type', ''); // error, warning, info
        $status = $request->get('status', ''); // read, unread
        $perPage = $request->get('per_page', 20);

        // Base query
        $query = DB::table('notifications')
            ->select('id', 'title', 'message', 'type', 'is_read', 'created_at', 'updated_at');

        // Apply filters
        if (!empty($type)) {
            $query->where('type', $type);
        }

        if ($status === 'read') {
            $query->where('is_read', 1);
        } elseif ($status === 'unread') {
            $query->where('is_read', 0);
        }

        // Order by latest
        $query->orderBy('created_at', 'desc');

        // Paginate
        $notifications = $query->paginate($perPage)
            ->appends([
                'type' => $type,
                'status' => $status,
                'per_page' => $perPage
            ]);

        // Statistics
        $totalNotifications = DB::table('notifications')->count();
        $unreadCount = DB::table('notifications')->where('is_read', 0)->count();
        $readCount = $totalNotifications - $unreadCount;

        $errorCount = DB::table('notifications')->where('type', 'error')->count();
        $warningCount = DB::table('notifications')->where('type', 'warning')->count();
        $infoCount = DB::table('notifications')->where('type', 'info')->count();

        return view('notifications.index', compact(
            'notifications',
            'totalNotifications',
            'unreadCount',
            'readCount',
            'errorCount',
            'warningCount',
            'infoCount',
            'type',
            'status',
            'perPage'
        ));
    }

    /**
     * Show create notification form
     */
    public function create()
    {
        return view('notifications.create');
    }

    /**
     * Store new notification
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'type' => 'required|in:error,warning,info,success',
        ]);

        try {
            DB::table('notifications')->insert([
                'title' => $validated['title'],
                'message' => $validated['message'],
                'type' => $validated['type'],
                'is_read' => 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);

            return redirect()
                ->route('notifications.index')
                ->with('success', 'Notification created successfully!');

        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Failed to create notification: ' . $e->getMessage());
        }
    }

    /**
     * Show notification detail
     */
    public function show($id)
    {
        $notification = DB::table('notifications')
            ->where('id', $id)
            ->first();

        if (!$notification) {
            abort(404, 'Notification not found');
        }

        // Mark as read when viewing
        if (!$notification->is_read) {
            DB::table('notifications')
                ->where('id', $id)
                ->update(['is_read' => 1, 'updated_at' => Carbon::now()]);
        }

        return view('notifications.show', compact('notification'));
    }

    /**
     * Show edit notification form
     */
    public function edit($id)
    {
        $notification = DB::table('notifications')
            ->where('id', $id)
            ->first();

        if (!$notification) {
            abort(404, 'Notification not found');
        }

        return view('notifications.edit', compact('notification'));
    }

    /**
     * Update notification
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'type' => 'required|in:error,warning,info,success',
        ]);

        try {
            $updated = DB::table('notifications')
                ->where('id', $id)
                ->update([
                    'title' => $validated['title'],
                    'message' => $validated['message'],
                    'type' => $validated['type'],
                    'updated_at' => Carbon::now(),
                ]);

            if ($updated) {
                return redirect()
                    ->route('notifications.show', $id)
                    ->with('success', 'Notification updated successfully!');
            } else {
                return redirect()
                    ->back()
                    ->with('error', 'Notification not found or no changes made.');
            }

        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Failed to update notification: ' . $e->getMessage());
        }
    }

    /**
     * Delete notification
     */
    public function destroy($id)
    {
        try {
            $deleted = DB::table('notifications')
                ->where('id', $id)
                ->delete();

            if ($deleted) {
                return redirect()
                    ->route('notifications.index')
                    ->with('success', 'Notification deleted successfully!');
            } else {
                return redirect()
                    ->back()
                    ->with('error', 'Notification not found.');
            }

        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Failed to delete notification: ' . $e->getMessage());
        }
    }

    /**
     * Mark single notification as read (AJAX)
     *
     * Used by: markAsRead() JavaScript function in view
     * Route: POST /notifications/{id}/mark-read
     */
    public function markRead($id)
    {
        try {
            $updated = DB::table('notifications')
                ->where('id', $id)
                ->update(['is_read' => 1, 'updated_at' => Carbon::now()]);

            if ($updated) {
                return response()->json([
                    'success' => true,
                    'message' => 'Notification marked as read'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Notification not found'
                ], 404);
            }

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mark all notifications as read
     *
     * Used by: markAllAsRead() JavaScript function in view
     * Route: POST /notifications/mark-all-read
     */
    public function markAllRead()
    {
        try {
            $updated = DB::table('notifications')
                ->where('is_read', 0)
                ->update(['is_read' => 1, 'updated_at' => Carbon::now()]);

            return redirect()
                ->route('notifications.index')
                ->with('success', $updated . ' notifications marked as read!');

        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Failed to mark notifications as read: ' . $e->getMessage());
        }
    }

    /**
     * Delete multiple notifications (bulk delete)
     */
    public function bulkDelete(Request $request)
    {
        $ids = $request->get('ids', []);

        if (empty($ids)) {
            return redirect()
                ->back()
                ->with('error', 'No notifications selected.');
        }

        try {
            $deleted = DB::table('notifications')
                ->whereIn('id', $ids)
                ->delete();

            return redirect()
                ->route('notifications.index')
                ->with('success', $deleted . ' notifications deleted successfully!');

        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Failed to delete notifications: ' . $e->getMessage());
        }
    }

    /**
     * Get unread notification count (API for AJAX)
     *
     * Used by: Sidebar badge and dashboard updates
     * Route: GET /notifications/api/unread-count
     */
    public function unreadCount()
    {
        try {
            $count = DB::table('notifications')
                ->where('is_read', 0)
                ->count();

            return response()->json([
                'success' => true,
                'count' => $count,
                'timestamp' => Carbon::now()->toDateTimeString()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'count' => 0,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get recent unread notifications (API for AJAX)
     *
     * Route: GET /notifications/api/unread
     */
    public function getUnread(Request $request)
    {
        $limit = $request->get('limit', 5);

        try {
            $notifications = DB::table('notifications')
                ->where('is_read', 0)
                ->orderBy('created_at', 'desc')
                ->limit($limit)
                ->get();

            return response()->json([
                'success' => true,
                'count' => $notifications->count(),
                'data' => $notifications,
                'timestamp' => Carbon::now()->toDateTimeString()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'count' => 0,
                'data' => [],
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
