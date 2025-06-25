<?php

use App\Models\Habit;
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
        Schema::create('habit_completions', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(Habit::class)->constrained()->cascadeOnDelete();
            $table->timestamp('completed_at');
            $table->integer('value')->nullable();
            $table->text('note')->nullable();
            $table->tinyInteger('mood')->nullable()->comment('1-5 rating');
            $table->timestamps();

            $table->index(['habit_id','completed_at']);
            $table->index(['user_id','completed_at']);
            $table->index(['habit_id','user_id','completed_at'],'unique_daily_completion');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('habit_completions');
    }
};
