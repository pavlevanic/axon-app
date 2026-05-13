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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique(); 
            $table->string('image')->nullable();
            $table->text('desc');
            
            
            $table->decimal('price', 10, 2)->nullable(); // Cena proizvoda
            $table->boolean('is_featured')->default(false); // Za karusel na landing page-u
            $table->integer('stock')->default(0); // "Rasprodato"
            
            $table->foreignId('category_id')->constrained('categories')->onDelete('cascade');
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('updated_by')->constrained('users');

            $table->string('status')->default('published'); // draft, published, archive
            $table->string('short_desc')->nullable(); 
            $table->decimal('discount_price', 10, 2)->nullable(); 
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};