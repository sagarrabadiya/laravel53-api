<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCompaniesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 100)->unique();
            $table->string('domain', 30)->nullable()->default(null);
            $table->text('settings');
            $table->string('stripe_id')->nullable()->default(null);
            $table->string('card_brand')->nullable()->default(null);
            $table->string('card_last_four')->nullable()->default(null);
            $table->timestamp('trial_ends_at')->nullable()->default(null);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        Schema::drop('companies');
    }
}
