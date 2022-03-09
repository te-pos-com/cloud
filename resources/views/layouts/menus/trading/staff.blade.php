@php $permissions = permission_list(); @endphp

<div class="scrollmenu">
    <div class="sb-sidenav-menu-heading">{{ _lang('NAVIGATIONS') }}</div>
    <a class="nav-link" href="{{ route('dashboard') }}">
        <div class="sb-nav-link-icon"><i class="ti-dashboard"></i></div>
        {{ _lang('Dashboard') }}
    </a>

    <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#master" aria-expanded="false"
        aria-controls="collapseLayouts">
        <div class="sb-nav-link-icon"><i class="ti-id-badge"></i></div>
        Master
        <div class="sb-sidenav-collapse-arrow"><i class="ti-angle-down"></i></div>
    </a>
    <div class="collapse" id="master" aria-labelledby="headingOne" data-parent="#sidenavAccordion">
        <nav class="sb-sidenav-menu-nested nav">
            @if (in_array('cabang.index',$permissions))
                <a class="nav-link" href="{{ route('cabang.index') }}">{{ _lang('Cabang') }}</a>
            @endif
            @if (in_array('gudang.index',$permissions))
                <a class="nav-link" href="{{ route('gudang.index') }}">{{ _lang('Gudang') }}</a>
            @endif
            @if (in_array('product_unit.index',$permissions))
                <a class="nav-link" href="{{ route('product_units.index') }}">{{ _lang('Product Unit') }}</a>
            @endif
            @if (in_array('product_merek.index',$permissions))
                <a class="nav-link" href="{{ route('product_merek.index') }}">{{ _lang('Product Merek') }}</a>
            @endif
            @if (in_array('product_kategori.index',$permissions))
                <a class="nav-link" href="{{ route('product_kategori.index') }}">{{ _lang('Product Kategori') }}</a>
            @endif
            @if (in_array('products.index',$permissions))
                <a class="nav-link" href="{{ route('products.index') }}">{{ _lang('Products') }}</a>
            @endif
            @if (in_array('suppliers.index',$permissions))
                <a class="nav-link" href="{{ route('suppliers.index') }}">{{ _lang('Supplier List') }}</a>
            @endif
            @if (in_array('contact_groups.index',$permissions))
                <a class="nav-link" href="{{ route('contact_groups.index') }}">{{ _lang('Contact Group') }}</a>
            @endif
            @if (in_array('contacts.index',$permissions))
                <a class="nav-link" href="{{ route('contacts.index') }}">{{ _lang('Contacts List') }}</a>
            @endif
        </nav>
    </div>

    @if (in_array('pembelian.index',$permissions))
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#purchase_orders" aria-expanded="false"
            aria-controls="collapseLayouts">
            <div class="sb-nav-link-icon"><i class="ti-bag"></i></div>
            {{ _lang('Purchase') }}
            <div class="sb-sidenav-collapse-arrow"><i class="ti-angle-down"></i></div>
        </a>
        <div class="collapse" id="purchase_orders" aria-labelledby="headingOne" data-parent="#sidenavAccordion">
            <nav class="sb-sidenav-menu-nested nav">
                    <a class="nav-link" href="{{ route('pembelian.index') }}">{{ _lang('Purchase') }}</a>
            </nav>
        </div>
    @endif

    @if (in_array('invoices.create',$permissions))
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#sales" aria-expanded="false"
            aria-controls="collapseLayouts">
            <div class="sb-nav-link-icon"><i class="ti-shopping-cart-full"></i></div>
            {{ _lang('Sales') }}
            <div class="sb-sidenav-collapse-arrow"><i class="ti-angle-down"></i></div>
        </a>
        <div class="collapse" id="sales" aria-labelledby="headingOne" data-parent="#sidenavAccordion">
            <nav class="sb-sidenav-menu-nested nav">
                    <a class="nav-link" href="{{ route('invoices.index') }}">{{ _lang('Invoice List') }}</a>
            </nav>
        </div>
    @endif


    <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#reports" aria-expanded="false"
        aria-controls="collapseLayouts">
        <div class="sb-nav-link-icon"><i class="ti-bar-chart"></i></div>
        {{ _lang('Reports') }}
        <div class="sb-sidenav-collapse-arrow"><i class="ti-angle-down"></i></div>
    </a>
    <div class="collapse" id="reports" aria-labelledby="headingOne" data-parent="#sidenavAccordion">
        <nav class="sb-sidenav-menu-nested nav">
        @if (in_array('reports.persediaan_barang_report',$permissions))
            <a class="nav-link" href="{{ route('reports.persediaan_barang_report') }}">{{ _lang('Laporan Persediaan') }}</a>
        @endif
        @if (in_array('reports.mutasi_stok_report',$permissions))
            <a class="nav-link" href="{{ route('reports.mutasi_stok_report') }}">{{ _lang('Laporan Mutasi Stok') }}</a>
        @endif
        @if (in_array('reports.pembelian_report',$permissions))
            <a class="nav-link" href="{{ route('reports.pembelian_report') }}">{{ _lang('Laporan Pembelian') }}</a>
        @endif
        @if (in_array('reports.penjualan_report',$permissions))
            <a class="nav-link" href="{{ route('reports.penjualan_report') }}">{{ _lang('Laporan Penjualan') }}</a>
        @endif
        @if (in_array('reports.laba_rugi_report',$permissions))
            <a class="nav-link" href="{{ route('reports.laba_rugi_report') }}">{{ _lang('Laporan Laba/Rugi') }}</a>
        @endif
        </nav>
    </div>



    <div class="sb-sidenav-menu-heading">{{ _lang('Company Settings') }}</div>

    <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#staffs" aria-expanded="false"
        aria-controls="collapseLayouts">
        <div class="sb-nav-link-icon"><i class="ti-user"></i></div>
        {{ _lang('Staff Management') }}
        <div class="sb-sidenav-collapse-arrow"><i class="ti-angle-down"></i></div>
    </a>
    <div class="collapse" id="staffs" aria-labelledby="headingOne" data-parent="#sidenavAccordion">
        <nav class="sb-sidenav-menu-nested nav">
            @if (in_array('staffs.index',$permissions))
                <a class="nav-link" href="{{ route('staffs.index') }}">{{ _lang('All Staff') }}</a>
            @endif
            @if (in_array('staffs.create',$permissions))
                <a class="nav-link" href="{{ route('staffs.create') }}">{{ _lang('Add New') }}</a>
            @endif
            @if (in_array('roles.index',$permissions))
                <a class="nav-link" href="{{ route('roles.index') }}">{{ _lang('Staff Roles') }}</a>
            @endif
            @if (in_array('permission.index',$permissions))
                <a class="nav-link" href="{{ route('permission.index') }}">{{ _lang('Access Control') }}</a>
            @endif
        </nav>
    </div>

    <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#company_settings" aria-expanded="false"
        aria-controls="collapseLayouts">
        <div class="sb-nav-link-icon"><i class="ti-settings"></i></div>
        {{ _lang('Company Settings') }}
        <div class="sb-sidenav-collapse-arrow"><i class="ti-angle-down"></i></div>
    </a>
    <div class="collapse" id="company_settings" aria-labelledby="headingOne" data-parent="#sidenavAccordion">
        <nav class="sb-sidenav-menu-nested nav">
            @if (in_array('company.change_settings',$permissions))
                <a class="nav-link" href="{{ route('company.change_settings') }}">{{ _lang('Company Settings') }}</a>
            @endif
            @if (in_array('company_email_template.index',$permissions))
                <a class="nav-link" href="{{ route('company_email_template.index') }}">{{ _lang('Email Template') }}</a>
            @endif
        </nav>
    </div>

    <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#transaction_settings" aria-expanded="false"
        aria-controls="collapseLayouts">
        <div class="sb-nav-link-icon"><i class="ti-credit-card"></i></div>
        {{ _lang('Transaction Settings') }}
        <div class="sb-sidenav-collapse-arrow"><i class="ti-angle-down"></i></div>
    </a>
    <div class="collapse" id="transaction_settings" aria-labelledby="headingOne" data-parent="#sidenavAccordion">
        <nav class="sb-sidenav-menu-nested nav">
            @if (in_array('payment_methods.index',$permissions))
                <a class="nav-link" href="{{ route('payment_methods.index') }}">{{ _lang('Payment Methods') }}</a>
            @endif
            @if (in_array('taxs.index',$permissions))
                <a class="nav-link" href="{{ route('taxs.index') }}">{{ _lang('Tax Settings') }}</a>
            @endif
        </nav>
    </div>
</div>

