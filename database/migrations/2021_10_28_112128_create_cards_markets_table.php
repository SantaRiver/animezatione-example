<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCardsMarketsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('nft_markets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('nft_id')->constrained('nft');
            $table->boolean('on_sale')->default(false);
            $table->float('price_usd')->comment('Price in USD')->nullable();
            $table->float('price_ani')->comment('Price in ANI')->nullable();
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
        Schema::dropIfExists('cards_markets');
    }
}
