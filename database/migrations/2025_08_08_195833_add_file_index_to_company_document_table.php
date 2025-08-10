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
            // Solo crear el índice único (la columna ya existe)
            $table->unique(['company_id', 'document_id', 'file_index'], 'company_document_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('company_document', function (Blueprint $table) {
            $table->dropUnique('company_document_unique');
        });
    }
};