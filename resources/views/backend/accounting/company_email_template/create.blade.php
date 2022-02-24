@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h4 class="header-title">{{ _lang('Create Email Template') }}</h4>
            </div>

            <div class="card-body">
                <form method="post" class="validate" autocomplete="off"
                    action="{{ route('company_email_template.store') }}" enctype="multipart/form-data">
                    {{ csrf_field() }}

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label">{{ _lang('Related To') }}</label>
                                <select class="form-control auto-select"
                                    data-selected="{{ old('related_to','invoice') }}" name="related_to"
                                    id="email_template_related_to" required>
                                    <option value="invoice">{{ _lang('Invoice') }}</option>
                                    @if(jenis_langganan()=="POS")
                                    @else
                                        <option value="quotation">{{ _lang('Quotation') }}</option>
                                    @endif
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label">{{ _lang('Name') }}</label>
                                <input type="text" class="form-control" name="name" value="{{ old('name') }}" required>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="control-label">{{ _lang('Subject') }}</label>
                                <input type="text" class="form-control" name="subject" value="{{ old('subject') }}"
                                    required>
                            </div>
                        </div>

                        <div class="col-md-12">
							<pre id="invoice-paremeter" class="border border-info p-2">{customer_name},{invoice_no},{invoice_date},{due_date},{payment_status},{grand_total},{amount_due},{total_paid},{invoice_link}</pre>
							<pre id="quotation-paremeter" class="border border-info p-2 d-none">{customer_name},{quotation_no},{quotation_date},{grand_total},{quotation_link}</pre>
						</div>

                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="control-label">{{ _lang('Body') }}</label>
                                <textarea class="form-control summernote" name="body">{{ old('body') }}</textarea>
                            </div>
                        </div>


                        <div class="col-md-12">
                            <div class="form-group">
                                <button type="submit" class="btn btn-primary btn-lg"><i class="ti-save"></i>
                                    {{ _lang('Save') }}</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection