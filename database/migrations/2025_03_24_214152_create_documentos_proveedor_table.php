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
        Schema::create('documentos_proveedor', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_proveedor_id')->constrained('cliente_proveedor')->cascadeOnDelete();
            $table->foreignId('documento_id')->constrained('documentos')->cascadeOnDelete();
            $table->string('mes');
            $table->string('anio');
            $table->string('ruta')->nullable();
            $table->string('extension')->nullable();
            $table->enum('estado', ['cargado', 'por_cargar', 'faltante', 'no_requerido'])->default('por_cargar');
            $table->dateTime('fecha_carga')->nullable();
            $table->timestamps();

            $table->unique(['cliente_proveedor_id', 'documento_id', 'mes', 'anio']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documentos_proveedor');
    }
};
