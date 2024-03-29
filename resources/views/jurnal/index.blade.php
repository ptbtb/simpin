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
                <li class="breadcrumb-item active">Jurnal</li>
            </ol>
        </div>
    </div>
@endsection

@section('plugins.Datatables', true)

@section('css')
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css"
        integrity="sha256-siyOpF/pBWUPgIcQi17TLBkjvNgNQArcmwJB8YvkAgg=" crossorigin="anonymous" />
    <style>
        .btn-sm {
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
            <form action="{{ route('jurnal-list') }}" method="post">
                @csrf
                <div class="row">
                    <div class="col-md-3 form-group">
                        <label>Tipe Jurnal</label>
                        {!! Form::select('id_tipe_jurnal', $tipeJurnal, $request->id_tipe_jurnal, [
                            'class' => 'form-control',
                            'placeholder' => 'All',
                        ]) !!}
                    </div>
                    <div class="col-md-3 form-group">
                        <label>Nomor</label>
                        <input type="text" name="serial_number" id="serial_number" class="form-control"
                            placeholder="Nomor Transaksi" autocomplete="off"
                            value="{{ old('serial_number', $request->serial_number) }}">
                    </div>
                    <div class="col-md-3 form-group">
                        <label>AKUN</label>
                        <input type="text" name="code" id="code" class="form-control" placeholder="COA"
                            autocomplete="off" value="{{ old('code', $request->code) }}">
                    </div>
                    <div class="col-md-3 form-group">
                        <label>Keterangan</label>
                        <input type="text" name="keterangan" id="keterangan" class="form-control"
                            placeholder="Keterangan" autocomplete="off"
                            value="{{ old('keterangan', $request->keterangan) }}">
                    </div>
                    <div class="col-md-3">
                        <label>Dari</label>
                        <input class="form-control datepicker" placeholder="dd-mm-yyyy" id="from" name="from"
                            value="{{ Carbon\Carbon::createFromFormat('d-m-Y', $request->from)->format('d-m-Y') }}"
                            autocomplete="off" />
                    </div>
                    <div class="col-md-3">
                        <label>Sampai</label>
                        <input class="form-control datepicker" placeholder="mm-yyyy" id="to" name="to"
                            value="{{ Carbon\Carbon::createFromFormat('d-m-Y', $request->to)->format('d-m-Y') }}"
                            autocomplete="off" />
                    </div>
                    <div class="col-md-12 form-group text-center">
                        <button type="submit" class="btn btn-sm btn-success"><i class="fa fa-filter"></i> Filter</button>
                        <a href="{{ route('jurnal-export-excel', ['id_tipe_jurnal' => $request->id_tipe_jurnal, 'serial_number' => $request->serial_number, 'code' => $request->code, 'from' => Carbon\Carbon::createFromFormat('d-m-Y', $request->from)->format('d-m-Y'), 'to' => Carbon\Carbon::createFromFormat('d-m-Y', $request->to)->format('d-m-Y'), 'keterangan' => $request->keterangan]) }}"
                            class="btn btn-success"><i class="fa fa-download"></i> export Excel</a>
                        <a href="{{ route('jurnal-export-pdf', ['id_tipe_jurnal' => $request->id_tipe_jurnal, 'serial_number' => $request->serial_number, 'code' => $request->code, 'from' => Carbon\Carbon::createFromFormat('d-m-Y', $request->from)->format('d-m-Y'), 'to' => Carbon\Carbon::createFromFormat('d-m-Y', $request->to)->format('d-m-Y'), 'keterangan' => $request->keterangan]) }}"
                            class="btn btn-info"><i class="fa fa-download"></i> export PDF</a>
                        <a href="{{ route('jurnal-resume', ['id_tipe_jurnal' => $request->id_tipe_jurnal, 'serial_number' => $request->serial_number, 'code' => $request->code, 'from' => Carbon\Carbon::createFromFormat('d-m-Y', $request->from)->format('d-m-Y'), 'to' => Carbon\Carbon::createFromFormat('d-m-Y', $request->to)->format('d-m-Y'), 'keterangan' => $request->keterangan]) }}"
                            class="btn btn-info"><i class="fa fa-file"></i> Resume</a>

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
                        <th>#</th>
                        <th>Nomor</th>
                        <th>No Anggota</th>
                        <th>Tipe Jurnal</th>
                        <th>Akun Debet</th>
                        <th style="width: 10%">Debet</th>
                        <th>Akun Kredit</th>
                        <th style="width: 10%">Kredit</th>
                        <th>Keterangan</th>
                        <th>Tanggal</th>
                        @can('edit jurnal')
                            <th>Action</th>
                        @endcan
                    </tr>
                </thead>
                <tfoot>
                    <tr>
                        <th colspan="5" style="text-align:right">Total:</th>
                        <th id="totaldebet" style="text-align:right"></th>
                        <th></th>
                        <th id="totalkredit" style="text-align:right"></th>
                        @if (Auth::user()->can('edit jurnal'))

                        <th colspan="3"></th>
                        @else
                        <th colspan="2"></th>

                        @endif
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
@endsection

@section('js')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"
        integrity="sha256-bqVeqGdJ7h/lYPq6xrPv/YGzMEb6dNxlfiTUHSgRCp8=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    <script>
        $(document).ready(function() {
            initiateDatatables();
            $("#from").change(function() {
                console.log($('#from').val());
                console.log({{ $request->from }});
            });

            $('.datepicker').datepicker({
                format: "dd-mm-yyyy"
            });

            $('input.datepicker').bind('keyup keydown keypress', function(evt) {
                return true;
            });
        });

        function initiateDatatables() {
            $.fn.dataTable.ext.errMode = 'none';
            var table = $('.table').DataTable({
                bProcessing: true,
                bServerSide: true,
                bSortClasses: false,
                ordering: false,
                searching: false,
                responsive: true,
                ajax: {
                    url: '{{ route('jurnal-list-ajax') }}',
                    dataSrc: 'data',
                    data: function(data) {
                        @if (isset($request->id_tipe_jurnal))
                            data.id_tipe_jurnal = '{{ $request->id_tipe_jurnal }}';
                        @endif

                        var serial_number = '{{ $request->serial_number }}';
                        data.serial_number = serial_number;
                        var code = '{{ $request->code }}';
                        data.code = code;

                        var keterangan = '{{ $request->keterangan }}';
                        data.keterangan = keterangan;
                        var from = '{{ $request->from }}';
                        data.from = from;
                        var to = '{{ $request->to }}';
                        data.to = to;
                    }
                },
                aoColumns: [{
                        mData: 'DT_RowIndex',
                        sType: "string",
                        className: "dt-body-center",
                        "name": "DT_RowIndex"
                    },
                    {
                        mData: 'jurnalable_view',
                        sType: "string",
                        className: "dt-body-center",
                        "name": "jurnalable_view",
                        mRender: function(data, type, full) {

                            if (data) {
                                if (full.id_tipe_jurnal == 2 && full.jurnalable_type ==
                                    "App\\Models\\Pinjaman") {
                                    return data.serial_number_kredit_view;
                                }
                                if (full.id_tipe_jurnal == 4 && full.jurnalable_type ==
                                    "App\\Models\\Pinjaman") {
                                    return data.serial_number_saldo_awal_view;
                                }
                                return data.serial_number_view;
                            }
                        }
                    },
                    {
                        mData: 'anggota',
                        sType: "string",
                        className: "dt-body-center",
                        "name": "anggota",

                    },
                    {
                        mData: 'tipe_jurnal.name',
                        sType: "string",
                        className: "dt-body-center",
                        "name": "tipe_jurnal.name"
                    },
                    {
                        mData: 'akun_debet',
                        sType: "string",
                        className: "dt-body-center",
                        "name": "akun_debet",
                    },
                    {
                        mData: 'nominal_rupiah_debet',
                        sType: "string",
                        className: "dt-body-center",
                        "name": "debet"
                    },
                    {
                        mData: 'akun_kredit',
                        sType: "string",
                        className: "dt-body-center",
                        "name": "akun_kredit"
                    },
                    {
                        mData: 'nominal_rupiah_kredit',
                        sType: "string",
                        className: "dt-body-center",
                        "name": "kredit"
                    },
                    {
                        mData: 'keterangan',
                        sType: "string",
                        className: "dt-body-center",
                        "name": "keterangan",
                    },
                    {
                        mData: 'tgl_transaksi',
                        sType: "string",
                        className: "dt-body-center",
                        "name": "tgl_transaksi",
                    },
                    @can('edit jurnal')

                    {
                        mData: 'id',
                        sType: "string",
                        className: "dt-body-center",
                        "name": "id",mRender: function(data, type, full)
                        {
                            var markup = '';
                            var baseURL = {!! json_encode(url('/')) !!};
                            @can('edit jurnal')
                                var serial_number = '';

                                @if ($request->serial_number)
                                    serial_number = '{{ $request->serial_number }}';
                                @else
                                    if (full.jurnalable_view) {
                                        if (full.id_tipe_jurnal == 2 && full.jurnalable_type ==
                                            "App\\Models\\Pinjaman") {
                                            serial_number = full.jurnalable_view.serial_number_kredit_view;
                                        }
                                        else
                                        {
                                            serial_number =  full.jurnalable_view.serial_number_view;
                                        }
                                    }
                                @endif
                                var editableType = ['App\\Models\\Simpanan', 'App\\Models\\Penarikan', 'App\\Models\\Pinjaman', 'App\\Models\\Angsuran'];
                                var editable = editableType.includes(full.jurnalable_type);
                                if(editable)
                                {
                                    markup += '<a href="' + baseURL + '/jurnal/edit/' + data + '?serial_number='+serial_number+'&from={{ $request->from }}&to={{ $request->to }}" class="btn btn-sm btn-warning"><i class="fa fa-edit"></i> Edit</a> '
                                }
                            @endcan
                            @can('delete jurnal')

                            markup += '<a data-url="' + baseURL + '/jurnal/delete/' + data + '" class="btn btn-sm btn-danger btn-approval"><i class="fa fa-edit"></i>Delete</a> '
                                // var csrf = '@csrf';
                                // var method = '@method("delete")';
                                // markup += '<form action="' + baseURL + '/jurnal/delete/' + data + '" method="post" style="display: inline"><button  class="btn btn-sm btn-danger" type="submit" value="Delete"><i class="fa fa-trash"></i> Delete</button>@method("delete")@csrf</form>';
                            @endcan
                            return markup;
                        }
                    },
                    @endcan
                    /*{
                        mData: 'created_by.name', sType: "string",
                        className: "dt-body-center", "name": "created_by.name",
                    },*/
                ],
                drawCallback: function(settings) {
                    $('#totaldebet').html(toRupiah(settings.json.totaldebet));
                    $('#totalkredit').html(toRupiah(settings.json.totalkredit));
                }

            });

            $(document).on('click', '.btn-approval', function() {
            var url = $(this).data('url');

            Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    html: '<div class="form-group text-left"><label>Keterangan</label><textarea placeholder="Keterangan" name="keterangan" id="keterangan" class="form-control"></textarea></div>',
                    input: 'password',
                    inputAttributes: {
                        name: 'password',
                        placeholder: 'Password',
                        required: 'required',
                        validationMessage: 'Password required',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Yes',
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        var password = result.value;
                        $.ajax({
                            type: 'get',
                            url: url+"?password="+password,
                            contentType: false,
                            processData: false,
                            success: function(data) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Success',
                                    text: 'Your has been changed',
                                    showConfirmButton: true
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        location.reload();
                                    }
                                });
                            },
                            error: function(error) {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error!',
                                    text: error.responseJSON.message,
                                    showConfirmButton: true
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        location.reload();
                                    }
                                });
                            }
                        })
                    }
                })
        });

            // add index column
            table.on('xhr.dt', function() {
                table.column(0, {
                    search: 'applied',
                    order: 'applied'
                }).nodes().each(function(cell, i) {
                    cell.innerHTML = i + 1;
                });
            }).draw();


        }


        function toRupiah(number) {
            number = parseFloat(number);
            number = number.toFixed(2);
            var stringNumber = number.toString();
            var splitStringNumber = stringNumber.split('.');
            var length = splitStringNumber[0].length;
            var temp = length;
            var res = "";
            for (let i = 0; i < length; i++) {
                res = res + splitStringNumber[0].charAt(i);
                temp--;
                if (temp % 3 == 0 && temp > 0) {
                    res = res + ".";
                }
            }
            if (splitStringNumber[1] !== 'undefined') {
                res = res + ',' + splitStringNumber[1];
            }
            return res;
        }
    </script>
@endsection
