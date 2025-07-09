<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Staff;
use App\Models\Company;
use App\Models\Document;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class StaffController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Company $company)
    {
        try {
            $company->load(['staff.documents']);
            
            return view('staff.index', compact('company'));
        } catch (\Exception $e) {
            Log::error("Error al cargar el personal: " . $e->getMessage());
            return redirect()->route('companies.show', $company)->with('error', 'No se pudo cargar el personal.');
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Company $company, Request $request)
    {
        try {
            $type = $request->get('type', Staff::TYPE_PROFESSIONAL);
            
            // Validar que el tipo sea válido
            if (!in_array($type, [Staff::TYPE_PROFESSIONAL, Staff::TYPE_CLINICAL])) {
                $type = Staff::TYPE_PROFESSIONAL;
            }
            
            // Obtener documentos según el tipo
            $documents = Document::where('category', $type)->active()->orderBy('order')->get();
            
            return view('staff.create', compact('company', 'type', 'documents'));
        } catch (\Exception $e) {
            Log::error("Error al cargar formulario de creación de personal: " . $e->getMessage());
            return redirect()->route('staff.index', $company)->with('error', 'No se pudo cargar el formulario.');
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Company $company)
    {
        try {
            $validatedData = $request->validate([
                'type' => 'required|in:professional,clinical',
                // COMENTAR LOS CAMPOS BÁSICOS TEMPORALMENTE
                // 'name' => 'required|string|max:255',
                // 'dni' => 'required|string|max:25',
                // 'email' => 'nullable|email|max:255',
                // 'phone' => 'nullable|string|max:50',
                'documents' => 'nullable',
                'documents.*' => 'nullable|file|mimes:jpeg,png,pdf|max:4096',
            ]);

            // Crear el personal con datos temporales
            $staff = $company->staff()->create([
                'type' => $validatedData['type'],
                'name' => 'Personal ' . ucfirst($validatedData['type']) . ' - Temporal', // Nombre temporal
                'dni' => 'TEMP-' . time(), // DNI temporal único
                'email' => null,
                'phone' => null,
                'user_id' => 1,
                'status' => Staff::STATUS_TRAMITACION,
            ]);

            // Procesar documentos si se subieron
            if ($request->hasFile('documents')) {
                $this->processStaffDocuments($request, $staff);
            }

            return redirect()->route('staff.index', $company)->with('success', 'Personal registrado exitosamente con documentos.');
        } catch (\Exception $e) {
            Log::error("Error al crear personal: " . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'No se pudo registrar el personal: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Company $company, Staff $staff)
    {
        try {
            $staff->load('documents');
            
            return view('staff.show', compact('company', 'staff'));
        } catch (\Exception $e) {
            Log::error("Error al mostrar personal: " . $e->getMessage());
            return redirect()->route('staff.index', $company)->with('error', 'No se pudo cargar la información del personal.');
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Company $company, Staff $staff)
    {
        try {
            $documents = Document::where('category', $staff->type)->active()->orderBy('order')->get();
            $staff->load('documents');
            
            return view('staff.edit', compact('company', 'staff', 'documents'));
        } catch (\Exception $e) {
            Log::error("Error al cargar formulario de edición de personal: " . $e->getMessage());
            return redirect()->route('staff.index', $company)->with('error', 'No se pudo cargar el formulario de edición.');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Company $company, Staff $staff)
    {
        try {
            // 🔧 CAMBIO: Solo validar documentos por ahora (campos básicos comentados)
            $validatedData = $request->validate([
                // CAMPOS BÁSICOS COMENTADOS TEMPORALMENTE
                // 'name' => 'required|string|max:255',
                // 'dni' => 'required|string|max:25',
                // 'email' => 'nullable|email|max:255',
                // 'phone' => 'nullable|string|max:50',
                'documents' => 'nullable',
                'documents.*' => 'nullable|file|mimes:jpeg,png,pdf|max:4096',
            ]);

            // 🔧 CAMBIO: Solo actualizar datos básicos si están presentes en la validación
            // $staff->update($validatedData);  // COMENTADO porque no hay campos básicos

            // Procesar documentos si se subieron
            if ($request->hasFile('documents')) {
                $this->processStaffDocuments($request, $staff);
            }

            return redirect()->route('staff.show', [$company, $staff])->with('success', 'Documentos del personal actualizados exitosamente.');
        } catch (\Exception $e) {
            Log::error("Error al actualizar personal: " . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'No se pudieron actualizar los documentos del personal: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Company $company, Staff $staff)
    {
        try {
            // Eliminar archivos asociados
            foreach ($staff->documents as $document) {
                if ($document->pivot->path) {
                    Storage::disk('public')->delete($document->pivot->path);
                }
            }
            
            // Eliminar relaciones
            $staff->documents()->detach();
            
            // Eliminar personal
            $staff->delete();

            return redirect()->route('staff.index', $company)->with('success', 'Personal eliminado exitosamente.');
        } catch (\Exception $e) {
            Log::error("Error al eliminar personal: " . $e->getMessage());
            return redirect()->route('staff.index', $company)->with('error', 'No se pudo eliminar el personal.');
        }
    }

    /**
     * Procesar documentos del personal
     */
    private function processStaffDocuments(Request $request, Staff $staff)
    {
        $documentsToAttach = [];
        
        foreach ($request->file('documents') as $documentId => $file) {
            if ($file) {
                $document = Document::find($documentId);
                if ($document) {
                    // Generar nombre seguro
                    $safeName = 'staff_' . $staff->id . '_doc_' . $documentId . '_' . time() . '.' . $file->getClientOriginalExtension();
                    
                    // Guardar archivo
                    $path = $file->storeAs(
                        'staff_documents/' . $staff->company_id . '/' . $staff->id,
                        $safeName,
                        'public'
                    );

                    // Si ya existe el documento, actualizarlo
                    if ($staff->documents->contains($documentId)) {
                        $existingDocument = $staff->documents->find($documentId);
                        if ($existingDocument && $existingDocument->pivot->path) {
                            Storage::disk('public')->delete($existingDocument->pivot->path);
                        }
                        
                        $staff->documents()->updateExistingPivot($documentId, [
                            'path' => $path,
                            'original_file_name' => $file->getClientOriginalName(),
                            'user_id' => 1,
                            'updated_at' => now(),
                        ]);
                    } else {
                        // Crear nuevo documento
                        $documentsToAttach[$documentId] = [
                            'path' => $path,
                            'original_file_name' => $file->getClientOriginalName(),
                            'user_id' => 1,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                    }
                }
            }
        }

        // Adjuntar documentos nuevos
        if (!empty($documentsToAttach)) {
            $staff->documents()->attach($documentsToAttach);
        }
    }
}