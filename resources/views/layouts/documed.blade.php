<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'DocuMed') }} - @yield('title', 'Sistema')</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
        
    @livewireStyles
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <!-- Logo -->
            <a class="navbar-brand" href="{{ auth()->check() ? route('companies.index') : route('login') }}">
                <i class="fas fa-file-medical"></i> DocuMed
            </a>
            
            <!-- Toggle button para móvil -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                @auth
                    <!-- Menú principal para usuarios autenticados -->
                    <div class="navbar-nav me-auto">
                        <a class="nav-link {{ request()->routeIs('companies.*') ? 'active' : '' }}" 
                           href="{{ route('companies.index') }}">
                            <i class="fas fa-building"></i> Empresas
                        </a>
                        
                        {{-- Panel de Aprobación (Solo Admin) --}}
                        @if(auth()->user()->isAdmin())
                            <a class="nav-link {{ request()->routeIs('admin.document-approval.*') ? 'active' : '' }}" 
                               href="{{ route('admin.document-approval.index') }}">
                                <i class="fas fa-check-circle text-warning"></i>
                                Panel de Aprobación
                                {{-- Contador de documentos pendientes --}}
                                @php
                                    $pendingCompanyDocs = \App\Models\Company::with(['documents' => function($q) { 
                                        $q->where('valid', false)->orWhereNull('valid'); 
                                    }])->get()->sum(function($company) { 
                                        return $company->documents->count(); 
                                    });
                                    
                                    $pendingStaffDocs = \App\Models\Staff::with(['documents' => function($q) { 
                                        $q->where('valid', false)->orWhereNull('valid'); 
                                    }])->get()->sum(function($staff) { 
                                        return $staff->documents->count(); 
                                    });
                                    
                                    $totalPending = $pendingCompanyDocs + $pendingStaffDocs;
                                @endphp
                                @if($totalPending > 0)
                                    <span class="badge bg-warning text-dark ms-1">{{ $totalPending }}</span>
                                @endif
                            </a>
                        @endif

                        {{-- Dashboard de Laravel Breeze --}}
                        <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" 
                           href="{{ route('dashboard') }}">
                            <i class="fas fa-tachometer-alt"></i> Dashboard
                        </a>
                    </div>
                    
                    <!-- Menú de usuario -->
                    <div class="navbar-nav ms-auto">
                        {{-- Información del usuario --}}
                        <span class="nav-link text-light">
                            <i class="fas fa-user"></i> {{ auth()->user()->name }}
                            <span class="badge {{ auth()->user()->isAdmin() ? 'bg-danger' : 'bg-info' }}">
                                {{ auth()->user()->isAdmin() ? 'Admin' : 'Usuario' }}
                            </span>
                        </span>
                        
                        {{-- Dropdown de usuario --}}
                        <div class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                <i class="fas fa-cog"></i>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li>
                                    <a class="dropdown-item" href="{{ route('profile.edit') }}">
                                        <i class="fas fa-user-edit"></i> Mi Perfil
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('dashboard') }}">
                                        <i class="fas fa-tachometer-alt"></i> Dashboard
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="dropdown-item">
                                            <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    </div>
                @else
                    <!-- Menú para usuarios no autenticados -->
                    <div class="navbar-nav ms-auto">
                        <a class="nav-link {{ request()->routeIs('login') ? 'active' : '' }}" 
                           href="{{ route('login') }}">
                            <i class="fas fa-sign-in-alt"></i> Iniciar Sesión
                        </a>
                        <a class="nav-link {{ request()->routeIs('register') ? 'active' : '' }}" 
                           href="{{ route('register') }}">
                            <i class="fas fa-user-plus"></i> Registrarse
                        </a>
                    </div>
                @endauth
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <!-- Breadcrumb -->
        @auth
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="{{ route('companies.index') }}">
                            <i class="fas fa-home"></i> Inicio
                        </a>
                    </li>
                    @if(request()->routeIs('admin.document-approval.*'))
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.document-approval.index') }}">Panel de Aprobación</a>
                        </li>
                        @if(request()->routeIs('admin.document-approval.company'))
                            <li class="breadcrumb-item active">Documentos de Empresa</li>
                        @elseif(request()->routeIs('admin.document-approval.staff'))
                            <li class="breadcrumb-item active">Documentos de Personal</li>
                        @endif
                    @elseif(request()->routeIs('companies.*'))
                        @if(request()->routeIs('companies.show'))
                            <li class="breadcrumb-item active">Ver Empresa</li>
                        @elseif(request()->routeIs('companies.wizard'))
                            <li class="breadcrumb-item active">Nueva Empresa</li>
                        @elseif(request()->routeIs('companies.edit'))
                            <li class="breadcrumb-item active">Editar Empresa</li>
                        @elseif(request()->routeIs('staff.*'))
                            <li class="breadcrumb-item">
                                <a href="{{ route('companies.show', request()->route('company')) }}">Empresa</a>
                            </li>
                            <li class="breadcrumb-item active">Personal</li>
                        @endif
                    @endif
                </ol>
            </nav>
        @endauth

        <!-- Mensajes de éxito/error -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-triangle"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('status'))
            <div class="alert alert-info alert-dismissible fade show" role="alert">
                <i class="fas fa-info-circle"></i> {{ session('status') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @yield('content-documed')
    </div>

    <!-- Footer -->
    <footer class="bg-light text-center text-muted py-4 mt-5">
        <div class="container">
            <p>&copy; {{ date('Y') }} DocuMed - Sistema de Gestión Documental</p>
            @auth
                <small>
                    Conectado como: <strong>{{ auth()->user()->name }}</strong> 
                    ({{ auth()->user()->isAdmin() ? 'Administrador' : 'Usuario' }})
                </small>
            @endauth
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
        
    @livewireScripts
</body>
</html>