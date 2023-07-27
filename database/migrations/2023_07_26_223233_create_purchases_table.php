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
        Schema::create('purchases', function (Blueprint $table) {
            $table->id();
            $table->text('supplier_id');
            $table->string('code');
            $table->date('date');
            $table->text('note')->nullable();
            $table->decimal('shipping_fee')->nullable();
            $table->decimal('additional_fee')->nullable();
            $table->text('created_by')->nullable();
            $table->text('updated_by')->nullable();
            $table->text('last_updated_by')->nullable();
            $table->timestamp('updated_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchases');
    }
};
