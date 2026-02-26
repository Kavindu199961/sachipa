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
        Schema::create('advances', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('invoice_id')->nullable();
            $table->unsignedBigInteger('invoice_customer_id');
            $table->decimal('advance_amount', 10, 2)->default(0);
            $table->decimal('due_balance', 10, 2)->default(0);
            $table->date('date');
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('invoice_id')
                  ->references('id')
                  ->on('invoices')
                  ->onDelete('set null');
                  
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
        Schema::dropIfExists('advances');
    }
};