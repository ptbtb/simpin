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
            <li class="breadcrumb-item"><a href="">Pinjaman</a></li>
			<li class="breadcrumb-item"><a href="">List Pinjaman</a></li>
			<li class="breadcrumb-item active">Detail Pinjaman</li>
		</ol>
	</div>
</div>
@endsection

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
        <h6 style="font-weight: 600">{{ $title }}</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped">
                <tr>
                    <td>Nama</td>
                    <td>:</td>
                    <td>
                        @if ($pinjaman->anggota)
                            {{ $pinjaman->anggota->nama_anggota }}
                        @else
                            -
                        @endif
                    </td>
                    <td>Jenis Pinjaman</td>
                    <td>:</td>
                    <td>{{ $jenisPinjaman->nama_pinjaman }}</td>
                    <td>Besar Pinjaman</td>
                    <td>:</td>
                    <td>Rp. {{ number_format($pinjaman->besar_pinjam,0,",",".") }}</td>
                </tr>
                <tr>
                    <td>Tanggal Peminjaman</td>
                    <td>:</td>
                    <td>{{ $pinjaman->tgl_entri->format('d M Y') }}</td>
                    <td>Lama Angsuran</td>
                    <td>:</td>
                    <td>{{ $pinjaman->lama_angsuran }}</td>
                    <td>Besar Angsuran</td>
                    <td>:</td>
                    <td>Rp. {{ number_format($pinjaman->besar_angsuran,0,",",".") }}</td>
                </tr>
                <tr>
                    <td>Jatuh Tempo</td>
                    <td>:</td>
                    <td>{{ $pinjaman->tgl_tempo->format('d M Y') }}</td>
                    <td>Sisa Angsuran</td>
                    <td>:</td>
                    <td>{{ $pinjaman->sisa_angsuran }}</td>
                    <td>Sisa Pinjaman</td>
                    <td>:</td>
                    <td>Rp. {{ number_format($pinjaman->sisa_pinjaman,0,",",".") }}</td>
                </tr>
                <tr>
                    <td>Status</td>
                    <td>:</td>
                    <td class="font-weight-bold">{{ ucwords($pinjaman->statusPinjaman->name) }}</td>
                    <td>Administrasi</td>
                    <td>:</td>
                    <td>Rp. {{ number_format($pinjaman->biaya_administrasi,0,",",".") }}</td>
                    <td>Provisi</td>
                    <td>:</td>
                    <td>Rp. {{ number_format($pinjaman->biaya_provisi,0,",",".") }}</td>
                </tr>
            </table>
        </div>

        <div class="mt-3 p-2 box-custom">
            <div class="d-flex">
                <h6 style="font-weight: 600">Angsuran</h6>
                <a class="btn btn-sm btn-success ml-auto mb-2 btn-bayarAngsuran text-white"><i class="fas fa-plus"></i> Bayar Angsuran</a>
            </div>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Angsuran Ke</th>
                            <th>Angsuran Pokok</th>
                            <th>Jasa</th>
                            <th>Total Angsuran</th>
                            <th>Denda</th>
                            {{-- <th>Sisa Pinjaman</th> --}}
                            <th>Dibayar Pada Tanggal</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($listAngsuran as $angsuran)
                            <tr>
                                <td>{{ $angsuran->angsuran_ke }}</td>
                                <td>Rp. {{ number_format($angsuran->besar_angsuran,0,",",".") }}</td>
                                <td>Rp. {{ number_format($angsuran->jasa,0,",",".") }}</td>
                                <td>Rp. {{ number_format($angsuran->besar_angsuran + $angsuran->jasa,0,",",".") }}</td>
                                <td>Rp. {{ number_format($angsuran->denda,0,",",".") }}</td>
                                {{-- <td>Rp. {{ number_format($angsuran->sisa_pinjam,0,",",".") }}</td> --}}
                                <td>{{($angsuran->paid_at)?  $angsuran->paid_at->format('d M Y'):'-' }}</td>
                                <td>{{ $angsuran->statusAngsuran->name }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<div id="my-modal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
    <form action="{{ route('pinjaman-bayar-angsuran', ['id'=>$pinjaman->kode_pinjam]) }}" method="POST">
        @csrf
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5>Pembayaran Angsuran</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Bulan</label>
                        <input type="text" name="bulan" class="form-control" value="{{ ($tagihan->jatuh_tempo)? $tagihan->jatuh_tempo->format('d-m-Y'):'' }}" readonly>
                    </div>
                    <div class="form-group">
                        <label>Total Angsuran</label>
                        <input type="text" name="total_angsuran" class="form-control" value="Rp. {{ number_format($angsuran->besar_angsuran + $angsuran->jasa,0,",",".") }}" readonly>
                    </div>
                    <div class="form-group">
                        <label>Besar Pembayaran</label>
                        <input type="text" name="besar_pembayaran" class="form-control" placeholder="Besar Pembayaran">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">Submit</button>
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@section('js')
    <script>
        $('.btn-bayarAngsuran').on('click', function ()
        {
            $('#my-modal').modal({
                backdrop: false 
            });
            $('#my-modal').modal('show');
        });
    </script>
@endsection