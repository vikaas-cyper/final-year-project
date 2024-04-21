<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('patches', function (Blueprint $table) {
            $table->dropColumn('head_quarter_id');
            $table->bigInteger('state_id')->nullable();
            $table->foreign('state_id')->references('id')->on('states');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('patches', function (Blueprint $table) {
            $table->bigInteger('head_quarter_id');
            $table->dropColumn('state_id');
        });
    }
};
