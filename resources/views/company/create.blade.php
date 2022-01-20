@extends('adminlte::page')

@section('title')
    {{ $title }}
@endsection

@section('content_header')
    <div class="row">
        <div class="col-6">
            <h4>{{ $title }}</h4>
        </div>
        <div class="col-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                <li class="breadcrumb-item active">Company</li>
            </ol>
        </div>
    </div>
@endsection

@section('plugins.Datatables', true)
@section('plugins.SweetAlert2', true)

@section('css')
@endsection

@section('content')
    <div class="card">

        <div class="card-body">
<form action="{{ route('company.create') }}" method="POST">
    @csrf
    <div class="form-group">
        <label for="group_id">Group</label>
        {!! Form::select('company_group_id', $groups,'', ['class' => 'form-control', 'placeholder' => 'Pilih satu', 'required']) !!}
    </div>
    <div class="form-group">
        <label for="company_name">Company Name</label>
        {!! Form::text('company_name', '', ['class' => 'form-control', 'placeholder' => 'Company Name', 'required']) !!}
    </div>
    <div class="text-center">
        <button type="submit" class="btn btn-sm btn-success"><i class="fa fa-save"></i> Simpan</button>
    </div>
</form>
</div>
</div>
@endsection
