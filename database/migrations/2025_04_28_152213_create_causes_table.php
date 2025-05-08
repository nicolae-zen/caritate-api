<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('causes', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('image')->nullable(); // imagine reprezentativă
            $table->decimal('goal_amount', 10, 2)->nullable(); // suma țintă de strâns
            $table->boolean('is_active')->default(true); // activă sau nu
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('causes');
    }
};
