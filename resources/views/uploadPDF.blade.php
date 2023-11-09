<form method="POST" action="{{ route('agregarQR') }}" enctype="multipart/form-data">
    @csrf
    <input type="file" name="pdf_file">
    <button type="submit">Subir archivo y agregar QR</button>
</form>
