@extends('adminlte::page')

@section('title', 'Data Anggota')
@section('plugins.Datatables', true)
@section('content_header')
<h1>Data Anggota</h1>
@stop

@section('content')
<div class="card">
              <div class="card-header">
                <a class="btn btn-primary" href="/anggota"><i class="glyphicon glyphicon-subtitles"></i> Aktif</a>
                <a class="btn btn-primary" href="/anggota/nonaktif"><i class="glyphicon glyphicon-subtitles"></i> Non Aktif</a>
              
              </div>
              <!-- /.card-header -->
              <div class="card-body">
                    
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
    $(document).ready(function(){
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