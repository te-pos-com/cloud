@php $permissions = permission_list(); @endphp

<div class="sb-sidenav-menu-heading">{{ _lang('NAVIGATIONS') }}</div>

<a class="nav-link" href="{{ route('dashboard') }}">
    <div class="sb-nav-link-icon"><i class="ti-dashboard"></i></div>
    {{ _lang('Dashboard') }}
</a>

<a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#contacts" aria-expanded="false"
    aria-controls="collapseLayouts">
    <div class="sb-nav-link-icon"><i class="ti-id-badge"></i></div>
    {{ _lang('Contacts') }}
    <div class="sb-sidenav-collapse-arrow"><i class="ti-angle-down"></i></div>
</a>
<div class="collapse" id="contacts" aria-labelledby="headingOne" data-parent="#sidenavAccordion">
    <nav class="sb-sidenav-menu-nested nav">
        @if (in_array('contacts.index',$permissions))
            <a class="nav-link" href="{{ route('contacts.index') }}">{{ _lang('Contacts List') }}</a>
        @endif

        @if (in_array('contacts.create',$permissions))
        <a class="nav-link" href="{{ route('contacts.create') }}">{{ _lang('Add New') }}</a>
        @endif

        @if (in_array('contact_groups.index',$permissions))
        <a class="nav-link" href="{{ route('contact_groups.index') }}">{{ _lang('Contact Group') }}</a>
        @endif
    </nav>
</div>

@if (in_array('products.index',$permissions))
<a class="nav-link" href="{{ route('products.index') }}">
    <div class="sb-nav-link-icon"><i class="ti-shopping-cart"></i></div>
    {{ _lang('Products') }}
</a>
@endif

@if (in_array('services.index',$permissions))
<a class="nav-link" href="{{ route('services.index') }}">
    <div class="sb-nav-link-icon"><i class="ti-agenda"></i></div>
    {{ _lang('Services') }}
</a>
@endif

<a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#suppliers" aria-expanded="false"
    aria-controls="collapseLayouts">
    <div class="sb-nav-link-icon"><i class="ti-truck"></i></div>
    {{ _lang('Supplier') }}
    <div class="sb-sidenav-collapse-arrow"><i class="ti-angle-down"></i></div>
</a>
<div class="collapse" id="suppliers" aria-labelledby="headingOne" data-parent="#sidenavAccordion">
    <nav class="sb-sidenav-menu-nested nav">
        @if (in_array('suppliers.index',$permissions))
        <a class="nav-link" href="{{ route('suppliers.index') }}">{{ _lang('Supplier List') }}</a>
        @endif

        @if (in_array('suppliers.create',$permissions))
        <a class="nav-link" href="{{ route('suppliers.create') }}">{{ _lang('Add New') }}</a>
        @endif
    </nav>
</div>

<a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#purchase_orders" aria-expanded="false"
    aria-controls="collapseLayouts">
    <div class="sb-nav-link-icon"><i class="ti-bag"></i></div>
    {{ _lang('Purchase') }}
    <div class="sb-sidenav-collapse-arrow"><i class="ti-angle-down"></i></div>
</a>
<div class="collapse" id="purchase_orders" aria-labelledby="headingOne" data-parent="#sidenavAccordion">
    <nav class="sb-sidenav-menu-nested nav">
        @if (in_array('purchase_orders.index',$permissions))
        <a class="nav-link" href="{{ route('purchase_orders.index') }}">{{ _lang('Purchase Orders') }}</a>
        @endif

        @if (in_array('purchase_orders.create',$permissions))
        <a class="nav-link" href="{{ route('purchase_orders.create') }}">{{ _lang('New Purchase Order') }}</a>
        @endif
    </nav>
</div>

<a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#purchase_returns" aria-expanded="false"
    aria-controls="collapseLayouts">
    <div class="sb-nav-link-icon"><i class="ti-back-left"></i></div>
    {{ _lang('Return') }}
    <div class="sb-sidenav-collapse-arrow"><i class="ti-angle-down"></i></div>
