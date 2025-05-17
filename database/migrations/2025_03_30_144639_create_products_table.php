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
            $table->text('description')->nullable();
            $table->text('short_description')->nullable();
            $table->double('price', 10, 2);
            $table->double('compare_price', 10, 2)->nullable();
            $table->integer('quantity');
            $table->string('image')->nullable();
            $table->Integer('status')->default(1);
            $table->enum('is_featured', ['yes', 'no'])->default('no');
            $table->string('sku')->nullable();

            // product parameter
            $table->string('cpu')->nullable();
            $table->string('gpu')->nullable();
            $table->string('operating_system')->nullable();
            $table->string('storage_capacity')->nullable();
            $table->string('ram')->nullable();
            $table->string('screen_size')->nullable();
            $table->string('camera_resolution')->nullable();
            $table->string('battery_capacity')->nullable();

            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->foreignId('brand_id')->constrained()->onDelete('cascade');
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
