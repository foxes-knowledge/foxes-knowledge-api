<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('username', 16);
            $table->string('name', 32)->nullable();
            $table->string('email', 64)->unique();
            $table->string('password', 256);
            $table->boolean('isEmailPublic')->default(false);
            $table->string('picture', 256)->nullable();
            $table->string('bio', 200)->nullable();
            $table->string('color', 7);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
};
