<?php

use App\Enums\UserRole;
use App\Models\SupportTicket;
use App\Models\SupportTicketMessage;
use App\Models\User;

test('admin can see support ticket messages in oldest first order', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);
    $user = User::factory()->create(['role' => UserRole::User]);

    $ticket = SupportTicket::factory()->create(['user_id' => $user->id]);

    $message1 = SupportTicketMessage::factory()->create([
        'support_ticket_id' => $ticket->id,
        'author_user_id' => $user->id,
        'created_at' => now()->subMinutes(10),
    ]);

    $message2 = SupportTicketMessage::factory()->create([
        'support_ticket_id' => $ticket->id,
        'author_user_id' => $admin->id,
        'created_at' => now()->subMinutes(5),
    ]);

    $message3 = SupportTicketMessage::factory()->create([
        'support_ticket_id' => $ticket->id,
        'author_user_id' => $user->id,
        'created_at' => now(),
    ]);

    $response = $this->actingAs($admin, 'admin')
        ->get(route('admin.support-tickets.show', $ticket));

    $response->assertStatus(200);

    // Verify messages are in order: message1, message2, message3
    $messages = $response->viewData('ticket')->messages;

    expect($messages)->toHaveCount(3);
    expect($messages[0]->id)->toBe($message1->id);
    expect($messages[1]->id)->toBe($message2->id);
    expect($messages[2]->id)->toBe($message3->id);
});
