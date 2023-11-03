<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private $tableName = 'ads';
    private $fieldName = 'image_src';
    private $fieldNameToDrop = 'product_code';
    private $fieldNameAfter = 'href';

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        \DB::statement("ALTER TABLE $this->tableName MODIFY COLUMN $this->fieldNameAfter VARCHAR(255) NULL AFTER price_updated_at");

        Schema::table($this->tableName, function (Blueprint $table) {
            $table->string($this->fieldName, 255)->nullable()->after($this->fieldNameAfter);

            $table->dropColumn($this->fieldNameToDrop);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        \DB::statement("ALTER TABLE $this->tableName MODIFY COLUMN $this->fieldNameAfter VARCHAR(255) NULL AFTER ad_type");

        Schema::table($this->tableName, function (Blueprint $table) {
            $table->string('product_code', 30)->nullable()->comment('Applies to Amazon product');

            $table->dropColumn($this->fieldName);
        });
    }
};
