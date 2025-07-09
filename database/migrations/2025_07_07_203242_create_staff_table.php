<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('staff', function (Blueprint $table) {
            $table->id();
            
            // Relación con empresa
            $table->unsignedBigInteger('company_id');
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            
            // Tipo de personal
            $table->enum('type', ['professional', 'clinical']); // professional = Profesional, clinical = Personal Clínico
            
            // Datos básicos
            $table->string('name', 255);
            $table->string('dni', 25);
            $table->string('email', 255)->nullable();
            $table->string('phone', 50)->nullable();
            
            // Estado
            $table->string('status', 150)->default('Tramitación');
            $table->boolean('active')->default(true);
            
            // Auditoría
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users');
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('staff');
    }
};