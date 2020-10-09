<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>

    <style>
        p{
            font-family: -apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Helvetica,Arial,sans-serif,'Apple Color Emoji','Segoe UI Emoji','Segoe UI Symbol';
            font-size: 16px;
            line-height: 1.5em;
        }

        .box{
            width: 65%; 
            margin-left:auto; 
            margin-right:auto; 
            padding: 25px;
            border-radius: 2%;
            background-color: white;
            margin-top: 50px;
            margin-bottom: 50px;
        }
    </style>
</head>
<body>
    <div style="display: flex; background-color: #edf2f7">
        <div class="box">
            <h1>Hallo!</h1>
            
            <p>
                @foreach ($introLines as $line)
                    {{ $line }}
                @endforeach
            </p>
    
            @isset($actionText)
                <?php
                    switch ($level) {
                        case 'success':
                        case 'error':
                            $color = $level;
                            break;
                        default:
                            $color = 'primary';
                    }
                ?>
                @component('mail::button', ['url' => $actionUrl, 'color' => $color])
                    {{ $actionText }}
                @endcomponent
            @endisset
    
            <p>
                @foreach ($outroLines as $line)
                    {{ $line }}
                @endforeach
            </p>
            <p>
                Terima kasih
            </p>
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
        </div>
    </div>
</body>
</html>