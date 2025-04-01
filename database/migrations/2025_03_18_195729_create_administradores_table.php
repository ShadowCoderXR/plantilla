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
        Schema::create('administradores', function (Blueprint $table) {
            $table->id();
            $table->string('nombre')->unique();
            $table->string('descripcion')->default('Sin descripción');
            $table->string('logo')->default('img/logos/default.webp');
            $table->string('small_logo')->default('img/small-logos/default.webp');
            $table->string('color')->default('#000000');
            $table->string('telefono')->default('Sin datos');
            $table->string('correo')->default('Sin datos');
            $table->text('descripcion_adicional')->default('Sin descripción adicional');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('administradores');
    }
};
