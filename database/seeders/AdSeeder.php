<?php
// To run: php artisan db:seed --class=AdSeeder

namespace Database\Seeders;

class AdSeeder extends \Illuminate\Database\Seeder
{
    use \App\Http\Traits\SeederTrait;

    public function run()
    {
        $this->start();

        echo "• Clearing old data\n";
        $this->deleteTable();

        // === Google AdSense ===
        \App\Models\Ad::insert(array_combine(
            [
                "ad_code",
                "ad_type",
                "ad_format",
                "ad_layout_key",
                "display_ratio",
            ],
            [
                "7471404401",
                "GoogleAdSense",
                "fluid",
                "-fb+5w+4e-db+86",
                1,
            ]
        ));
        echo "• Populated Google AdSense Ads\n";

        // === Mochahost Banner ===
        \App\Models\Ad::insert(array_combine(
            [
                "ad_code",
                "ad_type",
                "href",
                "image_alt_text",
                "display_ratio",
            ],
            [
                "web-hosting-mochahost",
                "MochahostBanner",
                "https://ivan-lim.com/mochahost.php",
                "MochaHost Web Hosting",
                1,
            ]
        ));
        echo "• Populated Mochahost Ads\n";
    }

    public static function addFieldNames($dataArray)
    {
        $fieldNames = [
            'ad_code',
            'ad_type',
            'product_code',
            'image_alt_text',
            'image_description',
            'image_path',
            'price',
            'price_discount_amount',
            'height',
            'width',
            'display_ratio',
        ];
        return array_combine($fieldNames, $dataArray);
    }
}
