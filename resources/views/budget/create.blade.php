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
                <li class="breadcrumb-item"><a href="{{ route('budget.list') }}">Budget</a></li>
                <li class="breadcrumb-item active">Create Budget</li>
            </ol>
        </div>
    </div>
@endsection

@section('plugins.Datatables', true)
@section('plugins.SweetAlert2', true)

@section('css')
    <link href="https://unpkg.com/gijgo@1.9.13/css/gijgo.min.css" rel="stylesheet" type="text/css" />
    <style>
        .btn-sm {
            font-size: .8rem;
        }

    </style>
@endsection

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('budget.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="form-group">
                    <label>Budget Name</label>
                    <input type="text" name="name" class="form-control" placeholder="Budget Name" required>
                </div>
                <div class="form-group">
                    <label>Date</label>
                    <input type="text" name="date" class="form-control date" placeholder="Date" required autocomplete="off">
                </div>
                <div class="form-group">
                    <label>Amount</label>
                    <input type="text" name="amount" class="form-control amount" placeholder="Amount" required>
                </div>
                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description" class="form-control" placeholder="Description"></textarea>
                </div>
                <div class="form-group">
                    <button type="submit" class="btn btn-sm btn-success"><i class="fa fa-save"></i> Simpan</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('js')
    <script src="https://unpkg.com/gijgo@1.9.13/js/gijgo.min.js" type="text/javascript"></script>
    <script>
        $('.date').datepicker({
            uiLibrary: 'bootstrap4',
            format: 'mm-yyyy'
        });
    </script>
@endsection
