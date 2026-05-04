<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Http\Requests\Public\ContactRequest;
use App\Models\SupportTicket;
use App\Notifications\AdminSupportTicketReceivedNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Notification;
use Illuminate\View\View;

class ContactController extends Controller
{
    public function create(): View
    {
        return view('public.pages.contact');
    }

    public function store(ContactRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $user = $request->user('user');

        $ticket = SupportTicket::query()->create([
            'user_id' => $user?->id,
            'requester_name' => $validated['name'],
            'requester_email' => $validated['email'],
            'subject' => $validated['subject'],
            'status' => 'open',
        ]);

        $ticket->messages()->create([
            'author_user_id' => $user?->id,
            'body' => $validated['message'],
            'is_internal' => false,
        ]);

        // Notify Admin
        Notification::route('mail', 'concierge@watchhub.jp')
            ->notify(new AdminSupportTicketReceivedNotification($ticket));

        return back()->with('success', 'Your message has been transmitted successfully to our administrative hub. Reference ID: #'.$ticket->id);
    }
}
