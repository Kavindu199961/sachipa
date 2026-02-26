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
        Schema::create('invoice_customers', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('phone_number')->nullable();
            $table->string('email')->nullable();
            $table->string('location')->nullable();
            $table->decimal('total_amount', 10, 2)->default(0);
            $table->decimal('final_discount', 5, 2)->default(0);
            $table->decimal('final_amount', 10, 2)->default(0);
            $table->unsignedBigInteger('invoice_id')->nullable();
            $table->unsignedBigInteger('advanced_id')->nullable();
            $table->timestamps();

            // Foreign keys (will be added after tables are created)
            // We'll add these in a separate migration or after all tables are created
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice_customers');
    }
};