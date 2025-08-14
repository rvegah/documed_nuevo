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
                                'user_id' => auth()->id(),
                                'file_index' => 1, // Los documentos en creación siempre empiezan con index 1
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
     * 🚀 ACTUALIZADO: Con validación de documentos obligatorios
     */
    public function update(Request $request, Company $company)
    {
        try {
            //  NUEVO: Verificar autorización
            $this->authorizeCompanyAccess($company);
            
            // Validar campos básicos
            $validatedData = $request->validate([
                'company_name' => 'required|string|max:255',
                'legal_representative_dni' => 'required|string|max:25',
                'rn_owner' => 'required|string|max:250',
                'has_center_staff' => 'boolean',
                'documents.*' => 'nullable',
                'documents.*.*' => 'nullable|file|mimes:jpeg,png,pdf|max:4096',
            ]);

            // 🚀 NUEVO: Validar documentos obligatorios
            $this->validateRequiredDocuments($request, $company);

            // Actualizar datos básicos de la empresa
            $company->update($validatedData);

            // Procesar documentos si se subieron
            if ($request->hasFile('documents')) {
                $this->processUploadedDocuments($request, $company);
            }

            return redirect()->route('companies.show', $company)->with('success', 'Empresa actualizada exitosamente.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Re-lanzar excepciones de validación
            throw $e;
        } catch (\Exception $e) {
            Log::error("Error al actualizar la compañía: " . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'No se pudo actualizar la empresa.');
        }
    }

    /**
     * 🚀 NUEVO MÉTODO: Validar documentos obligatorios en el backend
     */
    private function validateRequiredDocuments(Request $request, Company $company)
    {
        // Obtener documentos obligatorios básicos
        $requiredDocuments = Document::where('category', 'basic')
            ->where('required', true)
            ->where('active', true)
            ->get();

        if ($requiredDocuments->isEmpty()) {
            Log::info('No hay documentos obligatorios configurados');
            return; // No hay documentos obligatorios
        }

        Log::info('Validando documentos obligatorios:', $requiredDocuments->pluck('id', 'name')->toArray());

        // Obtener documentos ya subidos por la empresa
        $uploadedDocumentIds = $company->documents()->pluck('document_id')->toArray();
        Log::info('Documentos ya subidos:', $uploadedDocumentIds);

        // Obtener documentos que se están subiendo ahora
        $newDocumentIds = [];
        if ($request->hasFile('documents')) {
            foreach ($request->file('documents') as $documentId => $files) {
                if ($files) {
                    // Para documentos múltiples, verificar que al menos un archivo sea válido
                    if (is_array($files)) {
                        foreach ($files as $file) {
                            if ($file && $file->isValid()) {
                                $newDocumentIds[] = (int)$documentId;
                                break; // Solo necesitamos uno válido
                            }
                        }
                    } else {
                        // Documento único
                        if ($files->isValid()) {
                            $newDocumentIds[] = (int)$documentId;
                        }
                    }
                }
            }
        }
        Log::info('Documentos nuevos siendo subidos:', $newDocumentIds);

        // Combinar documentos ya subidos + nuevos
        $allAvailableDocuments = array_unique(array_merge($uploadedDocumentIds, $newDocumentIds));
        Log::info('Todos los documentos disponibles:', $allAvailableDocuments);

        // Verificar qué documentos obligatorios faltan
        $missingDocuments = [];
        foreach ($requiredDocuments as $requiredDoc) {
            if (!in_array($requiredDoc->id, $allAvailableDocuments)) {
                $missingDocuments[] = $requiredDoc->name;
            }
        }

        // Si faltan documentos obligatorios, lanzar error de validación
        if (!empty($missingDocuments)) {
            $errorMessage = 'Debe subir los siguientes documentos obligatorios: ' . implode(', ', $missingDocuments);
            Log::warning('Documentos obligatorios faltantes:', $missingDocuments);
            
            throw \Illuminate\Validation\ValidationException::withMessages([
                'documents' => [$errorMessage]
            ]);
        }

        Log::info('Validación de documentos obligatorios exitosa');
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
     * 🚀 ACTUALIZADO: Soporte para múltiples archivos en documentos específicos
     */
    private function processUploadedDocuments(Request $request, Company $company)
    {
        $documentsToAttach = [];
        
        foreach ($request->file('documents') as $documentId => $files) {
            if ($files) {
                $document = Document::find($documentId);
                if ($document) {
                    // Verificar si es un documento de múltiples archivos
                    $isMultiple = $company->isMultipleFileDocument($documentId);
                    
                    if ($isMultiple && is_array($files)) {
                        \Log::info("=== PROCESANDO COMO MÚLTIPLE ===");
                        \Log::info("Documento {$documentId} - es múltiple: SÍ");
                        \Log::info("Files es array: SÍ");
                        \Log::info("Cantidad de archivos: " . count($files));
                        
                        // ✅ AGREGAR: Obtener el siguiente índice disponible
                        $existingFiles = $company->getDocumentFiles($documentId);
                        $nextIndex = $existingFiles->count() > 0 ? $existingFiles->max('file_index') + 1 : 1;
                        \Log::info("Documento {$documentId}: {$existingFiles->count()} archivos existentes, siguiente índice: {$nextIndex}");
                        
                        // DOCUMENTO MÚLTIPLE: Procesar array de archivos
                        $this->processMultipleFiles($files, $documentId, $company, $documentsToAttach, $nextIndex);
                        
                        \Log::info("Después de processMultipleFiles, total documentos: " . count($documentsToAttach));
                    } else {
                        \Log::info("=== PROCESANDO COMO ÚNICO ===");
                        \Log::info("Documento {$documentId} - es múltiple: " . ($isMultiple ? 'SÍ' : 'NO'));
                        \Log::info("Files es array: " . (is_array($files) ? 'SÍ' : 'NO'));
                        
                        // DOCUMENTO ÚNICO: Lógica original
                        $file = is_array($files) ? $files[0] : $files;
                        $this->processSingleFile($file, $documentId, $company, $documentsToAttach);
                    }
                }
            }
        }

        // ✅ CAMBIO IMPORTANTE: Usar INSERT directo para múltiples archivos
        \Log::info('Documentos a insertar:', $documentsToAttach);

        if (!empty($documentsToAttach)) {
            \Log::info('Insertando ' . count($documentsToAttach) . ' documentos');
            \DB::table('company_document')->insert($documentsToAttach);
            \Log::info('Documentos insertados exitosamente');
        } else {
            \Log::info('No hay documentos para insertar');
        }
    }

    /**
     * Procesar múltiples archivos para un documento
     */
    private function processMultipleFiles($files, $documentId, $company, &$documentsToAttach, $startIndex = 1)
    {
        \Log::info("=== PROCESANDO MÚLTIPLES ARCHIVOS ===");
        \Log::info("Documento ID: {$documentId}");
        \Log::info("Índice inicial: {$startIndex}");
        \Log::info("Cantidad de archivos recibidos: " . count($files));
        
        $fileIndex = $startIndex;
        
        foreach ($files as $index => $file) {
            \Log::info("Procesando archivo #{$index}");
            
            if ($file && $file->isValid()) {
                \Log::info("Archivo válido: " . $file->getClientOriginalName());
                
                // Verificar límite de 6 archivos total
                if ($fileIndex > 6) {
                    \Log::info("Límite de 6 archivos alcanzado, saltando archivo");
                    break;
                }
                
                // Generar nombre seguro
                $safeName = 'doc_' . $documentId . '_' . $fileIndex . '_' . time() . '.' . $file->getClientOriginalExtension();
                \Log::info("Nombre de archivo generado: {$safeName}");
                
                // Guardar archivo
                $path = $file->storeAs(
                    'company_documents/' . $company->id,
                    $safeName,
                    'public'
                );
                \Log::info("Archivo guardado en: {$path}");

                // Preparar datos para insert
                $fileData = [
                    'company_id' => $company->id,
                    'document_id' => $documentId,
                    'file_index' => $fileIndex,
                    'path' => $path,
                    'original_file_name' => $file->getClientOriginalName(),
                    'user_id' => auth()->id(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
                
                $documentsToAttach[] = $fileData;
                \Log::info("Archivo agregado al array, fileIndex: {$fileIndex}");
                \Log::info("Total documentos en array ahora: " . count($documentsToAttach));
                
                $fileIndex++;
            } else {
                \Log::warning("Archivo inválido en índice {$index}");
            }
        }
        
        \Log::info("=== FIN PROCESAMIENTO MÚLTIPLES ARCHIVOS ===");
        \Log::info("Total archivos procesados: " . (count($documentsToAttach)));
    }

    /**
     * Procesar un solo archivo (lógica original)
     */
    private function processSingleFile($file, $documentId, $company, &$documentsToAttach)
    {
        if ($file && $file->isValid()) {
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
                    'user_id' => auth()->id(),
                    'updated_at' => now(),
                ]);
            } else {
                // Crear nuevo registro
                $documentsToAttach[] = [
                    'company_id' => $company->id,
                    'document_id' => $documentId,
                    'file_index' => 1, // Los documentos únicos siempre tienen file_index = 1
                    'path' => $path,
                    'original_file_name' => $file->getClientOriginalName(),
                    'user_id' => auth()->id(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }
    }

    /**
     * Descargar un documento individual de la empresa
     */
    public function downloadDocument(Company $company, Document $document)
    {
        try {
            // Verificar autorización
            $this->authorizeCompanyAccess($company);
            
            // Verificar que el documento esté asociado a la empresa
            $companyDocument = $company->documents()->where('document_id', $document->id)->first();
            
            if (!$companyDocument || !$companyDocument->pivot->path) {
                return redirect()->back()->with('error', 'El documento no existe o no está disponible.');
            }
            
            $filePath = storage_path('app/public/' . $companyDocument->pivot->path);
            
            if (!file_exists($filePath)) {
                return redirect()->back()->with('error', 'El archivo no se encuentra en el servidor.');
            }
            
            // Descargar con el nombre original
            return response()->download($filePath, $companyDocument->pivot->original_file_name);
            
        } catch (\Exception $e) {
            Log::error("Error al descargar documento: " . $e->getMessage());
            return redirect()->back()->with('error', 'No se pudo descargar el documento.');
        }
    }

    /**
     * Descargar todos los documentos de la empresa en un ZIP
     */
    public function downloadAllDocuments(Company $company)
    {
        try {
            // Verificar autorización
            $this->authorizeCompanyAccess($company);
            
            // Verificar que la empresa tenga documentos
            if ($company->documents()->count() == 0) {
                return redirect()->back()->with('error', 'Esta empresa no tiene documentos para descargar.');
            }
            
            // Crear un nombre único para el archivo ZIP
            $zipName = 'Documentos_' . Str::slug($company->company_name) . '_' . date('Y-m-d') . '.zip';
            $zipPath = storage_path('app/temp/' . $zipName);
            
            // Crear directorio temporal si no existe
            if (!file_exists(storage_path('app/temp'))) {
                mkdir(storage_path('app/temp'), 0755, true);
            }
            
            // Crear el archivo ZIP
            $zip = new \ZipArchive();
            
            if ($zip->open($zipPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== TRUE) {
                return redirect()->back()->with('error', 'No se pudo crear el archivo ZIP.');
            }
            
            $addedFiles = 0;
            
            // Agregar documentos de la empresa al ZIP
            foreach ($company->documents as $document) {
                if ($document->pivot->path) {
                    $filePath = storage_path('app/public/' . $document->pivot->path);
                    
                    if (file_exists($filePath)) {
                        // Usar el nombre original del archivo
                        $fileName = $document->pivot->original_file_name ?: $document->name;
                        
                        // Agregar prefijo para organizar
                        $zipFileName = '1_Empresa/' . $fileName;
                        
                        $zip->addFile($filePath, $zipFileName);
                        $addedFiles++;
                    }
                }
            }
            
            // Agregar documentos del personal si existe
            if ($company->staff()->count() > 0) {
                foreach ($company->staff as $staff) {
                    if ($staff->documents()->count() > 0) {
                        foreach ($staff->documents as $document) {
                            if ($document->pivot->path) {
                                $filePath = storage_path('app/public/' . $document->pivot->path);
                                
                                if (file_exists($filePath)) {
                                    $fileName = $document->pivot->original_file_name ?: $document->name;
                                    
                                    // Organizar por tipo de personal
                                    $staffType = $staff->type == 'professional' ? 'Profesionales' : 'Personal_Clinico';
                                    $zipFileName = "2_{$staffType}/{$staff->name}/" . $fileName;
                                    
                                    $zip->addFile($filePath, $zipFileName);
                                    $addedFiles++;
                                }
                            }
                        }
                    }
                }
            }
            
            $zip->close();
            
            if ($addedFiles == 0) {
                // Eliminar ZIP vacío
                if (file_exists($zipPath)) {
                    unlink($zipPath);
                }
                return redirect()->back()->with('error', 'No hay archivos válidos para descargar.');
            }
            
            // Programar eliminación del archivo temporal después de descarga
            register_shutdown_function(function() use ($zipPath) {
                if (file_exists($zipPath)) {
                    unlink($zipPath);
                }
            });
            
            // Descargar el ZIP
            return response()->download($zipPath, $zipName)->deleteFileAfterSend(true);
            
        } catch (\Exception $e) {
            Log::error("Error al crear ZIP de documentos: " . $e->getMessage());
            return redirect()->back()->with('error', 'No se pudo crear el archivo ZIP: ' . $e->getMessage());
        }
    }
}