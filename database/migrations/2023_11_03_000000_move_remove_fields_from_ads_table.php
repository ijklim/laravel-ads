<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private $tableName = 'ads';
    private $fieldNameToDrop = 'product_code';
    private $fieldNameToMove = 'href';

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        \DB::statement("ALTER TABLE $this->tableName MODIFY COLUMN $this->fieldNameToMove VARCHAR(255) NULL AFTER price_updated_at");

        Schema::table($this->tableName, function (Blueprint $table) {
            $table->dropColumn($this->fieldNameToDrop);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        \DB::statement("ALTER TABLE $this->tableName MODIFY COLUMN $this->fieldNameToMove VARCHAR(255) NULL AFTER ad_type");

        Schema::table($this->tableName, function (Blueprint $table) {
            $table->string($this->fieldNameToDrop, 30)->nullable()->comment('Applies to Amazon product');
        });
    }
};
