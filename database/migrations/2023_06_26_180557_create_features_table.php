<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

use App\Models\Category;
use App\Enums\Status as StatusEnum;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('features', function (Blueprint $table) {
            $table->id();
            $table->string('slug',255);
            $table->string('title');
            $table->foreignIdFor(Category::class);
            $table->text('description');
            $table->text('example');
            $table->text('spec');
            $table->enum('status', StatusEnum::names() )->nullable();

            $usableEnum = ['unknown','no','partial','yes'];
            $table->enum('usable_in_chrome',$usableEnum )->default('unknown');
            $table->enum('usable_in_edge',$usableEnum )->default('unknown');
            $table->enum('usable_in_safari',$usableEnum )->default('unknown');
            $table->enum('usable_in_firefox',$usableEnum )->default('unknown');
            $table->enum('usable_in_opera',$usableEnum )->default('unknown');
            $table->enum('usable_in_ie',$usableEnum )->default('unknown');
            $table->enum('usable_in_mobile_chrome',$usableEnum )->default('unknown');
            $table->enum('usable_in_ios',$usableEnum )->default('unknown');
            $table->enum('usable_in_samsung',$usableEnum )->default('unknown');
            $table->enum('usable_in_mobile_firefox',$usableEnum )->default('unknown');

            $table->json('json');
            $table->timestamps();

            $table->unique('slug');
            $table->index('title');
        });


    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('features');
    }
};
