<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('staff_document', function (Blueprint $table) {
            // Claves foráneas
            $table->unsignedBigInteger('staff_id');
            $table->foreign('staff_id')->references('id')->on('staff')->onDelete('cascade');
            
            $table->unsignedBigInteger('document_id');
            $table->foreign('document_id')->references('id')->on('documents')->onDelete('cascade');
            
            // Clave primaria compuesta
            $table->primary(['staff_id', 'document_id']);
            
            // Datos del archivo
            $table->string('path', 250)->nullable();
            $table->string('original_file_name', 250)->nullable();
            
            // Validación
            $table->boolean('valid')->default(false);
            $table->dateTime('valid_date')->nullable();
            $table->bigInteger('valid_user_id')->nullable();
            $table->text('comments')->nullable();
            
            // Auditoría
            $table->bigInteger('user_id');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('staff_document');
    }
};