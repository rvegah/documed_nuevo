<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\DocumentApprovalController;
use Illuminate\Support\Facades\Route;

// RUTA RAÍZ - Redirige al login si no está autenticado, al dashboard si está autenticado
Route::get('/', function () {
    return auth()->check() ? redirect()->route('dashboard') : redirect()->route('login');
});

// RUTAS DE DASHBOARD
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

// TUS RUTAS ORIGINALES RESTAURADAS
Route::get('/', function () {
    return auth()->check() ? redirect()->route('dashboard') : redirect()->route('login');
});

Route::get('/companies/wizard', function () {
    return view('companies.wizard');
})->name('companies.wizard');

Route::get('/test-livewire', function () {
    return view('test-livewire');
});

// RUTAS DE EMPRESAS
Route::resource('companies', CompanyController::class);

// RUTAS DE STAFF
Route::prefix('companies/{company}')->group(function () {
    Route::resource('staff', StaffController::class);
});

// RUTAS DEL PANEL DE APROBACIÓN (PROTEGIDAS CON AUTH)
Route::prefix('admin')->middleware(['auth', 'admin'])->group(function () {
    
    // Panel principal de aprobación
    Route::get('/document-approval', [DocumentApprovalController::class, 'index'])
        ->name('admin.document-approval.index');
    
    // Ver documentos de empresa específica
    Route::get('/document-approval/company/{company}', [DocumentApprovalController::class, 'showCompanyDocuments'])
        ->name('admin.document-approval.company');
    
    // Ver documentos de staff específico
    Route::get('/document-approval/staff/{staff}', [DocumentApprovalController::class, 'showStaffDocuments'])
        ->name('admin.document-approval.staff');
    
    // Aprobar/Rechazar documentos de empresa
    Route::post('/document-approval/company/{company}/document/{document}/approve', [DocumentApprovalController::class, 'approveCompanyDocument'])
        ->name('admin.document-approval.company.approve');
    
    Route::post('/document-approval/company/{company}/document/{document}/reject', [DocumentApprovalController::class, 'rejectCompanyDocument'])
        ->name('admin.document-approval.company.reject');
    
    // Aprobar/Rechazar documentos de staff
    Route::post('/document-approval/staff/{staff}/document/{document}/approve', [DocumentApprovalController::class, 'approveStaffDocument'])
        ->name('admin.document-approval.staff.approve');
    
    Route::post('/document-approval/staff/{staff}/document/{document}/reject', [DocumentApprovalController::class, 'rejectStaffDocument'])
        ->name('admin.document-approval.staff.reject');
});

// RUTAS DE DESCARGA DE DOCUMENTOS
Route::middleware('auth')->group(function () {
    // Descargar documento individual
    Route::get('/companies/{company}/documents/{document}/download', [CompanyController::class, 'downloadDocument'])
        ->name('companies.documents.download');
    
    // Descargar todos los documentos en ZIP
    Route::get('/companies/{company}/documents/download-all', [CompanyController::class, 'downloadAllDocuments'])
        ->name('companies.documents.download-all');
});