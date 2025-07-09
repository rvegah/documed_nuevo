<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Document;

class DocumentsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Primero eliminamos todos los documentos existentes
        Document::query()->delete();
        
        $documents = [
            // ==========================================
            // DOCUMENTOS BÁSICOS DE EMPRESA (1-19)
            // ==========================================
            ['name' => 'Copia del DNI del Representante Legal', 'order' => 1, 'category' => 'basic', 'required' => true],
            ['name' => 'Copia RC del Titular', 'order' => 2, 'category' => 'basic', 'required' => true],
            ['name' => 'Copia del Último Pago de la RC del Titular', 'order' => 3, 'category' => 'basic', 'required' => true],
            ['name' => 'Copia de la Compra Venta / Alquiler del Local', 'order' => 4, 'category' => 'basic', 'required' => true],
            ['name' => 'Copia Licencia de Actividad (Ayuntamiento)', 'order' => 5, 'category' => 'basic', 'required' => true],
            ['name' => 'Copia Memoria Técnica del Centro', 'order' => 6, 'category' => 'basic', 'required' => true],
            ['name' => 'Plano de Situación', 'order' => 7, 'category' => 'basic', 'required' => true],
            ['name' => 'Plano de Planta, Firmado 1/100 o 1/150', 'order' => 8, 'category' => 'basic', 'required' => true],
            ['name' => 'Plano de Planta con Especificaciones', 'order' => 9, 'category' => 'basic', 'required' => true],
            ['name' => 'Contratos de Mantenimiento', 'order' => 10, 'category' => 'basic', 'required' => true],
            ['name' => 'Alta Agencia Protección de Datos', 'order' => 11, 'category' => 'basic', 'required' => true],
            ['name' => 'Contrato de Protección de Datos', 'order' => 12, 'category' => 'basic', 'required' => true],
            ['name' => 'Copia Alta Productor Residuos Tipo III', 'order' => 13, 'category' => 'basic', 'required' => true],
            ['name' => 'Copia Contrato Recogida de Residuos', 'order' => 14, 'category' => 'basic', 'required' => true],
            ['name' => 'Alta Instalación de RX', 'order' => 15, 'category' => 'basic', 'required' => true],
            ['name' => 'Contrato Protección Radiológica', 'order' => 16, 'category' => 'basic', 'required' => true],
            ['name' => 'Programa de Garantía de Calidad', 'order' => 17, 'category' => 'basic', 'required' => true],
            ['name' => 'Programa de Protección Radiológica', 'order' => 18, 'category' => 'basic', 'required' => true],
            ['name' => 'Contrato de Dosimetría', 'order' => 19, 'category' => 'basic', 'required' => true],
            
            // ==========================================
            // DOCUMENTOS PARA PROFESIONALES (20-29)
            // ==========================================
            ['name' => 'DNI Profesional', 'order' => 20, 'category' => 'professional', 'required' => true],
            ['name' => 'Título General Profesional', 'order' => 21, 'category' => 'professional', 'required' => true],
            ['name' => 'Títulos de Especialidades', 'order' => 22, 'category' => 'professional', 'required' => false],
            ['name' => 'Copia Póliza Responsabilidad Civil Profesional', 'order' => 23, 'category' => 'professional', 'required' => true],
            ['name' => 'Comprobante Último Pago RC Profesional', 'order' => 24, 'category' => 'professional', 'required' => true],
            ['name' => 'Certificado Colegiación Actual', 'order' => 25, 'category' => 'professional', 'required' => true],
            ['name' => 'Certificado Delitos Sexuales Profesional', 'order' => 26, 'category' => 'professional', 'required' => true],
            ['name' => 'Acuerdo de Colaboración', 'order' => 27, 'category' => 'professional', 'required' => true],
            ['name' => 'Título RX Profesional', 'order' => 28, 'category' => 'professional', 'required' => false],
            ['name' => 'Título RCP Profesional', 'order' => 29, 'category' => 'professional', 'required' => false],
            
            // ==========================================
            // DOCUMENTOS PARA PERSONAL CLÍNICO (30-35)
            // ==========================================
            ['name' => 'DNI Personal Clínico', 'order' => 30, 'category' => 'clinical', 'required' => true],
            ['name' => 'Título General Personal Clínico', 'order' => 31, 'category' => 'clinical', 'required' => true],
            ['name' => 'Otros Títulos Personal Clínico', 'order' => 32, 'category' => 'clinical', 'required' => false],
            ['name' => 'Contrato/ITA Personal Clínico', 'order' => 33, 'category' => 'clinical', 'required' => true],
            ['name' => 'Certificado Delitos Sexuales Personal Clínico', 'order' => 34, 'category' => 'clinical', 'required' => true],
            ['name' => 'Título RX Personal Clínico', 'order' => 35, 'category' => 'clinical', 'required' => false],
            ['name' => 'Título RCP Personal Clínico', 'order' => 36, 'category' => 'clinical', 'required' => false],
        ];

        foreach ($documents as $document) {
            Document::create($document);
        }
    }
}
