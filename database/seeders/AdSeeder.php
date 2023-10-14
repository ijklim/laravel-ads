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

        $seeds = [
            [
                "CarSeatGracoSlimfit3In1",
                "AmazonBanner",
                "https://www.amazon.com/gp/product/B01N3MYVZM?&linkCode=ll1&tag=aimprove-20&linkId=882aafc778550a74c9ffba47c4687100&language=en_US&ref_=as_li_ss_tl",
                "Graco Slimfit 3 in 1 Car Seat",
                "Graco Slimfit 3 in 1 Car Seat - Slim & Comfy Design Saves Space in Your Back Seat, Darcie, One Size",
                'Amazon/CarSeatGracoSlimfit3In1.webp',
                157.51,
                '-28%',
                300,
                null,
                2,
            ],
            [
                "BikePeloton",
                "AmazonBanner",
                "https://www.amazon.com/gp/product/B0C4ZB3WT5?&linkCode=ll1&tag=aimprove-20&linkId=f82bd4a05db75d2626fab82620fb9964&language=en_US&ref_=as_li_ss_tl",
                "Original Peloton Bike",
                "Original Peloton Bike | Indoor Stationary Exercise Bike with Immersive 22' HD Touchscreen",
                'Amazon/BikePeloton.webp',
                1445,
                null,
                339,
                null,
                3,
            ],
        ];

        foreach ($seeds as $seed) {
            \App\Models\Ad::insert(self::addFieldNames($seed));
        }
        echo "• Populated Ads: " . count($seeds) . "\n";
    }

    public static function addFieldNames($dataArray)
    {
        $fieldNames = [
            'ad_code',
            'ad_type',
            'href',
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
