<h5>Documentos</h5>
@foreach($documents as $document)
    <div class="form-group">
        <label for="d-{{ $document->id }}">{{ $document->name }}</label>
        <input type="file" class="form-control form-control-sm @error('documents.'.$document->id) is-invalid @enderror"
               id="d-{{ $document->id }}" name="documents[{{$document->id}}]">
        @error('documents.'.$document->id)
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
@endforeach

