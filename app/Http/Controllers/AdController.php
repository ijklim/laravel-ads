<?php

namespace App\Http\Controllers;

use App\Models\Ad;
use Illuminate\Http\Request;

class AdController extends Controller
{
    use \App\Http\Traits\ControllerTrait;

    /**
     * Retrieve the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function get(Request $request)
    {
        $modelClass = $this->getModelClass();
        $primaryKeyField = (new $modelClass)->getKeyName();
        $query = $modelClass::whereNotNull($primaryKeyField);

        if ($request->adCode) {
            // Specific search, returns all fields
            return $query
                ->where($primaryKeyField, $request->adCode)
                ->get();
        }

        // Select fields from core table
        $query->select(
            'ad_code',
            'ad_type',
            'html_updated_at',
            'price',
            'price_discount_amount',
            'price_updated_at',
            'product_code',
            'title',
        );

        return $query->get();
    }

    /**
     * Retrieve the specified resource in Json format.
     *
     * @return \Illuminate\Http\Response
     */
    public function getJson(Request $request)
    {
        return response()->json(\App\Models\Ad::all()->toArray());
    }

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

            // === Search for price info ===
            $prices = $dom->find('.a-price > .a-offscreen');

            // === Debug Info ===
            // echo ('• No. of prices: ' . count($prices) . PHP_EOL);
            // Example: No. of prices: 13
            if (is_countable($prices) && count($prices)) {
                $price = $prices[0]->text();
                // Example: $165.81

                // echo ('•• First instance of prices / Current Price: ' . $prices . ' / ' . $ad->price . PHP_EOL);
                // Example: First instance of prices / Current Price: <span class="a-offscreen">$165.81</span> / 157.51

                if ($price !== "$$ad->price") {
                    $ad->price = str_replace('$', '', $price);

                    // === Search for price discount info ===
                    $priceDiscountAmounts = $dom->find('.savingsPercentage', 1);
                    if (is_countable($priceDiscountAmounts) && count($priceDiscountAmounts)) {
                        $priceDiscountAmount = $priceDiscountAmounts[0]->text();
                        $ad->price_discount_amount = $priceDiscountAmount;
                    } else {
                        $ad->price_discount_amount = null;
                    }

                    $isPriceUpdated = true;
                }

                $ad->price_updated_at = now();
                $ad->save();
            }
        } catch (\Exception $e) {
            echo ('[' . __CLASS__ . '::autoUpdateAmazonPrice] Error encountered: ' . substr($e->getMessage(), 0, 2000) . PHP_EOL);

            return false;
        }

        return $isPriceUpdated;
    }
}
