<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Company;
use App\Models\Staff;
use App\Models\Document;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class DocumentApprovalController extends Controller
{
    /**
     * Mostrar panel de aprobación para admin
     */
    public function index()
    {
        // Solo admins pueden acceder
        if (!Auth::user()->isAdmin()) {
            return redirect()->route('companies.index')->with('error', 'No tienes permisos para acceder a esta sección.');
        }

        // Obtener todas las empresas con documentos pendientes
        $companies = Company::with(['documents' => function($query) {
            $query->wherePivot('valid', false)->orWherePivot('valid', null);
        }])->get();

        // Obtener todo el staff con documentos pendientes
        $staffWithPendingDocs = Staff::with(['documents' => function($query) {
            $query->wherePivot('valid', false)->orWherePivot('valid', null);
        }, 'company'])->get();

        return view('admin.document-approval.index', compact('companies', 'staffWithPendingDocs'));
    }

    /**
     * Mostrar documentos de una empresa específica
     */
    public function showCompanyDocuments(Company $company)
    {
        if (!Auth::user()->isAdmin()) {
            return redirect()->route('companies.index')->with('error', 'No tienes permisos para acceder a esta sección.');
        }

        $company->load('documents');
        
        return view('admin.document-approval.company-documents', compact('company'));
    }

    /**
     * Mostrar documentos de un staff específico
     */
    public function showStaffDocuments(Staff $staff)
    {
        if (!Auth::user()->isAdmin()) {
            return redirect()->route('companies.index')->with('error', 'No tienes permisos para acceder a esta sección.');
        }

        $staff->load(['documents', 'company']);
        
        return view('admin.document-approval.staff-documents', compact('staff'));
    }

    /**
     * Aprobar un documento de empresa
     */
    public function approveCompanyDocument(Request $request, Company $company, Document $document)
    {
        try {
            if (!Auth::user()->isAdmin()) {
                return response()->json(['error' => 'No autorizado'], 403);
            }

            $validatedData = $request->validate([
                'comments' => 'nullable|string|max:500',
            ]);

            // Actualizar el documento en la tabla pivot
            $company->documents()->updateExistingPivot($document->id, [
                'valid' => true,
                'valid_date' => now(),
                'valid_user_id' => Auth::id(),
                'comments' => $validatedData['comments'] ?? null,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Documento aprobado exitosamente.'
            ]);

        } catch (\Exception $e) {
            Log::error("Error al aprobar documento de empresa: " . $e->getMessage());
            return response()->json(['error' => 'Error al aprobar documento'], 500);
        }
    }

    /**
     * Rechazar un documento de empresa
     */
    public function rejectCompanyDocument(Request $request, Company $company, Document $document)
    {
        try {
            if (!Auth::user()->isAdmin()) {
                return response()->json(['error' => 'No autorizado'], 403);
            }

            $validatedData = $request->validate([
                'comments' => 'required|string|max:500',
            ]);

            // Actualizar el documento en la tabla pivot
            $company->documents()->updateExistingPivot($document->id, [
                'valid' => false,
                'valid_date' => now(),
                'valid_user_id' => Auth::id(),
                'comments' => $validatedData['comments'],
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Documento rechazado exitosamente.'
            ]);

        } catch (\Exception $e) {
            Log::error("Error al rechazar documento de empresa: " . $e->getMessage());
            return response()->json(['error' => 'Error al rechazar documento'], 500);
        }
    }

    /**
     * Aprobar un documento de staff
     */
    public function approveStaffDocument(Request $request, Staff $staff, Document $document)
    {
        try {
            if (!Auth::user()->isAdmin()) {
                return response()->json(['error' => 'No autorizado'], 403);
            }

            $validatedData = $request->validate([
                'comments' => 'nullable|string|max:500',
            ]);

            // Actualizar el documento en la tabla pivot
            $staff->documents()->updateExistingPivot($document->id, [
                'valid' => true,
                'valid_date' => now(),
                'valid_user_id' => Auth::id(),
                'comments' => $validatedData['comments'] ?? null,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Documento aprobado exitosamente.'
            ]);

        } catch (\Exception $e) {
            Log::error("Error al aprobar documento de staff: " . $e->getMessage());
            return response()->json(['error' => 'Error al aprobar documento'], 500);
        }
    }

    /**
     * Rechazar un documento de staff
     */
    public function rejectStaffDocument(Request $request, Staff $staff, Document $document)
    {
        try {
            if (!Auth::user()->isAdmin()) {
                return response()->json(['error' => 'No autorizado'], 403);
            }

            $validatedData = $request->validate([
                'comments' => 'required|string|max:500',
            ]);

            // Actualizar el documento en la tabla pivot
            $staff->documents()->updateExistingPivot($document->id, [
                'valid' => false,
                'valid_date' => now(),
                'valid_user_id' => Auth::id(),
                'comments' => $validatedData['comments'],
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Documento rechazado exitosamente.'
            ]);

        } catch (\Exception $e) {
            Log::error("Error al rechazar documento de staff: " . $e->getMessage());
            return response()->json(['error' => 'Error al rechazar documento'], 500);
        }
    }
}