<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateActiveStackingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('active_stacking', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('wax_users');
            $table->string('status')->default('stacking');
            $table->float('value', 16, 8);
            $table->float('reward', 16, 8);
            $table->timestamps();
            $table->dateTime('end_time');
        });
        Schema::create('active_stacking_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('wax_users');
            $table->foreignId('active_stacking_id')->constrained('active_stacking');
            $table->string('status');
            $table->string('transaction_id');
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
        Schema::dropIfExists('active_stacking_transactions');
        Schema::dropIfExists('active_stacking');
    }
}
