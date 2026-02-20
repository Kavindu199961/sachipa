<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * This migration creates two tables:
     * 1. customer - Stores customer information
     * 2. fabric_cal - Stores fabric calculations related to customers (all fields nullable)
     */
    public function up(): void
    {
        // Create customer table
        Schema::create('customer', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('Customer full name');
            $table->string('phone_number')->nullable()->comment('Customer contact number, optional field');
            $table->timestamps();
        });

        // Create fabric_cal table with all fields nullable
        Schema::create('fabric_cal', function (Blueprint $table) {
            $table->id();
            
            // Foreign key relationship
            $table->foreignId('customer_id')
                  ->nullable() // Making customer_id nullable
                  ->constrained('customer')
                  ->nullOnDelete() // Set to null instead of cascade delete
                  ->comment('References the customer table, nullable');
            
            // All fabric calculation fields are now nullable
            $table->float('stick')->nullable()->comment('Stick measurement, nullable field');
            $table->decimal('one_rali', 10, 2)->nullable()->comment('First Rali measurement, nullable');
            $table->decimal('two_rali', 10, 2)->nullable()->comment('Second Rali measurement, nullable');
            $table->decimal('tree_rali', 10, 2)->nullable()->comment('Third Rali measurement, nullable');
            $table->decimal('four_rali', 10, 2)->nullable()->comment('Fourth Rali measurement, nullable');
            $table->decimal('ilets', 10, 2)->nullable()->comment('Ilets measurement, nullable');
            $table->decimal('sum_one_four', 10, 2)->nullable()->comment('Sum of one_rali and four_rali, nullable');
            $table->decimal('sum_two_tree', 10, 2)->nullable()->comment('Sum of two_rali and tree_rali, nullable');
            
            $table->timestamps();
            
            // Optional: Add indexes for better performance
            $table->index('customer_id');
        });
    }

    /**
     * Reverse the migrations.
     * 
     * Drops both tables in reverse order to maintain referential integrity
     */
    public function down(): void
    {
        Schema::dropIfExists('fabric_cal');
        Schema::dropIfExists('customer');
    }
};