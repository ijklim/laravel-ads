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
        $results = $modelClass::whereNotNull($primaryKeyField);

        if (is_numeric($request->id)) {
            // Specific search, returns all fields
            return $results
                ->where($primaryKeyField, $request->id)
                ->get();
        }

        // Tip: Return specific fields from relationship
        // Important: Must include foreign key in both (e.g. account_id)
        $results->select('ad_code', 'image_alt_text');

        // Order by Attorney Name by default, suitable for dropdown
        $results->orderBy('ad_code');

        return $results->get();
    }

    /**
     * Scan Amazon ad and update price and price_discount_amount
     */
    public static function autoUpdateAmazonPrice(Ad $ad)
    {
        if (!$ad->href) {
            // Without href, not possible to check for price
            return false;
        }

        try {
            // === Load href html into object ===
            // Doc: https://packagist.org/packages/seyyedam7/laravel-html-parser
            // $dom = new \PHPHtmlParser\Dom;
            // $html = $dom->loadFromUrl($ad->href);
            // $ad->html = $html;

            $response = \Illuminate\Support\Facades\Http::get($ad->href);
            // print_r($response->body());
            $ad->html = $response->body();

            $ad->save();

            // $prices = $dom->find('.a-price > .a-offscreen');
            // echo ('No. of prices: ' . count($prices) . PHP_EOL);
        } catch (\Exception $e) {
            // Most likely invalid url
            return false;
        }
    }
}
