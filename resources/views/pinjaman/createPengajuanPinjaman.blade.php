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
            <li class="breadcrumb-item"><a href="{{ route('pengajuan-pinjaman-list') }}">Pengajuan Pinjaman</a></li>
			<li class="breadcrumb-item active">Create</li>
		</ol>
	</div>
</div>
@endsection

@section('plugins.Select2', true)
@section('plugins.Sweetalert2', true)

@section('content')
{{-- <div class="card">
    <div class="card-body">
        <h6 class="m-0" style="font-weight: 700; font-size: 14px">Informasi Anggota</h6>
        <table class="table table-stripped">
            <tr>
                <th style="width: 12%">Kode Anggota</th>
                <td style="width: 1%">:</td>
                <td style="20%">{{ Auth::user()->anggota->kode_anggota }}</td>
                <th style="width: 12%">Nama Anggota</th>
                <td style="width: 1%">:</td>
                <td>{{ Auth::user()->anggota->nama_anggota  }}</td>
                <th style="width: 12%">Kelas Company</th>
                <td style="width: 1%">:</td>
                <td>{{ Auth::user()->anggota->nama_anggota  }}</td>
            </tr>
        </table>
    </div>
</div> --}}
<div class="card">
    <div class="card-body">
        <form action="{{ route('pengajuan-pinjaman-add') }}" method="post" enctype="multipart/form-data">
            @csrf
            <div class="row">
                @if (\Auth::user()->isAnggota())
                    <div class="col-md-6 form-group">
                        <label>Kode Anggota</label>
                        <input type="text" name="kode_anggota" id="anggotaName" class="form-control" readonly value="{{ Auth::user()->anggota->kode_anggota }}">
                    </div>
                    <div class="col-md-6 form-group">
                        <label>Nama Anggota</label>
                        <input type="text" name="nama_anggota" class="form-control" readonly value="{{ Auth::user()->anggota->nama_anggota }}">
                    </div>
                @else
                    <div class="col-md-6 form-group">
                        <label for="anggotaName">Anggota Name</label>
                        <select name="kode_anggota" id="anggotaName" class="form-control">
                        </select>
                    </div>
                @endif
                @if ($listPinjaman && $listPinjaman->count() > 0)
                    <div class="col-md-12 form-group">
                        <label>Jenis Pengajuan</label>
                        <select name="jenis_pengajuan" class="form-control" id="jenisPengajuan">
                            <option value="0">Pengajuan Pinjaman</option>
                            <option value="1">Top Up</option>
                        </select>
                    </div>
                    <div class="col-md-12 form-group" style="display: none" id="panelTopup">
                        <label>Topup Pinjaman</label>
                        <select name="topup_pinjaman[]" class="form-control select2" id="topupPinjaman" multiple>
                            @foreach ($listPinjaman as $pinjaman)
                                <option value="{{ $pinjaman->kode_pinjam }}">{{ $pinjaman->jenisPinjaman->nama_pinjaman }}</option>
                            @endforeach
                        </select>
                    </div>
                @endif
                <div class="col-md-6 form-group">
                    <label>Jenis Pinjaman</label>
                    <select name="jenis_pinjaman" class="form-control" required id="jenisPinjaman">
                        <option value="">Pilih Salah Satu</option>
                        @foreach ($listJenisPinjaman as $jenisPinjaman)
                            <option value="{!! $jenisPinjaman->kode_jenis_pinjam !!}">{{ $jenisPinjaman->nama_pinjaman }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6 form-group">
                    <label>Sumber Dana</label>
                    <select name="sumber_dana" class="form-control" required id="sumberDana">
                        @foreach ($sumberDana as $sumber)
                            <option value="{!! $sumber->id !!}">{{ $sumber->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6 form-group">
                    <label>Besar Pinjaman</label>
                    <input type="text" name="besar_pinjaman" class="form-control" placeholder="Besar Pinjaman"required id="besarPinjaman" readonly>
                    <label id="warningBesarPinjaman" style="color: red; font-size: 12px; font-weight: normal; display: none">Besar pinjaman melebihi maksimal pinjaman</label>
                </div>
                <div class="col-md-6 form-group">
                    <label>Maksimal Pinjaman</label>
                    <input type="text" name="maksimal_besar_pinjaman" class="form-control" readonly id="maksimalBesarPinjaman" placeholder="Maksimal Pinjaman">
                </div>
                <div class="col-md-6 form-group">
                    <label>Lama Angsuran</label>
                    <input type="text" name="lama_angsuran" class="form-control" placeholder="Lama Angsuran" readonly id="lamaAngsuran">
                </div>
                <div class="col-md-6 form-group">
                    <label>Upload Form Persetujuan Atasan</label>
                    <div class="custom-file">
                        <input type="file" class="custom-file-input" id="formPersetujuan" name="form_persetujuan"  accept="application/pdf" style="cursor: pointer" required>
                        <label class="custom-file-label" for="customFile">Choose Document</label>
                    </div>
                </div>
                <div class="col-md-6 form-group">
                    <label>Keperluan</label>
                    <input type="text" name="keperluan" class="form-control" placeholder="Keperluan"  id="keperluan">
                </div>
                <div id="suratPenghasilanTertentu" class="col-md-6 form-group" style="display: none">
                    <label>Upload Surat Mendapatkan Penghasilan Tertentu</label>
                    <div class="custom-file">
                        <input type="file" class="custom-file-input" name="surat_penghasilan_tertentu"  accept="application/pdf" style="cursor: pointer">
                        <label class="custom-file-label" for="customFile">Choose Document</label>
                    </div>
                </div>
                {{-- <div class="col-md-6 form-group">
                    <label>Bunga</label>
                    <input type="text" name="bunga" class="form-control" placeholder="Bunga" readonly id="bunga">
                </div>
                <div class="col-md-6 form-group">
                    <label>Besar Angsuran</label>
                    <input type="text" name="besar_angsuran" class="form-control" placeholder="Besar Angsuran" readonly id="besarAngsuran">
                </div> --}}
                <div class="col-12">
                    <button type="submit" name="ajukan_pinjaman" id="submit" class="btn btn-success btn-sm"><i class="fas fa-paper-plane"></i> Ajukan Pinjaman</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@section('js')
    <script src="{{ asset('js/collect.min.js') }}"></script>
    <script>
        var jenisPinjaman = collect({!!$listJenisPinjaman!!});

        $(document).ready(function ()
        {
            @if (!\Auth::user()->isAnggota())
                initiateSelect2();
            @endif
            initialEvent();
        });
        
       function initialEvent()
       {
            $('#jenisPinjaman').on('change', function ()
            {
                var kode_anggota = $('#anggotaName').val();
                var selectedId = $(this).find(":selected").val();
                var besarPinjaman = $('#besarAngsuran').val();
                updateInfo(selectedId, kode_anggota);
                if (selectedId != '' && selectedId != null)
                {
                    $('#besarPinjaman').removeAttr('readonly');
                }
                else
                {
                    $('#besarPinjaman').val('');
                    $('#besarPinjaman').attr('readonly','readonly');
                }
                // updateAngsuran(idJenisPinjaman, besarPinjaman);
            });

            $('#besarPinjaman').on('keyup', function ()
            {
                var besarPinjaman = $(this).val();
                besarPinjaman = besarPinjaman.replace(/[^\d]/g, "",'');
                var idJenisPinjaman = $('#jenisPinjaman').find(":selected").val();
                var maxPinjaman = parseInt($('#maksimalBesarPinjaman').val().replace(/[^\d]/g, "",''));

                var selectedPinjaman = jenisPinjaman.where('kode_jenis_pinjam', idJenisPinjaman).first();
                if (parseInt(besarPinjaman) > 40000000 && selectedPinjaman.kategori_jenis_pinjaman_id == {{ KATEGORI_JENIS_PINJAMAN_JANGKA_PENDEK }})
                {
                    $('#suratPenghasilanTertentu').show();
                }
                else
                {
                    $('#suratPenghasilanTertentu').hide();
                }
                console.log(selectedPinjaman);

                if (besarPinjaman > maxPinjaman)
                {
                    $('#submit').attr('disabled','disabled');
                    $('#warningBesarPinjaman').show();
                }
                else
                {
                    $('#submit').removeAttr("disabled");
                    $('#warningBesarPinjaman').hide();
                }
                $(this).val(toRupiah(besarPinjaman));
            });

            $(".custom-file-input").on("change", function() {
                var fileName = $(this).val().split("\\").pop();
                $(this).siblings(".custom-file-label").addClass("selected").html(fileName);
            });

            $('#anggotaName').on('change', function ()
            {
                var selectedId = $('#jenisPinjaman').find(":selected").val();
                var kode_anggota = $(this).val();
                if (kode_anggota == null)
                {
                    kode_anggota = $(this).find(":selected").val();
                }
                updateInfo(selectedId, kode_anggota);
            });

            $('#jenisPengajuan').on('change', function ()
            {
                var selected = $('#jenisPengajuan').find(":selected").val();
                if (selected == 1)
                {
                    $('#panelTopup').show('slow');
                    $('.select2').select2({
                        placeholder: "Select one",
                    });
                }
                else
                {
                    $('#panelTopup').hide('slow');
                    $('.select2').select2({
                        placeholder: "Select one",
                    });
                }
            });
       }

       function initiateSelect2()
       {
            $("#anggotaName").select2({
                ajax: {
                    url: '{{ route('anggota-ajax-search') }}',
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
                        var query = {
                            search: params.term,
                            type: 'public'
                        }
                        return query;
                    },
                    processResults: function (data) {
                        return {
                            results: data
                        };
                    }
                }
            });
       }

        function updateInfo(id, kode_anggota)
        {
            var selectedJenisPinjaman = jenisPinjaman.where('kode_jenis_pinjam',id).first();
            $.ajax({
                url: '{{ route('pengajuan-pinjaman-calculate-max-pinjaman') }}',
                data: {
                    id_jenis_pinjaman: id,
                    kode_anggota: kode_anggota
                },
                type: 'get',
                success: function (data) {
                    $('#maksimalBesarPinjaman').val(toRupiah(Math.ceil(data)));
                },
                error: function (xhr, status, error)
                {
                    Swal.fire({
                        type: 'error',
                        title: 'Error!',
                        text: xhr.responseJSON.message,
                        showConfirmButton: true
                    }).then((result) => {
                        $('#besarPinjaman').val('');
                        $('#besarPinjaman').attr('readonly','readonly');
                    });;
                }
            });
            if (selectedJenisPinjaman)
            {
                $('#lamaAngsuran').val(selectedJenisPinjaman.lama_angsuran);
            }
            else
            {
                $('#lamaAngsuran').val(0);
            }
            // $('#bunga').val(selectedJenisPinjaman.bunga);
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

        function updateAngsuran(idJenisPinjaman, besarPinjaman) {
            var jPinjaman = jenisPinjaman.where('kode_jenis_pinjam',idJenisPinjaman).first();
            var bunga = jPinjaman.bunga;
            var lamaAngsuran = jPinjaman.lama_angsuran;
            var angsuranBulan = besarPinjaman/lamaAngsuran;
            var persentaseBunga = angsuranBulan*bunga/100;
            var angsuran = angsuranBulan + persentaseBunga;
            // var b = $('#besarAngsuran').val(angsuran);
        }

        function initiateOnLoad()
        {
            var selected = $('#jenisPengajuan').find(":selected").val();
            if (selected == 1)
            {
                $('#panelTopup').show('slow');
            }
            else
            {
                $('#panelTopup').hide('slow');
            }
        }
    </script>
@endsection