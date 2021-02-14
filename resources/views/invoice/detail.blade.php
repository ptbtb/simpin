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
            <h5>
                Invoice {{ $invoice->invoice_number }}
            </h5>
        </div>
        <div class="card-body">
            <table class="table table-striped">
                <tbody>
                    <tr>
                        <th style="width: 15%">Invoice Number</th>
                        <th>:</th>
                        <td style="width: 10%">{{ $invoice->invoice_number }}</td>
                        <td style="width: 10%"></td>
                        <th style="width: 12%">Invoice Type</th>
                        <th>:</th>
                        <td style="width: 13%">{{ $invoice->invoiceType->name }}</td>
                        <td style="width: 10%"></td>
                        <th style="width: 10%">Date</th>
                        <th>:</th>
                        <td>{{ $invoice->viewDate }}</td>
                    </tr>
                    <tr>
                        <th>Member Code</th>
                        <th>:</th>
                        <td>{{ $invoice->kode_anggota }}</td>
                        <td></td>
                        <th>Member Name</th>
                        <th>:</th>
                        <td>{{ $invoice->anggota->nama_anggota }}</td>
                        <td></td>
                        <th>Company</th>
                        <th>:</th>
                        <td>{{ $invoice->anggota->company->nama }}</td>
                    </tr>
                    <tr>
                        <th>Amount</th>
                        <th>:</th>
                        <td>{{ $invoice->final_amount }}</td>
                        <td></td>
                        <th>Due Date</th>
                        <th>:</th>
                        <td>{{ $invoice->viewDueDate }}</td>
                        <td></td>
                        <th>Invoice Status</th>
                        <th>:</th>
                        <td>{{ $invoice->invoiceStatus->name }}</td>
                    </tr>
                    <tr>
                        <th>Information</th>
                        <th>:</th>
                        <td colspan="9">{{ $invoice->description }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
@endsection

@section('js')
@endsection