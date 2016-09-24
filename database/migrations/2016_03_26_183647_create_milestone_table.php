<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMilestoneTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('milestones', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('project_id')->unsigned();
            $table->integer('created_by')->unsigned()->nullable();
            $table->integer('updated_by')->unsigned()->nullable()->default(null);
            $table->integer('responsible_member_id')->unsigned()->nullable()->default(null);
            $table->string('title');
            $table->text('description');
            $table->dateTime('deadline');
            $table->enum('status', ['created', 'active', 'archived', 'completed'])->default('created');
            $table->string('type', 50)->nullable()->default(null);
            $table->timestamps();
            $table->softDeletes();
            $table->index(['type', 'status']);
            $table->foreign('project_id')->references('id')->on('projects')->onDelete('CASCADE');
            $table->foreign('responsible_member_id')->references('id')->on('users');
            $table->foreign('created_by')->references('id')->on('users')->onDelete("SET NULL");
            $table->foreign('updated_by')->references('id')->on('users')->onDelete("SET NULL");
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
        Schema::drop('milestones');
        Schema::enableForeignKeyConstraints();
    }
}
