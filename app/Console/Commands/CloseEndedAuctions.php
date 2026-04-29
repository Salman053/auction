<?php

namespace App\Console\Commands;

use App\Models\Auction;
use App\Models\Bid;
use App\Notifications\AuctionWonNotification;
use App\Services\AuctionSettlementService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CloseEndedAuctions extends Command
{
    protected $signature = 'auctions:close';

    protected $description = 'Close auctions that have reached their end time and determine winners.';

    public function handle(AuctionSettlementService $settlementService)
    {
        $this->info('Settling ended auctions...');
        
        $count = $settlementService->settleEndedAuctions();

        $this->info("Successfully settled {$count} auctions.");

        return 0;
    }
}
