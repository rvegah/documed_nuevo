@extends('layouts.documed')

@section('title', 'Registro')

@section('content-documed')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-success text-white">
                <h4 class="mb-0">
                    <i class="fas fa-user-plus"></i> Crear Cuenta en DocuMed
                </h4>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('register') }}">
                    @csrf

                    <!-- Name -->
                    <div class="mb-3">
                        <label for="name" class="form-label">Nombre Completo</label>
                        <input id="name" 
                               class="form-control @error('name') is-invalid @enderror" 
                               type="text" 
                               name="name" 
                               value="{{ old('name') }}" 
                               required 
                               autofocus 
                               autocomplete="name">
                        @error('name')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <!-- Email Address -->
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input id="email" 
                               class="form-control @error('email') is-invalid @enderror" 
                               type="email" 
                               name="email" 
                               value="{{ old('email') }}" 
                               required 
                               autocomplete="username">
                        @error('email')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <!-- Password -->
                    <div class="mb-3">
                        <label for="password" class="form-label">Contraseña</label>
                        <input id="password" 
                               class="form-control @error('password') is-invalid @enderror"
                               type="password"
                               name="password"
                               required 
                               autocomplete="new-password">
                        @error('password')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <!-- Confirm Password -->
                    <div class="mb-3">
                        <label for="password_confirmation" class="form-label">Confirmar Contraseña</label>
                        <input id="password_confirmation" 
                               class="form-control @error('password_confirmation') is-invalid @enderror"
                               type="password"
                               name="password_confirmation" 
                               required 
                               autocomplete="new-password">
                        @error('password_confirmation')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-between align-items-center">
                        <a class="text-decoration-none" href="{{ route('login') }}">
                            ¿Ya tienes cuenta?
                        </a>

                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-user-plus"></i> Registrarse
                        </button>
                    </div>
                </form>

                <div class="mt-4">
                    <div class="alert alert-warning">
                        <small>
                            <i class="fas fa-info-circle"></i> 
                            Los nuevos usuarios se registran como <strong>Usuario Normal</strong>. 
                            Solo el administrador puede otorgar permisos de admin.
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection