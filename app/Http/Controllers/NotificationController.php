<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $query = Notification::where('user_id', auth()->id())->latest();

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('status')) {
            $query->where('is_read', $request->status === 'read');
        }

        $notifications = $query->paginate(20)->withQueryString();
        $unreadCount   = Notification::where('user_id', auth()->id())
            ->where('is_read', false)
            ->count();

        return view('notifications.index', compact('notifications', 'unreadCount'));
    }

    public function dropdown(): JsonResponse
    {
        $notifications = Notification::where('user_id', auth()->id())
            ->latest()
            ->limit(8)
            ->get()
            ->map(fn ($n) => [
                'id'         => $n->id,
                'title'      => $n->title,
                'message'    => $n->message,
                'type'       => $n->type,
                'type_color' => $n->type_color,
                'type_icon'  => $n->type_icon,
                'type_label' => $n->type_label,
                'is_read'    => $n->is_read,
                'time'       => $n->created_at->diffForHumans(),
            ]);

        $unreadCount = Notification::where('user_id', auth()->id())
            ->where('is_read', false)
            ->count();

        return response()->json([
            'status'        => true,
            'notifications' => $notifications,
            'unread_count'  => $unreadCount,
        ]);
    }

    public function unreadCount(): JsonResponse
    {
        $count = Notification::where('user_id', auth()->id())
            ->where('is_read', false)
            ->count();

        return response()->json(['status' => true, 'count' => $count]);
    }

    public function markRead(Notification $notification): JsonResponse
    {
        abort_if($notification->user_id !== auth()->id(), 403);

        $notification->markAsRead();

        return response()->json(['status' => true, 'message' => 'Marked as read.']);
    }

    public function markAllRead(): JsonResponse
    {
        Notification::where('user_id', auth()->id())
            ->where('is_read', false)
            ->update(['is_read' => true, 'read_at' => now()]);

        return response()->json(['status' => true, 'message' => 'All notifications marked as read.']);
    }

    public function destroy(Notification $notification)
    {
        abort_if($notification->user_id !== auth()->id(), 403);

        $notification->delete();

        if (request()->expectsJson()) {
            return response()->json(['status' => true, 'message' => 'Notification deleted.']);
        }

        return back()->with('success', 'Notification deleted.');
    }
}
