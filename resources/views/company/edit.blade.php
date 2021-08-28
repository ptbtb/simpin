<form action="{{ route('company.update', [$company->id]) }}" method="POST">
    @csrf
    @method('PUT')
    <div class="form-group">
        <label for="group_id">Group</label>
        {!! Form::select('company_group_id', $groups, $company->company_group_id, ['class' => 'form-control', 'placeholder' => 'Pilih satu', 'required']) !!}
    </div>
    <div class="form-group">
        <label for="company_name">Company Name</label>
        {!! Form::text('company_name', $company->nama, ['class' => 'form-control', 'placeholder' => 'Company Name', 'required']) !!}
    </div>
    <div class="text-center">
        <button type="submit" class="btn btn-sm btn-success"><i class="fa fa-save"></i> Simpan</button>
    </div>
</form>
