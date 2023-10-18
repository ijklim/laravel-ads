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
                "B01N3MYVZM",
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
                "B0C4ZB3WT5",
                "Original Peloton Bike",
                "Original Peloton Bike | Indoor Stationary Exercise Bike with Immersive 22' HD Touchscreen",
                'Amazon/BikePeloton.webp',
                1445,
                null,
                339,
                null,
                3,
            ],
            [
                "CellPhoneSamsungGalaxyS23",
                "AmazonBanner",
                "B0BLP45GY8",
                "SAMSUNG Galaxy S23 Ultra Cell Phone",
                "SAMSUNG Galaxy S23 Ultra Cell Phone, Factory Unlocked Android Smartphone, 256GB, 200MP Camera, Night Mode, Long Battery Life, S Pen, US Version, 2023",
                'Amazon/CellPhoneSamsungGalaxyS23.webp',
                949.99,
                '-21%',
                306,
                null,
                3,
            ],
            [
                "BlenderVitamix5200",
                "AmazonBanner",
                "B008H4SLV6",
                "Vitamix 5200 Blender",
                "Vitamix 5200 Blender, Professional-Grade, Container, Black, Self-Cleaning 64 oz",
                'Amazon/BlenderVitamix5200.webp',
                299.95,
                '-45%',
                300,
                null,
                3,
            ],
            [
                "TabletAmazonFireMax11",
                "AmazonBanner",
                "B0B1VQ1ZQY",
                "Amazon Fire Max 11 Tablet",
                "Amazon Fire Max 11 tablet, our most powerful tablet yet, vivid 11' display, octa-core processor, 4 GB RAM, 14-hour battery life, 64 GB, Gray",
                'Amazon/TabletAmazonFireMax11.webp',
                149.99,
                '-35%',
                240,
                null,
                3,
            ],
            [
                "TabletAmazonFireHd10KidsPro",
                "AmazonBanner",
                "B0BL8VHN81",
                "Amazon Fire HD 10 Kids Pro Tablet",
                "Amazon Fire HD 10 Kids Pro tablet, 10.1', 1080p Full HD, ages 6-12, 32 GB, (2021 release), named 'Best Tablet for Big Kids' by Good Housekeeping",
                'Amazon/TabletAmazonFireHd10KidsPro.webp',
                119.99,
                '-40%',
                251,
                null,
                3,
            ],
            [
                "TabletAmazonFireHd8KidsPro",
                "AmazonBanner",
                "B09BG6DSBY",
                "Amazon Fire HD 8 Kids Pro Tablet",
                "Amazon Fire HD 8 Kids Pro tablet, 8' HD display, ages 6-12, 30% faster processor, 13 hours battery life, Kid-Friendly Case, 32 GB, (2022 release)",
                'Amazon/TabletAmazonFireHd8KidsPro.webp',
                74.99,
                '-50%',
                278,
                null,
                3,
            ],
            [
                "LaptopLenovoIdeaPad3",
                "AmazonBanner",
                "B0C5Y2XC79",
                "Lenovo IdeaPad 3 Laptop",
                "Lenovo IdeaPad 3 14' FHD Laptop, Intel Core i5-1135G7 (up to 4.20GHz, beats i7-1065G7), 20GB DDR4 RAM, 1TB NVMe SSD, Webcam, Fingerprint Reader, HDMI, WiFi 6, Win 11",
                'Amazon/LaptopLenovoIdeaPad3.webp',
                519,
                '',
                235,
                null,
                3,
            ],
            [
                "LaptopHpPavilionX360",
                "AmazonBanner",
                "B0C6QJT6G9",
                "HP Pavilion x360 Laptop",
                "HP Pavilion x360 Laptop, 14' Touchscreen 2in1 Convertible Newest, Intel Core i5-1135G7 (beats i7-1065G7), 16GB RAM, 1TB PCle SSD, Intel Iris Xe Graphics, Webcam, Win 1",
                'Amazon/LaptopHpPavilionX360.webp',
                639.99,
                '',
                190,
                null,
                3,
            ],
            [
                "CoolerArcticZoneTitan",
                "AmazonBanner",
                "B09YFGYYNF",
                "Arctic Zone Titan Deep Freeze Zipperless Hardbody Cooler",
                "Arctic Zone Titan Deep Freeze Cooler - 16 Can Zipperless Hardbody Cooler - Deep Freeze Insulation, HardBody Liner, and SmartShelf, Blue Lagoon",
                'Amazon/CoolerArcticZoneTitan.webp',
                42.99,
                '',
                304,
                null,
                3,
            ],
        ];

        foreach ($seeds as $seed) {
            \App\Models\Ad::insert(self::addFieldNames($seed));
        }
        echo "• Populated Amazon Ads: " . count($seeds) . "\n";
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
