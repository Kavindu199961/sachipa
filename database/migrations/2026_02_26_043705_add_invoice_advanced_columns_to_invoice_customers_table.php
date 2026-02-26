<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('invoice_customers', function (Blueprint $table) {
            $table->unsignedBigInteger('invoice_id')->nullable();
            $table->unsignedBigInteger('advanced_id')->nullable();
          

            // Foreign keys
            $table->foreign('invoice_id')->references('id')->on('invoices')->onDelete('set null');
            $table->foreign('advanced_id')->references('id')->on('advances')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('invoice_customers', function (Blueprint $table) {
            // Drop foreign keys first
            $table->dropForeign(['invoice_id']);
            $table->dropForeign(['advanced_id']);

            // Drop columns
            $table->dropColumn(['invoice_id', 'advanced_id']);
        });
    }
};