<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Document;
use Illuminate\Support\Facades\Log;

class StoreCompanyRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = [
            'company_name' => ['required', 'string', 'max:255'],
            'legal_representative_dni' => ['required', 'string', 'max:250'],
            'rn_owner' => ['required', 'string', 'max:250'],
            'documents' => ['nullable'],
            'documents.*' => 'nullable|file|mimes:jpeg,png,pdf|max:4096',
        ];

        // 🚀 NUEVO: Agregar validaciones dinámicas para documentos obligatorios
        $this->addRequiredDocumentValidations($rules);

        return $rules;
    }

    /**
     * 🚀 NUEVO MÉTODO: Agregar validaciones para documentos obligatorios
     */
    private function addRequiredDocumentValidations(array &$rules)
    {
        try {
            // Obtener documentos obligatorios básicos
            $requiredDocuments = Document::where('category', 'basic')
                ->where('required', true)
                ->where('active', true)
                ->get();

            Log::info('Documentos obligatorios encontrados para validación:', $requiredDocuments->pluck('id', 'name')->toArray());

            // Si no hay documentos obligatorios, no hacer nada
            if ($requiredDocuments->isEmpty()) {
                Log::info('No hay documentos obligatorios configurados en el sistema');
                return;
            }

            // Agregar reglas de validación para cada documento obligatorio
            foreach ($requiredDocuments as $document) {
                $documentId = $document->id;
                
                // Verificar si es documento de múltiples archivos
                $isMultiple = $this->isMultipleFileDocument($documentId);
                
                if ($isMultiple) {
                    // Para documentos múltiples: requerir al menos un archivo
                    $rules["documents.{$documentId}"] = 'required|array|min:1';
                    $rules["documents.{$documentId}.*"] = 'nullable|file|mimes:jpeg,png,pdf|max:4096';
                } else {
                    // Para documentos únicos: requerir el archivo
                    $rules["documents.{$documentId}"] = 'required|file|mimes:jpeg,png,pdf|max:4096';
                }

                Log::info("Regla de validación agregada para documento {$documentId} ({$document->name}): " . ($isMultiple ? 'múltiple' : 'único'));
            }

        } catch (\Exception $e) {
            Log::error('Error al agregar validaciones de documentos obligatorios: ' . $e->getMessage());
            // No lanzar excepción para no romper el proceso, solo log del error
        }
    }

    /**
     * 🚀 NUEVO MÉTODO: Verificar si un documento permite múltiples archivos
     */
    private function isMultipleFileDocument($documentId)
    {
        // Solo "Contratos de Mantenimiento" (ID: 45) permite múltiples archivos
        return $documentId == 45;
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        $messages = [
            'company_name.required' => 'El nombre de la empresa es obligatorio.',
            'company_name.max' => 'El nombre de la empresa no puede exceder 255 caracteres.',
            'legal_representative_dni.required' => 'El DNI del representante legal es obligatorio.',
            'legal_representative_dni.max' => 'El DNI del representante legal no puede exceder 250 caracteres.',
            'rn_owner.required' => 'El RC del titular es obligatorio.',
            'rn_owner.max' => 'El RC del titular no puede exceder 250 caracteres.',
            'documents.*.mimes' => 'Solo se permiten archivos PDF, JPG, JPEG y PNG.',
            'documents.*.max' => 'El archivo no puede ser mayor a 4MB.',
        ];

        // 🚀 NUEVO: Agregar mensajes personalizados para documentos obligatorios
        $this->addRequiredDocumentMessages($messages);

        return $messages;
    }

    /**
     * 🚀 NUEVO MÉTODO: Agregar mensajes personalizados para documentos obligatorios
     */
    private function addRequiredDocumentMessages(array &$messages)
    {
        try {
            // Obtener documentos obligatorios básicos
            $requiredDocuments = Document::where('category', 'basic')
                ->where('required', true)
                ->where('active', true)
                ->get();

            // Agregar mensajes personalizados para cada documento obligatorio
            foreach ($requiredDocuments as $document) {
                $documentId = $document->id;
                $documentName = $document->name;
                
                // Verificar si es documento de múltiples archivos
                $isMultiple = $this->isMultipleFileDocument($documentId);
                
                if ($isMultiple) {
                    // Mensajes para documentos múltiples
                    $messages["documents.{$documentId}.required"] = "Debe subir al menos un archivo para: {$documentName}";
                    $messages["documents.{$documentId}.array"] = "El campo {$documentName} debe ser un conjunto de archivos.";
                    $messages["documents.{$documentId}.min"] = "Debe subir al menos un archivo para: {$documentName}";
                    $messages["documents.{$documentId}.*.mimes"] = "Los archivos de {$documentName} deben ser PDF, JPG, JPEG o PNG.";
                    $messages["documents.{$documentId}.*.max"] = "Cada archivo de {$documentName} no puede ser mayor a 4MB.";
                } else {
                    // Mensajes para documentos únicos
                    $messages["documents.{$documentId}.required"] = "El documento '{$documentName}' es obligatorio.";
                    $messages["documents.{$documentId}.file"] = "El campo {$documentName} debe ser un archivo.";
                    $messages["documents.{$documentId}.mimes"] = "El archivo {$documentName} debe ser PDF, JPG, JPEG o PNG.";
                    $messages["documents.{$documentId}.max"] = "El archivo {$documentName} no puede ser mayor a 4MB.";
                }
            }

        } catch (\Exception $e) {
            Log::error('Error al agregar mensajes de documentos obligatorios: ' . $e->getMessage());
        }
    }

    /**
     * 🚀 NUEVO MÉTODO: Validación personalizada después de las reglas básicas
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $this->validateRequiredDocumentsCustom($validator);
        });
    }

    /**
     * 🚀 NUEVO MÉTODO: Validación personalizada para documentos obligatorios
     */
    private function validateRequiredDocumentsCustom($validator)
    {
        try {
            // Obtener documentos obligatorios básicos
            $requiredDocuments = Document::where('category', 'basic')
                ->where('required', true)
                ->where('active', true)
                ->get();

            if ($requiredDocuments->isEmpty()) {
                return; // No hay documentos obligatorios
            }

            $missingDocuments = [];
            $files = $this->file('documents', []);

            Log::info('Validando documentos obligatorios en request:', [
                'required_count' => $requiredDocuments->count(),
                'uploaded_files' => array_keys($files)
            ]);

            foreach ($requiredDocuments as $document) {
                $documentId = $document->id;
                $hasValidFile = false;

                if (isset($files[$documentId])) {
                    $documentFiles = $files[$documentId];
                    
                    // Verificar si es documento de múltiples archivos
                    $isMultiple = $this->isMultipleFileDocument($documentId);
                    
                    if ($isMultiple && is_array($documentFiles)) {
                        // Para documentos múltiples: verificar que al menos uno sea válido
                        foreach ($documentFiles as $file) {
                            if ($file && $file->isValid()) {
                                $hasValidFile = true;
                                break;
                            }
                        }
                    } else {
                        // Para documentos únicos: verificar que el archivo sea válido
                        $file = is_array($documentFiles) ? $documentFiles[0] : $documentFiles;
                        if ($file && $file->isValid()) {
                            $hasValidFile = true;
                        }
                    }
                }

                if (!$hasValidFile) {
                    $missingDocuments[] = $document->name;
                }
            }

            // Si hay documentos faltantes, agregar error
            if (!empty($missingDocuments)) {
                $errorMessage = 'Faltan los siguientes documentos obligatorios: ' . implode(', ', $missingDocuments);
                $validator->errors()->add('documents', $errorMessage);
                
                Log::warning('Documentos obligatorios faltantes en request:', $missingDocuments);
            } else {
                Log::info('Todos los documentos obligatorios están presentes');
            }

        } catch (\Exception $e) {
            Log::error('Error en validación personalizada de documentos: ' . $e->getMessage());
            $validator->errors()->add('documents', 'Error al validar documentos obligatorios.');
        }
    }
}