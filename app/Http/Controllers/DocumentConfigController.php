<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Document;

class DocumentConfigController extends Controller
{
    public function index()
    {
        // Solo admin puede acceder
        if (!auth()->user()->isAdmin()) {
            return redirect()->route('companies.index')
                ->with('error', 'No tienes permisos para acceder a esta sección.');
        }

        $documents = Document::orderBy('category')->orderBy('order')->get();

        return view('admin.document-config.index', compact('documents'));
    }

    public function toggleRequired(Document $document)
    {
        // Cambiar el estado required
        $document->required = !$document->required;
        $document->save();

        return response()->json([
            'success' => true,
            'required' => $document->required,
            'message' => $document->required ? 'Documento marcado como obligatorio' : 'Documento marcado como opcional'
        ]);
    }
}