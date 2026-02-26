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
        // Add foreign key to invoice_customers table
        Schema::table('invoice_customers', function (Blueprint $table) {
            // Drop existing columns if they exist (to avoid duplicate column errors)
            // But first check if they exist
            if (!Schema::hasColumn('invoice_customers', 'total_amount')) {
                $table->decimal('total_amount', 10, 2)->default(0)->after('location');
            }
            
            if (!Schema::hasColumn('invoice_customers', 'final_discount')) {
                $table->decimal('final_discount', 5, 2)->default(0)->after('total_amount');
            }
            
            if (!Schema::hasColumn('invoice_customers', 'final_amount')) {
                $table->decimal('final_amount', 10, 2)->default(0)->after('final_discount');
            }
            
            // Add foreign key constraints
            $table->foreign('invoice_id')
                  ->references('id')
                  ->on('invoices')
                  ->onDelete('set null');
                  
            $table->foreign('advanced_id')
                  ->references('id')
                  ->on('advances')
                  ->onDelete('set null');
        });

        // Add foreign key to invoices table if not exists
        Schema::table('invoices', function (Blueprint $table) {
            $table->foreign('invoice_customer_id')
                  ->references('id')
                  ->on('invoice_customers')
                  ->onDelete('cascade');
        });

        // Add foreign keys to advances table if not exists
        Schema::table('advances', function (Blueprint $table) {
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
        // Drop foreign keys from invoice_customers
        Schema::table('invoice_customers', function (Blueprint $table) {
            $table->dropForeign(['invoice_id']);
            $table->dropForeign(['advanced_id']);
        });

        // Drop foreign keys from invoices
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropForeign(['invoice_customer_id']);
        });

        // Drop foreign keys from advances
        Schema::table('advances', function (Blueprint $table) {
            $table->dropForeign(['invoice_id']);
            $table->dropForeign(['invoice_customer_id']);
        });
    }
};