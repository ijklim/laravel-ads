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
        Schema::table($this->tableName, function (Blueprint $table) {
            $table->renameColumn('image_alt_text', 'title');
            $table->dropColumn('image_path');
            $table->timestamp('html_updated_at')->nullable()->after('html');

            // Changing data type, ensure nullable is set to avoid error "Warning: 1265 Data truncated for column 'html'..."
            $table->longText('html')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table($this->tableName, function (Blueprint $table) {
            $table->renameColumn('title', 'image_alt_text');
            $table->string('image_path', 255)->nullable();
            $table->dropColumn('html_updated_at');
            $table->text('html')->nullable()->change();
        });
    }
};
