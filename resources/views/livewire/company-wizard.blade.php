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
                                        Suba los documentos básicos requeridos. Puede continuar sin subir todos los archivos y completarlos más tarde.
                                    </p>
                                    
                                    <div class="alert alert-info">
                                        <i class="fas fa-info-circle"></i>
                                        <strong>Información:</strong> Se encontraron {{ count($basicDocuments) }} documentos básicos requeridos.
                                    </div>

                                    <!-- Lista de documentos con subida de archivos -->
                                    <div class="row">
                                        @foreach($basicDocuments as $document)
                                            <div class="col-md-6 mb-3">
                                                <div class="card">
                                                    <div class="card-body">
                                                        <h6 class="card-title">{{ $document->name }}</h6>
                                                        <p class="card-text text-muted small">
                                                            @if($document->required)
                                                                <span class="badge bg-danger">Requerido</span>
                                                            @else
                                                                <span class="badge bg-secondary">Opcional</span>
                                                            @endif
                                                        </p>
                                                        
                                                        <!-- Input para subir archivo -->
                                                        <div class="mb-2">
                                                            <input type="file" 
                                                                   class="form-control form-control-sm"
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
                                                        @if(isset($uploadedFiles[$document->id]))
                                                            <div class="alert alert-success py-2 mt-2">
                                                                <i class="fas fa-check-circle"></i>
                                                                <small>Archivo seleccionado: {{ $uploadedFiles[$document->id]->getClientOriginalName() }}</small>
                                                            </div>
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
                                                @foreach($uploadedFiles as $documentId => $file)
                                                    @if($file)
                                                        @php
                                                            $document = $basicDocuments->find($documentId);
                                                        @endphp
                                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                                            <span>{{ $document->name ?? 'Documento desconocido' }}</span>
                                                            <span class="badge bg-success">{{ $file->getClientOriginalName() }}</span>
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
                                    <!--<button type="button" class="btn btn-warning" onclick="alert('Botón funciona')">
                                        Test Normal
                                    </button>
                                    <button type="button" class="btn btn-danger" wire:click="$set('currentStep', 2)">
                                        Test Livewire
                                    </button>-->
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
    </style>
</div>