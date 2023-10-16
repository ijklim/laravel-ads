<?php

namespace App\Http\Controllers;

use App\Models\Ad;
use Illuminate\Http\Request;

class AdController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Ad $ad)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Ad $ad)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Ad $ad)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Ad $ad)
    {
        //
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
