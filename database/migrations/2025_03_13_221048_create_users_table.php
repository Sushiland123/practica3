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
        Schema::create('users', function (Blueprint $table) {
            $table->id(); // Crea una columna 'id' como clave primaria autoincremental
            $table->string('nombre');
            $table->string('correo')->unique();
            $table->string('nombreUsuario')->unique();
            $table->integer('edad');
            $table->string('país');
            $table->string('password');
            $table->rememberToken();
            $table->timestamps(); // Crea las columnas 'created_at' y 'updated_at'
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
