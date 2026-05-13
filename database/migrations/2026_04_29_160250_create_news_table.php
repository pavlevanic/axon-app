<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('news', function (Blueprint $table) {
            $table->id();
            $table->string('title'); // Naslov vesti
            $table->string('slug')->unique();
            $table->text('summary')->nullable(); // Kratak opis (za karticu ispod hero-a)
            $table->longText('content'); // Glavni tekst vesti
            $table->string('image')->nullable(); // Putanja do slike
            $table->string('type')->default('regular'); // Tip: 'hero', 'promo', 'regular'
            $table->boolean('is_active')->default(true); // Da li je vest objavljena
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('news');
    }
};
