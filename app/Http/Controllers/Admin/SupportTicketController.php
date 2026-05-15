<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SupportTicketReplyRequest;
use App\Models\SupportTicket;
use App\Models\SupportTicketMessage;
use App\Notifications\SupportTicketRepliedNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Illuminate\View\View;

class SupportTicketController extends Controller
{
    public function index(Request $request): View
    {
        $status = (string) $request->string('status', 'open');
        if (! in_array($status, ['open', 'closed', 'all'], true)) {
            $status = 'open';
        }

        $query = SupportTicket::query()->latest();
        if ($status !== 'all') {
            $query->where('status', $status);
        }

        return view('admin.support-tickets.index', [
            'tickets' => $query->paginate(25)->withQueryString(),
            'status' => $status,
        ]);
    }

    public function show(Request $request, SupportTicket $supportTicket): View
    {
        $supportTicket->load(['messages' => fn ($query) => $query->oldest()->with('author'), 'user']);

        return view('admin.support-tickets.show', [
            'ticket' => $supportTicket,
        ]);
    }

    public function reply(
        SupportTicketReplyRequest $request,
        SupportTicket $supportTicket
    ): RedirectResponse {
        if ($supportTicket->status === 'closed') {
            return back()->with('error', 'You cannot reply to a closed ticket. Please reopen it first.');
        }

        $supportTicket->load('user');

        $admin = $request->user('admin');

        $validated = $request->validated();

        $message = $supportTicket->messages()->create([
            'author_user_id' => $admin?->id,
            'body' => $validated['body'],
            'is_internal' => (bool) ($validated['is_internal'] ?? false),
        ]);

        if (! $message->is_internal) {
            if ($supportTicket->user) {
                $supportTicket->user->notify(new SupportTicketRepliedNotification($supportTicket, $message));
            } elseif ($supportTicket->requester_email) {
                Notification::route('mail', $supportTicket->requester_email)
                    ->notify(new SupportTicketRepliedNotification($supportTicket, $message));
            }
        }

        return back()->with('success', 'Your response has been recorded.');
    }

    public function close(Request $request, SupportTicket $supportTicket): RedirectResponse
    {
        $supportTicket->update([
            'status' => 'closed',
            'closed_at' => now(),
        ]);

        return back()->with('success', 'Ticket has been closed.');
    }

    public function reopen(Request $request, SupportTicket $supportTicket): RedirectResponse
    {
        $supportTicket->update([
            'status' => 'open',
            'closed_at' => null,
        ]);

        return back()->with('success', 'Ticket has been reopened.');
    }

    public function destroy(SupportTicket $supportTicket): RedirectResponse
    {
        $supportTicket->delete();

        return redirect()->route('admin.support-tickets.index')->with('success', 'Ticket has been deleted.');
    }

    public function destroyMessage(SupportTicket $supportTicket, SupportTicketMessage $message): RedirectResponse
    {
        if ($message->support_ticket_id !== $supportTicket->id) {
            abort(404);
        }

        $message->delete();

        return back()->with('success', 'Message has been removed.');
    }
}
