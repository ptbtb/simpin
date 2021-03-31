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
            <li class="breadcrumb-item"><a href="">Penarikan</a></li>
			<li class="breadcrumb-item active">History Penarikan</li>
		</ol>
	</div>
</div>
@endsection

@section('plugins.Datatables', true)
@section('plugins.Select2', true)

@section('css')
    <link href="https://unpkg.com/gijgo@1.9.13/css/gijgo.min.css" rel="stylesheet" type="text/css" />
    <style>
        .btn-sm{
            font-size: .8rem;
        }

        .box-custom{
            border: 1px solid black;
            border-radius: 0;
        }
    </style>
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <label class="m-0">Filter</label>
        </div>
        <div class="card-body">
            <form action="{{ route('penarikan-history') }}" method="post">
                @csrf
                <div class="row">
                    @if (!Auth::user()->isAnggota())
                        <div class="col-md-4 form-group">
                            <label>Nama Anggota</label>
                            <select name="kode_anggota" id="namaAnggota" class="form-control"></select>
                        </div>
                    @endif
                    <div class="col-md-4 form-group">
                        <label>From</label>
                        <input id="from" type="text" name="from" class="form-control" placeholder="yyyy-mm-dd" value="{{ ($request->from)? $request->from:'' }}">
                    </div>
                    <div class="col-md-4 form-group">
                        <label>To</label>
                        <input id="to" type="text" name="to" class="form-control" placeholder="yyyy-mm-dd" value="{{ ($request->to)? $request->to:'' }}">
                    </div>
                    <div class="col-md-1 form-group">
                        <button type="submit" class="btn btn-sm btn-success form-control"><i class="fa fa-filter"></i> Filter</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <div class="card">
        <div class="card-header text-right">
            <a href="{{ route('penarikan-download-pdf', ['from' => $request->from, 'to' => $request->to, 'kode_anggota' => $request->kode_anggota]) }}" class="btn btn-info btn-sm"><i class="fa fa-download"></i> Download PDF</a>
            <a href="{{ route('penarikan-download-excel', ['from' => $request->from, 'to' => $request->to, 'kode_anggota' => $request->kode_anggota]) }}" class="btn btn-sm btn-success"><i class="fa fa-download"></i> Download Excel</a>
        </div>
        <div class="card-body table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Anggota</th>
                        <th>Tanggal Penarikan</th>
                        <th>Besar Penarikan</th>
                        <th>Status Penarikan</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($listPenarikan as $penarikan)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $penarikan->anggota->nama_anggota }}</td>
                            <td>{{ $penarikan->tgl_ambil->format('d M Y') }}</td>
                            <td>Rp. {{ number_format($penarikan->besar_ambil,0,",",".") }}</td>
                            <td>{{ $penarikan->anggota->nama_anggota }}</td>
                            <td>
                                <a data-id="{{ $penarikan->kode_ambil }}" class="text-white btn btn-sm btn-info btn-jurnal"><i class="fas fa-eye"></i> Jurnal</a>
                                <a style="cursor: pointer" class="btn btn-sm btn-warning mt-1 mt-md-0 btn-information" data-id="{{ $penarikan->kode_ambil }}"><i class="fa fa-info"></i> Info</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody> 
            </table>
        </div>
    </div>
@endsection

@section('js')
    <script src="https://unpkg.com/gijgo@1.9.13/js/gijgo.min.js" type="text/javascript"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    <script>
        var baseURL = {!! json_encode(url('/')) !!};
        var kode_anggota = '{{ ($request->kode_anggota)? $request->kode_anggota:'' }}';
        
        $(document).ready(function ()
        {
            $('#from').datepicker({
                uiLibrary: 'bootstrap4',
                format: 'yyyy-mm-dd'
            });
            
            $('#to').datepicker({
                uiLibrary: 'bootstrap4',
                format: 'yyyy-mm-dd'
            });

            $('.table').DataTable();

            @if (!Auth::user()->isAnggota())
                select2Anggota();
                updateSelectedAnggota();
            @endif
        });

        function select2Anggota()
        {
            $("#namaAnggota").select2({
            placeholder: "Select All",
            allowClear: true,
            ajax: {
                    url: '{{ route('anggota-ajax-search') }}',
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
                        var query = {
                            search: params.term,
                            type: 'public'
                        }
                        return query;
                    },
                    processResults: function (data) {
                        return {
                            results: data
                        };
                    }
                }
            });
        }

        function updateSelectedAnggota()
        {
            // Fetch the preselected item, and add to the control
            var anggotaDropdown = $('#namaAnggota');
            $.ajax({
                type: 'GET',
                url: '{{ route('anggota-ajax-search') }}' + '/' +kode_anggota
            }).then(function (data) {
                // create the option and append to Select2
                var option = new Option(data.nama_anggota, data.kode_anggota, true, true);
                anggotaDropdown.append(option).trigger('change');
                // manually trigger the `select2:select` event
                anggotaDropdown.trigger({
                    type: 'select2:select',
                    params: {
                        data: data
                    }
                });
            });
        }

        $('.btn-jurnal').on('click', function ()
        {
            htmlText = '';
            var id = $(this).data('id');
            $.ajax({
                url: baseURL + '/penarikan/data-jurnal/' + id,
                success : function (data, status, xhr) {
                    htmlText = data;
                    Swal.fire({
                        title: 'Jurnal Penarikan',
                        html: htmlText, 
                        icon: "info",
                        showCancelButton: false,
                        confirmButtonText: "Ok",
                        confirmButtonColor: "#00a65a",
                        grow: 'row',
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        allowEnterKey: false,
                    }).then((result) => {
                        if (result.value) {
                        }
                    });
                },
                error : function (xhr, status, error) {
                    Swal.fire({
                        title: 'Error',
                        html: htmlText, 
                        icon: "error",
                        showCancelButton: false,
                        confirmButtonText: "Ok",
                        confirmButtonColor: "#00a65a",
                        grow: 'row',
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        allowEnterKey: false,
                    }).then((result) => {
                        if (result.value) {
                        }
                    });
                }
            });
        });

        $('.btn-information').on('click', function ()
        {
            var dataId = $(this).data('id');
            var listPenarikan = collect(@json($listPenarikan));
            var penarikan = listPenarikan.where('kode_ambil', dataId).first();
            var htmlText = '<div class="container-fluid">' + 
                                '<div class="row">' + 
                                    '<div class="col-md-6 mx-0 my-2">Created At <br> <b>' + penarikan['created_at_view'] + '</b></div>' + 
                                    '<div class="col-md-6 mx-0 my-2">Created By <br> <b>' + penarikan['created_by_view'] + '</b></div>' + 
                                    '<div class="col-md-6 mx-0 my-2">Updated At <br> <b>' + penarikan['updated_at_view'] + '</b></div>' + 
                                    '<div class="col-md-6 mx-0 my-2">Created By <br> <b>' + penarikan['updated_by_view'] + '</b></div>' + 
                                '</div>' + 
                            '</div>';

            Swal.fire({
                title: 'Info',
                html: htmlText, 
                showCancelButton: false,
                confirmButtonText: "Ok",
                confirmButtonColor: "#00a65a",
            });
        });
    </script>
@endsection