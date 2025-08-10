<?php

namespace App\Livewire;

use App\Models\Company;
use App\Models\Document;
use Livewire\Component;
use Livewire\WithFileUploads;

class CompanyWizard extends Component
{
    use WithFileUploads;

    // Control de pasos
    public $currentStep = 1;
    public $totalSteps = 3;
    
    // PASO 1: Solo datos básicos mínimos
    public $company_name = '';
    public $legal_representative_dni = '';
    public $rn_owner = '';
    
    // PASO 2: Documentos
    public $basicDocuments = [];
    public $uploadedFiles = [];

    // Reglas de validación simplificadas
    protected $rules = [
        'company_name' => 'required|string|max:255',
        'legal_representative_dni' => 'required|string|max:25',
        'rn_owner' => 'required|string|max:250',
        // AGREGAR: Validación para archivos únicos y múltiples
        'uploadedFiles.*' => 'nullable',
        'uploadedFiles.*.*' => 'nullable|file|mimes:jpeg,png,pdf|max:4096',
    ];

    // Mensajes de validación en español
    protected $messages = [
        'company_name.required' => 'El nombre de la empresa es obligatorio.',
        'legal_representative_dni.required' => 'El DNI del representante legal es obligatorio.',
        'rn_owner.required' => 'El RC del titular es obligatorio.',
    ];

    public function mount()
    {
        $this->loadDocuments();
    }

    public function loadDocuments()
    {
        // Solo documentos básicos para el paso 2
        $this->basicDocuments = Document::where('category', 'basic')
            ->where('active', true)
            ->orderBy('order')
            ->get();
    }

    public function nextStep()
    {
        // LÍNEA DE DEBUG
        logger('nextStep called - currentStep: ' . $this->currentStep);
        
        $this->validateCurrentStep();
        
        if ($this->currentStep < $this->totalSteps) {
            $this->currentStep++;
        }
        
        // OTRA LÍNEA DE DEBUG  
        logger('nextStep finished - currentStep now: ' . $this->currentStep);
    }

    public function previousStep()
    {
        if ($this->currentStep > 1) {
            $this->currentStep--;
        }
    }

    public function goToStep($step)
    {
        if ($step >= 1 && $step <= $this->totalSteps) {
            $this->currentStep = $step;
        }
    }

    public function validateCurrentStep()
    {
        switch ($this->currentStep) {
            case 1:
                // Solo validar los 3 campos básicos
                $this->validate([
                    'company_name' => 'required|string|max:255',
                    'legal_representative_dni' => 'required|string|max:25',
                    'rn_owner' => 'required|string|max:250',
                ]);
                break;
            case 2:
                // Validación de documentos (opcional por ahora)
                break;
            case 3:
                // Validación final
                $this->validate();
                break;
        }
    }

    public function save()
    {
        try {
            // Validar datos básicos
            $this->validate([
                'company_name' => 'required|string|max:255',
                'legal_representative_dni' => 'required|string|max:25',
                'rn_owner' => 'required|string|max:250',
            ]);

            // Crear empresa primero
            $company = Company::create([
                'company_name' => $this->company_name,
                'legal_representative_dni' => $this->legal_representative_dni,
                'rn_owner' => $this->rn_owner,
                'user_id' => auth()->id(),
                'status' => 'Tramitación',
            ]);

            // Procesar archivos de forma segura
            if (!empty($this->uploadedFiles)) {
                $this->processFilesSecurely($company);
            }

            session()->flash('success', 'Empresa creada exitosamente.');
            return redirect()->route('companies.index');

        } catch (\Exception $e) {
            \Log::error('Error en save(): ' . $e->getMessage());
            session()->flash('error', 'Error al crear la empresa: ' . $e->getMessage());
            return;
        }
    }

    /**
     * Procesar archivos de forma más segura
     */
    private function processFilesSecurely($company)
    {
        foreach ($this->uploadedFiles as $documentId => $files) {
            if (empty($files)) continue;

            try {
                // Verificar si el documento existe
                $document = \App\Models\Document::find($documentId);
                if (!$document) continue;

                $isMultiple = ($documentId == 45); // Contratos de Mantenimiento

                if ($isMultiple && is_array($files)) {
                    // PROCESAR MÚLTIPLES ARCHIVOS
                    $fileIndex = 1;
                    foreach ($files as $file) {
                        if ($file && is_object($file) && method_exists($file, 'isValid') && $file->isValid()) {
                            $this->saveFileToDatabase($company, $documentId, $file, $fileIndex);
                            $fileIndex++;
                        }
                    }
                } else {
                    // PROCESAR ARCHIVO ÚNICO
                    $file = is_array($files) ? $files[0] : $files;
                    if ($file && is_object($file) && method_exists($file, 'isValid') && $file->isValid()) {
                        $this->saveFileToDatabase($company, $documentId, $file, 1);
                    }
                }

            } catch (\Exception $e) {
                \Log::error("Error procesando documento {$documentId}: " . $e->getMessage());
                // Continuar con el siguiente documento
            }
        }
    }

