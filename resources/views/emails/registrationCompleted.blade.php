<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Registration Completed</title>
</head>
<body>
    <h4>Dear {{ $details['anggota']->nama_anggota }}</h4>
    <p>Anda telah berhasil melakukan registrasi user untuk e-simpin dan mobile-simpin  dengan akun sebagai berikut:</p>
    <p>
        Username: <b>{{ $details['user']->email }}</b><br>
        Password: <b>{{ $details['password'] }}</b>
    </p>
    <p>
        Silakan klik tautan berikut <a href="{{ route('user-validation', ['validation_id'=>$details['user']->activation_code]) }}">{{ route('user-validation', ['validation_id'=>$details['user']->activation_code]) }}</a> untuk menyelesaikan proses validasi akun anda.
    </p>
    <p>Terima Kasih</p>
    <br>
    <p>
        Admin SIMPIN <br>
        <img src="{{ asset('img/new-logo.jpg') }}" style="width: 110px"><br>
        {{-- <img src="https://simpin.kopegmar.co.id/img/new-logo.jpg" style="width: 110px"><br> --}}
        KOPEGMAR  Tj Priok  <br>
        Jl. Cempaka No.14, RT.4/RW.12,  <br>
        Rawabadak Utara, <br>
        Kec. Koja, Kota Jkt Utara,  <br>
        Daerah Khusus Ibukota Jakarta 14230 <br>
        Hotline WA : 08111821414  <br>
        Cs email : uspkopegmar@yahoo.co.id <br>
    </p>
</body>
</html>