<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Category;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('category_feature', function (Blueprint $table) {
            $table->foreignId('category_id');
            $table->foreignId('feature_id');
        });

        Schema::table('features', function (Blueprint $table) {
            $table->dropColumn('category_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('features', function (Blueprint $table) {
            $table->foreignIdFor(Category::class);
        });

        Schema::dropIfExists('category_feature');
    }
};
