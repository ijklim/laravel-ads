<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private $tableName = 'ads';

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->string('ad_code', 30)->primary()->comment('Used for GoogleAdSense.adSlot');

            $table->string('ad_type', 30);
            $table
                ->foreign('ad_type')
                ->references('ad_type')
                ->on('ad_types');

            // === Amazon Banner ===
            $table->string('product_code', 30)->nullable()->comment('Applies to Amazon product');
            $table->string('href', 255)->nullable();

            $table->string('image_alt_text', 255)->nullable();
            $table->string('image_description', 255)->nullable();
            $table->string('image_path', 255)->nullable();

            $table->unsignedDecimal('price')->nullable();
            $table->string('price_discount_amount', 10)->nullable();
            $table->timestamp('price_updated_at')->nullable();

            $table->unsignedSmallInteger('height')->nullable();
            $table->unsignedSmallInteger('width')->nullable();

            $table->unsignedTinyInteger('display_ratio')->default(1);

            $table->text('html')->nullable();

            // === Google AdSense ===
            $table->string('ad_format', 10)->nullable();
            $table->string('ad_layout_key', 20)->nullable();


            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists($this->tableName);
    }
};
