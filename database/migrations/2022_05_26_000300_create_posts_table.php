<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\User;
use App\Models\Post;

return new class () extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class)->constrained()->onDelete('cascade');

            $table->foreignId('parent_id')
                ->nullable()
                ->references('id')
                ->on('posts')
                ->onDelete('cascade');

            $table->foreignId('child_id')
                ->nullable()
                ->references('id')
                ->on('posts')
                ->onDelete('cascade');

            $table->string('title', 64);
            $table->text('content');
            $table->timestamps();
        });

//        Schema::table('posts', function (Blueprint $table) {
//            $table->foreign('parent_id')->references('id')->on('posts')->onDelete('cascade');
//            $table->foreign('child_id')->references('id')->on('posts')->onDelete('cascade');
//        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('posts');
    }
};
