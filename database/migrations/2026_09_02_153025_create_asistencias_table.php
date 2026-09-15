<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('asistencias', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained()->onDelete('cascade');
        $table->enum('tipo', ['entrada','salida']);
        $table->timestamps(); // created_at y updated_at
    });


    }

    public function down(): void {
        Schema::dropIfExists('asistencias');
    }
};
