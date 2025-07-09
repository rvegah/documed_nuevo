@extends('layouts.documed')

@section('title', 'Iniciar Sesión')

@section('content-documed')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0">
                    <i class="fas fa-sign-in-alt"></i> Iniciar Sesión en DocuMed
                </h4>
            </div>
            <div class="card-body">
                <!-- Session Status -->
                @if (session('status'))
                    <div class="alert alert-success" role="alert">
                        {{ session('status') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <!-- Email Address -->
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input id="email" 
                               class="form-control @error('email') is-invalid @enderror" 
                               type="email" 
                               name="email" 
                               value="{{ old('email') }}" 
                               required 
                               autofocus 
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
                               autocomplete="current-password">
                        @error('password')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <!-- Remember Me -->
                    <div class="mb-3 form-check">
                        <input id="remember_me" 
                               type="checkbox" 
                               class="form-check-input" 
                               name="remember">
                        <label class="form-check-label" for="remember_me">
                            Recordarme
                        </label>
                    </div>

                    <div class="d-flex justify-content-between align-items-center">
                        @if (Route::has('password.request'))
                            <a class="text-decoration-none" href="{{ route('password.request') }}">
                                ¿Olvidaste tu contraseña?
                            </a>
                        @endif

                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-sign-in-alt"></i> Iniciar Sesión
                        </button>
                    </div>
                </form>

                <hr>

                <div class="text-center">
                    <p class="mb-0">¿No tienes cuenta? 
                        <a href="{{ route('register') }}" class="text-decoration-none">
                            Regístrate aquí
                        </a>
                    </p>
                </div>

                <div class="mt-4">
                    <div class="alert alert-info">
                        <h6><i class="fas fa-info-circle"></i> Cuentas de prueba:</h6>
                        <small>
                            <strong>Admin:</strong> admin@documed.com / 123456<br>
                            <strong>Usuario:</strong> user@documed.com / 123456
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection