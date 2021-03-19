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
			<li class="breadcrumb-item active">Jurnal</li>
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
        <div class="card-header">
            <label class="m-0">Filter</label>
        </div>
        <div class="card-body">
            <form action="{{ route('jurnal-list') }}" method="post">
                @csrf
                <div class="row">
                    <div class="col-md-4 form-group">
                        <label>Tipe Jurnal</label>
                        {!! Form::select('id_tipe_jurnal', $tipeJurnal, $request->id_tipe_jurnal, ['class' => 'form-control', 'placeholder' => 'All']) !!}
                    </div>
                    <div class="col-md-12 form-group text-center">
                        <button type="submit" class="btn btn-sm btn-success"><i class="fa fa-filter"></i> Filter</button>
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
                        <th>Tipe Jurnal</th>
                        <th>Akun Kredit</th>
                        <th style="width: 10%">Kredit</th>
                        <th>Akun Debet</th>
                        <th style="width: 10%">Debet</th>
                        <th>Keterangan</th>
                        <th>Created At</th>
                        <th>Updated By</th>
                    </tr>
                </thead>
            </table>
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
            var table = $('.table').DataTable({
                processing: true,
                serverside: true,
                ajax: {
                    url: '{{ route('jurnal-list-ajax') }}',
                    dataSrc: 'data',
                    data: function(data){
                        @if(isset($request->id_tipe_jurnal)) data.id_tipe_jurnal = '{{ $request->id_tipe_jurnal }}'; @endif
                    }
                },
                aoColumns: [
                    { 
                        mData: 'null', sType: "string", 
                        className: "dt-body-center", "name": "index"				
                    },
                    { 
                        mData: 'nomer', sType: "string", 
                        className: "dt-body-center", "name": "nomer"						
                    },
                    { 
                        mData: 'tipe_jurnal.name', sType: "string", 
                        className: "dt-body-center", "name": "tipe_jurnal.name"				
                    },
                    { 
                        mData: 'akun_kredit', sType: "string", 
                        className: "dt-body-center", "name": "akun_kredit"				
                    },
                    { 
                        mData: 'kredit', sType: "string", 
                        className: "dt-body-center", "name": "kredit",
                        mRender: function(data, type, full) 
                        {
                            var kredit = toRupiah(data);
                            return kredit;
                        }			
                    },
                    { 
                        mData: 'akun_debet', sType: "string", 
                        className: "dt-body-center", "name": "akun_debet",				
                    },
                    { 
                        mData: 'debet', sType: "string", 
                        className: "dt-body-center", "name": "debet",
                        mRender: function(data, type, full) 
                        {
                            var debet = toRupiah(data);
                            return debet;
                        }			
                    },
                    { 
                        mData: 'keterangan', sType: "string", 
                        className: "dt-body-center", "name": "keterangan",				
                    },
                    { 
                        mData: 'view_created_at', sType: "string", 
                        className: "dt-body-center", "name": "view_created_at",				
                    },
                    { 
                        mData: 'created_by.name', sType: "string", 
                        className: "dt-body-center", "name": "created_by.name",				
                    },
                ]
            });

            // add index column
            table.on( 'order.dt search.dt', function () {
                table.column(0, {search:'applied', order:'applied'}).nodes().each( function (cell, i) {
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