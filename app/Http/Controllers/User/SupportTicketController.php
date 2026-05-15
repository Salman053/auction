<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\SupportTicketReplyRequest;
use App\Models\SupportTicket;
use App\Notifications\AdminSupportTicketReceivedNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Illuminate\View\View;

class SupportTicketController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user('user');
        $tickets = SupportTicket::query()
            ->where('user_id', $user->id)
            ->latest()
            ->paginate(15);

        return view('user.support.index', compact('tickets'));
    }

    public function create(Request $request): View
    {
        return view('user.support.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $user = $request->user('user');

        $validated = $request->validate([
            'subject' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string', 'min:10', 'max:5000'],
        ]);

        $ticket = SupportTicket::query()->create([
            'user_id' => $user->id,
            'requester_name' => $user->name,
            'requester_email' => $user->email,
            'subject' => $validated['subject'],
            'status' => 'open',
        ]);

        $ticket->messages()->create([
            'author_user_id' => $user->id,
            'body' => $validated['body'],
            'is_internal' => false,
        ]);

        // Notify Admin
        Notification::route('mail', config('mail.from.address'))
            ->notify(new AdminSupportTicketReceivedNotification($ticket));

        return redirect()->route('user.support.show', $ticket)
            ->with('success', 'Support ticket created successfully.');
    }

    public function show(Request $request, SupportTicket $supportTicket): View
    {
        $user = $request->user('user');
        if ($supportTicket->user_id !== $user->id) {
            abort(403);
        }

        $supportTicket->load(['messages' => function ($query) {
            $query->where('is_internal', false)->oldest();
        }]);

        return view('user.support.show', [
            'ticket' => $supportTicket,
        ]);
    }

    public function reply(SupportTicketReplyRequest $request, SupportTicket $supportTicket): RedirectResponse
    {
        $user = $request->user('user');
        if ($supportTicket->user_id !== $user->id) {
            abort(403);
        }

        if ($supportTicket->status === 'closed') {
            return back()->with('error', 'You cannot reply to a closed ticket.');
        }

        $validated = $request->validated();

        $supportTicket->messages()->create([
            'author_user_id' => $user->id,
            'body' => $validated['body'],
            'is_internal' => false,
        ]);

        $supportTicket->update(['status' => 'open']);

        return back();
    }

    public function close(Request $request, SupportTicket $supportTicket): RedirectResponse
    {
        $user = $request->user('user');
        if ($supportTicket->user_id !== $user->id) {
            abort(403);
        }

        $supportTicket->update([
            'status' => 'closed',
            'closed_at' => now(),
        ]);

        return back()->with('success', 'Ticket has been closed.');
    }

    public function reopen(Request $request, SupportTicket $supportTicket): RedirectResponse
    {
        $user = $request->user('user');
        if ($supportTicket->user_id !== $user->id) {
            abort(403);
        }

        $supportTicket->update([
            'status' => 'open',
            'closed_at' => null,
        ]);

        return back()->with('success', 'Ticket has been reopened.');
    }
}
