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
                $priceDiscountAmount = $priceDiscountAmounts[0]->text();
            } else {
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
}
