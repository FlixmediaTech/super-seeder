<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ExecutedSeeders extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(config('super_seeder.table_name'), function (Blueprint $table) {
            $table->id();
            $table->string('seeder_name');
            $table->timestamp('executed_at');
        });
    }

    public function down()
    {
        Schema::dropIfExists(config('super_seeder.table_name'));
    }
}
