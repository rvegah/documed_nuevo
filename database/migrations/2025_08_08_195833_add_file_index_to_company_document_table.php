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
        Schema::table('company_document', function (Blueprint $table) {
            // Agregar columna si no existe
            if (!Schema::hasColumn('company_document', 'file_index')) {
                $table->integer('file_index')->default(1)->after('document_id');
                // Crear índice único solo cuando se crea la columna
                $table->unique(['company_id', 'document_id', 'file_index'], 'company_document_unique');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('company_document', function (Blueprint $table) {
            if (Schema::hasColumn('company_document', 'file_index')) {
                $table->dropUnique('company_document_unique');
                $table->dropColumn('file_index');
            }
        });
    }
};