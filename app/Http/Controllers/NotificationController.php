<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Inertia\Inertia;

class NotificationController extends Controller
{
    public function __construct(
        private NotificationService $notificationService
    ) {}

    /**
     * Display all notifications for the authenticated user
     */
    public function index()
    {
        $user = auth()->user();
        $notifications = $user->notifications()->paginate(20);

        return Inertia::render('Notifications/Index', [
            'notifications' => $notifications,
        ]);
    }

    /**
     * Mark a specific notification as read
     */
    public function markAsRead(Notification $notification)
    {
        // Ensure the notification belongs to the authenticated user
        if ($notification->user_id !== auth()->id()) {
            abort(403);
        }

        $this->notificationService->markAsRead($notification);

        return response()->json(['success' => true]);
    }

    /**
     * Mark all notifications as read for the authenticated user
     */
    public function markAllAsRead()
    {
        $user = auth()->user();
        $count = $this->notificationService->markAllAsReadForUser($user);

        return response()->json([
            'success' => true,
            'marked_count' => $count,
        ]);
    }

    /**
     * Delete a notification
     */
    public function destroy(Notification $notification)
    {
        // Ensure the notification belongs to the authenticated user
        if ($notification->user_id !== auth()->id()) {
            abort(403);
        }

        $notification->delete();

        return response()->json(['success' => true]);
    }

    /**
     * Get unread notification count for the authenticated user
     */
    public function getUnreadCount()
    {
        $user = auth()->user();
        $count = $user->unreadNotifications()->count();

        return response()->json(['count' => $count]);
    }
}
