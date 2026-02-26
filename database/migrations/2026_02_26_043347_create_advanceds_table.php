<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('advances', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('invoice_id');
            $table->unsignedBigInteger('invoice_customer_id');
            $table->decimal('advance_amount', 10, 2);
            $table->decimal('due_balance', 10, 2);
            $table->date('date');
            $table->timestamps();

            // Foreign keys
            $table->foreign('invoice_id')->references('id')->on('invoices')->onDelete('cascade');
            $table->foreign('invoice_customer_id')->references('id')->on('invoice_customers')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('advances');
    }
};