<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Artisan;

class ScrapeAllYahooJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public int $pages = 1,
        public int $scrapeDelay = 2,
        public ?int $min = 1,
        public ?int $max = 200,
        public bool $fetchDetails = true
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $args = [
            '--pages' => $this->pages,
            '--delay' => $this->scrapeDelay,
            '--min' => $this->min,
            '--max' => $this->max,
        ];

        if ($this->fetchDetails) {
            $args['--fetch-details'] = true;
        }

        Artisan::call('yahoo:scrape-all', $args);
    }
}
