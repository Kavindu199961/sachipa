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
        // Update invoice_customers table
        Schema::table('invoice_customers', function (Blueprint $table) {
            if (!Schema::hasColumn('invoice_customers', 'total_amount')) {
                $table->decimal('total_amount', 10, 2)->default(0)->after('location');
            }
            
            if (!Schema::hasColumn('invoice_customers', 'final_discount')) {
                $table->decimal('final_discount', 5, 2)->default(0)->after('total_amount');
            }
            
            if (!Schema::hasColumn('invoice_customers', 'final_amount')) {
                $table->decimal('final_amount', 10, 2)->default(0)->after('final_discount');
            }
            
            if (!Schema::hasColumn('invoice_customers', 'invoice_id')) {
                $table->unsignedBigInteger('invoice_id')->nullable()->after('final_amount');
            }
            
            if (!Schema::hasColumn('invoice_customers', 'advanced_id')) {
                $table->unsignedBigInteger('advanced_id')->nullable()->after('invoice_id');
            }
        });

        // Update invoices table
        Schema::table('invoices', function (Blueprint $table) {
            if (!Schema::hasColumn('invoices', 'item_discount')) {
                $table->decimal('item_discount', 5, 2)->default(0)->after('qty');
            }
            
            if (!Schema::hasColumn('invoices', 'final_amount')) {
                $table->decimal('final_amount', 10, 2)->default(0)->after('amount');
            }
        });

        // Add foreign keys
        try {
            Schema::table('invoice_customers', function (Blueprint $table) {
                $table->foreign('invoice_id')
                      ->references('id')
                      ->on('invoices')
                      ->onDelete('set null');
                      
                $table->foreign('advanced_id')
                      ->references('id')
                      ->on('advances')
                      ->onDelete('set null');
            });

            Schema::table('invoices', function (Blueprint $table) {
                $table->foreign('invoice_customer_id')
                      ->references('id')
                      ->on('invoice_customers')
                      ->onDelete('cascade');
            });

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
        } catch (\Exception $e) {
            // Foreign keys might already exist
            echo "Foreign keys might already exist: " . $e->getMessage() . "\n";
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop foreign keys first
        try {
            Schema::table('invoice_customers', function (Blueprint $table) {
                $table->dropForeign(['invoice_id']);
                $table->dropForeign(['advanced_id']);
            });
        } catch (\Exception $e) {
            // Foreign keys might not exist
        }

        try {
            Schema::table('invoices', function (Blueprint $table) {
                $table->dropForeign(['invoice_customer_id']);
            });
        } catch (\Exception $e) {
            // Foreign keys might not exist
        }

        try {
            Schema::table('advances', function (Blueprint $table) {
                $table->dropForeign(['invoice_id']);
                $table->dropForeign(['invoice_customer_id']);
            });
        } catch (\Exception $e) {
            // Foreign keys might not exist
        }

        // Drop columns from invoice_customers
        Schema::table('invoice_customers', function (Blueprint $table) {
            $columns = ['total_amount', 'final_discount', 'final_amount', 'invoice_id', 'advanced_id'];
            foreach ($columns as $column) {
                if (Schema::hasColumn('invoice_customers', $column)) {
                    $table->dropColumn($column);
                }
            }
        });

        // Drop columns from invoices
        Schema::table('invoices', function (Blueprint $table) {
            $columns = ['item_discount', 'final_amount'];
            foreach ($columns as $column) {
                if (Schema::hasColumn('invoices', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};