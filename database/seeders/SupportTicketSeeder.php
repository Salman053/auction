<?php

namespace Database\Seeders;

use App\Models\SupportTicket;
use Illuminate\Database\Seeder;

class SupportTicketSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (SupportTicket::query()->count() > 0) {
            return;
        }

        $ticket = SupportTicket::query()->create([
            'requester_name' => 'Guest',
            'requester_email' => 'guest@example.com',
            'subject' => 'How do deposits work?',
            'status' => 'open',
        ]);

        $ticket->messages()->create([
            'author_user_id' => null,
            'body' => 'Hi, I want to know how deposits and bidding capacity work.',
            'is_internal' => false,
        ]);
    }
}
