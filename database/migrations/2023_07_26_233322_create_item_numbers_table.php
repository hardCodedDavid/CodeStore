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
        Schema::create('item_numbers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id');
            $table->foreignId('purchase_item_id')->nullable();
            $table->foreignId('sale_item_id')->nullable();
            $table->string('no');
            $table->enum('status', ['available', 'sold'])->default('available');
            $table->timestamp('date_sold')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('item_numbers');
    }
};
