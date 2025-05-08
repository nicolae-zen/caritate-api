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
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->string('title');                    // Titlul raportului
            $table->text('description')->nullable();    // Descriere (obtional)
            $table->string('file_path');                // Calea catre fisierul PDF
            $table->boolean('is_published')->default(false);    // Publicat sau nu
            $table->timestamp('published_at')->nullable();      // Cand a fost publicat
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};
