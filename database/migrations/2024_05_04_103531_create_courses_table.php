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
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable(false);
            $table->boolean('certificate')->nullable(false)->default(false);
            $table->string('thumbnail')->nullable();
            $table->enum('type', ['free', 'premium'])->nullable(false)->default('free');
            $table->enum('status', ['draft', 'published'])->nullable(false)->default('draft');
            $table->integer('price')->nullable(true)->default(0);
            $table->enum('level', ['all-level', 'beginner', 'intermediate', 'advanced'])->nullable(false);
            $table->longText('description')->nullable();
            $table->foreignId('mentor_id')->constrained('mentors')->onDelete('cascade')->nullable(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};
