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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('invoice_customer_id');
            $table->string('item_name');
            $table->decimal('rate', 10, 2)->default(0);
            $table->integer('qty')->default(1);
            $table->decimal('item_discount', 5, 2)->default(0);
            $table->decimal('amount', 10, 2)->default(0);
            $table->decimal('final_amount', 10, 2)->default(0);
            $table->timestamps();

            // Foreign key constraint
            $table->foreign('invoice_customer_id')
                  ->references('id')
                  ->on('invoice_customers')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};