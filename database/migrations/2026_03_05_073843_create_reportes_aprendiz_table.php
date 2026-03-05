<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reportes_aprendiz', function (Blueprint $table) {
            $table->id('id_reporte');
            $table->unsignedBigInteger('id_usuario');
            $table->enum('tipo_reporte', ['daño_articulo', 'perdida_articulo', 'incidente_sede']);
            $table->string('titulo', 150);
            $table->text('descripcion');
            $table->string('foto_url')->nullable();
            $table->enum('estado', ['pendiente', 'en_revision', 'resuelto'])->default('pendiente');
            $table->text('respuesta')->nullable();
            $table->unsignedBigInteger('id_respondido_por')->nullable();
            $table->timestamp('fecha_reporte')->useCurrent();
            $table->timestamp('fecha_respuesta')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reportes_aprendiz');
    }
};