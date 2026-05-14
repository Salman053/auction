<?php

use App\Jobs\ScrapeAllYahooJob;
use App\Models\User;
use Illuminate\Support\Facades\Queue;

it('allows admin to trigger manual scrape', function () {
    Queue::fake();

    $admin = User::factory()->create(['role' => 'admin']);

    $response = $this->withoutMiddleware()
        ->actingAs($admin, 'admin')
        ->post(route('admin.scraping-logs.start'));

    $response->assertStatus(302);

    Queue::assertPushed(ScrapeAllYahooJob::class, function ($job) {
        return $job->pages === 13 && $job->scrapeDelay === 1;
    });
});

it('prevents non-admins from triggering manual scrape', function () {
    Queue::fake();

    $user = User::factory()->create(['role' => 'user']);

    $response = $this
        ->actingAs($user, 'user')
        ->post(route('admin.scraping-logs.start'));

    // The admin middleware redirects non-admin users away.
    $response->assertStatus(302);
    Queue::assertNotPushed(ScrapeAllYahooJob::class);
});
