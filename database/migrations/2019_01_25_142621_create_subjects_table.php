<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSubjectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('subjects', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->integer('idAuthor');
            $table->integer('noQuestions')->nullable();
            $table->boolean('multipleChoice')->default(false);
            $table->boolean('separatePage')->default(false);
            $table->boolean('canBack')->nullable()->default(false);
            $table->boolean('limitedTime')->default(false);
            $table->integer('time')->nullable();
            $table->string('course')->nullable();
            $table->text('description')->nullable();
            $table->boolean('shared')->nullable();
            $table->boolean('categorysed')->nullable();
            $table->boolean('randomize')->default(false);
            $table->string('subject');
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
        Schema::dropIfExists('subjects');
    }
}
