<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddApiPerHour extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('wax_users', function (Blueprint $table) {
            $table->integer('ANI_per_hour')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('wax_users', 'ANI_per_hour')){
            Schema::dropColumns('wax_users', ['ANI_per_hour']);
        }
    }
}
