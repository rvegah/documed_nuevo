{{-- resources/views/companies/create.blade.php --}}
@extends('layouts.documed')

@section('title', 'Crear Empresa')

@section('content-documed')
    <h1>Crear Nueva Empresa</h1>
    <form action="{{ route('companies.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        {{-- Incluye el parcial del formulario --}}
        @include('companies._form', ['company' => $company])
        <hr>
        @include('companies._form_documents', ['documents' => $documents])
        <button type="submit" class="btn btn-sm btn-success">Guardar Compañía</button>
        <a href="{{ route('companies.index') }}" class="btn btn-sm btn-secondary">Cancelar</a>
    </form>
@endsection
