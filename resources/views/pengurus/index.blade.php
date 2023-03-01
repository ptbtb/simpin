@extends('adminlte::page')

@section('plugins.Datatables', true)

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
                <li class="breadcrumb-item"><a href="">Pengurus</a></li>
            </ol>
        </div>
    </div>
@endsection

@section('css')
@endsection

@section('content')
    <div class="card">
        <div class="card-header text-right">
            @can('add pengurus')
                <a href="{{ route('pengurus.create') }}" class="btn btn-xs btn-success">
                    <i class="fa fa-plus"></i> Tambah Pengurus
                </a>
            @endcan
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped" id="tablePengurus">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama</th>
                            <th>Jabatan</th>
                            <th>Start</th>
                            <th>Expire</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($listPengurus as $pengurus)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $pengurus->nama }}</td>
                                <td>{{ ARRAY_JABATAN_PENGURUS[$pengurus->jabatan] }}</td>
                                <td>{{ $pengurus->start->format('d-M-Y') }}</td>
                                <td>{{ $pengurus->expired->format('d-M-Y') }}</td>
                                <td>
                                    @can('edit pengurus')
                                        <a href="{{ route('pengurus.edit', [$pengurus->id]) }}"
                                            class="btn btn-xs btn-warning"><i class="fa fa-edit"></i> Edit
                                        </a>
                                    @endcan
                                    @can('delete pengurus')
                                        <a class="btn btn-xs btn-danger btn-delete" data-id="{{ $pengurus->id }}">
                                            <i class="fa fa-trash"></i> Delete
                                        </a>
                                    @endcan
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@stop

@section('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
<script>
    var baseURL = {!! json_encode(url('/')) !!};
    $('#tablePengurus').DataTable();
    $(document).on('click', '.btn-delete', function () 
    {
        var id = $(this).data('id');
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes'
        }).then((result) => {
            if (result.isConfirmed) 
            {
                var url = "{{ route('pengurus.destroy', ['']) }}/"  + id;
                $.ajax({
                    type: "delete",
                    url: url,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (response) 
                    {
                        Swal.fire(
                            'Success',
                            'Data Updated',
                            'success'
                        );

                        setTimeout(location.reload.bind(location), 2000);
                    },
                    error: function (xhr, status, th) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: 'Something went wrong!'
                        })
                    }
                });
            }
        });
    })
</script>
@stop
