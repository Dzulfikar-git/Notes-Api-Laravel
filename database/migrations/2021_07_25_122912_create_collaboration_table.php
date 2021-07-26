<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCollaborationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('collaboration', function (Blueprint $table) {
            $table->id('collaboration_id');
            $table->foreignId('categories_id')->constrained('categories', 'categories_id')->onDelete('cascade');
            $table->string('user');
            $table->timestamps();

            $table->foreign('user')->references('email')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('collaboration');
    }
}
