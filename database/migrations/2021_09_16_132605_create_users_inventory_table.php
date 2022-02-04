<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersInventoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users_inventory', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('wax_users');
            $table->foreignId('card_id')->constrained('nft');
            $table->string('asset_id');
            $table->string('mint')->nullable();
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
        Schema::dropIfExists('users_inventory');
    }
}
