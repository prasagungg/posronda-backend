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
        Schema::create('post_image_tag', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('post_image_id');
            $table->unsignedBigInteger('user_id');
            $table->timestamps();

            $table->foreign('post_image_id')->references('id')->on('post_image')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('post_image_tag');
    }
};
