<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('donations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete(); // utilizatorul
            $table->foreignId('cause_id')->nullable()->constrained('causes')->nullOnDelete(); // cauza
            $table->decimal('amount', 10, 2);
            $table->string('currency', 3)->default('MDL');
            $table->string('status')->default('pending'); // pending, paid, failed
            $table->string('payment_gateway')->nullable();
            $table->string('payment_reference')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('donations');
    }
};
