<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateResourceLimitsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('resource_limits', function(Blueprint $table)
        {
            $table->increments('id');
            $table->string('stripe_plan_identifier')->unique();
            $table->integer('projects_allowed');
            $table->integer('archived_projects_allowed');
            $table->integer('users_allowed');
            $table->integer('storage_allowed')->comment('the storage is in mb');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('resource_limits');
    }
}
