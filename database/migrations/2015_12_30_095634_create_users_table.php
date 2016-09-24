<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('company_id')->unsigned();
            $table->string('username', 50)->unique();
            $table->string('password', 120);
            $table->string('designation', 20)->default('employee');
            $table->string('firstname', 50)->nullable()->default(null);
            $table->string('lastname', 50)->nullable()->default(null);
            $table->string('email', 50)->unique();
            $table->string('avatar')->nullable()->default(null);
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
            $table->index(['designation']);
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
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
        Schema::drop('users');
        Schema::enableForeignKeyConstraints();
    }
}
