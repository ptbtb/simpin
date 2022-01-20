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
                <li class="breadcrumb-item"><a href="{{ route('company.index') }}">Company</a></li>
                <li class="breadcrumb-item active">Kelas</li>
            </ol>
        </div>
    </div>
@endsection

@section('plugins.Datatables', true)
@section('plugins.SweetAlert2', true)

@section('css')
@endsection

@section('content')
    <div class="card">
        <div class="card-header text-right">
            <a class="btn btn-sm btn-success btn-create" data-id="{{ $company->id }}">
                <i class="fa fa-plus"></i> Add
            </a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped" id="companyTable">
                    <thead>
                        <tr>
                            <th style="width: 5%">No</th>
                            <th style="width: 20%">Name</th>
                            <th style="width: 20%">Jenis Anggota</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($list as $item)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $item->nama }}</td>
                                <td>{{ ($item->jenisAnggota)?$item->jenisAnggota->nama_jenis_anggota:'-' }}</td>
                                <td class="text-center">
                                    <a class="btn btn-sm btn-warning btn-edit" data-id="{{ $item->id }}">
                                        <i class="fa fa-edit"></i> Edit
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div id="editCompany" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-body">
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script>
        $('#companyTable').DataTable({
            dom: 'Bfrtip',
        buttons: [
        { extend: 'excelHtml5', footer: true,
        className: 'btn btn-info btn-sm',
        text: '<i class="fa fa-download"></i> Excel',
        title: 'List Company',
        exportOptions: {
                    columns: [ 0,1, 2]
                }
    },
    { extend: 'pdfHtml5', footer: true,
    orientation: 'landscape',
    className: 'btn btn-sm btn-default',
    text: '<i class="fa fa-download"></i>PDF',
    title: 'List Company',
    exportOptions: {
                    columns: [ 0,1, 2]
                }
}
]
        });
        $('#companyTable').on('click', 'a.btn-edit', function ()
        {
            var data_id = $(this).data('id');
            var url = "{{ route('company.kelas.edit', ['id']) }}";
            url = url.replace('id', data_id);
            $.ajax({
                url: url,
                success: function (data, status, xhr)
                {
                    $('.modal-body').html(data);
                    $('#editCompany').modal();
                },
                error: function (xhr, status, th)
                {
                    console.log(status);
                }
            });
        })
        $('.btn-create').on('click', function ()
        {
            var data_id = $(this).data('id');
            var url = "{{ route('company.kelas.create', ['id']) }}";
            url = url.replace('id', data_id);
            $.ajax({
                url: url,
                success: function (data, status, xhr)
                {
                    $('.modal-body').html(data);
                    $('#editCompany').modal();
                },
                error: function (xhr, status, th)
                {
                    console.log(status);
                }
            });
        })
    </script>
@endsection