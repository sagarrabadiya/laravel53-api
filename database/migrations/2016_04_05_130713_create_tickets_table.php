<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTicketsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('project_id')->unsigned();
            $table->string('title');
            $table->text('description');
            $table->integer('sequence_id');
            $table->string('priority', 15);
            $table->string('status', 15);
            $table->string('resolution_type', 15)->nullable()->default(null);
            $table->text('resolution_text')->nullable()->default(null);
            $table->integer('milestone_id')->unsigned()->nullable()->default(null);
            $table->integer('created_by')->unsigned()->nullable()->default(null);
            $table->integer('updated_by')->unsigned()->nullable()->default(null);
            $table->integer('assigned_by')->unsigned()->nullable()->default(null);
            $table->integer('assigned_to')->unsigned()->nullable()->default(null);
            $table->softDeletes();
            $table->timestamps();
            /**
             * defines foreign keys
             */
            $table->foreign('project_id')->references('id')->on('projects')->onDelete('cascade');
            $table->foreign('milestone_id')->references('id')->on('milestones')->onDelete('SET NULL');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('SET NULL');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('SET NULL');
            $table->foreign('assigned_by')->references('id')->on('users')->onDelete('SET NULL');
            $table->foreign('assigned_to')->references('id')->on('users')->onDelete('SET NULL');
            $table->index(['sequence_id', 'status', 'priority']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::disableForeignKeyConstraints();
        Schema::drop('tickets');
        Schema::enableForeignKeyConstraints();
    }
}
