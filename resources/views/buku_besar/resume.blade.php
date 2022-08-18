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
                <div class="col-md-6 table-responsive">
                    <h5 class="text-center">Aktiva</h5>
                    <table class="table table-striped table-aktiva">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>Code</th>
                            <th>Nama</th>
                            <th style="width: 20%">Saldo Awal</th>
                            <th style="width: 20%">Trx</th>
                            <th style="width: 20%">Saldo Akhir</th>
                        </tr>
                        </thead>
                        <tbody>

                        @foreach($codes->where('code_type_id',CODE_TYPE_ACTIVA)->sortBy('CODE') as $item)

                            <tr>
                                <td></td>
                                <td><a href="{{ route('jurnal-list',['code'=>$item->CODE ,'from'=> $request->from,'to'=> $request->to]) }}" target="_blank">{{ $item->CODE}}</a></td>
                                <td>{{$item->NAMA_TRANSAKSI}}</td>
                                <td class="text-right">{{ number_format($item->awal, 0, ',', '.') }}</td>
                                <td class="text-right">{{ number_format($item->trx, 0, ',', '.') }}</td>
                                <td class="text-right">{{ number_format($item->akhir, 0, ',', '.') }}</td>
                            </tr>

                        @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="col-md-6 table-responsive">
                    <h5 class="text-center">Passiva</h5>
                    <table class="table table-striped table-passiva">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>Code</th>
                            <th>Nama</th>
                            <th style="width: 20%">Saldo Awal</th>
                            <th style="width: 20%">Trx</th>
                            <th style="width: 20%">Saldo Akhir</th>
                        </tr>
                        </thead>
                        <tbody>

                        @foreach($codes->where('code_type_id',CODE_TYPE_PASSIVA)->sortBy('CODE') as $item)

                            <tr>
                                <td></td>
                                <td><a href="{{ route('jurnal-list',['code'=>$item->CODE ,'from'=> $request->from,'to'=> $request->to]) }}" target="_blank">{{ $item->CODE}}</a></td>
                                <td>{{$item->NAMA_TRANSAKSI}}</td>
                                <td class="text-right">{{ number_format($item->awal, 0, ',', '.') }}</td>
                                <td class="text-right">{{ number_format($item->trx, 0, ',', '.') }}</td>
                                <td class="text-right">{{ number_format($item->akhir, 0, ',', '.') }}</td>
                            </tr>

                        @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="col-md-6 table-responsive">
                    <h5 class="text-center">Pendapatan</h5>
                    <table class="table table-striped table-laba">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>Code</th>
                            <th>Nama</th>
                            <th style="width: 20%">Saldo Awal</th>
                            <th style="width: 20%">Trx</th>
                            <th style="width: 20%">Saldo Akhir</th>
                        </tr>
                        </thead>
                        <tbody>

                        @foreach($codes->where('code_type_id',CODE_TYPE_LABA)->sortBy('CODE') as $item)

                            <tr>
                                <td></td>
                                <td><a href="{{ route('jurnal-list',['code'=>$item->CODE ,'from'=> $request->from,'to'=> $request->to]) }}" target="_blank">{{ $item->CODE}}</a></td>
                                <td>{{$item->NAMA_TRANSAKSI}}</td>
                                <td class="text-right">{{ number_format($item->awal, 0, ',', '.') }}</td>
                                <td class="text-right">{{ number_format($item->trx, 0, ',', '.') }}</td>
                                <td class="text-right">{{ number_format($item->akhir, 0, ',', '.') }}</td>
                            </tr>

                        @endforeach

                        </tbody>
                    </table>
                </div>
                <div class="col-md-6 table-responsive">
                    <h5 class="text-center">Beban</h5>
                    <table class="table table-striped table-rugi">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>Code</th>
                            <th>Nama</th>
                            <th style="width: 20%">Saldo Awal</th>
                            <th style="width: 20%">Trx</th>
                            <th style="width: 20%">Saldo Akhir</th>
                        </tr>
                        </thead>
                        <tbody>


                        @foreach($codes->where('code_type_id',CODE_TYPE_RUGI)->sortBy('CODE') as $item)

                            <tr>
                                <td></td>
                                <td><a href="{{ route('jurnal-list',['code'=>$item->CODE ,'from'=> $request->from,'to'=> $request->to]) }}" target="_blank">{{ $item->CODE}}</a></td>
                                <td>{{$item->NAMA_TRANSAKSI}}</td>
                                <td class="text-right">{{ number_format($item->awal, 0, ',', '.') }}</td>
                                <td class="text-right">{{ number_format($item->trx, 0, ',', '.') }}</td>
                                <td class="text-right">{{ number_format($item->akhir, 0, ',', '.') }}</td>
                            </tr>

                        @endforeach
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






            // add index column
            tableAktiva.on( 'xhr.dt', function () {
                tableAktiva.column(0, {search:'applied', order:'applied'}).nodes().each( function (cell, i) {
                    cell.innerHTML = i+1;
                } );
            }).draw();

            // add index column
            tableAktiva.on( 'xhr.dt', function () {
                tableAktiva.column(0, {search:'applied', order:'applied'}).nodes().each( function (cell, i) {
                    cell.innerHTML = i+1;
                } );
            }).draw();

            // add index column
            tableRugi.on( 'xhr.dt', function () {
                tableRugi.column(0, {search:'applied', order:'applied'}).nodes().each( function (cell, i) {
                    cell.innerHTML = i+1;
                } );
            }).draw();

            // add index column
            tableLaba.on( 'xhr.dt', function () {
                tableLaba.column(0, {search:'applied', order:'applied'}).nodes().each( function (cell, i) {
                    cell.innerHTML = i+1;
                } );
            }).draw();
        }

        function toRupiah(number)
        {
            var stringNumber = number.toString();
            var length = stringNumber.length;
            var temp = length;
            var res = "Rp ";
            for (let i = 0; i < length; i++) {
                res = res + stringNumber.charAt(i);
                temp--;
                if (temp%3 == 0 && temp > 0)
                {
                    res = res + ".";
                }
            }
            return res;
        }
    </script>
@endsection
