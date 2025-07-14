<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Company;
use App\Models\Document;
use App\Http\Requests\StoreCompanyRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class CompanyController extends Controller
{
    /**
     * Display a listing of the resource.
     *  ACTUALIZADO: Ahora filtra por usuario (solo admin ve todo)
     */
    public function index(Request $request)
    {
        try {
            $query = Company::query();
            
            //  NUEVO: Filtrar por usuario si no es admin
            if (!auth()->user()->isAdmin()) {
                $query->where('user_id', auth()->id());
            }
            
            // Aplicar filtro si existe
            if ($request->get('filter')) {
                $filter = $request->get('filter');
                switch($filter) {
                    case 'tramitacion':
                        $query->where('status', 'Tramitación');
                        break;
                    case 'presentada':
                        $query->where('status', 'Presentada');
                        break;
                    case 'aprobada':
                        $query->where('status', 'Aprobada');
                        break;
                    case 'resuelta':
                        $query->where('status', 'Resuelta');
                        break;
                    case 'rechazada':
                        $query->where('status', 'Rechazada');
                        break;
                }
            }
            
            // Ordenar por fecha de creación (más recientes primero)
            $companies = $query->orderBy('created_at', 'desc')->get();
            
            return view('companies.index', compact('companies'));
        } catch (\Exception $e) {
            Log::error("Error al obtener el listado de compañías: " . $e->getMessage());
            return redirect()->back()->with('error', 'No se pudo cargar el listado de compañías. Intente de nuevo.');
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $company = new Company();
        $documents = Document::active()->orderBy('order', 'asc')->get();
        return view('companies.create', compact('company', 'documents'));
    }

    /**
     * Store a newly created resource in storage.
     *  ACTUALIZADO: Usa auth()->id() en lugar de hardcodear user_id = 1
     */
    public function store(StoreCompanyRequest $request)
    {
        Log::info('=== DEBUG CREACIÓN EMPRESA ===');
        Log::info('Usuario autenticado ID: ' . (auth()->id() ?? 'NULL'));
        Log::info('Usuario email: ' . (auth()->user()->email ?? 'NULL'));
        Log::info('Usuario name: ' . (auth()->user()->name ?? 'NULL'));
        try {
            $validateData = $request->validated();
            $validateData['user_id'] = auth()->id(); //  CAMBIO: Usar usuario autenticado
            $company = Company::create($validateData);

            $documentsToAttach = [];
            $dirDocuments = $company->urlDocuments;
            if ($request->hasFile('documents')) {
                foreach ($request->file('documents') as $documentId => $uploadedFile) {
                    if ($uploadedFile) {
                        $document = Document::find($documentId);

                        if ($document) {
                            $documentName = $document->name;
                            $originalFileName = $uploadedFile->getClientOriginalName();

                            Log::info("Documento ID: {$documentId}, Nombre DB: {$documentName}, Archivo Subido: {$originalFileName}");

                            $path = $uploadedFile->storeAs(
                                $dirDocuments . DIRECTORY_SEPARATOR . $documentId,
                                $documentName . '_' . time() . '.' . $uploadedFile->getClientOriginalExtension(),
                                'public'
                            );

                            $documentsToAttach[$documentId] = [
                                'path' => $path,
                                'original_file_name' => $uploadedFile->getClientOriginalName(),
                                'user_id' => auth()->id(), // CAMBIO: Usar usuario autenticado
                                'created_at' => now(),
                                'updated_at' => now(),
                            ];
                        } else {
                            Log::warning("Document ID {$documentId} no encontrado en la base de datos al subir archivo.");
                        }
                    }
                }
                try {
                    $company->documents()->syncWithoutDetaching($documentsToAttach);
                    return redirect()->route('companies.index')->with('success', 'Archivos de documentos subidos y vinculados exitosamente.');
                } catch (\Exception $e) {
                    Log::error("Error al vincular documentos a la compañía: " . $e->getMessage());
                    return redirect()->back()->withInput()->with('error', 'No se pudieron vincular los documentos a la compañía.');
                }
            }

            return redirect()->route('companies.index')->with('success', 'Empresa creada exitosamente.');
        } catch (\Exception $e) {
            Log::error("Error al crear la compañía: " . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'No se pudo crear la empresa. Verifique los datos e intente de nuevo.');
        }
    }

    /**
     * Display the specified resource.
     *  ACTUALIZADO: Verifica que el usuario tenga acceso a esta empresa
     */
    public function show(Company $company)
    {
        try {
            //  NUEVO: Verificar autorización
            $this->authorizeCompanyAccess($company);
            
            // Cargar documentos relacionados
            $company->load('documents');
            
            return view('companies.show', compact('company'));
        } catch (\Exception $e) {
            Log::error("Error al mostrar la compañía: " . $e->getMessage());
            return redirect()->route('companies.index')->with('error', 'No se pudo cargar la información de la empresa.');
        }
    }

    /**
     * Show the form for editing the specified resource.
     *  ACTUALIZADO: Verifica que el usuario tenga acceso a esta empresa
     */
    public function edit(Company $company)
    {
        try {
            // NUEVO: Verificar autorización
            $this->authorizeCompanyAccess($company);
            
            $documents = Document::active()->orderBy('order', 'asc')->get();
            $company->load('documents');
            
            return view('companies.edit', compact('company', 'documents'));
        } catch (\Exception $e) {
            Log::error("Error al cargar el formulario de edición: " . $e->getMessage());
            return redirect()->route('companies.index')->with('error', 'No se pudo cargar el formulario de edición.');
        }
    }

    /**
     * Update the specified resource in storage.
     *  ACTUALIZADO: Verifica autorización y usa auth()->id()
     */
    public function update(Request $request, Company $company)
    {
        try {
            //  NUEVO: Verificar autorización
            $this->authorizeCompanyAccess($company);
            
            // Solo validar campos básicos por ahora
            $validatedData = $request->validate([
                'company_name' => 'required|string|max:255',
                'legal_representative_dni' => 'required|string|max:25',
                'rn_owner' => 'required|string|max:250',
                'has_center_staff' => 'boolean',
                'documents' => 'nullable',
                'documents.*' => 'nullable|file|mimes:jpeg,png,pdf|max:4096',
            ]);

            // Actualizar datos básicos de la empresa
            $company->update($validatedData);

            // Procesar documentos si se subieron
            if ($request->hasFile('documents')) {
                $this->processUploadedDocuments($request, $company);
            }

            return redirect()->route('companies.show', $company)->with('success', 'Empresa actualizada exitosamente.');
        } catch (\Exception $e) {
            Log::error("Error al actualizar la compañía: " . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'No se pudo actualizar la empresa.');
        }
    }

    /**
     * Remove the specified resource from storage.
     *  ACTUALIZADO: Verifica autorización antes de eliminar
     */
    public function destroy(Company $company)
    {
        try {
            //  NUEVO: Verificar autorización
            $this->authorizeCompanyAccess($company);
            
            // Eliminar archivos asociados si existen
            if ($company->documents()->count() > 0) {
                foreach ($company->documents as $document) {
                    if ($document->pivot->path) {
                        \Storage::disk('public')->delete($document->pivot->path);
                    }
                }
            }
            
            // Eliminar relaciones en la tabla pivot
            $company->documents()->detach();
            
            // Eliminar la empresa
            $company->delete();

            return redirect()->route('companies.index')->with('success', 'Empresa eliminada exitosamente.');
        } catch (\Exception $e) {
            Log::error("Error al eliminar la compañía: " . $e->getMessage());
            return redirect()->route('companies.index')->with('error', 'No se pudo eliminar la empresa.');
        }
    }

    /**
     *  NUEVO MÉTODO: Verificar que el usuario tenga acceso a la empresa
     */
    private function authorizeCompanyAccess(Company $company)
    {
        // Admin puede acceder a todo
        if (auth()->user()->isAdmin()) {
            return true;
        }
        
        // Usuario normal solo puede acceder a sus propias empresas
        if ($company->user_id !== auth()->id()) {
            abort(403, 'No tienes permiso para acceder a esta empresa.');
        }
        
        return true;
    }

    /**
     * Procesar documentos subidos en edición
     * 🚀 ACTUALIZADO: Usa auth()->id() en lugar de hardcodear user_id = 1
     */
    private function processUploadedDocuments(Request $request, Company $company)
    {
        $documentsToAttach = [];
        
        foreach ($request->file('documents') as $documentId => $file) {
            if ($file) {
                $document = Document::find($documentId);
                if ($document) {
                    // Generar nombre seguro para el archivo
                    $safeName = 'doc_' . $documentId . '_' . time() . '.' . $file->getClientOriginalExtension();
                    
                    // Guardar en la carpeta de la empresa
                    $path = $file->storeAs(
                        'company_documents/' . $company->id,
                        $safeName,
                        'public'
                    );

                    // Si ya existe este documento, lo reemplazamos
                    if ($company->documents->contains($documentId)) {
                        // Eliminar el archivo anterior si existe
                        $existingDocument = $company->documents->find($documentId);
                        if ($existingDocument && $existingDocument->pivot->path) {
                            \Storage::disk('public')->delete($existingDocument->pivot->path);
                        }
                        
                        // Actualizar el registro existente
                        $company->documents()->updateExistingPivot($documentId, [
                            'path' => $path,
                            'original_file_name' => $file->getClientOriginalName(),
                            'user_id' => auth()->id(), // CAMBIO: Usar usuario autenticado
                            'updated_at' => now(),
                        ]);
                    } else {
                        // Crear nuevo registro
                        $documentsToAttach[$documentId] = [
                            'path' => $path,
                            'original_file_name' => $file->getClientOriginalName(),
                            'user_id' => auth()->id(), // CAMBIO: Usar usuario autenticado
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                    }
                }
            }
        }

        // Adjuntar documentos nuevos si los hay
        if (!empty($documentsToAttach)) {
            $company->documents()->attach($documentsToAttach);
        }
    }
}