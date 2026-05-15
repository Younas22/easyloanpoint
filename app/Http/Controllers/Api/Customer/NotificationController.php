<?php

namespace App\Http\Controllers\Api\Customer;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    use ApiResponse;

    public function index(Request $request): JsonResponse
    {
        $notifications = Notification::where('user_id', auth()->id())
            ->latest()
            ->paginate(20);

        return $this->success('Notifications retrieved.', [
            'notifications' => $notifications->map(fn ($n) => [
                'id'         => $n->id,
                'title'      => $n->title,
                'message'    => $n->message,
                'type'       => $n->type,
                'type_label' => $n->type_label,
                'is_read'    => $n->is_read,
                'read_at'    => $n->read_at?->toISOString(),
                'created_at' => $n->created_at->toISOString(),
                'time_ago'   => $n->created_at->diffForHumans(),
            ]),
            'pagination' => [
                'current_page' => $notifications->currentPage(),
                'last_page'    => $notifications->lastPage(),
                'per_page'     => $notifications->perPage(),
                'total'        => $notifications->total(),
            ],
            'unread_count' => Notification::where('user_id', auth()->id())
                ->where('is_read', false)
                ->count(),
        ]);
    }

    public function unreadCount(): JsonResponse
    {
        $count = Notification::where('user_id', auth()->id())
            ->where('is_read', false)
            ->count();

        return $this->success('Unread count.', ['count' => $count]);
    }

    public function markRead(int $id): JsonResponse
    {
        $notification = Notification::where('user_id', auth()->id())->findOrFail($id);
        $notification->markAsRead();

        return $this->success('Marked as read.');
    }

    public function markAllRead(): JsonResponse
    {
        Notification::where('user_id', auth()->id())
            ->where('is_read', false)
            ->update(['is_read' => true, 'read_at' => now()]);

        return $this->success('All notifications marked as read.');
    }
}
