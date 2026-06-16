<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


return new class extends Migration
{
    public function up(): void
    {
        Schema::create('builder_products', function (Blueprint $table) {
            $table->id();

            // Identifikacija
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('brand')->nullable();

            // Tip komponente 
            $table->enum('component_type', [
                'cpu',
                'gpu',
                'motherboard',
                'ram',
                'case',
                'cpu_cooler',
                'case_fan',
                'storage',
                'psu',
            ])->index();

            // Slike i linkovi
            $table->string('image')->nullable();
            $table->string('axon_product_slug')->nullable(); // link na axon shop
            $table->string('amazon_url')->nullable();        // link na amazon

            // Cijene
            $table->decimal('price', 10, 2)->default(0);
            $table->decimal('discount_price', 10, 2)->default(0);

            // Stanje
            $table->boolean('in_stock')->default(true)->index();
            $table->boolean('is_active')->default(true)->index();

           
            $table->string('socket', 20)
                ->nullable()
                ->storedAs("JSON_UNQUOTE(JSON_EXTRACT(specs, '$.socket'))")
                ->index();

            $table->unsignedSmallInteger('tdp')
                ->nullable()
                ->storedAs("CAST(JSON_UNQUOTE(JSON_EXTRACT(specs, '$.tdp')) AS UNSIGNED)");

            $table->unsignedSmallInteger('psu_wattage')
                ->nullable()
                ->storedAs("CAST(JSON_UNQUOTE(JSON_EXTRACT(specs, '$.wattage')) AS UNSIGNED)");

            $table->string('ram_type', 10)
                ->nullable()
                ->storedAs("JSON_UNQUOTE(JSON_EXTRACT(specs, '$.ram_type'))")
                ->index();

            $table->string('form_factor', 20)
                ->nullable()
                ->storedAs("JSON_UNQUOTE(JSON_EXTRACT(specs, '$.form_factor'))")
                ->index();

            //PERFORMANCE SCORES za FPS/3DMark kalkulator

            $table->unsignedSmallInteger('perf_score')->default(0)
                ->comment('Generički score 0-1000 za kompatibilnost i sorting');
            $table->unsignedSmallInteger('tdmark_base')->default(0)
                ->comment('3DMark Time Spy bazni score (GPU-specific)');
            $table->unsignedSmallInteger('fps_base_1080')->default(0)
                ->comment('Bazni FPS na 1080p za GPU (0 za ne-GPU komponente)');

            $table->json('specs')->nullable();

            $table->string('short_desc')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['component_type', 'is_active', 'in_stock', 'price']);
        });

        /*
         * TABELA ZA SAČUVANE BUILDOVE 
         */
        Schema::create('builder_saves', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('share_token', 32)->unique()->nullable();
            $table->string('name')->default('Moj Build');
            $table->json('components'); // {cpu: id, gpu: id, ...}
            $table->decimal('total_price', 10, 2)->default(0);
            $table->unsignedSmallInteger('estimated_3dmark')->default(0);
            $table->timestamps();

            $table->index(['user_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('builder_saves');
        Schema::dropIfExists('builder_products');
    }
};