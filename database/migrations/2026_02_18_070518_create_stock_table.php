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
        Schema::create('stock', function (Blueprint $table) {
            $table->id();
            
            // All fields are nullable as requested
            $table->string('item_code', 100)->nullable();
            $table->string('item_name', 255)->nullable();
            $table->text('description')->nullable();
            
            // Price fields - decimal for accurate calculations
            $table->decimal('cost', 15, 2)->nullable();
            $table->decimal('whole_sale_price', 15, 2)->nullable();
            $table->decimal('retail_price', 15, 2)->nullable();
            
            // Vendor/supplier information
            $table->string('vender', 255)->nullable(); // Note: 'vender' spelling as per model
            
            // Stock tracking
            $table->date('stock_date')->nullable();
            $table->integer('quantity')->nullable()->default(0);
            
            // Barcode field
            $table->string('barcode', 100)->nullable()->unique();
            
            // Foreign key to users table
            $table->foreignId('user_id')
                  ->nullable()
                  ->constrained()
                  ->onDelete('set null'); // If user deleted, set user_id to null
                  
            // Timestamps
            $table->timestamps();
            
            // Indexes for better performance
            $table->index('item_code');
            $table->index('item_name');
            $table->index('vender');
            $table->index('stock_date');
            $table->index('user_id');
            $table->index('barcode');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock');
    }
};