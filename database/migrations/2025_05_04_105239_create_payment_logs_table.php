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
        Schema::create('payment_logs', function (Blueprint $table) {
            $table->id();
            $table->string('gateway');                  // Ex: maib, stripe, paypal
            $table->string('event_type')->nullable();   // Ex: payment_succes. charge_failed
            $table->json('payload');                    // Tot JSON-ul brut primit de la maib, stripe, paypal, etc...
            $table->string('signature')->nullable();    // Pentru validare HMAC
            $table->boolean('processed')->default(false); // A fost legat de donation? / Procesata
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_logs');
    }
};
