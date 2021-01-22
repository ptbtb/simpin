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
        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
          <i class="fa fa-bell"></i>
          <span class="label label-danger">10</span>
        </a>
        <ul class="dropdown-menu">
          <li class="header">You have 10 notifications</li>
          <li class="notification-content">
            <!-- inner menu: contains the actual data -->
            <ul class="menu">
              <li>
                <a href="#">
                  <i class="fa fa-users"></i> Anggota <strong>Endang H</strong> melakukan penarikan
                </a>
              </li>
              <li>
                <a href="#">
                  <i class="fa fa-users"></i> Anggota <strong>Endang H</strong> melakukan penarikan
                </a>
              </li>
              <li>
                <a href="#">
                  <i class="fa fa-users"></i> Anggota <strong>Endang H</strong> melakukan penarikan
                </a>
              </li>
              <li>
                <a href="#">
                  <i class="fa fa-users"></i> Anggota <strong>Endang H</strong> melakukan penarikan
                </a>
              </li>
              <li>
                <a href="#">
                  <i class="fa fa-users"></i> Anggota <strong>Endang H</strong> melakukan penarikan
                </a>
              </li>
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
@stop
