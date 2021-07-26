<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNotesInCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notes_in_categories', function (Blueprint $table) {
            $table->id('notes_categories_id');
            $table->bigInteger('categories_id')->unsigned();
            $table->bigInteger('notes_id')->unsigned();
            $table->timestamps();

            $table->foreign('categories_id')->references('categories_id')->on('categories');
            $table->foreign('notes_id')->references('notes_id')->on('notes');
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('notes_in_categories');
    }
}
