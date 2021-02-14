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
			<li class="breadcrumb-item active">Invoice</li>
		</ol>
	</div>
</div>
@endsection

@section('plugins.Datatables', true)
@section('plugins.SweetAlert2', true)

@section('css')
    <style>
        .btn-sm{
            font-size: .8rem;
        }
    </style>
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <label class="m-0">Filter</label>
        </div>
        <div class="card-body">
            <form action="{{ route('invoice-list') }}" method="post">
                @csrf
                <div class="row">
                    <div class="col-md-4 form-group">
                        <label>Invoice Type</label>
                        {!! Form::select('invoice_type_id', $invoiceType, $request->invoice_type_id, ['class' => 'form-control', 'placeholder' => 'Choose one']) !!}
                    </div>
                    <div class="col-md-4 form-group">
                        <label>Invoice Status</label>
                        {!! Form::select('invoice_status_id', $invoiceStatus, $request->invoice_status_id, ['class' => 'form-control', 'placeholder' => 'Choose one']) !!}
                    </div>
                    <div class="col-md-12 form-group text-center">
                        <button type="submit" class="btn btn-sm btn-success"><i class="fa fa-filter"></i> Filter</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <div class="card">
        <div class="card-body table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>No Invoice</th>
                        <th>Type</th>
                        <th>Kode Anggota</th>
                        <th>Nama Anggota</th>
                        <th>Besar Tagihan</th>
                        <th>Keterangan</th>
                        <th>Tanggal Invoice</th>
                        <th>Jatuh Tempo</th>
                        <th>Status</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
@endsection

@section('js')
    <script>
        $(document).ready(function ()
        {
            initiateDatatables();
        });
        function initiateDatatables()
        {
            $.fn.dataTable.ext.errMode = 'none';
            $('.table').DataTable({
                processing: true,
                serverside: true,
                ajax: {
                    url: '{{ route('invoice-list-ajax') }}',
                    dataSrc: 'data',
                    data: function(data){
                        @if(isset($request->invoice_status_id)) data.invoice_status_id = '{{ $request->invoice_status_id }}'; @endif
                        @if(isset($request->invoice_type_id)) data.invoice_type_id = '{{ $request->invoice_type_id }}'; @endif
                    }
                },
                aoColumns: [
                    { 
                        mData: 'invoice_number', sType: "string", 
                        className: "dt-body-center", "name": "invoice_number"				
                    },
                    { 
                        mData: 'invoice_type.name', sType: "string", 
                        className: "dt-body-center", "name": "invoice_type.name"						
                    },{ 
                        mData: 'anggota.kode_anggota', sType: "string", 
                        className: "dt-body-center", "name": "anggota.kode_anggota"				
                    },
                    { 
                        mData: 'anggota.nama_anggota', sType: "string", 
                        className: "dt-body-center", "name": "anggota.nama_anggota"				
                    },
                    { 
                        mData: 'final_amount', sType: "string", 
                        className: "dt-body-center", "name": "final_amount",				
                    },
                    { 
                        mData: 'description', sType: "string", 
                        className: "dt-body-center", "name": "description",				
                    },
                    { 
                        mData: 'view_date', sType: "string", 
                        className: "dt-body-center", "name": "view_date",				
                    },
                    { 
                        mData: 'view_due_date', sType: "string", 
                        className: "dt-body-center", "name": "view_due_date",				
                    },
                    { 
                        mData: 'invoice_status.name', sType: "string", 
                        className: "dt-body-center", "name": "invoice_status.name",				
                    },
                ]
            });
        }
    </script>
@endsection