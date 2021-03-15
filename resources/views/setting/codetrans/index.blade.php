@extends('adminlte::page')

@section('title', 'Kode Transaksi')
@section('plugins.Datatables', true)
@section('content_header')
<h1>Kode Transaksi</h1>
@stop

@section('content')
<div class="card">
    <div class="card-header">
        <a class="btn btn-success" href="{{ route('kode-transaksi-create') }}"><i class="glyphicon glyphicon-subtitles"></i> +</a>
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
        <table id="table_work" class="table table-bordered table-striped ">
            <thead>
                <tr class="info">
                    <th><a href="#">No</a></th>
                    <th><a href="#">Kode</a></th>
                    <th><a href="#">Nama Transaksi</a></th>
                    <th><a href="#">Action</a></th>

                </tr>
            </thead>
            <tbody id="fbody">
                <?php $i = 1; ?>
                @foreach($data['codetrans'] as $codetrans)
                <tr>
                    <td class="text-center">{{$loop->iteration}}</td>
                    <td >{{$codetrans->CODE}}</td>
                    <td >{{$codetrans->NAMA_TRANSAKSI}}</td>
                    <td class="text-center">
                        <a class="btn btn-info" href="/setting/codetrans/edit/{{$codetrans->CODE}}"><i class="glyphicon glyphicon-subtitles"></i>edit</a>
                        <a class="btn btn-danger" onclick="return confirm('Yakin Untuk Dihapus?')" href="/setting/codetrans/destroy/{{$codetrans->CODE}}"><i class="glyphicon glyphicon-subtitles"></i>hapus</a>
                    </td>

                </tr>   
                <?php $i++; ?>
                @endforeach
            </tbody> </table>
        

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