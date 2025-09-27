<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Ad>
 */
class AdFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = \App\Models\Ad::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'ad_code' => $this->faker->unique()->bothify('??###??##'),
                        'ad_type' => $this->faker->randomElement(['GoogleAdSense', 'ImageAd', 'MochahostBanner']),
            'title' => $this->faker->sentence,
            'image_description' => $this->faker->paragraph,
            'price' => $this->faker->randomFloat(2, 10, 1000),
            'price_discount_amount' => $this->faker->randomElement([null, '-' . $this->faker->numberBetween(5, 50) . '%']),
            'display_ratio' => $this->faker->numberBetween(1, 10),
            'is_enabled' => true,
            'href' => $this->faker->url,
            'ad_format' => 'image',
            'ad_layout_key' => 'standard',
            'width' => 600,
            'height' => 300,
            'price_updated_at' => now(),
            'html_updated_at' => now(),
        ];
    }
}
