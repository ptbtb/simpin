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
                <li class="breadcrumb-item"><a href="">Angsuran</a></li>
                <li class="breadcrumb-item active">List Angsuran</li>
            </ol>
        </div>
    </div>
@endsection

@section('plugins.Datatables', true)

@section('css')
    <link href="https://unpkg.com/gijgo@1.9.13/css/gijgo.min.css" rel="stylesheet" type="text/css" />
    <style>
        .btn-sm {
            font-size: .8rem;
        }

        .box-custom {
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
            <form action="{{ route('angsuran-list') }}" method="post">
                @csrf
                <input type="hidden" name="status" value="belum lunas">
                <div class="row">
                    <div class="col-md-4 form-group">
                        <label>Kode Pinjam</label>
                        {!! Form::select('kode_pinjam', $kodepinjam, $request->kode_pinjam, [
                            'id' => 'kode_pinjam',
                            'class' => 'form-control',
                            'placeholder' => 'Semua',
                        ]) !!}
                    </div>
                    <div class="col-md-4 form-group">
                        <label>From</label>
                        <input id="from" type="text" name="from" class="form-control datepicker"
                            placeholder="yyyy-mm-dd" value="{{ $request->from ? $request->from : '' }}">
                    </div>
                    <div class="col-md-4 form-group">
                        <label>To</label>
                        <input id="to" type="text" name="to" class="form-control datepicker"
                            placeholder="yyyy-mm-dd" value="{{ $request->to ? $request->to : '' }}">
                    </div>
                    <div class="col-md-1 form-group" style="margin-top: 26px">
                        <button type="submit" class="btn btn-sm btn-success form-control"><i class="fa fa-filter"></i>
                            Filter</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <div class="card">
        <div class="card-header text-right">
            {{-- <a href="{{ route('pinjaman-download-pdf', ['from' => $request->from,'to' => $request->to,'status' => STATUS_PINJAMAN_BELUM_LUNAS,'unit_kerja' => $request->unit_kerja,'tenor' => $request->tenor]) }}"
                class="btn btn-info btn-sm"><i class="fa fa-download"></i> Download PDF</a>
            <a href="{{ route('pinjaman-download-excel', ['from' => $request->from,'to' => $request->to,'status' => STATUS_PINJAMAN_BELUM_LUNAS,'unit_kerja' => $request->unit_kerja,'tenor' => $request->tenor]) }}"
                class="btn btn-sm btn-success"><i class="fa fa-download"></i> Download Excel</a> --}}
        </div>
        <div class="card-body table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Kode Pinjam</th>
                        <th>Anggota</th>
                        <th>Angsuran Ke</th>
                        <th>Besar Angsuran</th>
                        <th>Denda</th>
                        <th>Jasa</th>
                        <th>Diskon</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($listAngsuran as $angsuran)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $angsuran->kode_pinjam }}</td>
                            @if (\Auth::user()->roles()->first()->id != ROLE_ANGGOTA)
                                <td>
                                    @if ($angsuran->anggota)
                                            {{ $angsuran->anggota->kode_anggota . "-" . $angsuran->anggota->nama_anggota }}
                                        @else
                                            -
                                        @endif
                                </td>
                            @endif
                            <td>{{ $angsuran->angsuran_ke }}</td>
                            <td>{{ $angsuran->besar_angsuran }}</td>
                            <td>{{ $angsuran->denda }}</td>
                            <td>{{ $angsuran->jasa }}</td>
                            <td>{{ $angsuran->diskon }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div id="my-modal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5>Detail Pinjaman</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modal-default">
        <form method="post" id="formedit" action="{{ route('edit-saldo-awalpinjaman') }}">
            @csrf
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Edit Saldo Awal</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <h6 class="kode_anggota"></h6>
                        <h6 class="nama_anggota"></h6>
                        <div class="form-group">
                            <label for="exampleInputBorder">Saldo Mutasi</label>
                            <input type="text" name=saldo_mutasi class="form-control form-control-border nominal"
                                id="saldo_mutasi">
                            <input type="hidden" name=kode_pinjam class="form-control form-control-border "
                                id="kode_pinjam">
                        </div>
                    </div>
                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary buttonedit">update</button>
                    </div>
                </div>
                <!-- /.modal-content -->
            </div>
            <!-- /.modal-dialog -->
        </form>
    </div>
@endsection

@section('js')
    <script src="https://unpkg.com/gijgo@1.9.13/js/gijgo.min.js" type="text/javascript"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    <script src="{{ asset('js/cleave.min.js') }}"></script>
    <script src="{{ asset('js/moment.js') }}"></script>
    <script>
        var baseURL = {!! json_encode(url('/')) !!};
        $('.openedit').click(function() {
            $('.kode_anggota').html($(this).data('kode'));
            $('.nama_anggota').html($(this).data('nama'));
            $('#saldo_mutasi').val($(this).data('jumlah'));
            $('#kode_pinjam').val($(this).data('id'));
            $('.nominal').toArray().forEach(function(field) {
                toRupiah(field);
            });
            // show Modal
            $('#modal-default').modal('show');
        });
        $('#from').datepicker({
            uiLibrary: 'bootstrap4',
            format: 'yyyy-mm-dd'
        });
        $('#to').datepicker({
            uiLibrary: 'bootstrap4',
            format: 'yyyy-mm-dd'
        });
        $('.table').DataTable();
        $('.nominal').toArray().forEach(function(field) {
            toRupiah(field);
        });

        $('#formedit').on('submit', function(e) {
            e.preventDefault();

            let kode_pinjam = $('#kode_pinjam').val();
            let saldo_mutasi = $('#saldo_mutasi').val();


            $.ajax({
                url: "{{ route('edit-saldo-awalpinjaman') }}",
                type: "POST",
                data: {
                    "_token": "{{ csrf_token() }}",
                    kode_pinjam: kode_pinjam,
                    saldo_mutasi: saldo_mutasi,
                },
                success: function(data, status, xhr) {
                    htmlText = data;
                    Swal.fire({
                        title: 'Update Sukses',

                        icon: "info",
                        showCancelButton: false,
                        confirmButtonText: "Ok",
                        confirmButtonColor: "#00a65a",
                        grow: 'row',
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        allowEnterKey: false,
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $('#modal-default').modal('hide');
                            location.reload();

                        }
                    });
                },
                error: function(xhr, status, error) {
                    Swal.fire({
                        title: 'Error',

                        icon: "error",
                        showCancelButton: false,
                        confirmButtonText: "Ok",
                        confirmButtonColor: "#00a65a",
                        grow: 'row',
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        allowEnterKey: false,
                    }).then((result) => {
                        if (result.value) {}
                    });
                }
            });
        });




        $('.table').on('click', 'a', function() {
            var data_id = $(this).data('id');
            var data_action = $(this).data('action');
            var url = baseURL + '/pinjaman/delete/' + data_id;
            var token = $(this).data('token');
            if (data_action == 'delete') {
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    input: 'password',
                    inputAttributes: {
                        name: 'password',
                        placeholder: 'Password',
                        required: 'required',
                    },
                    validationMessage: 'Password required',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes'
                }).then((result) => {
                    if (result.isConfirmed) {
                        var password = result.value;
                        url = url + '?pw=' + password
                        $.ajax({
                            url: url,
                            type: 'delete',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
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
                        });
                    }
                });
            }
        });

        function toRupiah(field) {
            new Cleave(field, {
                numeralDecimalMark: ',',
                delimiter: '.',
                numeral: true,
                numeralThousandsGroupStyle: 'thousand',
            });
        }

        var today = new Date(new Date().getFullYear(), new Date().getMonth(), new Date().getDate());
        $('.datepicker').datepicker({
            format: 'dd-mm-yyyy',
            uiLibrary: 'bootstrap4'
        });
    </script>
@endsection