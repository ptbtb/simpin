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
            <li class="breadcrumb-item"><a href="">Simpanan</a></li>
            <li class="breadcrumb-item active">PendingJurnal</li>
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
         <form action="{{ route('simpanan-pending-jurnal') }}" method="post">
                @csrf
        <div class="row">
            <div class="col-md-3">
                <label>Dari</label>
                <input class="form-control datepicker" placeholder="dd-mm-yyyy" id="from" name="from" value="{{ Carbon\Carbon::createFromFormat('d-m-Y', $request->from)->format('d-m-Y') }}" autocomplete="off" />
            </div>
            <div class="col-md-3">
                <label>Sampai</label>
                <input class="form-control datepicker" placeholder="mm-yyyy" id="to" name="to" value="{{ Carbon\Carbon::createFromFormat('d-m-Y', $request->to)->format('d-m-Y') }}" autocomplete="off" />
            </div>
        </div>
        <div class="row">
        <div class="col-md-12 mt-1 form-group text-center">
                        <button type="submit" class="btn btn-sm btn-success"><i class="fa fa-filter"></i> Filter</button>
                    </div>
                </div>
    </form>
        
    </div>
    

    <div class="card-body table-responsive">
        <div class="row">
           @if (auth()->user()->can('posting jurnal'))
           <form id="form-approve" action="{{ route('simpanan-post-jurnal') }}" method="post">
               {{ csrf_field() }}
               <button class="btn btn-success btn-sm" type="submit"><i class="fa fa-check-square"></i>Posting Jurnal</button>
               
           </form>
           @endif
           <div class="clearfix">&numsp; </div>
           
            <i class="buffBTT">&numsp;</i>
            
        
        </div>
        <table class="table table-striped cell-border">
            <thead>
                <tr class="info">
                    <th><input type="checkbox" name="select_all" value="1" id="select-all"></th>
                    <th>No</th>
                    <th>Nama Anggota</th>
                    <th>Jenis Simpanan</th>
                    <th>Periode</th>
                    <th>Besar Simpanan</th>
                    <th>User Entry</th>
                    <th>Status</th>
                    <th>Posting</th>
                    <th>Input</th>
                    

                </tr>
            </thead>
            <tbody >
            	@foreach ($list as $item)
            	<tr>
            	<td><input type="checkbox" name="kode_simpan[]" value="{{ $item->kode_simpan }}"></td>
            	<td>{{ $loop->iteration }}</td>
            	<td>{{ ($item->anggota)?$item->anggota->nama_anggota:'-' }}</td>
            	<td>{{ $item->jenis_simpan }}</td>
            	<td>{{ $item->periode_view }}</td>
            	<td>{{ $item->besar_simpanan_rupiah }}</td>
            	<td>{{ $item->u_entry }}</td>
            	<td>{{ $item->status_simpanan_view }}</td>
            	<td>{{ $item->tgl_transaksi }}</td>
            	<td>{{ $item->tanggal_entri }}</td>
            	</tr>
            	@endforeach
            </tbody>


        </table>
    </div>
</div>
@endsection

@section('js')
<script src="https://unpkg.com/gijgo@1.9.13/js/gijgo.min.js" type="text/javascript"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js" integrity="sha256-bqVeqGdJ7h/lYPq6xrPv/YGzMEb6dNxlfiTUHSgRCp8=" crossorigin="anonymous"></script>
<script>
var baseURL = {!! json_encode(url('/')) !!};
var table = $('.table').DataTable();
// add index column

$(document).ready(function ()
        {
        table.buttons().container() .insertAfter( '.buffBTT' );
        $('#select-all').on('click', function(){
   // Get all rows with search applied
   var rows = table.rows({ 'search': 'applied' }).nodes();
   // Check/uncheck checkboxes for all rows in the table
   $('input[type="checkbox"]', rows).prop('checked', this.checked);
});
$('.table tbody').on('change', 'input[type="checkbox"]', function(){
	// If checkbox is not checked
   if(!this.checked){
      var el = $('#select-all').get(0);
      // If "Select all" control is checked and has 'indeterminate' property
      if(el && el.checked && ('indeterminate' in el)){
         // Set visual state of "Select all" control
         // as 'indeterminate'
         el.indeterminate = true;
      }
   }
});

$('#form-approve').on('submit', function(e){
   var form = this;

   // Iterate over all checkboxes in the table
   table.$('input[type="checkbox"]').each(function(){
         if(this.checked){
            // Create a hidden element
            $(form).append(
               $('<input>')
                  .attr('type', 'hidden')
                  .attr('name', this.name)
                  .val(this.value)
            );
         }
   });
});
$('.datepicker').datepicker({
        format: "dd-mm-yyyy"
    });

    $('input.datepicker').bind('keyup keydown keypress', function (evt) {
        return true;
    });
});

</script>
@endsection