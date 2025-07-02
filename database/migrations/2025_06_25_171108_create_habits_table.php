<?php

use App\Models\User;
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
        Schema::create('habits', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class)->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('description');
            $table->string('category');
            $table->enum('frequency', ['daily', 'weekly', 'monthly'])->default('daily');
            $table->integer('target')->default(1);
            $table->string('color',7);
            $table->string('icon')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('current_streak')->default(0);
            $table->integer('longest_streak')->default(0);
            $table->integer('total_completions')->default(0);
            $table->time('reminder_time')->nullable();
            $table->json('reminder_days')->nullable();
            $table->enum('difficulty', ['easy', 'medium', 'hard'])->default('easy');
            $table->timestamps();


            $table->index(['user_id','is_active']);
            $table->index(['category']);
            $table->index(['frequency']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('habits');
    }
};
