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
        Schema::create('company_document', function (Blueprint $table) {
            // 1. Clave foránea para la tabla 'companies'
            $table->unsignedBigInteger('company_id');
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');

            // 2. Clave foránea para la tabla 'documents'
            $table->unsignedBigInteger('document_id');
            $table->foreign('document_id')->references('id')->on('documents')->onDelete('cascade');

            // 3. Clave primaria compuesta
            // Esto asegura que la combinación de company_id y document_id sea única,
            // evitando que un mismo documento se asocie múltiples veces a la misma compañía.
            $table->primary(['company_id', 'document_id']);

            $table->string('path', 250)->nullable();
            $table->boolean('valid')->default(false);
            $table->dateTime('valid_date')->nullable();
            $table->bigInteger('valid_user_id')->nullable();
            $table->text('comments')->nullable();
            $table->bigInteger('user_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('company_document');
    }
};
