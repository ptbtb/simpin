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
            <li class="breadcrumb-item active">Jkk Printed</li>
        </ol>
    </div>
</div>
@endsection

@section('plugins.Datatables', true)
@section('plugins.SweetAlert2', true)

@section('css')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css" integrity="sha256-siyOpF/pBWUPgIcQi17TLBkjvNgNQArcmwJB8YvkAgg=" crossorigin="anonymous" />
<style>
    .btn-sm {
        font-size: .8rem;
    }
</style>
@endsection

@section('content')
@can('filter user')
<div class="card">
    <div class="card-header">
        <label class="m-0">Filter</label>
    </div>
    <div class="card-body">
        <form action="{{ route('jkk-printed-list') }}" method="post">
            @csrf
            <div class="row">
                <div class="col-md-4 form-group">
                    <label>JKK Type</label>
                    <select name="type_id" class="form-control">
                        <option value="">Select All</option>
                        @foreach ($types as $type)
                        <option value="{{ $type->id }}" {{ $request->type_id && $request->type_id == $type->id ? 'selected' : '' }}>
                            {{ $type->name }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label>TGL Print Dari</label>
                    <input class="form-control datepicker" placeholder="dd-mm-yyyy" id="from" name="from" value="{{ Carbon\Carbon::createFromFormat('d-m-Y', $request->from)->format('d-m-Y') }}" autocomplete="off" />
                </div>
                <div class="col-md-3">
                    <label>TGL Print Sampai</label>
                    <input class="form-control datepicker" placeholder="mm-yyyy" id="to" name="to" value="{{ Carbon\Carbon::createFromFormat('d-m-Y', $request->to)->format('d-m-Y') }}" autocomplete="off" />
                </div>
                <div class="col-md-3">
                    <label>No JKK</label>
                    <input class="form-control" name="no_jkk" value="{{ $request->no_jkk}}" autocomplete="off" />
                </div>
                <div class="col-md-1 form-group" style="margin-top: 26px">
                    <button type="submit" class="btn btn-sm btn-success form-control">
                        <i class="fa fa-filter"></i>Filter
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endcan
<div class="card">
    <div class="card-body table-responsive">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th style="width: 5%">#</th>
                    <th style="width: 20%">No Jkk</th>
                    <th>NO.Angg</th>
                    <th>Nama</th>
                    <th>Type</th>
                    <th>Created Date</th>
                    <th>Created By</th>
                    <th style="width: 25%">Action</th>
                </tr>
            </thead>
        </table>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="paymentConfirmationModal" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="" method="POST" enctype="multipart/form-data" id="paymentConfirmationForm">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Re-print JKK</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Print</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('js')
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js" integrity="sha256-bqVeqGdJ7h/lYPq6xrPv/YGzMEb6dNxlfiTUHSgRCp8=" crossorigin="anonymous"></script>
<script>
    $.fn.dataTable.ext.errMode = 'none';
    $('.table').DataTable({
        processing: true,
        serverside: true,
        ajax: {
            url: '{{ route("jkk-printed-data") }}',
            dataSrc: 'data',
            data: function(data) {
                @if  (isset($request->type_id)) data.type_id = '{{ $request->type_id }}';
                @endif
                    var from = '{{ $request->from }}';
                data.from = from;
                var to = '{{ $request->to }}';
                data.to = to;
                var no_jkk = '{{ $request->no_jkk }}';
                data.no_jkk = no_jkk;
            }
        },
        aoColumns: [{
                mData: 'id',
                sType: "string",
                className: "dt-body-center",
                "name": "id"
            },
            {
                mData: 'jkk_number',
                sType: "string",
                className: "dt-body-center",
                "name": "jkk_number"
            },
            {
                mData: 'anggota_view',
                sType: "string",
                className: "dt-body-center",
                "name": "anggota_view"
            },
            {
                mData: 'nama_anggota_view',
                sType: "string",
                className: "dt-body-center",
                "name": "nama_anggota_view"
            },
            {
                mData: 'jkk_printed_type.name',
                sType: "string",
                className: "dt-body-center",
                "name": "jkk_printed_type.name"
            },
            {
                mData: 'printed_at_view',
                sType: "string",
                className: "dt-body-center",
                "name": "printed_at_view"
            },
            {
                mData: 'printed_by_view',
                sType: "string",
                className: "dt-body-center",
                "name": "printed_by_view"
            },
            {
                mData: 'id',
                sType: "string",
                className: "text-center",
                mRender: function(data, type, full) {
                    var link = '';
                    if (full.konfirmasi_pembayaran) {
                        link = link + '<a href="{{ route("jkk-printed-show", [""]) }}/' + data + '" class="btn btn-sm btn-primary"><i class="fa fa-check"></i> Konfirmasi Pembayaran</a>';
                    }
                    link = link + '<button data-toggle="modal" data-target="#paymentConfirmationModal" class="btn btn-sm btn-info btn-reprint mt-1" data-url="{{ route("jkk-printed-reprint", [""]) }}/' + data + '"/>' +
                        '<i class="fa fa-print"></i> Reprint' +
                        '</button>';
                    return link;
                }
            },
        ]
    });

    $(document).on('click', '.btn-reprint', function() {
        var dataUrl = $(this).data('url');
        $('#paymentConfirmationForm').attr('action', dataUrl);
    });

    $('#paymentConfirmationForm').on('submit', function() {
        setTimeout(function() {
            window.location.reload(1);
        }, 3000);
    });
    $('.datepicker').datepicker({
        format: "dd-mm-yyyy"
    });

    $('input.datepicker').bind('keyup keydown keypress', function(evt) {
        return true;
    });
</script>
@endsection
