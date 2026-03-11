<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('messages', function (Blueprint $table) {

            $table->id();
            $table->mediumText('body');
            $table->unsignedBigInteger('from_user_id');
            $table->unsignedBigInteger('room_id');
            $table->timestamps();

            $table->foreign('from_user_id')
                ->references('id')
                ->on('users');

            $table->foreign('room_id')
                ->references('id')
                ->on('rooms');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('messages');
    }
};
