<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMarketTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('market_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('wax_users');
            $table->string('transaction_id')->nullable();
            $table->string('template_id')->nullable();
            $table->string('status');
            $table->string('message')->nullable();
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
        Schema::dropIfExists('market_transactions');
    }
}
