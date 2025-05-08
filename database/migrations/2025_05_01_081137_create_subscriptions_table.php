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
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('cause_id')->nullable()->constrained()->nullOnDelete();
            $table->decimal('amount', 10, 2);
            $table->tinyInteger('day_of_month'); // 1–28
            $table->string('status')->default('ACTIVE'); // ACTIVE, CANCELED, PAUSED
            $table->date('start_date');
            $table->date('next_charge_date');
            $table->string('payment_token')->nullable(); // token Maib sau card
            $table->foreignId('last_payment_id')->nullable()->constrained('donations')->nullOnDelete();
            $table->timestamps();
        });        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};
