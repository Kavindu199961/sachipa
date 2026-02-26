<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('invoice_customer_id');
            $table->string('item_name');
            $table->decimal('rate', 10, 2);
            $table->integer('qty');
            $table->decimal('amount', 10, 2); // rate * qty
            $table->decimal('item_discount', 10, 2)->nullable();
            $table->decimal('final_amount', 10, 2);
            $table->decimal('final_amount_discount', 10, 2)->nullable();
            $table->timestamps();

            // Foreign key
            $table->foreign('invoice_customer_id')->references('id')->on('invoice_customers')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('invoices');
    }
};