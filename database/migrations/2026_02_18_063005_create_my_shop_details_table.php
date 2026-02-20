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
        Schema::create('my_shop_details', function (Blueprint $table) {
            $table->id();
            
            // Shop Information
            $table->string('shop_name');
            $table->text('description')->nullable();
            $table->text('address')->nullable();
            $table->string('hotline')->nullable();
            $table->string('email')->nullable();
            $table->string('logo_image')->nullable(); // Path to logo image
            
            // Conditions/Terms
            $table->text('condition_1')->nullable();
            $table->text('condition_2')->nullable();
            $table->text('condition_3')->nullable();
            
            // Foreign key to users table
            $table->foreignId('user_id')
                  ->constrained()
                  ->onDelete('cascade'); // If user is deleted, delete their shop details
                  
            $table->timestamps();
            
            // Optional: Add indexes for better performance
            $table->index('user_id');
            $table->index('shop_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('my_shop_details');
    }
};