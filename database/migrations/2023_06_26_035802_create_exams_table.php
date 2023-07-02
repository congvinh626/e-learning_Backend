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
        Schema::create('exams', function (Blueprint $table) {
            // $table->dropForeign('lesson_id');

            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('numberOfQuestion')->nullable();
            $table->string('time')->nullable();
            $table->dateTime('startTime')->nullable();
            $table->dateTime('endTime')->nullable();
            $table->json('classify')->nullable();
            $table->boolean('showResult');
            $table->unsignedBigInteger('lesson_id');
            $table->foreign('lesson_id')->references('id')->on('lessons')->onDelete('cascade');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Schema::dropIfExists('exams');
        // Schema::table('exams', function (Blueprint $table) {
        //     $table->dropForeign('lesson_id');
        // });
        Schema::dropIfExists('exams');
    }
};
