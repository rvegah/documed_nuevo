<div class="row">
    <div class="col-md-12 form-group mb-3">
        <label for="company_name">Nombre de la Empresa *</label>
        <input type="text" name="company_name" id="company_name" class="form-control @error('company_name') is-invalid @enderror"
               value="{{ old('company_name', $company->company_name ?? '') }}" required>
        @error('company_name')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>

<div class="row">
    <div class="col-md-6 form-group mb-3">
        <label for="legal_representative_dni">REPRESENTATE LEGAL *</label>
        <input type="text" name="legal_representative_dni" id="legal_representative_dni" class="form-control @error('legal_representative_dni') is-invalid @enderror"
               value="{{ old('legal_representative_dni', $company->legal_representative_dni ?? '') }}" required>
        @error('legal_representative_dni')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6 form-group mb-3">
        <label for="rn_owner">RC DEL TITULAR *</label>
        <input type="text" name="rn_owner" id="rn_owner" class="form-control @error('rn_owner') is-invalid @enderror"
               value="{{ old('rn_owner', $company->rn_owner ?? '') }}" required>
        @error('rn_owner')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>