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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->double('subtotal', 10, 2);
            $table->double('grand_total', 10, 2);
            $table->double('shipping', 10,2);
            $table->double('discount', 10,2)->nullable();
            $table->enum('payment_status', ['paid', 'not paid'])->default('not paid');
            $table->enum('payment_method', ['bank', 'cod'])->default('cod');
            $table->enum('status', ['pending','processing', 'shipped', 'delivered', 'cancelled', 'completed', 'refunded'])->default('pending');
            $table->string('name');
            $table->string('email');
            $table->string('phone');
            $table->string('address');
            $table->string('city');  //tinh
            $table->string('district');  //huyen
            $table->string('commune');  //xa
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
