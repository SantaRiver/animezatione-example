<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFaucetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('faucets', function (Blueprint $table) {
            $table->id();
            $table->string('token');
            $table->float('counter', 10, 6);
            $table->integer('timer')->comment('second');
            $table->string('name')->nullable();
            $table->string('contract')->nullable();
            $table->string('link')->nullable();
            $table->integer('alcor_id')->nullable();
            $table->boolean('active')->default(true);
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
        Schema::dropIfExists('faucets');
    }
}