</a>
<div class="collapse" id="purchase_returns" aria-labelledby="headingOne" data-parent="#sidenavAccordion">
    <nav class="sb-sidenav-menu-nested nav">
        @if (in_array('purchase_returns.index',$permissions))
        <a class="nav-link" href="{{ route('purchase_returns.index') }}">{{ _lang('Purchase Return') }}</a>
        @endif

        @if (in_array('sales_returns.index',$permissions))
        <a class="nav-link" href="{{ route('sales_returns.index') }}">{{ _lang('Sales Return') }}</a>
        @endif
    </nav>
</div>

<a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#sales" aria-expanded="false"
    aria-controls="collapseLayouts">
    <div class="sb-nav-link-icon"><i class="ti-shopping-cart-full"></i></div>
    {{ _lang('Sales') }}
    <div class="sb-sidenav-collapse-arrow"><i class="ti-angle-down"></i></div>
</a>
<div class="collapse" id="sales" aria-labelledby="headingOne" data-parent="#sidenavAccordion">
    <nav class="sb-sidenav-menu-nested nav">
        @if (in_array('invoices.create',$permissions))
        <a class="nav-link" href="{{ route('invoices.create') }}">{{ _lang('Create Invoice') }}</a>
        @endif

        @if (in_array('invoices.index',$permissions))
        <a class="nav-link" href="{{ route('invoices.index') }}">{{ _lang('Invoice List') }}</a>
        @endif

        @if (in_array('quotations.create',$permissions))
        <a class="nav-link" href="{{ route('quotations.create') }}">{{ _lang('Create Quotation') }}</a>
        @endif

        @if (in_array('quotations.index',$permissions))
        <a class="nav-link" href="{{ route('quotations.index') }}">{{ _lang('Quotation List') }}</a>
        @endif
    </nav>
</div>

<a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#accounts" aria-expanded="false"
    aria-controls="collapseLayouts">
    <div class="sb-nav-link-icon"><i class="ti-credit-card"></i></div>
    {{ _lang('Accounts') }}
    <div class="sb-sidenav-collapse-arrow"><i class="ti-angle-down"></i></div>
</a>
<div class="collapse" id="accounts" aria-labelledby="headingOne" data-parent="#sidenavAccordion">
    <nav class="sb-sidenav-menu-nested nav">
        @if (in_array('accounts.index',$permissions))
        <a class="nav-link" href="{{ route('accounts.index') }}">{{ _lang('All Account') }}</a>
        @endif

        @if (in_array('accounts.create',$permissions))
        <a class="nav-link" href="{{ route('accounts.create') }}">{{ _lang('Add New Account') }}</a>
        @endif

    </nav>
</div>

<a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#transactions" aria-expanded="false"
    aria-controls="collapseLayouts">
    <div class="sb-nav-link-icon"><i class="ti-receipt"></i></div>
    {{ _lang('Transactions') }}
    <div class="sb-sidenav-collapse-arrow"><i class="ti-angle-down"></i></div>
</a>
<div class="collapse" id="transactions" aria-labelledby="headingOne" data-parent="#sidenavAccordion">
    <nav class="sb-sidenav-menu-nested nav">
        @if (in_array('income.index',$permissions))
        <a class="nav-link" href="{{ route('income.index') }}">{{ _lang('Income/Deposit') }}</a>
        @endif

        @if (in_array('expense.index',$permissions))
        <a class="nav-link" href="{{ route('expense.index') }}">{{ _lang('Expense') }}</a>
        @endif

        @if (in_array('transfer.create',$permissions))
        <a class="nav-link" href="{{ route('transfer.create') }}">{{ _lang('Transfer') }}</a>
        @endif

        @if (in_array('income.income_calendar',$permissions))
        <a class="nav-link" href="{{ route('income.income_calendar') }}">{{ _lang('Income Calendar') }}</a>
        @endif

        @if (in_array('expense.expense_calendar',$permissions))
        <a class="nav-link" href="{{ route('expense.expense_calendar') }}">{{ _lang('Expense Calendar') }}</a>
        @endif
    </nav>
</div>

