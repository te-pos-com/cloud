<div class="sb-sidenav-menu-heading">{{ _lang('NAVIGATIONS') }}</div>

<a class="nav-link" href="{{ route('dashboard') }}">
    <div class="sb-nav-link-icon"><i class="ti-dashboard"></i></div>
    {{ _lang('Dashboard') }}
</a>


<a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#userManagement" aria-expanded="false"
    aria-controls="collapseLayouts">
    <div class="sb-nav-link-icon"><i class="ti-user"></i></div>
    {{ _lang('User Management') }}
    <div class="sb-sidenav-collapse-arrow"><i class="ti-angle-down"></i></div>
</a>
<div class="collapse" id="userManagement" aria-labelledby="headingOne" data-parent="#sidenavAccordion">
    <nav class="sb-sidenav-menu-nested nav">
        <a class="nav-link" href="{{ route('users.index') }}">{{ _lang('All Users') }}</a>
        <a class="nav-link" href="{{ route('users.create') }}">{{ _lang('Add New User') }}</a>
    </nav>
</div>

<a class="nav-link" href="{{ route('users.membership_payments') }}">
    <div class="sb-nav-link-icon"><i class="ti-credit-card"></i></div>
    {{ _lang('Membership Payments') }}
</a>

<div class="sb-sidenav-menu-heading">{{ _lang('System Settings') }}</div>

<a class="nav-link" href="{{ route('settings.update_settings') }}">
    <div class="sb-nav-link-icon"><i class="ti-settings"></i></div>
    {{ _lang('General Settings') }}
</a>

<a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#languages" aria-expanded="false"
    aria-controls="collapseLayouts">
    <div class="sb-nav-link-icon"><i class="ti-world"></i></div>
    {{ _lang('Languages') }}
    <div class="sb-sidenav-collapse-arrow"><i class="ti-angle-down"></i></div>
</a>
<div class="collapse" id="languages" aria-labelledby="headingOne" data-parent="#sidenavAccordion">
    <nav class="sb-sidenav-menu-nested nav">
        <a class="nav-link" href="{{ route('languages.index') }}">{{ _lang('All Language') }}</a>
        <a class="nav-link" href="{{ route('languages.create') }}">{{ _lang('Add New') }}</a>
    </nav>
</div>

<a class="nav-link" href="{{ route('email_templates.index') }}">
    <div class="sb-nav-link-icon"><i class="ti-email"></i></div>
    {{ _lang('Email Template') }}
</a>

<a class="nav-link" href="{{ route('database_backups.list') }}">
    <div class="sb-nav-link-icon"><i class="ti-harddrives"></i></div>
    {{ _lang('Database Backup') }}
</a>