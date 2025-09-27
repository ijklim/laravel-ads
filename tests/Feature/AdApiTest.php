<?php

namespace Tests\Feature;

use App\Models\Ad;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed the ad_types table for foreign key constraints
        $this->seed(\Database\Seeders\AdTypeSeeder::class);
    }

    /**
     * Test fetching all ads.
     *
     * @return void
     */
    public function test_can_get_all_ads()
    {
        // Create 5 ads using the factory
        Ad::factory()->count(5)->create();

        $response = $this->getJson('/api/ads');

        $response->assertStatus(200)
            ->assertJsonCount(5);
    }

    /**
     * Test fetching a single ad by its primary key.
     *
     * @return void
     */
    public function test_can_get_a_single_ad_by_pk()
    {
        // Create an ad
        $ad = Ad::factory()->create();

        $response = $this->getJson('/api/ads?pk=' . $ad->ad_code);

        $response->assertStatus(200)
            ->assertJson([
                'ad_code' => $ad->ad_code,
                'title' => $ad->title,
            ]);
    }

    /**
     * Test filtering ads by a specific type.
     *
     * @return void
     */
    public function test_can_filter_ads_by_type()
    {
        // Create ads of different types
        Ad::factory()->create(['ad_type' => 'GoogleAdSense']);
        Ad::factory()->create(['ad_type' => 'GoogleAdSense']);
        Ad::factory()->create(['ad_type' => 'ImageAd']);

        $response = $this->getJson('/api/ads?at=GoogleAdSense');

        $response->assertStatus(200)
            ->assertJsonCount(2);

        // Check that all returned ads are of the correct type
        foreach ($response->json() as $ad) {
            $this->assertEquals('GoogleAdSense', $ad['ad_type']);
        }
    }

    /**
     * Test excluding ads by a specific type.
     *
     * @return void
     */
    public function test_can_filter_ads_by_negative_type()
    {
        // Create ads of different types
        Ad::factory()->create(['ad_type' => 'GoogleAdSense']);
        Ad::factory()->create(['ad_type' => 'GoogleAdSense']);
        Ad::factory()->create(['ad_type' => 'ImageAd']);

        $response = $this->getJson('/api/ads?at=-GoogleAdSense');

        $response->assertStatus(200)
            ->assertJsonCount(1);

        $this->assertEquals('ImageAd', $response->json()[0]['ad_type']);
    }

    /**
     * Test fetching a random ad.
     *
     * @return void
     */
    public function test_can_get_a_random_ad()
    {
        // Create several enabled ads
        Ad::factory()->count(10)->create(['is_enabled' => true]);

        $response = $this->getJson('/api/ads?random=1');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'ad_code',
                'title',
                'price',
            ]);
    }

    /**
     * Test that the main listing does not include the 'html' field.
     *
     * @return void
     */
    public function test_get_all_ads_does_not_contain_html_field()
    {
        Ad::factory()->create();

        $response = $this->getJson('/api/ads');

        $response->assertStatus(200)
            ->assertJsonMissingPath('0.html');
    }

    /**
     * Test that fetching a single ad by PK does include the 'html' field.
     *
     * @return void
     */
    public function test_get_single_ad_by_pk_contains_html_field()
    {
        $ad = Ad::factory()->create(['html' => '<p>Test HTML</p>']);

        $response = $this->getJson('/api/ads?pk=' . $ad->ad_code);

        $response->assertStatus(200)
            ->assertJsonPath('html', '<p>Test HTML</p>');
    }
}