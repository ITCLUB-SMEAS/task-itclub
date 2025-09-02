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
        Schema::table('tasks', function (Blueprint $table) {
            $table->unsignedBigInteger('assignment_id')->nullable()->after('user_id');
            $table->datetime('deadline')->nullable()->after('tanggal_mengumpulkan');
            $table->string('category')->default('umum')->after('deskripsi_tugas');
            $table->string('difficulty')->default('medium')->after('category');
            $table->boolean('is_late')->default(false)->after('status');
            $table->text('file_uploads')->nullable()->after('github_link');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropColumn(['assignment_id', 'deadline', 'category', 'difficulty', 'is_late', 'file_uploads']);
        });
    }
};
