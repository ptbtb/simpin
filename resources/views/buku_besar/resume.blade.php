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
                <li class="breadcrumb-item ">Buku Besar</li>
                <li class="breadcrumb-item active">Resume</li>
            </ol>
        </div>
    </div>
@endsection

@section('plugins.Datatables', true)

@section('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css" integrity="sha256-siyOpF/pBWUPgIcQi17TLBkjvNgNQArcmwJB8YvkAgg=" crossorigin="anonymous" />
    <style>
        .btn-sm{
            font-size: .8rem;
        }
    </style>
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <div class="col-md-12">
                <form id="myForm" role="form" method="GET" enctype="multipart/form-data" action="{{ route('buku-besar-resume') }}">
                    {{-- <input type="hidden" name="_token" value="{{ csrf_token() }}"> --}}
                    <div class="row">
                        <div class="col-md-3 form-group">
                            <label>Dari</label>
                            <input class="form-control datepicker" placeholder="yyyy-mm-dd" id="from" name="from" value="{{ Carbon\Carbon::createFromFormat('Y-m-d', $request->from)->format('Y-m-d') }}" autocomplete="off" />

                        </div>
                        <div class="col-md-3 form-group">

                            <label>Sampai</label>
                            <input class="form-control datepicker" placeholder="yyyy-mm-dd" id="to" name="to" value="{{ Carbon\Carbon::createFromFormat('Y-m-d', $request->to)->format('Y-m-d') }}" autocomplete="off" />
                        </div>
                    </div>

                    <div class="col-md-6 text-right" style="margin-top: 10px;">
                        <button type="submit" class="btn btn-primary" name="search" value="search"><span class="fa fa-search"></span> Search</button>
                        <a href="{{ route('buku-besar-resume-excel',['from' =>$request->from,'to' =>$request->to]) }}" class="btn btn-sm btn-success"><i class="fa fa-download"></i> Download Excel</a>
{{--                        <a href="{{ route('buku-besar-download-excel',['period' =>$request->period]) }}" class="btn btn-sm btn-success"><i class="fa fa-download"></i> Download Excel</a>--}}
{{--                        <a href="{{ route('buku-besar-download-pdf',['period' =>$request->period]) }}" class="btn btn-sm btn-info"><i class="fa fa-download"></i> Download PDF</a>--}}
                    </div>
                </form>
            </div>
        </div>

        @if ($request->search)
            @php
                $sumaktiva=0;
                $sumpasiva=0;
                $sumpendapatan=0;
                $sumbeban=0;
            @endphp
            <div class="card-body row">
                <div class="col-md-12 table-responsive">
                    <h5 class="text-center">Aktiva</h5>
                    <table class="table table-striped table-aktiva">
                        <thead>
                        <tr>
                            <th rowspan="2">Code</th>
                            <th rowspan="2">Nama</th>
                            <th style="width: 20%" colspan="2">Saldo Awal</th>
                            <th style="width: 20%" colspan="2">Trx</th>
                            <th style="width: 20%" colspan="2">Saldo Akhir</th>
                        </tr>
                        <tr>
                            <th class="text-right">dr</th>
                            <th class="text-right">cr</th>
                            <th class="text-right">dr</th>
                            <th class="text-right">cr</th>
                            <th class="text-right">dr</th>
                            <th class="text-right">cr</th>

                        </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                </div>
                <div class="col-md-12 table-responsive">
                    <h5 class="text-center">PASIVA</h5>
                    <table class="table table-striped table-passiva">
                        <thead>
                        <tr>
                            <th rowspan="2">Code</th>
                            <th rowspan="2">Nama</th>
                            <th style="width: 20%" colspan="2">Saldo Awal</th>
                            <th style="width: 20%" colspan="2">Trx</th>
                            <th style="width: 20%" colspan="2">Saldo Akhir</th>
                        </tr>
                        <tr>
                            <th class="text-right">dr</th>
                            <th class="text-right">cr</th>
                            <th class="text-right">dr</th>
                            <th class="text-right">cr</th>
                            <th class="text-right">dr</th>
                            <th class="text-right">cr</th>

                        </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                </div>
                <div class="col-md-12 table-responsive">
                    <h5 class="text-center">Pendapapan</h5>
                    <table class="table table-striped table-pendapatan">
                        <thead>
                        <tr>
                            <th rowspan="2">Code</th>
                            <th rowspan="2">Nama</th>
                            <th style="width: 20%" colspan="2">Saldo Awal</th>
                            <th style="width: 20%" colspan="2">Trx</th>
                            <th style="width: 20%" colspan="2">Saldo Akhir</th>
                        </tr>
                        <tr>
                            <th class="text-right">dr</th>
                            <th class="text-right">cr</th>
                            <th class="text-right">dr</th>
                            <th class="text-right">cr</th>
                            <th class="text-right">dr</th>
                            <th class="text-right">cr</th>

                        </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                </div>
                <div class="col-md-12 table-responsive">
                    <h5 class="text-center">Beban</h5>
                    <table class="table table-striped table-beban">
                        <thead>
                        <tr>
                            <th rowspan="2">Code</th>
                            <th rowspan="2">Nama</th>
                            <th style="width: 20%" colspan="2">Saldo Awal</th>
                            <th style="width: 20%" colspan="2">Trx</th>
                            <th style="width: 20%" colspan="2">Saldo Akhir</th>
                        </tr>
                        <tr>
                            <th class="text-right">dr</th>
                            <th class="text-right">cr</th>
                            <th class="text-right">dr</th>
                            <th class="text-right">cr</th>
                            <th class="text-right">dr</th>
                            <th class="text-right">cr</th>

                        </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                </div>




            </div>
            <div class="col-md-12 table-responsive"> <h5 class="text-center"></h5></div>
        @endif
    </div>
@endsection

@section('js')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js" integrity="sha256-bqVeqGdJ7h/lYPq6xrPv/YGzMEb6dNxlfiTUHSgRCp8=" crossorigin="anonymous"></script>
    <script>
        $(document).ready(function ()
        {
            initiateDatatables();
            $("#period").change(function(){
                console.log($('#period').val());
                console.log({{ $request->period }});
            });

            $('.datepicker').datepicker({
                format: "yyyy-mm-dd"
            });

            $('input.datepicker').bind('keyup keydown keypress', function (evt) {
                return true;
            });
            // initiateDatatables();
        });
        function initiateDatatables()
        {
            $.fn.dataTable.ext.errMode = 'none';
            var tableaktiva = $('.table-aktiva').DataTable({
                bProcessing: true,
                bServerSide: true,
                bPaginate: false,
                bSortClasses: false,
                ordering: false,
                searching: false,
                responsive: true,
                ajax: {
                    url: '{{ route('buku-besar-resume-ajax') }}',
                    dataSrc: 'data',
                    data: function(data){
                        @if(isset($request->from)) data.from = '{{ $request->from }}'; @endif
                        @if(isset($request->to)) data.to = '{{ $request->to }}'; @endif
                        @if(isset($request->search)) data.search = '{{ $request->search }}'; @endif
                        @if(isset($request->search)) data.search = '{{ $request->search }}'; @endif

                        data.code_type_id = '{{ CODE_TYPE_ACTIVA}}';
                    }
                },
                aoColumns: [

                    {
                        mData: 'CODE', sType: "string",
                        className: "dt-body-center", "name": "CODE",

                    },
                    {
                        mData: 'NAMA_TRANSAKSI', sType: "string",
                        className: "dt-body-left", "name": "NAMA_TRANSAKSI",

                    },
                    {
                        mData: 'awaldr', sType: "string",
                        className: "dt-body-right", "name": "awaldr"
                    },
                    {
                        mData: 'awalcr', sType: "string",
                        className: "dt-body-right", "name": "awalcr",
                    },
                    {
                        mData: 'trxdr', sType: "string",
                        className: "dt-body-right", "name": "trxdr"
                    },
                    {
                        mData: 'trxcr', sType: "string",
                        className: "dt-body-right", "name": "trxcr"
                    },
                    {
                        mData: 'akhirdr', sType: "string",
                        className: "dt-body-right", "name": "akhirdr"
                    },
                    {
                        mData: 'akhircr', sType: "string",
                        className: "dt-body-right", "name": "akhircr",
                    },

                    /*{
                        mData: 'created_by.name', sType: "string",
                        className: "dt-body-center", "name": "created_by.name",
                    },*/
                ],fnInitComplete: function(oSettings, json) {

                    var _that = this;

                    this.each(function(i) {
                        $.fn.dataTableExt.iApiIndex = i;
                        var $this = this;
                        var anControl = $('input', _that.fnSettings().aanFeatures.f);
                        anControl
                            .unbind('keyup search input')
                            .bind('keypress', function(e) {
                                if (e.which == 13) {
                                    $.fn.dataTableExt.iApiIndex = i;
                                    _that.fnFilter(anControl.val());
                                }
                            });
                        return this;
                    });
                    return this;
                },
                // drawCallback:function(settings)
                // {
                //     $('#totaldebet').html(toRupiah(settings.json.totaldebet));
                //     $('#totalkredit').html(toRupiah(settings.json.totalkredit));
                // }

            });
            var tablepassiva = $('.table-passiva').DataTable({
                bProcessing: true,
                bServerSide: true,
                bPaginate: false,
                bSortClasses: false,
                ordering: false,
                searching: false,
                responsive: true,
                ajax: {
                    url: '{{ route('buku-besar-resume-ajax') }}',
                    dataSrc: 'data',
                    data: function(data){
                        @if(isset($request->from)) data.from = '{{ $request->from }}'; @endif
                            @if(isset($request->to)) data.to = '{{ $request->to }}'; @endif
                            @if(isset($request->search)) data.search = '{{ $request->search }}'; @endif
                            @if(isset($request->search)) data.search = '{{ $request->search }}'; @endif

                            data.code_type_id = '{{ CODE_TYPE_PASSIVA}}';
                    }
                },
                aoColumns: [

                    {
                        mData: 'CODE', sType: "string",
                        className: "dt-body-center", "name": "CODE",

                    },
                    {
                        mData: 'NAMA_TRANSAKSI', sType: "string",
                        className: "dt-body-left", "name": "NAMA_TRANSAKSI",

                    },
                    {
                        mData: 'awaldr', sType: "string",
                        className: "dt-body-right", "name": "awaldr"
                    },
                    {
                        mData: 'awalcr', sType: "string",
                        className: "dt-body-right", "name": "awalcr",
                    },
                    {
                        mData: 'trxdr', sType: "string",
                        className: "dt-body-right", "name": "trxdr"
                    },
                    {
                        mData: 'trxcr', sType: "string",
                        className: "dt-body-right", "name": "trxcr"
                    },
                    {
                        mData: 'akhirdr', sType: "string",
                        className: "dt-body-right", "name": "akhirdr"
                    },
                    {
                        mData: 'akhircr', sType: "string",
                        className: "dt-body-right", "name": "akhircr",
                    },

                    /*{
                        mData: 'created_by.name', sType: "string",
                        className: "dt-body-center", "name": "created_by.name",
                    },*/
                ],fnInitComplete: function(oSettings, json) {

                    var _that = this;

                    this.each(function(i) {
                        $.fn.dataTableExt.iApiIndex = i;
                        var $this = this;
                        var anControl = $('input', _that.fnSettings().aanFeatures.f);
                        anControl
                            .unbind('keyup search input')
                            .bind('keypress', function(e) {
                                if (e.which == 13) {
                                    $.fn.dataTableExt.iApiIndex = i;
                                    _that.fnFilter(anControl.val());
                                }
                            });
                        return this;
                    });
                    return this;
                },
                // drawCallback:function(settings)
                // {
                //     $('#totaldebet').html(toRupiah(settings.json.totaldebet));
                //     $('#totalkredit').html(toRupiah(settings.json.totalkredit));
                // }

            });
            var tablependapatan = $('.table-pendapatan').DataTable({
                bProcessing: true,
                bServerSide: true,
                bPaginate: false,
                bSortClasses: false,
                ordering: false,
                searching: false,
                responsive: true,
                ajax: {
                    url: '{{ route('buku-besar-resume-ajax') }}',
                    dataSrc: 'data',
                    data: function(data){
                        @if(isset($request->from)) data.from = '{{ $request->from }}'; @endif
                            @if(isset($request->to)) data.to = '{{ $request->to }}'; @endif
                            @if(isset($request->search)) data.search = '{{ $request->search }}'; @endif
                            @if(isset($request->search)) data.search = '{{ $request->search }}'; @endif

                            data.code_type_id = '{{ CODE_TYPE_LABA}}';
                    }
                },
                aoColumns: [

                    {
                        mData: 'CODE', sType: "string",
                        className: "dt-body-center", "name": "CODE",

                    },
                    {
                        mData: 'NAMA_TRANSAKSI', sType: "string",
                        className: "dt-body-left", "name": "NAMA_TRANSAKSI",

                    },
                    {
                        mData: 'awaldr', sType: "string",
                        className: "dt-body-right", "name": "awaldr"
                    },
                    {
                        mData: 'awalcr', sType: "string",
                        className: "dt-body-right", "name": "awalcr",
                    },
                    {
                        mData: 'trxdr', sType: "string",
                        className: "dt-body-right", "name": "trxdr"
                    },
                    {
                        mData: 'trxcr', sType: "string",
                        className: "dt-body-right", "name": "trxcr"
                    },
                    {
                        mData: 'akhirdr', sType: "string",
                        className: "dt-body-right", "name": "akhirdr"
                    },
                    {
                        mData: 'akhircr', sType: "string",
                        className: "dt-body-right", "name": "akhircr",
                    },

                    /*{
                        mData: 'created_by.name', sType: "string",
                        className: "dt-body-center", "name": "created_by.name",
                    },*/
                ],fnInitComplete: function(oSettings, json) {

                    var _that = this;

                    this.each(function(i) {
                        $.fn.dataTableExt.iApiIndex = i;
                        var $this = this;
                        var anControl = $('input', _that.fnSettings().aanFeatures.f);
                        anControl
                            .unbind('keyup search input')
                            .bind('keypress', function(e) {
                                if (e.which == 13) {
                                    $.fn.dataTableExt.iApiIndex = i;
                                    _that.fnFilter(anControl.val());
                                }
                            });
                        return this;
                    });
                    return this;
                },
                // drawCallback:function(settings)
                // {
                //     $('#totaldebet').html(toRupiah(settings.json.totaldebet));
                //     $('#totalkredit').html(toRupiah(settings.json.totalkredit));
                // }

            });
            var tablebeban = $('.table-beban').DataTable({
                bProcessing: true,
                bServerSide: true,
                bPaginate: false,
                bSortClasses: false,
                ordering: false,
                searching: false,
                responsive: true,
                ajax: {
                    url: '{{ route('buku-besar-resume-ajax') }}',
                    dataSrc: 'data',
                    data: function(data){
                        @if(isset($request->from)) data.from = '{{ $request->from }}'; @endif
                            @if(isset($request->to)) data.to = '{{ $request->to }}'; @endif
                            @if(isset($request->search)) data.search = '{{ $request->search }}'; @endif
                            @if(isset($request->search)) data.search = '{{ $request->search }}'; @endif

                            data.code_type_id = '{{ CODE_TYPE_RUGI}}';
                    }
                },
                aoColumns: [

                    {
                        mData: 'CODE', sType: "string",
                        className: "dt-body-center", "name": "CODE",

                    },
                    {
                        mData: 'NAMA_TRANSAKSI', sType: "string",
                        className: "dt-body-left", "name": "NAMA_TRANSAKSI",

                    },
                    {
                        mData: 'awaldr', sType: "string",
                        className: "dt-body-right", "name": "awaldr"
                    },
                    {
                        mData: 'awalcr', sType: "string",
                        className: "dt-body-right", "name": "awalcr",
                    },
                    {
                        mData: 'trxdr', sType: "string",
                        className: "dt-body-right", "name": "trxdr"
                    },
                    {
                        mData: 'trxcr', sType: "string",
                        className: "dt-body-right", "name": "trxcr"
                    },
                    {
                        mData: 'akhirdr', sType: "string",
                        className: "dt-body-right", "name": "akhirdr"
                    },
                    {
                        mData: 'akhircr', sType: "string",
                        className: "dt-body-right", "name": "akhircr",
                    },

                    /*{
                        mData: 'created_by.name', sType: "string",
                        className: "dt-body-center", "name": "created_by.name",
                    },*/
                ],fnInitComplete: function(oSettings, json) {

                    var _that = this;

                    this.each(function(i) {
                        $.fn.dataTableExt.iApiIndex = i;
                        var $this = this;
                        var anControl = $('input', _that.fnSettings().aanFeatures.f);
                        anControl
                            .unbind('keyup search input')
                            .bind('keypress', function(e) {
                                if (e.which == 13) {
                                    $.fn.dataTableExt.iApiIndex = i;
                                    _that.fnFilter(anControl.val());
                                }
                            });
                        return this;
                    });
                    return this;
                },
                // drawCallback:function(settings)
                // {
                //     $('#totaldebet').html(toRupiah(settings.json.totaldebet));
                //     $('#totalkredit').html(toRupiah(settings.json.totalkredit));
                // }

            });



        }

        
    </script>
@endsection
