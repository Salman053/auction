<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NotificationController extends Controller
{
    public function index(Request $request): View
    {
        $notifications = $request->user()->notifications()->paginate(25);
        
        return view('admin.notifications.index', [
            'notifications' => $notifications
        ]);
    }

    public function markAllRead(Request $request): \Illuminate\Http\RedirectResponse
    {
        $request->user()->unreadNotifications->markAsRead();
        
        return back()->with('success', 'All notifications marked as read.');
    }
}
