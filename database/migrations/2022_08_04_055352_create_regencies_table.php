<?php

use App\Models\Province;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRegenciesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('regencies', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignIdFor(Province::class);
            $table->string('name')->nullable();
            $table->string('lat')->nullable();
            $table->string('lng')->nullable();
            $table->string('tz')->nullable();
            $table->longText('path')->nullable();
            $table->string('area')->nullable();
            $table->string('zoom')->nullable();
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
        Schema::dropIfExists('regencies');
    }
}