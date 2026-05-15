<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class UpdateJobPriorities extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:update-job-priorities';

    protected $description = 'Update existing scraping jobs to the low priority queue';

    public function handle()
    {
        $jobsToUpdate = [
            'App\Jobs\SyncAuctionDetails',
            'App\Jobs\ScrapeAllYahooJob',
        ];

        $query = DB::table('jobs');

        foreach ($jobsToUpdate as $jobClass) {
            $query->orWhere('payload', 'LIKE', '%'.$jobClass.'%');
        }

        $count = $query->update(['queue' => 'low']);

        $this->info("Updated {$count} jobs to the 'low' queue.");
    }
}
