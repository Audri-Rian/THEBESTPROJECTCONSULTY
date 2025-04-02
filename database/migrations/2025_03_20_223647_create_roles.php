<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('level');
            $table->timestamps();
        });

        DB::table('roles')->insert([
            ['name' => 'admin','level' => 1, 'created_at' => now(),'updated_at' => now(),],
            ['name' => 'employee', 'level'=> 2, 'created_at' => now(),'updated_at' => now(),],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('roles');
    }
};
