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
                <li class="breadcrumb-item active">Jenis Penghasilan</li>
            </ol>
        </div>
    </div>
@endsection

@section('plugins.Datatables', true)
@section('plugins.SweetAlert2', true)
@section('plugins.Select2', true)

@section('css')
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <label>Generator</label>
        </div>
        <div class="card-body">
            <form action="{{ route('jenis.penghasilan.create') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label>Group</label>
                    {!! Form::select('group_id', $companyGroups, $request->group_id, ['class' => 'form-control', 'placeholder' => 'pilih satu', 'required']) !!}
                </div>
                <div class="form-group">
                    <label>Jumlah Form</label>
                    <input type="number" name="total_form" value="{{ $request->total_form }}" placeholder="total form" class="form-control" required>
                </div>
                <div class="form-group text-center">
                    <button type="submit" name="generator" value="generator" class="btn btn-sm btn-success">
                        <i class="fa fa-save"></i> Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>

    @if ($request->generator)
        <div class="card">
            <div class="card-header">
                Form
            </div>
            <div class="card-body">
                <form action="{{ route('jenis.penghasilan.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="group_id" value="{{ $request->group_id }}">
                    <div class="row">
                        @for ($i = 0; $i < $request->total_form; $i++)
                            <div class="form-group col-lg-4">
                                <label>Name {{ $i+1 }}</label>
                                <input type="text" name="form_name[]" class="form-control" placeholder="form-name" required>
                            </div>
                        @endfor
                    </div>
                    <div class="form-group text-center">
                        <button type="submit" class="btn btn-sm btn-success">
                            <i class="fa fa-save"></i> Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
@endsection

@section('js')
@endsection
