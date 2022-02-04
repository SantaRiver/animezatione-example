<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePacksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('packs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('wax_users');
            $table->string('template_id');
            $table->string('name');
            $table->integer('count')->default(0);
            $table->timestamps();
        });

        Schema::create('packs_pull', function (Blueprint $table) {
            $table->id();
            $table->foreignId('nft_id')->constrained('nft');
            $table->integer('count')->default(0);
            $table->string('pack_template_id');
            $table->timestamps();
        });

        Schema::create('rewards_log', function (Blueprint $table) {
            $table->id();
            $table->string('pack_template_id');
            $table->foreignId('user_id')->constrained('wax_users');
            $table->json('reward');
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
        Schema::dropIfExists('packs');
        Schema::dropIfExists('packs_pull');
        Schema::dropIfExists('rewards_log');
    }
}
