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
        Schema::create('task_assignments', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->string('category')->default('umum');
            $table->enum('difficulty', ['easy', 'medium', 'hard'])->default('medium');
            $table->datetime('deadline');
            $table->boolean('is_active')->default(true);
            $table->json('requirements')->nullable(); // Array requirements
            $table->string('target_class')->nullable(); // Kelas specific atau null untuk semua
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('task_assignments');
    }
};
