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
        Schema::create('promos', function (Blueprint $table) {
            $table->id(); // Primary key
            $table->string('code')->unique(); // Unique code
            $table->string('description')->nullable(); // Description of the discount
            $table->enum('discount_type', ['fixed', '%']); // Type of discount (fixed or percentage)
            $table->decimal('discount_value', 10, 2); // Value of the discount
            $table->date('start_date'); // Discount start date
            $table->date('end_date'); // Discount end date
            $table->enum('status', ['active', 'expired'])->default('active'); // Status of the discount
            $table->timestamps(); // created_at and updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('promos');
    }
};
