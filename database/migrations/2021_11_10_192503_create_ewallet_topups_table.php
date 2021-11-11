<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEwalletTopupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ewallet_topups', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->integer('billing_id')->nullable();
            $table->string('invoice');
            $table->string('payment_method');
            $table->enum('status',['paid' , 'unpaid']);
            $table->integer('amount');
            $table->string('description');

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
        Schema::dropIfExists('ewallet_topups');
    }
}
