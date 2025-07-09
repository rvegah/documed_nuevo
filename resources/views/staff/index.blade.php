@extends('layouts.documed')

@section('title', 'Personal del Centro')

@section('content-documed')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>
            <i class="fas fa-users"></i> Personal del Centro: {{ $company->company_name }}
        </h1>
        <div>
            <a href="{{ route('companies.show', $company) }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Volver a Empresa
            </a>
        </div>
    </div>

    @if($company->staff->count() == 0)
        <div class="alert alert-info">
            <h5><i class="fas fa-info-circle"></i> ¡Registra el Personal del Centro!</h5>
            <p>Esta empresa tiene habilitado el registro de personal. Puedes agregar:</p>
            <ul>
                <li><strong>Personal Profesional:</strong> Doctores, especialistas, etc.</li>
                <li><strong>Personal Clínico:</strong> Enfermeras, auxiliares, etc.</li>
            </ul>
        </div>
    @endif

    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card text-center">
                <div class="card-body">
                    <i class="fas fa-user-md fa-3x text-primary mb-3"></i>
                    <h5>Documentación Profesional</h5>
                    <p class="text-muted">Registrar doctores, especialistas y profesionales médicos</p>
                    <a href="{{ route('staff.create', ['company' => $company, 'type' => 'professional']) }}" 
                       class="btn btn-primary">
                        <i class="fas fa-plus"></i> Agregar Profesional
                    </a>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card text-center">
                <div class="card-body">
                    <i class="fas fa-user-nurse fa-3x text-success mb-3"></i>
                    <h5>Documentación Personal Clínica</h5>
                    <p class="text-muted">Registrar enfermeras, auxiliares y personal clínico</p>
                    <a href="{{ route('staff.create', ['company' => $company, 'type' => 'clinical']) }}" 
                       class="btn btn-success">
                        <i class="fas fa-plus"></i> Agregar Personal Clínico
                    </a>
                </div>
            </div>
        </div>
    </div>

    @if($company->staff->count() > 0)
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-list"></i> Personal Registrado ({{ $company->staff->count() }})</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Nombre</th>
                                <th>DNI</th>
                                <th>Tipo</th>
                                <th>Estado</th>
                                <th>Documentos</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($company->staff as $staff)
                                <tr>
                                    <td>
                                        <i class="fas {{ $staff->type == 'professional' ? 'fa-user-md text-primary' : 'fa-user-nurse text-success' }}"></i>
                                        {{ $staff->name }}
                                    </td>
                                    <td>{{ $staff->dni }}</td>
                                    <td>
                                        <span class="badge {{ $staff->type == 'professional' ? 'bg-primary' : 'bg-success' }}">
                                            {{ $staff->type_name }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-info">{{ $staff->status }}</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary">
                                            {{ $staff->documents->count() }} documentos
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('staff.show', [$company, $staff]) }}" 
                                               class="btn btn-sm btn-info">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('staff.edit', [$company, $staff]) }}" 
                                               class="btn btn-sm btn-warning">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('staff.destroy', [$company, $staff]) }}" 
                                                  method="POST" style="display: inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" 
                                                        onclick="return confirm('¿Estás seguro de eliminar este personal?')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif
@endsection