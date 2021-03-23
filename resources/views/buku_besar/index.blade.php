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
			<li class="breadcrumb-item active">Buku Besar</li>
		</ol>
	</div>
</div>
@endsection

@section('plugins.Datatables', true)

@section('css')
    <style>
        .btn-sm{
            font-size: .8rem;
        }
    </style>
@endsection

@section('content')
    <div class="card">
        <div class="card-body row">
            <div class="col-md-6 table-responsive">
                <h5 class="text-center">Aktiva</h5>
                <table class="table table-striped table-aktiva">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Code</th>
                            <th>Nama</th>
                            <th style="width: 35%">Saldo</th>
                        </tr>
                    </thead>
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
                            <th style="width: 40%">Saldo</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script>
        $(document).ready(function ()
        {
            initiateDatatables();
        });
        function initiateDatatables()
        {
            $.fn.dataTable.ext.errMode = 'none';
            var tableAktiva = $('.table-aktiva').DataTable({
                processing: true,
                serverside: true,
                paging:   false,
                ajax: {
                    url: '{{ route('buku-besar-list-ajax') }}',
                    dataSrc: 'data',
                    data: function(data){
                        data.code_type_id = {{ CODE_TYPE_ACTIVA }};
                    }
                },
                aoColumns: [
                    { 
                        mData: 'null', sType: "string", 
                        className: "dt-body-center", "name": "index"				
                    },
                    { 
                        mData: 'code', sType: "string", 
                        className: "dt-body-center", "name": "code"						
                    },
                    { 
                        mData: 'name', sType: "string", 
                        className: "dt-body-center", "name": "name"				
                    },
                    { 
                        mData: 'saldo', sType: "string", 
                        className: "dt-body-center text-right", "name": "saldo",
                        mRender: function(data, type, full) 
                        {
                            var saldo = toRupiah(data);
                            return saldo;
                        }			
                    },
                ]
            });

            var tablePassiva = $('.table-passiva').DataTable({
                processing: true,
                serverside: true,
                paging:   false,
                ajax: {
                    url: '{{ route('buku-besar-list-ajax') }}',
                    dataSrc: 'data',
                    data: function(data){
                        data.code_type_id = {{ CODE_TYPE_PASSIVA }};
                    }
                },
                aoColumns: [
                    { 
                        mData: 'null', sType: "string", 
                        className: "dt-body-center", "name": "index"				
                    },
                    { 
                        mData: 'code', sType: "string", 
                        className: "dt-body-center", "name": "code"						
                    },
                    { 
                        mData: 'name', sType: "string", 
                        className: "dt-body-center", "name": "name"				
                    },
                    { 
                        mData: 'saldo', sType: "string", 
                        className: "dt-body-center text-right", "name": "saldo",
                        mRender: function(data, type, full) 
                        {
                            var saldo = toRupiah(data);
                            return saldo;
                        }			
                    },
                ]
            });

            // add index column
            tableAktiva.on( 'order.dt search.dt', function () {
                tableAktiva.column(0, {search:'applied', order:'applied'}).nodes().each( function (cell, i) {
                    cell.innerHTML = i+1;
                } );
            }).draw();

            // add index column
            tablePassiva.on( 'order.dt search.dt', function () {
                tablePassiva.column(0, {search:'applied', order:'applied'}).nodes().each( function (cell, i) {
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