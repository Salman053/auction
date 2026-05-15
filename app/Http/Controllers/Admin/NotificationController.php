<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NotificationController extends Controller
{
    public function index(Request $request): View
    {
        $notifications = $request->user()->notifications()->paginate(25);

        return view('admin.notifications.index', [
            'notifications' => $notifications,
        ]);
    }

    public function read(string $id, Request $request): RedirectResponse
    {
        $notification = $request->user()->notifications()->findOrFail($id);
        $notification->markAsRead();

        if (isset($notification->data['ticket_id'])) {
            return redirect()->route('admin.support-tickets.show', $notification->data['ticket_id']);
        }

        $actionUrl = isset($notification->data['auction_id'])
            ? route('admin.auctions.show', $notification->data['auction_id'])
            : route('admin.notifications.index');

        return redirect($actionUrl);
    }

    public function markAllRead(Request $request): RedirectResponse
    {
        $request->user()->unreadNotifications->markAsRead();

        return back()->with('success', 'All notifications marked as read.');
    }
}
