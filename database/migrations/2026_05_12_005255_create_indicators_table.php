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
    Schema::create('indicators', function (Blueprint $table) {
        $table->id();
        $table->foreignId('aspect_id')->constrained()->cascadeOnDelete();
        $table->string('code');
        $table->text('name');
        $table->decimal('weight', 5, 4);
        $table->enum('score_type', ['likert', 'status']);
        $table->string('storage_link')->nullable();
        $table->text('template_text')->nullable();
        $table->string('template_file_url')->nullable();
        $table->string('template_pdf_url')->nullable();
        $table->integer('order')->default(0);
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('indicators');
    }
};
