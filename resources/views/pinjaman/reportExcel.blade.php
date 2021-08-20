<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
</head>
<body>
    <table class="table-bordered" style="text-align: center">
        <thead>
            <tr>
                <th rowspan="2" style="vertical-align: middle">Bulan</th>
                <th colspan="3">JAPAN {{ $request->period }}</th>
                <th colspan="3">JAPEN {{ $request->period }}</th>
            </tr>
            <tr>
                <th>TRX</th>
                <th>APPROVED</th>
                <th>DITERIMA</th>
                <th>TRX</th>
                <th>APPROVED</th>
                <th>DITERIMA</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($reports as $report)
                <tr>
                    <td>{{ Carbon\Carbon::createFromFormat('m', $loop->iteration)->format('M') }}</td>
                    <td>{{ number_format($report['trxJapen'],0,",",".") }}</td>
                    <td>{{ number_format($report['japenApproved'],0,",",".") }}</td>
                    <td>{{ number_format($report['japenDiterima'],0,",",".") }}</td>
                    <td>{{ number_format($report['trxJapan'],0,",",".") }}</td>
                    <td>{{ number_format($report['japanApproved'],0,",",".") }}</td>
                    <td>{{ number_format($report['japanDiterima'],0,",",".") }}</td>
                </tr>
            @endforeach
            <tr style="font-weight:bold">
                <td>Total</td>
                <td>{{ number_format($totalJapenTrx,0,",",".") }}</td>
                <td>{{ number_format($totalJapenApproved,0,",",".") }}</td>
                <td>{{ number_format($totalJapenDiterima,0,",",".") }}</td>
                <td>{{ number_format($totalJapanTrx,0,",",".") }}</td>
                <td>{{ number_format($totalJapanApproved,0,",",".") }}</td>
                <td>{{ number_format($totalJapanDiterima,0,",",".") }}</td>
            </tr>
        </tbody>
    </table>
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
</body>
</html>