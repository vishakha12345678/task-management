<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTasksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('taskboard');
            $table->foreignId('assigned_to');
            $table->string('project');
            $table->string('summary');
            $table->string('description')->nullable();
            $table->enum('status',array('to_do', 'in_progress','done'))->nullable();
            $table->string('label')->nullable();
            $table->string('reporter')->nullable();

            $table->foreign('taskboard')->references('id')->on('task_boards')->onDelete('cascade');
            $table->foreign('assigned_to')->references('id')->on('users')->onDelete('cascade');
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
        Schema::dropIfExists('tasks');
    }
}
