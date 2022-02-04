<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePackRewardsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pack_rewards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pack_id')->constrained('nft');
            $table->foreignId('card_id')->constrained('nft');
            $table->integer('count')->nullable();
            $table->string('type')->default('random');
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
        Schema::dropIfExists('pack_rewards');
    }
}
