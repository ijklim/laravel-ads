<?php
// To run: php artisan db:seed --class=AdTypeSeeder

namespace Database\Seeders;

class AdTypeSeeder extends \Illuminate\Database\Seeder
{
    use \App\Http\Traits\SeederTrait;

    public function run()
    {
        $this->start();

        echo "• Clearing old data\n";
        $this->deleteTable();

        $seeds = [
            ['AmazonBanner'],
            ['GoogleAdSense'],
            ['MochahostBanner'],
        ];

        foreach ($seeds as $seed) {
            \App\Models\AdType::insert(self::addFieldNames($seed));
        }
        echo "• Populated Ad Types: " . count($seeds) . "\n";
    }

    public static function addFieldNames($dataArray)
    {
        $fieldNames = [
            'ad_type',
        ];
        return array_combine($fieldNames, $dataArray);
    }
}
