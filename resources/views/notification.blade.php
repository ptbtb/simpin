@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
<h1>Notifikasi</h1>
@stop

@section('css')
<style>
    .link-dashboard{
        color: white;
    }

    .link-dashboard:hover{
        color: azure;
    }

    .c-title{
        font-weight: bold;
        font-size: 16px;
    }
</style>
@endsection

@section('content')
<div class="col-lg-12">
    <div class="d-flex">
        <table class="table table-notifications">
            <thead>
                <tr>
                    <td>#</td>
                    <td>NOTIFIKASI</td>
                    <td>TANGGAL</td>
                    <td>WAKTU</td>
                </tr>
            </thead>
            <tbody>
                @if(!empty($notifications))
                    @foreach ($notifications as $notif)
                        <tr data-href={{$notif->url}}  data={{ $notif->id }} class={{$notif->has_read ? '' : 'tr-unread'}}>
                            <td class="d-flex justify-content-center align-items-center">
                                @if(!$notif->has_read)
                                    <div class="tag-unread"></div>
                                @endif
                            </td>
                            <td class="">
                                {{-- <i class="fas fa-hand-holding-usd pr-3 text-info"></i>  --}}
                                {{ $notif->informasi_notifikasi }}
                            </td>
                            <td>{{ date_format($notif->created_at, "Y/m/d") }}</td>
                            <td>{{ date_format($notif->created_at, "H:i:s") }}</td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="4" class="text-center">
                            <div class="text-disabled">You have No Notifications</div>
                        </td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>
</div>
@stop

@section('css')
<link rel="stylesheet" href="/css/admin_custom.css">
@stop

@section('js')
<script>
    $('tr[data-href]').on("click", function() {
        var notifId = $(this).attr('data');
        if(notifId){
            updateNotifStatus(notifId).then((response)=>{
                if(response){
                    document.location = $(this).data('href');

                }
            });
        }

    });
    function updateNotifStatus(notifId){
        return $.ajax({
            url: '{{ route('notification-status-update') }}',
            type: "POST",
            dataType: 'json',
            data: {
                'notifId' : notifId,
                "_token": "{{ csrf_token() }}",
            },
            success: function (response) {
                return response;
            }
        })
        }
</script>
@stop