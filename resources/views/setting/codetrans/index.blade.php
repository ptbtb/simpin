@extends('adminlte::page')

@section('title', 'Kode Transaksi')
@section('plugins.Datatables', true)
@section('content_header')
<h1>Kode Transaksi</h1>
@stop

@section('content')
<div class="card">
    <div class="card-header">
        <a class="btn btn-success" href="{{ route('kode-transaksi-create') }}"><i class="glyphicon glyphicon-subtitles"></i>+ Add Kode Transaksi</a>
        <a href="{{ route('kode-transaksi-excel') }}" class="btn btn-success"><i class="fa fa-download"></i> Download Excel</a>
    </div>
    <!-- /.card-header -->
    <div class="card-body">
        <div class="col-6">
            @if (session('status'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('status') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            @endif
        </div>
        @include('setting.codetrans.excel')


    </div>

</div>

@stop

@section('css')
@stop

@section('js')
<script>
    $(document).ready(function () {
        $("#table_work").DataTable({
            "paging": true,
            "lengthChange": true,
            "searching": true,
            "ordering": true,
            "info": true,
            "autoWidth": false,
            "responsive": true,
        });
    });
</script>
@stop
