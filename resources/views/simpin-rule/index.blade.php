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
        <div class="card-header text-right">
            <a href="{{ route('simpin-rule-create') }}" class="btn btn-sm btn-success"><i class="fa fa-plus"></i> Create</a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Code</th>
                            <th>Description</th>
                            <th>Value</th>
                            <th>Amount</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($rules as $rule)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ (is_null($rule->code))? '-':$rule->code }}</td>
                                <td>{{ (is_null($rule->description))? '-':$rule->description }}</td>
                                <td>{{ (is_null($rule->value))? '-':$rule->value }}</td>
                                <td>{{ (is_null($rule->amount))? '-':$rule->amount }}</td>
                                <td>
                                    <a href="{{ route('simpin-rule-edit', [$rule->id]) }}" class="btn btn-sm btn-warning"><i class="fa fa-edit"></i> Edit</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script>
        $(document).ready(function ()
        {
            initiateDatatable();
        });

        function initiateDatatable()
        {
            $('.table').DataTable();
        }
    </script>
@endsection