<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCompanyRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'company_name' => ['required', 'string', 'max:255'],
            'legal_representative_dni' => ['required', 'string', 'max:250'],
            'rn_owner' => ['required', 'string', 'max:250'],
            'documents' => ['nullable'],
            'documents.*' => 'nullable|file|mimes:jpeg,png,pdf|max:4096',
        ];
    }
}
