@extends('adminlte::page')

@section('title')
    {{ $title }}
@endsection

@section('content_header')
<div class="row">
	<div class="col-6"><h4>{{ $title }}</h4></div>
	<div class="col-6">
		<ol class="breadcrumb float-sm-right">
			<li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
			<li class="breadcrumb-item active">SimPin Rule</li>
		</ol>
	</div>
</div>
@endsection

@section('plugins.Datatables', true)

@section('css')
    <style>
        .btn-sm{
            font-size: .8rem;
        }
    </style>
@endsection

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('simpin-rule-create') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label for="code">Code</label>
                    <input type="text" name="code" id="code" placeholder="Code" class="form-control">
                </div>
                <div class="form-group">
                    <label for="code">Description</label>
                    <textarea name="description" id="description" cols="30" rows="5" class="form-control" required placeholder="Description"></textarea>
                </div>
                <div class="form-group">
                    <label for="code">Value</label>
                    <input type="number" name="value" id="value" placeholder="Value" class="form-control">
                </div>
                <div class="form-group">
                    <label for="code">Amount</label>
                    <input type="number" name="amount" id="amount" placeholder="Amount" class="form-control">
                </div>
                <div class="form-group">
                    <button class="btn btn-sm btn-success"><i class="fa fa-save"></i> Save</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('js')
@endsection