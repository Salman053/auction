<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SupportTicketReplyRequest;
use App\Models\SupportTicket;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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
        $supportTicket->load(['messages' => fn ($query) => $query->latest(), 'user']);

        return view('admin.support-tickets.show', [
            'ticket' => $supportTicket,
        ]);
    }

    public function reply(
        SupportTicketReplyRequest $request,
        SupportTicket $supportTicket
    ): RedirectResponse {
        $admin = $request->user('admin');

        $validated = $request->validated();

        $supportTicket->messages()->create([
            'author_user_id' => $admin?->id,
            'body' => $validated['body'],
            'is_internal' => (bool) ($validated['is_internal'] ?? false),
        ]);

        return back()->with('success', 'Support ticket status updated.');
    }
}
