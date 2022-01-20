<form action="{{ route('company.kelas.create', [$company->id]) }}" method="POST">
    @csrf
    @method('PUT')
    <div class="form-group">
        <label for="group_id">Jenis Anggota</label>
        {!! Form::select('id_jenis_anggota', $jenisAnggota, '', ['class' => 'form-control', 'placeholder' => 'Pilih satu', 'required']) !!}
    </div>
    <div class="form-group">
        <label for="company_name">Nama Kelas</label>
        {!! Form::text('nama', '', ['class' => 'form-control', 'placeholder' => 'nama kelas', 'required']) !!}
    </div>
    <div class="form-group">
        <label for="company_name">Urutan</label>
        {!! Form::text('sequence', '', ['class' => 'form-control', 'placeholder' => 'urutan', 'required']) !!}
    </div>
    <div class="text-center">
        <button type="submit" class="btn btn-sm btn-success"><i class="fa fa-save"></i> Simpan</button>
    </div>
</form>
