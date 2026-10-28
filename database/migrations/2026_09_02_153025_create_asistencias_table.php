<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('asistencias', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->date('fecha')->nullable();
            $table->time('hora_entrada')->nullable();
            $table->time('hora_salida')->nullable();
            $table->enum('tipo', ['entrada', 'salida'])->nullable();
            $table->enum('estado', ['presente', 'ausente', 'atraso', 'salida_anticipada'])->nullable();
            $table->timestamps(); // created_at y updated_at
        });
    }

    public function down(): void {
        Schema::dropIfExists('asistencias');
    }
};
