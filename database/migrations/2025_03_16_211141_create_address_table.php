<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('address', function (Blueprint $table) {
            $table->id();
            $table->string('street', 100);
            $table->string('district', 100);
            $table->string('city', 100);
            $table->string('state', 100);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('address');
    }
};
