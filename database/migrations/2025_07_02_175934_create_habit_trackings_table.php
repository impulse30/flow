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
        Schema::create('habit_trackings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('habit_id')->constrained('habits')->cascadeOnDelete();
            $table->date('date');
            $table->boolean('completed');
            $table->text('notes')->nullable();
            $table->string('fail_reason')->nullable();

            $table->unique(['habit_id', 'date']);


            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('habit_trackings');
    }
};
