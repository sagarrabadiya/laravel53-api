<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProjectRolesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('project_roles', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('project_id')->unsigned();
            $table->string('name', 50);
            $table->boolean('board_read')->default(false);
            $table->boolean('board_write')->default(false);
            $table->boolean('milestone_read')->default(false);
            $table->boolean('milestone_write')->default(false);
            $table->boolean('ticket_read')->default(false);
            $table->boolean('ticket_write')->default(false);
            $table->boolean('note_read')->default(false);
            $table->boolean('note_write')->default(false);
            $table->boolean('team_read')->default(false);
            $table->boolean('team_write')->default(false);
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['name', 'project_id']);
            $table->foreign('project_id')->references('id')->on('projects')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('project_roles');
    }
}
