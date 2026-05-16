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
    Schema::create('submissions', function (Blueprint $table) {
        $table->id();
        $table->foreignId('indicator_id')->constrained()->cascadeOnDelete();
        $table->foreignId('period_id')->constrained()->cascadeOnDelete();
        $table->foreignId('operator_id')->constrained('users')->cascadeOnDelete();
        $table->enum('status', ['empty', 'uploaded'])->default('empty');
        $table->timestamp('marked_at')->nullable();
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('submissions');
    }
};
