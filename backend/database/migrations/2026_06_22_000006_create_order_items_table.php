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
        Schema::create('order_items', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained()->cascadeOnDelete();
        $table->enum('status', [
            'pending_payment',
            'paid',
            'processing',
            'shipping',
            'completed',
            'cancelled'
        ])->default('pending_payment');
        $table->decimal('total_amount', 10, 2);
        $table->string('shipping_name');
        $table->string('shipping_phone', 20);
        $table->text('shipping_address');
        $table->text('note')->nullable();
        $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
