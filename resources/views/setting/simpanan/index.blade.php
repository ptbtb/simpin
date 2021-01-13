@extends('adminlte::page')

@section('title', 'Jenis Simpanan')
@section('plugins.Datatables', true)
@section('content_header')
<h1>Jenis Simpanan</h1>
@stop

@section('content')
<div class="card">
    <div class="card-header">
        <a class="btn btn-success" href="{{ route('jenis-simpanan-add') }}"><i class="glyphicon glyphicon-subtitles"></i> + Add Jenis Simpanan</a>
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
        <table id="table_work" class="table table-bordered table-striped table-condensed">
            <thead>
                <tr class="info">
                    <th><a href="#">No</a></th>
                    <th><a href="#">Kode</a></th>
                    <th><a href="#">Nama Simpanan</a></th>
                    <th><a href="#">Amount</a></th>
                    <th><a href="#">Tgl Tagihan</a></th>
                    <th><a href="#">Jatuh Temp(hari)</a></th>

                    <th><a href="#">Action</a></th>

                </tr>
            </thead>
            <tbody id="fbody">
                <?php $i = 1; ?>
                @foreach($data['simpanans'] as $simpanan)
                <tr>
                    <td >{{$loop->iteration}}</td>
                    <td >{{$simpanan->kode_jenis_simpan}}</td>
                    <td >{{$simpanan->nama_simpanan}}</td>
                    <td class="text-right">{{$simpanan->besar_simpanan}}</td>
                    <td class="text-right">{{$simpanan->tgl_tagih}}</td>
                    <td class="text-right">{{$simpanan->hari_jatuh_tempo}}</td>
                    <td >
                        <a class="btn btn-info" href="{{ route('jenis-simpanan-edit', ['id'=>$simpanan->kode_jenis_simpan]) }}"><i class="glyphicon glyphicon-subtitles"></i>edit</a>
                        <a class="btn btn-danger" onclick="return confirm('Yakin Untuk Dihapus?')" href="{{ route('jenis-simpanan-delete', ['id'=>$simpanan->kode_jenis_simpan]) }}"><i class="glyphicon glyphicon-subtitles"></i>hapus</a>
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