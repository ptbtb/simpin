@extends('adminlte::master')

@inject('layoutHelper', \JeroenNoten\LaravelAdminLte\Helpers\LayoutHelper)

@if($layoutHelper->isLayoutTopnavEnabled())
    @php( $def_container_class = 'container' )
@else
    @php( $def_container_class = 'container-fluid' )
@endif

@section('adminlte_css')
    @stack('css')
    @yield('css')
@stop

@section('classes_body', $layoutHelper->makeBodyClasses())

@section('body_data', $layoutHelper->makeBodyData())

@section('content_top_nav_right')
     <!-- Notifications: style can be found in dropdown.less -->
     <li class="dropdown notifications-menu">
         @csrf
        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
            <i class="fa fa-bell"></i>
            @if($notification['count'])
                <span class="label label-danger">{{ $notification['count'] }}</span>
            @endif
        </a>
        
        <ul class="dropdown-menu">
            <li class="header">You have {{ $notification['count'] ? $notification['count'] : 'no' }} notifications</li>
            <li class="notification-content">
              <!-- inner menu: contains the actual data -->
              <ul class="menu">
                @foreach ($notification['all_notification'] as $notif)
                  <li class="{{ $notif->has_read ? '' : 'unread'}} d-flex justify-content-center align-items-center">
                    <i class="fas fa-hand-holding-usd pr-3 text-info"></i> 
                    <a href={{ $notif->url }} data={{ $notif->id }} id="update-notif" >
                      {{ $notif->informasi_notifikasi }}
                    </a>
                  </li>
                @endforeach
              </ul>
            </li>
            <li class="footer text-center"><a href="#">View all</a></li>
        </ul>
      </li>
@endsection

@section('body')
    <div class="wrapper">

        {{-- Top Navbar --}}
        @if($layoutHelper->isLayoutTopnavEnabled())
            @include('adminlte::partials.navbar.navbar-layout-topnav')
        @else
            @include('adminlte::partials.navbar.navbar')
        @endif

        {{-- Left Main Sidebar --}}
        @if(!$layoutHelper->isLayoutTopnavEnabled())
            @include('adminlte::partials.sidebar.left-sidebar')
        @endif

        {{-- Content Wrapper --}}
        <div class="content-wrapper {{ config('adminlte.classes_content_wrapper') ?? '' }}">

            {{-- Content Header --}}
            <div class="content-header">
                <div class="{{ config('adminlte.classes_content_header') ?: $def_container_class }}">
                    @yield('content_header')
                </div>
            </div>

            {{-- Main Content --}}
            <div class="content">
                <div class="{{ config('adminlte.classes_content') ?: $def_container_class }}">
                    @include('flashAlert')
                    @yield('content')
                </div>
            </div>

        </div>

        {{-- Footer --}}
        @hasSection('footer')
            @include('adminlte::partials.footer.footer')
        @endif

        {{-- Right Control Sidebar --}}
        @if(config('adminlte.right_sidebar'))
            @include('adminlte::partials.sidebar.right-sidebar')
        @endif

    </div>
@stop

@section('adminlte_js')
    @stack('js')
    @yield('js')
    <script>
        $(document).ready(function() {
            $("#update-notif").click(function(event) {
                var notifId = $(this).attr('data');
                if(notifId){
                    updateNotifStatus(notifId);
                }
                // event.preventDefault();
            });
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
                   console.log(response);
                }
            })
        }

    </script>
@stop