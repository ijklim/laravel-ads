<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UpdateAdsInfo implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $adsUpdated = [];

        \App\Models\Ad::query()
            ->where('ad_type', 'AmazonBanner')
            ->where(function ($query) {
                // Never updated or updated more than 6 hours ago
                $query
                    ->whereNull('price_updated_at')
                    ->orWhere('price_updated_at', '<=', 'DATE_ADD(NOW(), INTERVAL -6 HOUR)');
            })
            ->get()
            ->each(function ($ad) {
                echo "• Processing ad '$ad->ad_code'...";

                $isPriceUpdated = \App\Http\Controllers\AdController::autoUpdateAmazonPrice($ad);

                if ($isPriceUpdated) {
                    $adsUpdated[] = $ad->ad_code;
                }

                echo "done\n";
            });

        // === Log Updated Ads Info ===
        if (count($adsUpdated)) {
            info('♦ [' . __CLASS__ . '::handle()] Ads Updated: ' . implode(',', $adsUpdated));
        }
    }
}