<a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#recurring_transaction" aria-expanded="false"
    aria-controls="collapseLayouts">
    <div class="sb-nav-link-icon"><i class="ti-wallet"></i></div>
    {{ _lang('Recurring Transaction') }}
    <div class="sb-sidenav-collapse-arrow"><i class="ti-angle-down"></i></div>
</a>
<div class="collapse" id="recurring_transaction" aria-labelledby="headingOne" data-parent="#sidenavAccordion">
    <nav class="sb-sidenav-menu-nested nav">
        @if (in_array('repeating_income.create',$permissions))
        <a class="nav-link" href="{{ route('repeating_income.create') }}">{{ _lang('Add Repeating Income') }}</a>
        @endif

        @if (in_array('repeating_income.index',$permissions))
        <a class="nav-link" href="{{ route('repeating_income.index') }}">{{ _lang('Repeating Income List') }}</a>
        @endif

        @if (in_array('repeating_expense.create',$permissions))
        <a class="nav-link" href="{{ route('repeating_expense.create') }}">{{ _lang('Add Repeating Expense') }}</a>
        @endif

        @if (in_array('repeating_expense.index',$permissions))
        <a class="nav-link" href="{{ route('repeating_expense.index') }}">{{ _lang('Repeating Expense List') }}</a>
        @endif
    </nav>
</div>

<a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#reports" aria-expanded="false"
    aria-controls="collapseLayouts">
    <div class="sb-nav-link-icon"><i class="ti-bar-chart"></i></div>
    {{ _lang('Reports') }}
    <div class="sb-sidenav-collapse-arrow"><i class="ti-angle-down"></i></div>
</a>
<div class="collapse" id="reports" aria-labelledby="headingOne" data-parent="#sidenavAccordion">
    <nav class="sb-sidenav-menu-nested nav">
      @if (in_array('reports.account_statement',$permissions))
      <a class="nav-link" href="{{ route('reports.account_statement') }}">{{ _lang('Account Statement') }}</a>
      @endif

      @if (in_array('reports.income_report',$permissions))
	  <a class="nav-link" href="{{ route('reports.income_report') }}">{{ _lang('Income Report') }}</a>
      @endif

      @if (in_array('reports.expense_report',$permissions))
	  <a class="nav-link" href="{{ route('reports.expense_report') }}">{{ _lang('Expense Report') }}</a>
      @endif

      @if (in_array('reports.transfer_report',$permissions))
	  <a class="nav-link" href="{{ route('reports.transfer_report') }}">{{ _lang('Transfer Report') }}</a>
      @endif

      @if (in_array('reports.income_vs_expense',$permissions))
	  <a class="nav-link" href="{{ route('reports.income_vs_expense') }}">{{ _lang('Income VS Expense') }}</a>
      @endif

      @if (in_array('reports.report_by_payer',$permissions))
	  <a class="nav-link" href="{{ route('reports.report_by_payer') }}">{{ _lang('Report by Payer') }}</a>
      @endif

      @if (in_array('reports.report_by_payee',$permissions))
	  <a class="nav-link" href="{{ route('reports.report_by_payee') }}">{{ _lang('Report by Payee') }}</a>
      @endif
    </nav>
</div>

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

        @if (in_array('product_units.index',$permissions))
        <a class="nav-link" href="{{ route('product_units.index') }}">{{ _lang('Product Unit') }}</a>
        @endif
        @if (in_array('product_merek.index',$permissions))
        <a class="nav-link" href="{{ route('product_merek.index') }}">Merek</a>
        @endif
        @if (in_array('product_kategori.index',$permissions))
        <a class="nav-link" href="{{ route('product_kategori.index') }}">Kategori</a>
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
        @if (in_array('chart_of_accounts.index',$permissions))
        <a class="nav-link" href="{{ route('chart_of_accounts.index') }}">{{ _lang('Income & Expense Types') }}</a>
        @endif

        @if (in_array('payment_methods.index',$permissions))
        <a class="nav-link" href="{{ route('payment_methods.index') }}">{{ _lang('Payment Methods') }}</a>
        @endif

        @if (in_array('taxs.index',$permissions))
        <a class="nav-link" href="{{ route('taxs.index') }}">{{ _lang('Tax Settings') }}</a>
        @endif
    </nav>
</div>