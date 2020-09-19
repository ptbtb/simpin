@extends('adminlte::page')

@section('title', 'Data Simpanan Angota')
@section('plugins.Datatables', true)
@section('content_header')
<h1>Data Simpanan Angota</h1>
@stop

@section('content')
<div class="card">
    <div class="card-header">
        <a class="btn btn-primary" href="/anggota/nonaktif"><i class="glyphicon glyphicon-subtitles"></i> Nonaktif</a>
        <a class="btn btn-primary" href="/anggota/all"><i class="glyphicon glyphicon-subtitles"></i> Semua</a>
        <a class="btn btn-success" href="/anggota/add"><i class="glyphicon glyphicon-subtitles"></i> +</a>
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
        <table id="table_anggota" class="table table-bordered table-striped table-condensed">
            <thead>
                <tr class="info">
                    <th><a href="#">No</a></th>
                    <th><a href="#">No Anggota</a></th>
                    <th><a href="#">NIPP</a></th>
                    <th><a href="#">Nama Anggota</a></th>
                    <th ><a href="#">Tempat</a></th>
                    <th ><a href="#">Tanggal Lahir</a></th>
                    <th><a href="#">Pekerjaan</a></th>
                    <th><a href="#">Tanggal Masuk</a></th>
                    <th><a href="#">Status</a></th>
                    <th><a href="#">Action</a></th>

                </tr>
            </thead>
            <tbody id="fbody">
                <?php $i = 1; ?>
                @foreach($data['anggota'] as $anggota)
                <tr>
                    <td class="text-center">{{$loop->iteration}}</td>
                    <td class="text-center">{{$anggota->kode_anggota}}</td>
                    <td class="text-center">{{$anggota->nipp}}</td>
                    <td class="text-center">{{$anggota->nama_anggota}}</td>
                    <td class="text-center">{{$anggota->tempat_lahir}}</td>
                    <td class="text-center">{{$anggota->tgl_lahir}}</td>
                    <td class="text-center">{{$anggota->lokasi_kerja}}</td>
                    <td class="text-center">{{$anggota->tgl_masuk}}</td>
                    <td class="text-center">{{$anggota->status}}</td>
                    <td class="text-center"><a class="btn btn-info" href="/anggota/edit/{{$anggota->kode_anggota}}"><i class="glyphicon glyphicon-subtitles"></i>edit</a></td>

                </tr>   
                <?php $i++; ?>
                @endforeach
            </tbody> </table>
    </div>

</div><!-- /row -->
@stop

@section('css')
@stop

@section('js')
<script>
    $(document).ready(function () {
        $("#table_anggota").DataTable({
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