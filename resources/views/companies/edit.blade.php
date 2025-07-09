@extends('layouts.documed')

@section('title', 'Editar Empresa')

@section('content-documed')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Editar Empresa: {{ $company->company_name }}</h1>
        <a href="{{ route('companies.show', $company) }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Volver
        </a>
    </div>

    <form action="{{ route('companies.update', $company) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        
        <div class="row">
            <!-- Información básica -->
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h5><i class="fas fa-building"></i> Información de la Empresa</h5>
                    </div>
                    <div class="card-body">
                        <!-- Campos básicos (no editables) -->
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label for="company_name">Nombre de la Empresa *</label>
                                <input type="text" name="company_name" id="company_name" 
                                       class="form-control @error('company_name') is-invalid @enderror"
                                       value="{{ old('company_name', $company->company_name) }}" required>
                                @error('company_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="legal_representative_dni">DNI Representante Legal *</label>
                                <input type="text" name="legal_representative_dni" id="legal_representative_dni" 
                                       class="form-control @error('legal_representative_dni') is-invalid @enderror"
                                       value="{{ old('legal_representative_dni', $company->legal_representative_dni) }}" required>
                                @error('legal_representative_dni')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="rn_owner">RC del Titular *</label>
                                <input type="text" name="rn_owner" id="rn_owner" 
                                       class="form-control @error('rn_owner') is-invalid @enderror"
                                       value="{{ old('rn_owner', $company->rn_owner) }}" required>
                                @error('rn_owner')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- CAMPOS ADICIONALES COMENTADOS TEMPORALMENTE
                        <!-- Campos adicionales -->
                        <hr>
                        <h6>Información Adicional</h6>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="email">Correo Electrónico</label>
                                <input type="email" name="email" id="email" 
                                       class="form-control @error('email') is-invalid @enderror"
                                       value="{{ old('email', $company->email) }}">
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="phone">Teléfono</label>
                                <input type="text" name="phone" id="phone" 
                                       class="form-control @error('phone') is-invalid @enderror"
                                       value="{{ old('phone', $company->phone) }}">
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label for="address">Dirección</label>
                                <input type="text" name="address" id="address" 
                                       class="form-control @error('address') is-invalid @enderror"
                                       value="{{ old('address', $company->address) }}">
                                @error('address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="city">Ciudad</label>
                                <input type="text" name="city" id="city" 
                                       class="form-control @error('city') is-invalid @enderror"
                                       value="{{ old('city', $company->city) }}">
                                @error('city')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="postal_code">Código Postal</label>
                                <input type="text" name="postal_code" id="postal_code" 
                                       class="form-control @error('postal_code') is-invalid @enderror"
                                       value="{{ old('postal_code', $company->postal_code) }}">
                                @error('postal_code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="province">Provincia</label>
                                <input type="text" name="province" id="province" 
                                       class="form-control @error('province') is-invalid @enderror"
                                       value="{{ old('province', $company->province) }}">
                                @error('province')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="company_type">Tipo de Empresa *</label>
                                <select name="company_type" id="company_type" 
                                        class="form-control @error('company_type') is-invalid @enderror" required>
                                    <option value="clinic" {{ old('company_type', $company->company_type) == 'clinic' ? 'selected' : '' }}>Clínica</option>
                                    <option value="professional" {{ old('company_type', $company->company_type) == 'professional' ? 'selected' : '' }}>Profesional</option>
                                    <option value="both" {{ old('company_type', $company->company_type) == 'both' ? 'selected' : '' }}>Ambos</option>
                                </select>
                                @error('company_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="form-check mt-4">
                                    <input type="checkbox" name="has_center_staff" id="has_center_staff" 
                                           class="form-check-input" value="1"
                                           {{ old('has_center_staff', $company->has_center_staff) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="has_center_staff">
                                        <strong>¿Tiene personal del centro?</strong>
                                    </label>
                                </div>
                                <small class="form-text text-muted">
                                    Marque esta opción si la empresa tiene personal profesional o clínico.
                                </small>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label for="notes">Notas Adicionales</label>
                                <textarea name="notes" id="notes" rows="3" 
                                          class="form-control @error('notes') is-invalid @enderror"
                                          placeholder="Información adicional relevante">{{ old('notes', $company->notes) }}</textarea>
                                @error('notes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        --}}

                        <!-- SECCIÓN PARA PERSONAL DEL CENTRO -->
                        @if($company->documents->count() >= 19)
                            <hr>
                            <div class="alert alert-success">
                                <h6><i class="fas fa-check-circle"></i> Documentación Básica Completada</h6>
                                <p class="mb-0">Has subido {{ $company->documents->count() }} documentos. Ahora puedes proceder con el personal del centro.</p>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <div class="form-check">
                                        <input type="checkbox" name="has_center_staff" id="has_center_staff" 
                                               class="form-check-input" value="1"
                                               {{ old('has_center_staff', $company->has_center_staff) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="has_center_staff">
                                            <strong>¿Tiene personal del centro?</strong>
                                        </label>
                                    </div>
                                    <small class="form-text text-muted">
                                        Marque esta opción si la empresa tiene personal profesional o clínico que requiere documentación adicional.
                                    </small>
                                </div>
                            </div>
                        @else
                            <hr>
                            <div class="alert alert-warning">
                                <h6><i class="fas fa-exclamation-triangle"></i> Documentación Pendiente</h6>
                                <p class="mb-0">Complete la subida de documentos básicos para poder registrar personal del centro.</p>
                                <p class="mb-0"><strong>Documentos subidos:</strong> {{ $company->documents->count() }} / 19 documentos básicos</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- 🚀 NUEVA SECCIÓN: CONTINUAR SUBIENDO DOCUMENTOS -->
                @if($company->documents->count() < 19)
                    <div class="card mt-3">
                        <div class="card-header">
                            <h5><i class="fas fa-file-upload"></i> Continuar Subiendo Documentos 
                                <span class="badge bg-info">{{ $company->documents->count() }}/19</span>
                            </h5>
                        </div>
                        <div class="card-body">
                            @include('companies._form_documents_edit', ['documents' => $documents, 'company' => $company])
                        </div>
                    </div>
                @endif
            </div>

            <!-- Estado y acciones -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h5><i class="fas fa-info-circle"></i> Estado Actual</h5>
                    </div>
                    <div class="card-body">
                        <p><strong>Estado:</strong> 
                            <span class="badge bg-info">{{ $company->status }}</span>
                        </p>
                        <p><strong>Creado:</strong> {{ $company->created_at->format('d/m/Y H:i') }}</p>
                        <p><strong>Documentos:</strong> {{ $company->documents->count() }}/19</p>
                        
                        <hr>
                        
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-save"></i> Guardar Cambios
                            </button>
                            <a href="{{ route('companies.show', $company) }}" class="btn btn-secondary">
                                Cancelar
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Próximos pasos -->
                @if($company->documents->count() > 0 && $company->has_center_staff)
                    <div class="card mt-3">
                        <div class="card-header bg-warning">
                            <h6 class="mb-0"><i class="fas fa-users"></i> Personal del Centro</h6>
                        </div>
                        <div class="card-body">
                            <p class="small">Esta empresa tiene personal del centro. Puede proceder a registrar:</p>
                            <ul class="small">
                                <li>Documentación Profesional</li>
                                <li>Documentación Personal Clínica</li>
                            </ul>
                            <a href="{{ route('staff.index', $company) }}" class="btn btn-warning btn-sm">
                                <i class="fas fa-users"></i> Gestionar Personal
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </form>

    <!-- Documentos existentes -->
    @if($company->documents->count() > 0)
        <div class="card mt-4">
            <div class="card-header">
                <h5><i class="fas fa-file-alt"></i> Documentos Subidos</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    @foreach($company->documents as $document)
                        <div class="col-md-4 mb-3">
                            <div class="card border-success">
                                <div class="card-body">
                                    <h6 class="card-title">{{ $document->name }}</h6>
                                    <p class="card-text">
                                        <small class="text-muted">{{ $document->pivot->original_file_name }}</small>
                                    </p>
                                    @if($document->pivot->path)
                                        <a href="{{ Storage::url($document->pivot->path) }}" 
                                           target="_blank" 
                                           class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye"></i> Ver
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif
@endsection