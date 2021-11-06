<aside class="page-sidebar">
    <div class="page-logo">
        <a href="#" class="page-logo-link press-scale-down d-flex align-items-center position-relative"
            data-toggle="modal" data-target="#modal-shortcut">
            <img src="{{asset('img/logo-gray.png')}}" class="rounded-circle" alt="{{env('APP_NAME','')}}"
                aria-roledescription="logo">
            <span class="page-logo-text mr-1">{{env('APP_NAME','')}}</span>
            <span class="position-absolute text-white opacity-50 small pos-top pos-right mr-2 mt-n2"></span>
            <i class="fal fa-angle-down d-inline-block ml-1 fs-lg color-primary-300"></i>
        </a>
    </div>
    <!-- BEGIN PRIMARY NAVIGATION -->
    <nav id="js-primary-nav" class="primary-nav" role="navigation">
        <div class="nav-filter">
            <div class="position-relative">
                <input type="text" id="nav_filter_input" placeholder="Filter menu" class="form-control" tabindex="0">
                <a href="#" onclick="return false;" class="btn-primary btn-search-close js-waves-off"
                    data-action="toggle" data-class="list-filter-active" data-target=".page-sidebar">
                    <i class="fal fa-chevron-up"></i>
                </a>
            </div>
        </div>
        <div class="info-card">
            {{-- <img src="{{asset('img/avatar').'/'.Auth::user()->avatar}}" class="profile-image rounded-circle"
                alt="Dr. Codex Lantern">
            <div class="info-card-text">
                <a href="#" class="d-flex align-items-center text-white">
                    <span class="text-truncate text-truncate-sm d-inline-block">
                        {{Auth::user()->name}}
                    </span>
                </a>
                <span class="d-inline-block text-truncate text-truncate-sm">Toronto, Canada</span>
            </div> --}}
            <img src="{{asset('img/card-backgrounds/cover-2-lg.png')}}" class="cover" alt="cover">
            <a href="#" onclick="return false;" class="pull-trigger-btn" data-action="toggle"
                data-class="list-filter-active" data-target=".page-sidebar" data-focus="nav_filter_input">
                <i class="fal fa-angle-down"></i>
            </a>
        </div>
        <ul id="js-nav-menu" class="nav-menu">
            <li class="nav-title">Menu</li>

            <li>
                <a href="{{route('backoffice.dashboard')}}" title="Dashboard" data-filter-tags="dashboard">
                    <i class="fal fa-desktop"></i>
                    <span class="nav-link-text">Dashboard</span>
                </a>
            </li>

            @hasanyrole('superadmin|pusaka')
            <li class="">
                <a href="#" title="Apps Content Management System" data-filter-tags="cms">
                    <i class="fal fa-mobile"></i>
                    <span class="nav-link-text" data-i18n="nav.cms">CMS</span>
                </a>
                <ul>
                    <li class="">
                        <a href="{{route('external-link.index')}}" title="Link Mitra" data-filter-tags="esternal link">
                            <span class="nav-link-text" data-i18n="nav.external_link">Link Mitra</span>
                        </a>
                    </li>
                    <li class="">
                        <a href="{{route('instagram.index')}}" title="Instagram Post" data-filter-tags="instagram post">
                            <span class="nav-link-text" data-i18n="nav.instagram_post">Instagram Post</span>
                        </a>
                    </li>
                    <li class="">
                        <a href="{{route('website.index')}}" title="Website Post" data-filter-tags="website_post">
                            <span class="nav-link-text" data-i18n="nav.website_post">Website Post</span>
                        </a>
                    </li>
                    <li class="">
                        <a href="{{route('banner.index')}}" title="Banner Image" data-filter-tags="banner image">
                            <span class="nav-link-text" data-i18n="nav.banner_image">Banner Aplikasi</span>
                        </a>
                    </li>
                    <li class="">
                        <a href="{{route('static-page.index')}}" title="Static Menu" data-filter-tags="static menu">
                            <span class="nav-link-text" data-i18n="nav.static_meni">Link Menu Aplikasi</span>
                        </a>
                    </li>
                </ul>
            </li>
            @endhasanyrole
            @hasanyrole('superadmin|pusaka|kementrian|pemda|who')
            <li class="">
                <a href="#" title="Laporan" data-filter-tags="laporan">
                    <i class="fal fa-clipboard-list"></i>
                    <span class="nav-link-text" data-i18n="nav.laporan">Laporan</span>
                </a>
                <ul>
                    <li>
                        <a href="{{route('laporan.index')}}" title="Laporan Data" data-filter-tags="laporan data">
                            <span class="nav-link-text" data-i18n="nav.laporan_data">Data Laporan</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{route('chart.index')}}" title="Laporan Statistic"
                            data-filter-tags="Laporan Statistic">
                            <span class="nav-link-text" data-i18n="nav.laporan_statistic">Statistik Laporan</span>
                        </a>
                    </li>
                </ul>
            </li>
            @endhasanyrole
            @hasanyrole('superadmin|pusaka')
            <li class="">
                <a href="#" title="Operator" data-filter-tags="operator">
                    <i class="fal fa-cogs"></i>
                    <span class="nav-link-text" data-i18n="nav.operator">Operator</span>
                </a>
                <ul>
                    <li>
                        <a href="{{route('operator.index')}}" title="Operator Data" data-filter-tags="operator data">
                            <span class="nav-link-text" data-i18n="nav.operator_data">Data Operator</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{route('operator-type.index')}}" title="Operator Type"
                            data-filter-tags="operators type">
                            <span class="nav-link-text" data-i18n="nav.operator_type">Tipe Operator</span>
                        </a>
                    </li>
                </ul>
            </li>
            @hasanyrole('superadmin')
            <li class="">
                <a href="{{route('pelapor.index')}}" title="Pelapor" data-filter-tags="pelapor">
                    <i class="fal fa-users"></i>
                    <span class="nav-link-text" data-i18n="nav.pelapor">Pelapor</span>
                </a>
            </li>
            @endhasanyrole

            <li class="">
                <a href="#" title="Referensi" data-filter-tags="reference">
                    <i class="fal fa-tasks"></i>
                    <span class="nav-link-text" data-i18n="nav.reference">Referensi</span>
                </a>
                <ul>
                    <li>
                        <a href="{{route('pelanggaran.index')}}" title="Jenis Pelanggaran"
                            data-filter-tags="jenis pelanggaran">
                            <span class="nav-link-text" data-i18n="nav.jenis_pelanggaran">Jenis Pelanggaran</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{route('bentuk-pelanggaran.index')}}" title="Bentuk Pelanggaran"
                            data-filter-tags="bentuk pelanggaran">
                            <span class="nav-link-text" data-i18n="nav.bentuk-pelanggaran">Bentuk Pelanggaran</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{route('kawasan.index')}}" title="Kawasan" data-filter-tags="kawasan">
                            <span class="nav-link-text" data-i18n="nav.kawasan">Kawasan</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{route('province.index')}}" title="Province" data-filter-tags="province">
                            <span class="nav-link-text" data-i18n="nav.province">Province</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{route('kota.index')}}" title="City" data-filter-tags="city">
                            <span class="nav-link-text" data-i18n="nav.city">City</span>
                        </a>
                    </li>
                </ul>
            </li>
            @endhasanyrole
            @hasanyrole('superadmin')
            <li class="nav-title">ACL & Settings</li>
            <li class="">
                <a href="#" title="Theme Settings" data-filter-tags="acl settings">
                    <i class="fal fa-cog"></i>
                    <span class="nav-link-text" data-i18n="nav.acl_settings">Access Control List</span>
                </a>
                <ul>
                    <li>
                        <a href="{{route('users.index')}}" title="Users Management"
                            data-filter-tags="users managements">
                            <span class="nav-link-text" data-i18n="nav.users_management">Users</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{route('permissions.index')}}" title="Permissions Management"
                            data-filter-tags="permissions managements">
                            <span class="nav-link-text" data-i18n="nav.permissions_management">Permissions</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{route('roles.index')}}" title="Roles Management" data-filter-tags="roles management">
                            <span class="nav-link-text" data-i18n="nav.roles_managements">Roles</span>
                        </a>
                    </li>
                </ul>
            </li>

            <li class="">
                <a href="{{route('logs')}}" title="System Log" data-filter-tags="System Log">
                    <i class="fal fa-shield-check"></i>
                    <span class="nav-link-text" data-i18n="nav.system_log">System Logs</span>
                </a>
            </li>
            @endhasanyrole
        </ul>
        <div class="filter-message js-filter-message bg-success-600"></div>
    </nav>
    <!-- END PRIMARY NAVIGATION -->
    <!-- NAV FOOTER -->
    <div class="nav-footer shadow-top">
        <a href="#" onclick="return false;" data-action="toggle" data-class="nav-function-minify"
            class="hidden-md-down">
            <i class="ni ni-chevron-right"></i>
            <i class="ni ni-chevron-right"></i>
        </a>
    </div> <!-- END NAV FOOTER -->
</aside>