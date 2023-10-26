<?php

namespace App\Http\Controllers;

use App\Models\Ad;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;

class AdController extends Controller
{
    use \App\Http\Traits\ControllerTrait;

    /**
     * Scan Amazon ad and update price and price_discount_amount
     */
    public static function autoUpdateAmazonPrice(Ad $ad)
    {
        $isPriceUpdated = false;

        if (!$ad->ad_type === 'AmazonBanner' || !$ad->product_code) {
            // Missing product code, not possible to check for price info
            return false;
        }

        try {
            // === Retrieve product page html from Amazon ===
            // Hint: Removing User-Agent could prevent Amazon from triggering captcha
            // Hint: Set $refreshHtml to false to skip Amazon call, for testing purpose
            if ($refreshHtml = 1) {
                $response = \Illuminate\Support\Facades\Http::withHeader('User-Agent', '')->get($ad->url_product);
                $ad->html = trim($response->body());
                $ad->html_updated_at = now();
                $ad->save();
            }

            // === Load href html into object ===
            // Doc: https://packagist.org/packages/seyyedam7/laravel-html-parser
            $dom = new \PHPHtmlParser\Dom;
            $dom->loadStr($ad->html);

            // === Price Check  ===
            // Collection: https://github.com/seyyedam7/laravel-html-parser/blob/master/src/PHPHtmlParser/Dom/Node/Collection.php
            $prices = $dom->find('.a-price > .a-offscreen');
            // echo ('• No. of prices: ' . count($prices) . PHP_EOL);
            // Example: No. of prices: 13

            if ($prices->count()) {
                $price = str_replace('$', '', $prices[0]->text());
                // Example: $165.81
            } else {
                $price = null;
            }

            // echo ('•• First instance of Web Price / Database Price: ' . $price . ' / ' . $ad->price . PHP_EOL);
            // Example: First instance of Web Price / Database Price: <span class="a-offscreen">$165.81</span> / 157.51

            // Note: Must consider null comparison
            if ($price !== "$ad->price") {
                $ad->price = $price;
                $isPriceUpdated = true;
            }


            // === Price Discount Amount Check  ===
            $priceDiscountAmounts = $dom->find('.savingsPercentage');
            if ($priceDiscountAmounts->count()) {
                // === Discount Amount found ===
                $priceDiscountAmountNode = $priceDiscountAmounts->offsetGet(0);

                // Look for ancestor with id `apex_desktop_usedAccordionRow` which hides discount amount
                $discountHiders = $dom->find('#apex_desktop_usedAccordionRow');
                if ($discountHiders->count()) {
                    $discountHiderNode = $discountHiders->offsetGet(0);

                    if ($priceDiscountAmountNode->getAncestor($discountHiderNode->id())) {
                        // === Discount Hider wraps Discount Amount, thus hiding discount amount ===
                        $priceDiscountAmount = null;
                    } else {
                        $priceDiscountAmount = $priceDiscountAmountNode->text();
                    }
                } else {
                    // === No Discount Hider ===
                    $priceDiscountAmount = $priceDiscountAmountNode->text();
                }
            } else {
                // === No Discount Amount found ===
                // Note: Missing discount could mean a previous discount has been removed
                $priceDiscountAmount = null;
            }

            // echo ('•• First instance of Web Discount Amount / Database Discount Amount: ' . $priceDiscountAmount . ' / ' . $ad->price_discount_amount . PHP_EOL);
            // Example: First instance of Web Discount Amount / Database Discount Amount: -12% / -12%

            // Note: Must consider null comparison
            if (!($priceDiscountAmount === $ad->price_discount_amount)) {
                $ad->price_discount_amount = $priceDiscountAmount;
                $isPriceUpdated = true;
            }

            if ($isPriceUpdated) {
                $ad->price_updated_at = now();
                $ad->save();
            }
        } catch (\Exception $e) {
            echo ('[' . __CLASS__ . '::autoUpdateAmazonPrice] Error encountered: ' . substr($e->getMessage(), 0, 2000) . PHP_EOL);

            return false;
        }

        return $isPriceUpdated;
    }

    /**
     * Retrieve the specified resource.
     *
     * Note: Laravel automatically returns JSON format for Eloquent models or collections without call to `->json()`
     *
     * Usage Tests:
     * • https://ads-server.localhost/api/ads
     * • https://ads-server.localhost/api/ads?pk=B008H4SLV6
     * • https://ads-server.localhost/api/ads?at=AmazonBanner&random=1
     *
     * Query Strings:
     * • at: Filter by ad_type
     * • pk: Search by primary key (i.e. adCode, highest priority, will ignore other query strings)
     * • random: 1 random row (might need to support multiple unique rows in the future)
     *
     * @return \Illuminate\Http\Response
     */
    public function get(Request $request)
    {
        $modelClass = $this->getModelClass();
        $primaryKeyField = (new $modelClass)->getKeyName();
        $query = $modelClass::whereNotNull($primaryKeyField);

        // === Search by Primary Key ===
        if ($request->pk) {
            // Specific search, returns all fields
            // Note: Converting to array to exclude large fields such as `html`
            return $query
                ->find($request->pk)
                ->toArray();
        }

        // Note: If not search by primary key, select necessary fields, skipping large fields such as `html`
        $query->select(
            'ad_code',
            'ad_format',
            'ad_layout_key',
            'ad_type',
            'display_ratio',
            'height',
            'html_updated_at',
            'image_description',
            'is_enabled',
            'price',
            'price_discount_amount',
            'price_updated_at',
            'product_code',
            'title',
            'width',
        );

        // === Ad Type Filter ===
        if ($request->at) {
            if (\Str::startsWith($request->at, '-')) {
                // If starts with -, then return all types NOT equal to text provided
                $query->whereNot('ad_type', substr($request->at, 1));
            } else {
                $query->where('ad_type', $request->at);
            }
        }

        // === Return random enabled ad based on display_ratio ===
        if ($request->random) {
            return self::getRandom($query->where('is_enabled', true)->get());
        }

        // === All rows meeting conditions ===
        return $query->get();
    }

    public static function getRandom(Collection $ads)
    {
        $sumOfDisplayRatios = $ads->reduce(function ($carry, $ad) : int {
            return $carry + $ad->display_ratio;
        }, 0);

        if (!$sumOfDisplayRatios) {
            // This should not happen
            return $ads->first();
        }

        // If $adDistance is 2, and first ad has display ratio of 2, then result will be first ad
        // If first ad has display ratio of 1, then result will be second ad
        $adDistance = rand(1, $sumOfDisplayRatios);

        $isAdFound = function ($adDistance) {
            return $adDistance <= 0;
        };

        while (!$isAdFound($adDistance)) {
            $adDistance -= $ads->first()->display_ratio;

            if (!$isAdFound($adDistance)) {
                // Haven't found the ad yet, remove first item from $ads and continue searching
                $ads->shift();
            }
        }

        return $ads->first();
    }
}
