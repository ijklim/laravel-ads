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
            $table->string('ad_code', 30)->primary();

            $table->string('ad_type', 30);
            $table
                ->foreign('ad_type')
                ->references('ad_type')
                ->on('ad_types');

            $table->string('href', 255);

            $table->string('image_alt_text', 255);
            $table->string('image_description', 255)->nullable();
            $table->string('image_path', 255);

            $table->unsignedDecimal('price');
            $table->string('price_discount_amount', 10)->nullable();

            $table->unsignedSmallInteger('height')->nullable();
            $table->unsignedSmallInteger('width')->nullable();

            $table->unsignedTinyInteger('display_ratio')->default(1);

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
