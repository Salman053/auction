<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\SupportTicketReplyRequest;
use App\Models\SupportTicket;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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

        $validated = $request->validated();

        $supportTicket->messages()->create([
            'author_user_id' => $user->id,
            'body' => $validated['body'],
            'is_internal' => false,
        ]);

        $supportTicket->update(['status' => 'open']); 
        return back();
    }
}
