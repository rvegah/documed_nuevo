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
        // 🚨 LÍNEA DE DEBUG
        logger('nextStep called - currentStep: ' . $this->currentStep);
        
        $this->validateCurrentStep();
        
        if ($this->currentStep < $this->totalSteps) {
            $this->currentStep++;
        }
        
        // 🚨 OTRA LÍNEA DE DEBUG  
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
        $this->validate();

        // Crear empresa con datos mínimos
        $companyData = [
            'company_name' => $this->company_name,
            'legal_representative_dni' => $this->legal_representative_dni,
            'rn_owner' => $this->rn_owner,
            'user_id' => 1, // Temporal - aquí irá el usuario autenticado
            'status' => 'Tramitación',
        ];

        $company = Company::create($companyData);

        // Procesar archivos subidos si existen
        if (!empty($this->uploadedFiles)) {
            $this->processUploadedFiles($company);
        }

        session()->flash('success', 'Empresa creada exitosamente. Ahora puede completar la información adicional.');
        return redirect()->route('companies.index');
    }

    private function processUploadedFiles($company)
    {
        $documentsToAttach = [];
        
        foreach ($this->uploadedFiles as $documentId => $file) {
            if ($file) {
                $document = Document::find($documentId);
                if ($document) {
                    // 🔧 CAMBIO: Usar solo IDs para evitar espacios en nombres
                    $safeName = 'doc_' . $documentId . '_' . time() . '.' . $file->getClientOriginalExtension();
                    
                    // 🔧 CAMBIO: Estructura de carpetas más simple
                    $path = $file->storeAs(
                        'company_documents/' . $company->id,  // Solo una carpeta por empresa
                        $safeName,
                        'public'
                    );

                    $documentsToAttach[$documentId] = [
                        'path' => $path,
                        'original_file_name' => $file->getClientOriginalName(),
                        'user_id' => 1,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
            }
        }

        if (!empty($documentsToAttach)) {
            $company->documents()->syncWithoutDetaching($documentsToAttach);
        }
    }

    public function render()
    {
        return view('livewire.company-wizard');
    }
}