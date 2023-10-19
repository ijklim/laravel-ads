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
    public function handle($limit = 5): void
    {
        $adCodesWithPriceChecked = [];

        \App\Models\Ad::query()
            ->where('ad_type', 'AmazonBanner')
            ->where(function ($query) {
                // Never updated or updated more than 6 hours ago
                $query
                    ->whereNull('html_updated_at')
                    ->orWhere('html_updated_at', '<=', 'DATE_ADD(NOW(), INTERVAL -12 HOUR)');
            })
            ->limit($limit)
            ->get()
            ->each(function ($ad) use (&$adCodesWithPriceChecked) {
                echo "• Processing ad '$ad->ad_code'...";

                echo \App\Http\Controllers\AdController::autoUpdateAmazonPrice($ad) ? 'price changed...' : '';

                $adCodesWithPriceChecked[] = $ad->ad_code;

                echo "done\n";
            });

        // === Log Updated Ads Info ===
        if (count($adCodesWithPriceChecked)) {
            echo "👉 Writing to log\n";
            logger()->channel('joblog')->info('♦ [' . __CLASS__ . '::handle()] Prices Checked: ' . implode(',', $adCodesWithPriceChecked));
        }
    }
}
