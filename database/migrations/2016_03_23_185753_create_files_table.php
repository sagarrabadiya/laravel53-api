<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('files', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('company_id')->unsigned();
            $table->integer('created_by')->unsigned()->nullable();
            $table->string('name');
            $table->string('salt');
            $table->string('ext', 20);
            $table->bigInteger('size')->nullable()->default(null)->comment('size is in kb');
            $table->boolean('is_orphan')->default(false);
            $table->integer('parent_id')->unsigned()->nullable()->default(null);
            $table->string('parent_type')->nullable()->default(null);
            $table->timestamps();
            $table->softDeletes();
            $table->index(['salt', 'size', 'is_orphan']);
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('SET NULL');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('files');
    }
}
