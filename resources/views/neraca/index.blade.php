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
			<li class="breadcrumb-item active">Neraca</li>
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
                <form id="myForm" role="form" method="GET" enctype="multipart/form-data" action="{{ route('neraca-list') }}">
                    {{-- <input type="hidden" name="_token" value="{{ csrf_token() }}"> --}}

                    <div class="col-md-6">
                        <label>Periode</label>
                        <input class="form-control datepicker" placeholder="yyyy-mm-dd" id="period" name="period" value="{{ Carbon\Carbon::createFromFormat('Y-m-d', $request->period)->format('Y-m-d') }}" autocomplete="off" />
                    </div>
                    <div class="col-md-12 text-center" style="margin-top: 10px;">
                        <button type="submit" name="search" value="search" class="btn btn-primary"><span class="fa fa-search"></span> Search</button>
                        <a href="{{ route('neraca-download-pdf', ['period' => $request->period]) }}" class="btn btn-info"><i class="fas fa-print"></i> Print</a>
                        <a href="{{ route('neraca-download-excel', ['period' => $request->period]) }}" class="btn btn-success"><i class="fa fa-download"></i> Excel</a>
                    </div>
                </form>
            </div>
        </div>

        @if ($request->search)
            <div class="card-body row">
                <div class="col-md-6 table-responsive">
                    <h5 class="text-center">Aktiva</h5>
                    <table class="table table-striped table-aktiva">
                        <thead>
                            <tr>
{{--                                <th></th>--}}
                                <th>Rek</th>
                                <th style="width: 30%">Nama Rekening</th>
                                <th>Bulan ini</th>
                                <th >Bulan lalu</th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                        <tfoot>
                        <tr>
                            <th colspan="2"></th>
                            <th id="totalaktiva"></th>
                            <th id="totalaktivalalu"></th>
                        </tr>
                        </tfoot>
                    </table>
                </div>


                <div class="col-md-6 table-responsive">
                    <h5 class="text-center">Passiva</h5>
                    <table class="table table-striped table-passiva">
                        <thead>
                        <tr>
{{--                            <th></th>--}}
                            <th>Rek</th>
                            <th style="width: 30%">Nama Rekening</th>
                            <th>Bulan ini</th>
                            <th >Bulan lalu</th>
                        </tr>
                        </thead>
                        <tbody>

                        </tbody>
                        <tfoot>
                        <tr>
                            <th colspan="2"></th>
                            <th id="totalpassiva"></th>
                            <th id="totalpassivalalu"></th>

                        </tr>
                        </tfoot>
                    </table>
                </div>

            </div>
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
                return false;
            });
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
                    url: '{{ route('neraca-list-ajax') }}',
                    dataSrc: 'data',
                    data: function(data){
                        @if(isset($request->period)) data.period = '{{ $request->period }}'; @endif
                            @if(isset($request->search)) data.search = '{{ $request->search }}'; @endif
                           data.code_type_id =1;
                    }
                },
                aoColumns: [
                    // {
                    //     mData: 'Kategori', sType: "string",
                    //     className: "dt-body-center", "name": "Kategori",
                    //
                    // },

                    {
                        mData: 'CODE', sType: "string",
                        className: "dt-body-center", "name": "CODE",

                    },
                    {
                        mData: 'NAMA_TRANSAKSI', sType: "string",
                        className: "dt-body-left", "name": "NAMA_TRANSAKSI",

                    },
                    {
                        mData: 'saldo', sType: "string",
                        className: "dt-body-right", "name": "saldo",
                        mRender: function(data, type, full)
                        {

                            if(data)
                            {
                                return toRupiah(data);
                            }else{
                                return toRupiah('0');
                            }
                        }
                    },
                    {
                        mData: 'saldoLalu', sType: "string",
                        className: "dt-body-right", "name": "saldoLalu",
                        mRender: function(data, type, full)
                        {

                            if(data)
                            {
                                return toRupiah(data);
                            }else{
                                return toRupiah('0');
                            }
                        }
                    },




                    /*{
                        mData: 'created_by.name', sType: "string",
                        className: "dt-body-center", "name": "created_by.name",
                    },*/
                ],
                drawCallback:function(settings)
                {
                    $('#totalaktiva').html(toRupiah(settings.json.total));
                    $('#totalaktivalalu').html(toRupiah(settings.json.totallalu));
                },
                fnInitComplete: function(oSettings, json) {

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
                    url: '{{ route('neraca-list-ajax') }}',
                    dataSrc: 'data',
                    data: function(data){
                        @if(isset($request->period)) data.period = '{{ $request->period }}'; @endif
                            @if(isset($request->search)) data.search = '{{ $request->search }}'; @endif
                           data.code_type_id = 2;
                    }
                },
                aoColumns: [
                    // {
                    //     mData: 'Kategori', sType: "string",
                    //     className: "dt-body-center", "name": "Kategori",
                    //
                    // },

                    {
                        mData: 'CODE', sType: "string",
                        className: "dt-body-center", "name": "CODE",

                    },
                    {
                        mData: 'NAMA_TRANSAKSI', sType: "string",
                        className: "dt-body-left", "name": "NAMA_TRANSAKSI",

                    },
                    {
                        mData: 'saldo', sType: "string",
                        className: "dt-body-right", "name": "saldo",
                        mRender: function(data, type, full)
                        {

                            if(data)
                            {
                                return toRupiah(data);
                            }else{
                                return toRupiah('0');
                            }
                        }
                    },
                    {
                        mData: 'saldoLalu', sType: "string",
                        className: "dt-body-right", "name": "saldoLalu",
                        mRender: function(data, type, full)
                        {

                            if(data)
                            {
                                return toRupiah(data);
                            }else{
                                return toRupiah('0');
                            }
                        }
                    },




                    /*{
                        mData: 'created_by.name', sType: "string",
                        className: "dt-body-center", "name": "created_by.name",
                    },*/
                ],
                drawCallback:function(settings)
                {
                    $('#totalpassiva').html(toRupiah(settings.json.total));
                    $('#totalpassivalalu').html(toRupiah(settings.json.totallalu));
                },
                fnInitComplete: function(oSettings, json) {

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
        function toRupiah(number)
        {
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
                if (temp%3 == 0 && temp > 0)
                {
                    res = res + ".";
                }
            }
            if(splitStringNumber[1] !== 'undefined')
            {
                res = res + ','+splitStringNumber[1];
            }
            return res;
        }
    </script>
@endsection
