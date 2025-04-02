<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('incomes', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('description', 255);
            $table->decimal('amount', 10, 2);
            $table->date('date');
            //$table->bigInteger('category_type_id');
            //$table->bigInteger('cashbox_id');
            $table->timestamps();
        });
    }
    public function down()
    {
        Schema::dropIfExists('incomes');
    }
};
