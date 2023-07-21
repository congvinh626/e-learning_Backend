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
        Schema::create('user_infos', function (Blueprint $table) {
            $table->id();
            $table->string('avatar')->nullable();
            $table->string('name')->nullable();
            $table->string('phone')->nullable();
            $table->datetime('dob')->nullable();
            $table->string('adress')->nullable();
            $table->unsignedBigInteger('user_id');
            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_infos');
    }
};
