<?php

return [
    /*
      |--------------------------------------------------------------------------
      | Title
      |--------------------------------------------------------------------------
      |
      | Here you can change the default title of your admin panel.
      |
      | For more detailed instructions you can look here:
      | https://github.com/jeroennoten/Laravel-AdminLTE/#61-title
      |
     */

    'title' => 'AdminLTE 3',
    'title_prefix' => '',
    'title_postfix' => '',
    /*
      |--------------------------------------------------------------------------
      | Favicon
      |--------------------------------------------------------------------------
      |
      | Here you can activate the favicon.
      |
      | For more detailed instructions you can look here:
      | https://github.com/jeroennoten/Laravel-AdminLTE/#62-favicon
      |
     */
    'use_ico_only' => false,
    'use_full_favicon' => false,
    /*
      |--------------------------------------------------------------------------
      | Logo
      |--------------------------------------------------------------------------
      |
      | Here you can change the logo of your admin panel.
      |
      | For more detailed instructions you can look here:
      | https://github.com/jeroennoten/Laravel-AdminLTE/#63-logo
      |
     */
    'logo' => '<b>Simpin</b>Kopegmar',
    'logo_img' => 'img/new-logo.jpg',
    'logo_img_class' => 'brand-image img-circle elevation-2',
    'logo_img_xl' => null,
    'logo_img_xl_class' => 'brand-image-xs',
    'logo_img_alt' => 'AdminLTE',
    /*
      |--------------------------------------------------------------------------
      | User Menu
      |--------------------------------------------------------------------------
      |
      | Here you can activate and change the user menu.
      |
      | For more detailed instructions you can look here:
      | https://github.com/jeroennoten/Laravel-AdminLTE/#64-user-menu
      |
     */
    'usermenu_enabled' => true,
    'usermenu_header' => true,
    'usermenu_header_class' => 'bg-primary',
    'usermenu_image' => true,
    'usermenu_desc' => true,
    'usermenu_profile_url' => true,
    /*
      |--------------------------------------------------------------------------
      | Layout
      |--------------------------------------------------------------------------
      |
      | Here we change the layout of your admin panel.
      |
      | For more detailed instructions you can look here:
      | https://github.com/jeroennoten/Laravel-AdminLTE/#65-layout
      |
     */
    'layout_topnav' => null,
    'layout_boxed' => null,
    'layout_fixed_sidebar' => null,
    'layout_fixed_navbar' => null,
    'layout_fixed_footer' => null,
    /*
      |--------------------------------------------------------------------------
      | Authentication Views Classes
      |--------------------------------------------------------------------------
      |
      | Here you can change the look and behavior of the authentication views.
      |
      | For more detailed instructions you can look here:
      | https://github.com/jeroennoten/Laravel-AdminLTE/#661-authentication-views-classes
      |
     */
    'classes_auth_card' => 'card-outline card-primary',
    'classes_auth_header' => '',
    'classes_auth_body' => '',
    'classes_auth_footer' => '',
    'classes_auth_icon' => '',
    'classes_auth_btn' => 'btn-flat btn-primary',
    /*
      |--------------------------------------------------------------------------
      | Admin Panel Classes
      |--------------------------------------------------------------------------
      |
      | Here you can change the look and behavior of the admin panel.
      |
      | For more detailed instructions you can look here:
      | https://github.com/jeroennoten/Laravel-AdminLTE/#662-admin-panel-classes
      |
     */
    'classes_body' => '',
    'classes_brand' => '',
    'classes_brand_text' => '',
    'classes_content_wrapper' => '',
    'classes_content_header' => '',
    'classes_content' => '',
    'classes_sidebar' => 'sidebar-dark-primary elevation-4',
    'classes_sidebar_nav' => '',
    'classes_topnav' => 'navbar-info navbar-light',
    'classes_topnav_nav' => 'navbar-expand',
    'classes_topnav_container' => 'container',
    /*
      |--------------------------------------------------------------------------
      | Sidebar
      |--------------------------------------------------------------------------
      |
      | Here we can modify the sidebar of the admin panel.
      |
      | For more detailed instructions you can look here:
      | https://github.com/jeroennoten/Laravel-AdminLTE/#67-sidebar
      |
     */
    'sidebar_mini' => true,
    'sidebar_collapse' => false,
    'sidebar_collapse_auto_size' => false,
    'sidebar_collapse_remember' => false,
    'sidebar_collapse_remember_no_transition' => true,
    'sidebar_scrollbar_theme' => 'os-theme-light',
    'sidebar_scrollbar_auto_hide' => 'l',
    'sidebar_nav_accordion' => true,
    'sidebar_nav_animation_speed' => 300,
    /*
      |--------------------------------------------------------------------------
      | Control Sidebar (Right Sidebar)
      |--------------------------------------------------------------------------
      |
      | Here we can modify the right sidebar aka control sidebar of the admin panel.
      |
      | For more detailed instructions you can look here:
      | https://github.com/jeroennoten/Laravel-AdminLTE/#68-control-sidebar-right-sidebar
      |
     */
    'right_sidebar' => false,
    'right_sidebar_icon' => 'fas fa-cogs',
    'right_sidebar_theme' => 'dark',
    'right_sidebar_slide' => true,
    'right_sidebar_push' => true,
    'right_sidebar_scrollbar_theme' => 'os-theme-light',
    'right_sidebar_scrollbar_auto_hide' => 'l',
    /*
      |--------------------------------------------------------------------------
      | URLs
      |--------------------------------------------------------------------------
      |
      | Here we can modify the url settings of the admin panel.
      |
      | For more detailed instructions you can look here:
      | https://github.com/jeroennoten/Laravel-AdminLTE/#69-urls
      |
     */
    'use_route_url' => false,
    'dashboard_url' => 'home',
    'logout_url' => 'logout',
    'login_url' => 'login',
    'register_url' => 'register',
    'password_reset_url' => 'password/reset',
    'password_email_url' => 'password/email',
    'profile_url' => false,
    /*
      |--------------------------------------------------------------------------
      | Laravel Mix
      |--------------------------------------------------------------------------
      |
      | Here we can enable the Laravel Mix option for the admin panel.
      |
      | For more detailed instructions you can look here:
      | https://github.com/jeroennoten/Laravel-AdminLTE/#610-laravel-mix
      |
     */
    'enabled_laravel_mix' => false,
    'laravel_mix_css_path' => 'css/app.css',
    'laravel_mix_js_path' => 'js/app.js',
    /*
      |--------------------------------------------------------------------------
      | Menu Items
      |--------------------------------------------------------------------------
      |
      | Here we can modify the sidebar/top navigation of the admin panel.
      |
      | For more detailed instructions you can look here:
      | https://github.com/jeroennoten/Laravel-AdminLTE/#611-menu
      |
     */
    'menu' => [
        [
            'text' => 'search',
            'search' => false,
            'topnav' => true,
        ],
        // [
        //     'text' => 'blog',
        //     'url' => 'admin/blog',
        //     'can' => 'manage-blog',
        // ],
        [
            'text' => 'Dashboard',
            'url' => '',
            'icon' => 'nav-icon fas fa-tachometer-alt',
            'label' => '',
            'label_color' => '',
        ],
        ['header' => 'MASTER DATA', 'can'  => ['view user', 'view anggota']],
        [
            'text' => 'Master Data',
            'icon' => 'fas fa-fw fa-database',
            'can'  => ['view role', 'view user', 'view anggota', 'add anggota'],
            'submenu' => [
                [
                    'text' => 'Role',
                    'url' => '/role/list',
                    'icon' => 'fa fa-cogs nav-icon',
                    'active' => ['/role/list', '/role/create', 'regex:@^role/edit/[0-9]+$@'],
                    'can'  => ['view role'],
                ],
                [
                    'text' => 'User',
                    'url' => '/user/list',
                    'icon' => 'fa fa-user nav-icon',
                    'can'  => ['view user'],
                    'active' => ['/user/list', '/user/create','/user/import/excel', 'regex:@^user/edit/[0-9]+$@'],
                ],
                [
                    'text' => 'Anggota',
                    'url' => '/anggota/list',
                    'icon' => 'fa fa-users nav-icon',
                    'active' => ['/anggota/list', '/anggota/create', '/anggota/import/excel', 'regex:@^anggota/edit/[0-9]+$@'],
                    'can'  => ['view anggota'],
                ],
            ],
        ],
        [
            'text' => 'Setting',
            'url' => '/setting',
            'icon' => 'fa fa-toolbox nav-icon',
            'can'  => ['view kode transaksi', 'view jenis simpanan', 'view jenis anggota'],
            'submenu' => [
                [
                    'text' => 'Kode Transaksi',
                    'url' => '/setting/codetrans',
                    'icon' => 'fa fa-superscript nav-icon',
                    'can'  => ['view kode transaksi'],
                ],
                [
                    'text' => 'Jenis Simpanan',
                    'url' => '/setting/simpanan',
                    'icon' => 'fa fa-money-bill nav-icon',
                    'can'  => ['view jenis simpanan'],
                ],
                [
                    'text' => 'Import Saldo Simpanan',
                    'url' => '/tabungan/import',
                    'icon' => 'fa fa-money-bill nav-icon',
                    'can'  => ['view jenis simpanan'],
                ],
                [
                    'text' => 'Jenis Pinjaman',
                    'url' => '/setting/pinjaman',
                    'icon' => 'fa fa-money-check-alt nav-icon',
                    'can'  => ['view jenis pinjaman'],
                ],
                [
                    'text' => 'Import Saldo Pinjaman',
                    'url' => '/pinjaman/import',
                    'icon' => 'fa fa-money-check-alt nav-icon',
                    'can'  => ['view jenis pinjaman'],
                ],
                [
                    'text' => 'Jenis Anggota',
                    'url' => '/setting/jenis-anggota/list',
                    'icon' => 'fa fa-address-book nav-icon',
                    'can'  => ['view jenis anggota'],
                    'active' => ['/setting/jenis-anggota/list', '/setting/jenis-anggota/create', 'regex:@^setting/jenis-anggota/edit/[0-9]+$@'],
                ],
                [
                    'text' => 'Status Pengajuan',
                    'url' => '/setting/status-pengajuan',
                    'icon' => 'fas fa-check-circle nav-icon',
                    'can'  => ['view status pengajuan'],
                    'active' => ['/setting/status-pengajuan/list', '/setting/status-pengajuan/create', 'regex:@^setting/status-pengajuan/edit/[0-9]+$@'],
                ],
            ],
        ],
        ['header' => 'OPERATOR','can'  => ['add penarikan', 'view history penarikan','import penarikan']],
        [
            'text' => 'Penarikan',
            'icon' => 'fas fa-exchange-alt nav-icon',
            'can'  => ['add penarikan', 'view history penarikan'],
            'active' => ['/penarikan/history', '/penarikan/create','/penarikan/import/excel'],
            'submenu' => [
                [
                    'text' => 'Import Data Penarikan',
                    'url' => '/penarikan/import/excel',
                    'icon' => 'fas fa-upload nav-icon',
                    'can'  => ['import penarikan'],
                ],
                [
                    'text' => 'History Penarikan',
                    'url' => '/penarikan/history',
                    'icon' => 'fas fa-history nav-icon',
                    'can'  => ['view history penarikan'],
                ],
                [
                    'text' => 'Buat Penarikan',
                    'url' => '/penarikan/create',
                    'icon' => 'fas fa-plus nav-icon',
                    'can'  => ['add penarikan'],
                    'active'  => ['/penarikan/create', 'regex:@^penarikan/receipt/[0-9]+$@'],
                ],
                [
                    'text' => 'List Penarikan',
                    'url' => '/penarikan/list',
                    'icon' => 'fas fa-file-invoice-dollar nav-icon',
                    'can'  => ['view penarikan'],
                ],
            ],
        ], 
        // ['header' => 'ANGGOTA'],
        [
            'text' => 'Transaksi',
            'url' => '/transaksi',
            'icon' => 'fas fa-handshake nav-icon',
            'can'  => ['view transaksi anggota'],
        ],
        [
            'text' => 'Pinjaman',
            'url' => '/pinjaman',
            'icon' => 'fas fa-hand-holding-usd nav-icon',
            'can'  => ['view history pinjaman', 'view pinjaman', 'view pengajuan pinjaman'],
            'submenu' => [
                [
                    'text' => 'Pengajuan Pinjaman',
                    'url' => '/pinjaman/pengajuan/list',
                    'icon' => 'fas fa-list nav-icon',
                    'can'  => ['view pengajuan pinjaman'],
                    'active' => ['/pinjaman/pengajuan/create']
                ],
                [
                    'text' => 'Download Form Pinjaman',
                    'url' => '/pinjaman/download-form-pinjaman',
                    'icon' => 'fas fa-download nav-icon',
                    'can'  => ['download form pinjaman'],
                ],
                [
                    'text' => 'List Pinjaman',
                    'url' => '/pinjaman/list',
                    'icon' => 'fas fa-file-invoice-dollar nav-icon',
                    'can'  => ['view pinjaman'],
                ],
                [
                    'text' => 'History Pinjaman',
                    'url' => '/pinjaman/history',
                    'icon' => 'fas fa-history nav-icon',
                    'can'  => ['view history pinjaman'],
                ],
            ],
        ],
        [
            'text' => 'Simpanan',
            'url' => '/simpanan/',
            'icon' => 'fa fa-money-bill nav-icon',
            'can'  => ['view simpanan','view history simpanan','kartu simpanan'],
            'submenu' => [
                [
                    'text' => 'Import Data Simpanan',
                    'url' => '/simpanan/import/excel',
                    'icon' => 'fas fa-upload nav-icon',
                    'can'  => ['import simpanan'],
                ],
                [
                    'text' => 'List Simpanan',
                    'url' => '/simpanan/list',
                    'icon' => 'fas fa-file-invoice-dollar nav-icon',
                    'can'  => ['view simpanan'],
                ],
                [
                    'text' => 'Buat Simpanan',
                    'url' => '/simpanan/create',
                    'icon' => 'fas fa-plus nav-icon',
                    'can'  => ['add simpanan'],
                ],
                [
                    'text' => 'History Simpanan',
                    'url' => '/simpanan/history',
                    'icon' => 'fas fa-history nav-icon',
                    'can'  => ['view history simpanan'],
                ],
                [
                    'text' => 'Kartu Simpanan',
                    'url' => '/simpanan/card',
                    'icon' => 'fas fa-clipboard nav-icon',
                    'can'  => ['kartu simpanan'],
                ],
            ],
        ],
        [
            'text' => 'Invoice',
            'icon' => 'fa fa-money-bill nav-icon',
            'route' => 'invoice-list',
            'can'  => ['view invoice'],
            'active'  => ['/invoice', 'regex:@^invoice/[0-9]+$@'],
        ],    
        [
            'text' => 'Jurnal',
            'url' => '/jurnal',
            'icon' => 'fas fa-book nav-icon',
            'can'  => ['view jurnal'],
        ],   
        [
            'text' => 'Jurnal Umum',
            'url' => '/jurnal-umum',
            'icon' => 'fas fa-book nav-icon',
            'can'  => ['view jurnal umum', 'add jurnal umum', 'edit jurnal umum'],
            'submenu' => [
                [
                    'text' => 'List Jurnal Umum',
                    'url' => '/jurnal-umum/list',
                    'icon' => 'fas fa-book nav-icon',
                    'can'  => ['view jurnal umum'],
                ],
                [
                    'text' => 'Buat Jurnal Umum',
                    'url' => '/jurnal-umum/create',
                    'icon' => 'fas fa-plus nav-icon',
                    'can'  => ['add jurnal umum'],
                ],
            ],
        ],   
        ['header' => 'account_settings'],
        [
            'text' => 'profile',
            'url' => 'user/profile',
            'icon' => 'fas fa-fw fa-user',
        ],
        [
            'text' => 'change_password',
            'url' => 'user/change-password',
            'icon' => 'fas fa-fw fa-lock',
        ],
    ],
    /*
      |--------------------------------------------------------------------------
      | Menu Filters
      |--------------------------------------------------------------------------
      |
      | Here we can modify the menu filters of the admin panel.
      |
      | For more detailed instructions you can look here:
      | https://github.com/jeroennoten/Laravel-AdminLTE/#612-menu-filters
      |
     */
    'filters' => [
        JeroenNoten\LaravelAdminLte\Menu\Filters\HrefFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\SearchFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\ActiveFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\ClassesFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\GateFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\LangFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\DataFilter::class,
    ],
    /*
      |--------------------------------------------------------------------------
      | Plugins Initialization
      |--------------------------------------------------------------------------
      |
      | Here we can modify the plugins used inside the admin panel.
      |
      | For more detailed instructions you can look here:
      | https://github.com/jeroennoten/Laravel-AdminLTE/#613-plugins
      |
     */
    'plugins' => [
        'Datatables' => [
            'active' => false,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js',
                ],
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdn.datatables.net/1.10.19/js/dataTables.bootstrap4.min.js',
                ],
                [
                    'type' => 'css',
                    'asset' => false,
                    'location' => '//cdn.datatables.net/1.10.19/css/dataTables.bootstrap4.min.css',
                ],
            ],
        ],
        'Select2' => [
            'active' => false,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js',
                ],
                [
                    'type' => 'css',
                    'asset' => false,
                    'location' => '//cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.css',
                ],
            ],
        ],
        'Chartjs' => [
            'active' => false,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.0/Chart.bundle.min.js',
                ],
            ],
        ],
        'Sweetalert2' => [
            'active' => false,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdn.jsdelivr.net/npm/sweetalert2@8',
                ],
            ],
        ],
        'Pace' => [
            'active' => false,
            'files' => [
                [
                    'type' => 'css',
                    'asset' => false,
                    'location' => '//cdnjs.cloudflare.com/ajax/libs/pace/1.0.2/themes/blue/pace-theme-center-radar.min.css',
                ],
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdnjs.cloudflare.com/ajax/libs/pace/1.0.2/pace.min.js',
                ],
            ],
        ],
    ],
];