    /**
     * Guardar archivo individual en la base de datos
     */
    private function saveFileToDatabase($company, $documentId, $file, $fileIndex)
    {
        // Crear directorio si no existe
        $directory = storage_path('app/public/company_documents/' . $company->id);
        if (!file_exists($directory)) {
            mkdir($directory, 0755, true);
        }

        // Generar nombre único
        $safeName = 'doc_' . $documentId . '_' . $fileIndex . '_' . time() . '.' . $file->getClientOriginalExtension();
        
        // Guardar archivo
        $path = $file->storeAs(
            'company_documents/' . $company->id,
            $safeName,
            'public'
        );

        // Insertar en base de datos
        \DB::table('company_document')->insert([
            'company_id' => $company->id,
            'document_id' => $documentId,
            'file_index' => $fileIndex,
            'path' => $path,
            'original_file_name' => $file->getClientOriginalName(),
            'user_id' => auth()->id(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
}

    private function processUploadedFiles($company)
    {
        $documentsToAttach = [];
        
        foreach ($this->uploadedFiles as $documentId => $files) {
            \Log::info("Processing document ID: {$documentId}");
            \Log::info("Files data:", is_array($files) ? ['count' => count($files)] : ['single_file' => true]);
            
            if (empty($files)) {
                \Log::info("Skipping empty files for document {$documentId}");
                continue;
            }
            
            $document = Document::find($documentId);
            if (!$document) {
                \Log::warning("Document not found: {$documentId}");
                continue;
            }

            $isMultiple = $this->isMultipleFileDocument($documentId);
            \Log::info("Document {$documentId} is multiple: " . ($isMultiple ? 'yes' : 'no'));

            if ($isMultiple && is_array($files)) {
                // ✅ MÚLTIPLES ARCHIVOS
                $fileIndex = 1;
                foreach ($files as $file) {
                    if ($file && is_object($file) && $file->isValid()) {
                        \Log::info("Processing multiple file {$fileIndex} for document {$documentId}");
                        
                        $safeName = 'doc_' . $documentId . '_' . $fileIndex . '_' . time() . '.' . $file->getClientOriginalExtension();
                        $path = $file->storeAs(
                            'company_documents/' . $company->id,
                            $safeName,
                            'public'
                        );

                        $documentsToAttach[] = [
                            'company_id' => $company->id,
                            'document_id' => $documentId,
                            'file_index' => $fileIndex,
                            'path' => $path,
                            'original_file_name' => $file->getClientOriginalName(),
                            'user_id' => auth()->id(),
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                        
                        $fileIndex++;
                    } else {
                        \Log::warning("Invalid file found in multiple files for document {$documentId}");
                    }
                }
            } else {
                // ✅ ARCHIVO ÚNICO
                $file = is_array($files) ? $files[0] : $files;
                if ($file && is_object($file) && $file->isValid()) {
                    \Log::info("Processing single file for document {$documentId}");
                    
                    $safeName = 'doc_' . $documentId . '_' . time() . '.' . $file->getClientOriginalExtension();
                    $path = $file->storeAs(
                        'company_documents/' . $company->id,
                        $safeName,
                        'public'
                    );

                    $documentsToAttach[] = [
                        'company_id' => $company->id,
                        'document_id' => $documentId,
                        'file_index' => 1, // Los documentos únicos siempre usan index 1
                        'path' => $path,
                        'original_file_name' => $file->getClientOriginalName(),
                        'user_id' => auth()->id(),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                } else {
                    \Log::warning("Invalid single file for document {$documentId}");
                }
            }
        }

        // ✅ USAR INSERT DIRECTO para incluir file_index
        if (!empty($documentsToAttach)) {
            \Log::info('Inserting ' . count($documentsToAttach) . ' documents to database');
            \DB::table('company_document')->insert($documentsToAttach);
            \Log::info('Documents inserted successfully');
        } else {
            \Log::info('No documents to insert');
        }
    }

    public function render()
    {
        return view('livewire.company-wizard');
    }

    /**
     * Verificar si un documento permite múltiples archivos
     */
    public function isMultipleFileDocument($documentId)
    {
        // Solo "Contratos de Mantenimiento" (ID: 45) permite múltiples archivos
        return $documentId == 45;
    }
}