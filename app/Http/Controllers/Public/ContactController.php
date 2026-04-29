<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Http\Requests\Public\ContactRequest;
use App\Models\SupportTicket;
use Illuminate\Http\RedirectResponse;
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

        $ticket = SupportTicket::query()->create([
            'requester_name' => $validated['name'],
            'requester_email' => $validated['email'],
            'subject' => $validated['subject'],
            'status' => 'open',
        ]);

        $ticket->messages()->create([
            'author_user_id' => null,
            'body' => $validated['message'],
            'is_internal' => false,
        ]);

        return back()->with('success', 'Your message has been sent successfully. We will get back to you soon.');
    }
}
