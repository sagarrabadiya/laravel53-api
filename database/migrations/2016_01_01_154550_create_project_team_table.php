<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProjectTeamTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('projects_teams', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('project_id')->unsigned();
            $table->integer('user_id')->unsigned();
            $table->integer('role_id')->unsigned()->nullable()->default(null);
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
            $table->text('settings')->nullable()->default(null);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('project_id')->references('id')->on('projects')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('role_id')->references('id')->on('project_roles')->onDelete('SET NULL');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('projects_teams');
    }
}
