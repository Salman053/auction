<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NotificationController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user('user');

        $notifications = $user
            ? $user->notifications()->latest()->paginate(25)
            : collect();

        return view('user.notifications.index', [
            'notifications' => $notifications,
        ]);
    }

    public function markAllRead(Request $request): RedirectResponse
    {
        $user = $request->user('user');
        $user?->unreadNotifications?->markAsRead();

        return back()->with('success', 'Notification marked as read.');
    }
}
