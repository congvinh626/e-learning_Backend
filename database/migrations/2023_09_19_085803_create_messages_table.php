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
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->string('room');
            $table->bigInteger('sender')->unsigned();
            $table->bigInteger('receiver')->unsigned()->nullable();
            $table->text('content');
            // $table->unsignedBigInteger('sender');
            // $table->unsignedBigInteger('receiver')->nullable();
            // $table->bigInteger('receiver')->unsigned()->nullable();


            // $table->text('message');
            // $table->unsignedBigInteger('user_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};
