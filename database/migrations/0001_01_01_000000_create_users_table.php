<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('phone_number')->unique();
            $table->string('name')->nullable();
            $table->string('email')->nullable()->unique();
            $table->boolean('is_admin')->default(false);
            $table->decimal('total_donated', 10, 2)->default(0);
            $table->timestamp('last_login')->nullable();
            $table->timestamps(); // created_at și updated_at
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
