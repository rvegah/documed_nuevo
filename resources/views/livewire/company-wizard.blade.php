<div>
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-building"></i> Asistente de Registro de Empresa
                        </h3>
                    </div>
                    <div class="card-body">
                        <!-- Progress Bar -->
                        <div class="progress mb-4" style="height: 10px;">
                            <div class="progress-bar bg-primary" role="progressbar" 
                                 style="width: {{ ($currentStep / $totalSteps) * 100 }}%"
                                 aria-valuenow="{{ $currentStep }}" 
                                 aria-valuemin="0" 
                                 aria-valuemax="{{ $totalSteps }}">
                            </div>
                        </div>

                        <!-- Step Navigation -->
                        <div class="wizard-steps mb-4">
                            <div class="row text-center">
                                <div class="col-4">
                                    <div class="step-item {{ $currentStep >= 1 ? 'active' : '' }}">
                                        <div class="step-number">1</div>
                                        <div class="step-title">Datos Básicos</div>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="step-item {{ $currentStep >= 2 ? 'active' : '' }}">
                                        <div class="step-number">2</div>
                                        <div class="step-title">Documentos</div>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="step-item {{ $currentStep >= 3 ? 'active' : '' }}">
                                        <div class="step-number">3</div>
                                        <div class="step-title">Revisión Final</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Step Content -->
                        <div class="step-content">
                            @if($currentStep == 1)
                                <!-- PASO 1: Solo datos básicos mínimos -->
                                <div class="step1-content">
                                    <h4 class="mb-3"><i class="fas fa-info-circle"></i> Información Básica de la Empresa</h4>
                                    <p class="text-muted mb-4">Ingrese los datos básicos requeridos para iniciar el registro.</p>
                                    
                                    <div class="row justify-content-center">
                                        <div class="col-md-8">
                                            <div class="form-group mb-3">
                                                <label for="company_name">Nombre de la Empresa *</label>
                                                <input type="text" 
                                                       class="form-control @error('company_name') is-invalid @enderror" 
                                                       id="company_name" 
                                                       wire:model="company_name"
                                                       placeholder="Ingrese el nombre completo de la empresa">
                                                @error('company_name') 
                                                    <span class="invalid-feedback">{{ $message }}</span> 
                                                @enderror
                                            </div>

                                            <div class="form-group mb-3">
                                                <label for="legal_representative_dni">DNI Representante Legal *</label>
                                                <input type="text" 
                                                       class="form-control @error('legal_representative_dni') is-invalid @enderror" 
                                                       id="legal_representative_dni" 
                                                       wire:model="legal_representative_dni"
                                                       placeholder="Ejemplo: 12345678A">
                                                @error('legal_representative_dni') 
                                                    <span class="invalid-feedback">{{ $message }}</span> 
                                                @enderror
                                            </div>

                                            <div class="form-group mb-3">
                                                <label for="rn_owner">RC del Titular *</label>
                                                <input type="text" 
                                                       class="form-control @error('rn_owner') is-invalid @enderror" 
                                                       id="rn_owner" 
                                                       wire:model="rn_owner"
                                                       placeholder="Ejemplo: RC-123456">
                                                @error('rn_owner') 
                                                    <span class="invalid-feedback">{{ $message }}</span> 
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div class="alert alert-info mt-3">
                                        <i class="fas fa-info-circle"></i>
                                        <strong>Información:</strong> Después de completar estos datos básicos, podrá subir los documentos requeridos y completar información adicional de la empresa.
                                    </div>
                                </div>

                            @elseif($currentStep == 2)
                                <!-- PASO 2: Documentos con subida de archivos -->
                                <div class="step2-content">
                                    <h4 class="mb-3"><i class="fas fa-file-alt"></i> Documentos Básicos Requeridos</h4>
                                    <p class="text-muted mb-4">
                                        Suba los documentos requeridos. Los documentos marcados como <span class="badge bg-danger">Obligatorio</span> deben subirse para continuar.
                                    </p>
                                    
                                    {{-- 🚀 NUEVO: Mostrar errores de validación --}}
                                    @error('uploadedFiles')
                                        <div class="alert alert-danger">
                                            <i class="fas fa-exclamation-triangle"></i>
                                            <strong>Error:</strong> {{ $message }}
                                        </div>
                                    @enderror

                                    {{-- 🚀 NUEVO: Mostrar progreso de documentos obligatorios --}}
                                    @if(count($requiredDocuments) > 0)
                                        @php
                                            $uploadedRequired = 0;
                                            foreach($requiredDocuments as $docId) {
                                                if(isset($uploadedFiles[$docId])) {
                                                    $files = $uploadedFiles[$docId];
                                                    $hasFile = false;
                                                    if($this->isMultipleFileDocument($docId)) {
                                                        if(is_array($files)) {
                                                            foreach($files as $file) {
                                                                if($file && is_object($file) && method_exists($file, 'isValid') && $file->isValid()) {
                                                                    $hasFile = true;
                                                                    break;
                                                                }
                                                            }
                                                        }
                                                    } else {
                                                        $file = is_array($files) ? $files[0] : $files;
                                                        if($file && is_object($file) && method_exists($file, 'isValid') && $file->isValid()) {
                                                            $hasFile = true;
                                                        }
                                                    }
                                                    if($hasFile) $uploadedRequired++;
                                                }
                                            }
                                            $progressPercent = count($requiredDocuments) > 0 ? ($uploadedRequired / count($requiredDocuments)) * 100 : 0;
                                        @endphp
                                        
                                        <div class="alert alert-info">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <i class="fas fa-info-circle"></i>
                                                    <strong>Progreso de documentos obligatorios:</strong> {{ $uploadedRequired }} / {{ count($requiredDocuments) }}
                                                </div>
                                                <div class="progress" style="width: 200px; height: 20px;">
                                                    <div class="progress-bar {{ $progressPercent == 100 ? 'bg-success' : 'bg-warning' }}" 
                                                         style="width: {{ $progressPercent }}%">
                                                        {{ round($progressPercent) }}%
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif

                                    <!-- Lista de documentos con subida de archivos -->
                                    <div class="row">
                                        @foreach($basicDocuments as $document)
                                            @php
                                                $isMultipleFile = $this->isMultipleFileDocument($document->id);
                                                $isRequired = $document->required;
                                                $hasUploadedFile = false;
                                                
                                                // Verificar si tiene archivo subido
                                                if(isset($uploadedFiles[$document->id])) {
                                                    $files = $uploadedFiles[$document->id];
                                                    if($isMultipleFile && is_array($files)) {
                                                        foreach($files as $file) {
                                                            if($file && is_object($file) && method_exists($file, 'isValid') && $file->isValid()) {
                                                                $hasUploadedFile = true;
                                                                break;
                                                            }
                                                        }
                                                    } else {
                                                        $file = is_array($files) ? $files[0] : $files;
                                                        if($file && is_object($file) && method_exists($file, 'isValid') && $file->isValid()) {
                                                            $hasUploadedFile = true;
                                                        }
                                                    }
                                                }
                                            @endphp
                                            
                                            <div class="col-md-6 mb-3">
                                                <div class="card {{ $isMultipleFile ? 'border-primary' : '' }} {{ $isRequired && !$hasUploadedFile ? 'border-danger' : '' }} {{ $hasUploadedFile ? 'border-success' : '' }}">
                                                    <div class="card-body">
                                                        <h6 class="card-title">
                                                            {{ $document->name }}
                                                            @if($hasUploadedFile)
                                                                <i class="fas fa-check-circle text-success"></i>
                                                            @endif
                                                            @if($isMultipleFile)
                                                                <span class="badge bg-primary">Múltiples archivos</span>
                                                            @endif
                                                        </h6>
                                                        <p class="card-text text-muted small">
                                                            @if($isRequired)
                                                                <span class="badge bg-danger">Obligatorio</span>
                                                            @else
                                                                <span class="badge bg-secondary">Opcional</span>
                                                            @endif
                                                            @if($isMultipleFile)
                                                                <span class="badge bg-info">Hasta 6 archivos</span>
                                                            @endif
                                                        </p>
                                                        
                                                        @if($isMultipleFile)
                                                            {{-- DOCUMENTO CON MÚLTIPLES ARCHIVOS --}}
                                                            <div class="multiple-files-section" id="multiple-{{ $document->id }}">
                                                                <div class="file-inputs-container">
                                                                    <!-- Primer input siempre visible -->
                                                                    <div class="input-group mb-2">
                                                                        <input type="file" 
                                                                            class="form-control form-control-sm {{ $isRequired && !$hasUploadedFile ? 'border-danger' : '' }}"
                                                                            wire:model="uploadedFiles.{{ $document->id }}.0"
                                                                            accept=".pdf,.jpg,.jpeg,.png"
                                                                            id="file_{{ $document->id }}_0">
                                                                    </div>
                                                                    
                                                                    <!-- Inputs adicionales (se mostrarán dinámicamente) -->
                                                                    @for($i = 1; $i < 6; $i++)
                                                                        <div class="input-group mb-2" style="display: none;" id="file-group-{{ $document->id }}-{{ $i }}">
                                                                            <input type="file" 
                                                                                class="form-control form-control-sm"
                                                                                wire:model="uploadedFiles.{{ $document->id }}.{{ $i }}"
                                                                                accept=".pdf,.jpg,.jpeg,.png"
                                                                                id="file_{{ $document->id }}_{{ $i }}">
                                                                            <button type="button" 
                                                                                    class="btn btn-outline-danger btn-sm"
                                                                                    onclick="removeWizardFileInput({{ $document->id }}, {{ $i }})"
                                                                                    title="Eliminar este campo">
                                                                                <i class="fas fa-trash"></i>
                                                                            </button>
                                                                        </div>
                                                                    @endfor
                                                                </div>
                                                                
                                                                <button type="button" 
                                                                        class="btn btn-sm btn-outline-primary"
                                                                        onclick="addWizardFileInput({{ $document->id }})"
                                                                        id="add-btn-{{ $document->id }}">
                                                                    <i class="fas fa-plus"></i> Agregar otro archivo
                                                                </button>
                                                            </div>
                                                            
                                                            <!-- Mostrar archivos seleccionados para múltiples -->
                                                            @if(isset($uploadedFiles[$document->id]) && is_array($uploadedFiles[$document->id]))
                                                                <div class="mt-2">
                                                                    @foreach($uploadedFiles[$document->id] as $index => $file)
                                                                        @if($file)
                                                                            <div class="alert alert-success py-1 mt-1">
                                                                                <i class="fas fa-check-circle"></i>
                                                                                <small>Archivo {{ $index + 1 }}: {{ $file->getClientOriginalName() }}</small>
                                                                            </div>
                                                                        @endif
                                                                    @endforeach
                                                                </div>
                                                            @endif
                                                            
                                                        @else
                                                            {{-- DOCUMENTO ÚNICO (lógica original) --}}
                                                            <!-- Input para subir archivo -->
                                                            <div class="mb-2">
                                                                <input type="file" 
                                                                    class="form-control form-control-sm {{ $isRequired && !$hasUploadedFile ? 'border-danger' : '' }}"
                                                                    wire:model="uploadedFiles.{{ $document->id }}"
                                                                    accept=".pdf,.jpg,.jpeg,.png"
                                                                    id="file_{{ $document->id }}">
                                                            </div>
                                                            
                                                            <!-- Mostrar progreso de subida -->
                                                            <div wire:loading wire:target="uploadedFiles.{{ $document->id }}">
                                                                <div class="progress" style="height: 10px;">
                                                                    <div class="progress-bar progress-bar-striped progress-bar-animated" 
                                                                        role="progressbar" style="width: 100%"></div>
                                                                </div>
                                                                <small class="text-muted">Subiendo archivo...</small>
                                                            </div>
                                                            
                                                            <!-- Mostrar archivo seleccionado -->
                                                            @if(isset($uploadedFiles[$document->id]) && !is_array($uploadedFiles[$document->id]))
                                                                <div class="alert alert-success py-2 mt-2">
                                                                    <i class="fas fa-check-circle"></i>
                                                                    <small>Archivo seleccionado: {{ $uploadedFiles[$document->id]->getClientOriginalName() }}</small>
                                                                </div>
                                                            @endif
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>

                                    <div class="alert alert-warning mt-3">
                                        <i class="fas fa-exclamation-triangle"></i>
                                        <strong>Nota:</strong> Formatos permitidos: PDF, JPG, JPEG, PNG. Tamaño máximo: 4MB por archivo.
                                    </div>
                                </div>
                                
                            @elseif($currentStep == 3)
                                <!-- PASO 3: Revisión Final -->
                                <div class="step3-content">
                                    <h4 class="mb-3"><i class="fas fa-check-circle"></i> Revisión Final del Registro</h4>
                                    <p class="text-muted mb-4">
                                        Revise toda la información antes de guardar el registro de la empresa.
                                    </p>

                                    {{-- 🚀 NUEVO: Mostrar errores de validación en paso final --}}
                                    @error('uploadedFiles')
                                        <div class="alert alert-danger">
                                            <i class="fas fa-exclamation-triangle"></i>
                                            <strong>Error:</strong> {{ $message }}
                                            <br><small>Regrese al paso 2 para completar los documentos faltantes.</small>
                                        </div>
                                    @enderror
                                    
                                    <div class="card">
                                        <div class="card-header">
                                            <h5><i class="fas fa-building"></i> Información de la Empresa</h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <p><strong>Nombre de la Empresa:</strong> {{ $company_name ?: 'No especificado' }}</p>
                                                    <p><strong>DNI Representante Legal:</strong> {{ $legal_representative_dni ?: 'No especificado' }}</p>
                                                    <p><strong>RC del Titular:</strong> {{ $rn_owner ?: 'No especificado' }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Resumen de documentos -->
                                    @if(!empty($uploadedFiles))
                                        <div class="card mt-3">
                                            <div class="card-header">
                                                <h5><i class="fas fa-file-alt"></i> Documentos a Subir</h5>
                                            </div>
                                            <div class="card-body">
                                                @foreach($uploadedFiles as $documentId => $files)
                                                    @if($files)
                                                        @php
                                                            $document = $basicDocuments->find($documentId);
                                                            $isMultiple = $this->isMultipleFileDocument($documentId);
                                                        @endphp
                                                        
                                                        <div class="mb-2">
                                                            <strong>{{ $document->name ?? 'Documento desconocido' }}</strong>
                                                            @if($document && $document->required)
                                                                <span class="badge bg-danger ms-2">Obligatorio</span>
                                                            @endif
                                                            
                                                            @if($isMultiple && is_array($files))
                                                                {{-- MÚLTIPLES ARCHIVOS --}}
                                                                <div class="mt-1">
                                                                    @foreach($files as $index => $file)
                                                                        @if($file)
                                                                            <div class="badge bg-success me-1 mb-1">
                                                                                Archivo {{ $index + 1 }}: {{ $file->getClientOriginalName() }}
                                                                            </div>
                                                                        @endif
                                                                    @endforeach
                                                                </div>
                                                            @else
                                                                {{-- ARCHIVO ÚNICO --}}
                                                                @php
                                                                    $file = is_array($files) ? $files[0] : $files;
                                                                @endphp
                                                                @if($file)
                                                                    <div class="badge bg-success">
                                                                        {{ $file->getClientOriginalName() }}
                                                                    </div>
                                                                @endif
                                                            @endif
                                                        </div>
                                                    @endif
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif

                                    <div class="alert alert-info mt-3">
                                        <i class="fas fa-info-circle"></i>
                                        <strong>Importante:</strong> Una vez guardada la empresa, podrá completar información adicional como datos de contacto, dirección y tipo de empresa.
                                    </div>
                                </div>
                            @endif
                        </div>

                        <!-- Navigation Buttons -->
                        <div class="row mt-4">
                            <div class="col-6">
                                @if($currentStep > 1)
                                    <button type="button" class="btn btn-secondary" wire:click="previousStep">
                                        <i class="fas fa-arrow-left"></i> Anterior
                                    </button>
                                @endif
                            </div>
                            <div class="col-6 text-right">
                                @if($currentStep < $totalSteps)
                                    <button type="button" class="btn btn-primary" wire:click="nextStep">
                                        Siguiente <i class="fas fa-arrow-right"></i>
                                    </button>
                                @else
                                    <button type="button" class="btn btn-success" wire:click="save">
                                        <i class="fas fa-save"></i> Guardar Empresa
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
    .step-item {
        padding: 1rem;
        border-radius: 8px;
        transition: all 0.3s ease;
    }

    .step-item.active {
        background-color: #007bff;
        color: white;
    }

    .step-number {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background-color: #e9ecef;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 0.5rem;
        font-weight: bold;
    }

    .step-item.active .step-number {
        background-color: white;
        color: #007bff;
    }

    .step-title {
        font-size: 0.9rem;
        font-weight: 500;
    }

    /* 🚀 NUEVO: Estilos para documentos obligatorios */
    .border-danger {
        border-color: #dc3545 !important;
    }

    .border-success {
        border-color: #28a745 !important;
    }
    </style>

    <script>
        // Variables globales
        window.fileCounters = window.fileCounters || {};
        
        // Función para agregar input de archivo
        window.addWizardFileInput = function(documentId) {
            console.log('Intentando agregar archivo para documento:', documentId);
            
            if (!window.fileCounters[documentId]) {
                window.fileCounters[documentId] = 1;
            }
            
            if (window.fileCounters[documentId] >= 6) {
                alert('Máximo 6 archivos permitidos.');
                return;
            }
            
            // Buscar el siguiente input oculto
            const nextInput = document.getElementById(`file-group-${documentId}-${window.fileCounters[documentId]}`);
            console.log('Buscando elemento:', `file-group-${documentId}-${window.fileCounters[documentId]}`, nextInput);
            
            if (nextInput) {
                nextInput.style.display = 'flex';
                window.fileCounters[documentId]++;
                console.log('Archivo agregado. Contador ahora:', window.fileCounters[documentId]);
                
                // Ocultar botón si llegamos al límite
                if (window.fileCounters[documentId] >= 6) {
                    const addBtn = document.getElementById(`add-btn-${documentId}`);
                    if (addBtn) addBtn.style.display = 'none';
                }
            } else {
                console.error('No se encontró el elemento:', `file-group-${documentId}-${window.fileCounters[documentId]}`);
            }
        };
        
        // Función para remover input de archivo
        window.removeWizardFileInput = function(documentId, index) {
            console.log('Removiendo archivo:', documentId, index);
            
            const inputGroup = document.getElementById(`file-group-${documentId}-${index}`);
            if (inputGroup) {
                inputGroup.style.display = 'none';
                
                // Limpiar el input
                const input = inputGroup.querySelector('input[type="file"]');
                if (input) {
                    input.value = '';
                    // Notificar a Livewire
                    input.dispatchEvent(new Event('input', { bubbles: true }));
                }
                
                // Reducir contador
                if (window.fileCounters[documentId] > 1) {
                    window.fileCounters[documentId]--;
                }
                
                // Mostrar botón agregar
                const addBtn = document.getElementById(`add-btn-${documentId}`);
                if (addBtn) {
                    addBtn.style.display = 'inline-block';
                }
            }
        };
        
        // Inicializar cuando el DOM esté listo
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Inicializando contadores de archivos...');
            @foreach($basicDocuments as $document)
                @if($this->isMultipleFileDocument($document->id))
                    window.fileCounters[{{ $document->id }}] = 1;
                    console.log('Inicializado contador para documento {{ $document->id }}');
                @endif
            @endforeach
        });
    </script>

</div>