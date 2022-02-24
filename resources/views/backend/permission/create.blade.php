@extends('layouts.app')

@section('content')
<h4 class="page-title">{{ _lang('Access Control') }}</h4>
		
<div class="row">
    <div class="col-lg-12">
        <form method="post" id="permissions" class="validate" autocomplete="off" action="{{ route('permission.store') }}">
            @csrf
			<div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="control-label">{{ _lang('Select Role') }}</label>
                                    <select class="form-control select2" id="role_id" name="role_id" required>
                                        <option value="">{{ _lang('Select One') }}</option>
                                        {{ create_option("staff_roles", "id", "name", $role_id) }}
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mt-4">
                <span class="d-none header-title">{{ _lang('Permission Control') }}</span>

                <div class="card-body">
                    <div class="row">
						<div class="col-md-12">
							<div id="accordion">
								@php $i = 1; @endphp
								@foreach($permission as $key => $val)
								<div class="card">
									<div class="card-header">
										<h4>
											<a class="card-link" data-toggle="collapse"
												href="#collapse-{{ explode("\\",$key)[3] }}">
												<i class="ti-arrow-right"></i>
												@if (str_replace("Controller","",explode("\\",$key)[3])=="Contact")
													{{ 'Pelanggan' }}
												@elseif (str_replace("Controller","",explode("\\",$key)[3])=="ContactGroup")
													{{ 'Group Pelanggan' }}
												@elseif (str_replace("Controller","",explode("\\",$key)[3])=="Invoice")
													{{ 'Penjualan' }}
												@elseif (str_replace("Controller","",explode("\\",$key)[3])=="CompanySettings")
													{{ 'Pengaturan Perusahaan' }}
												@elseif (str_replace("Controller","",explode("\\",$key)[3])=="CompanyEmailTemplate")
													{{ 'Template Email' }}
												@elseif (str_replace("Controller","",explode("\\",$key)[3])=="ProductUnit")
													{{ 'Satuan' }}
												@elseif (str_replace("Controller","",explode("\\",$key)[3])=="ProductMerek")
													{{ 'Merek' }}
												@elseif (str_replace("Controller","",explode("\\",$key)[3])=="ProductKategori")
													{{ 'Kategori' }}
												@elseif (str_replace("Controller","",explode("\\",$key)[3])=="Tax")
													{{ 'Pajak' }}
												@elseif (str_replace("Controller","",explode("\\",$key)[3])=="PaymentMethod")
													{{ 'Cara Pembayaran' }}
												@else
													{{ str_replace("Controller","",explode("\\",$key)[3]) }}
												@endif
											</a>
										</h4>
									</div>
									<div id="collapse-{{ explode("\\",$key)[3] }}" class="collapse">
										<div class="card-body">
											<table class="table">
												@foreach($val as $name => $url)
												@if (str_replace("Controller","",explode("\\",$key)[3])=="Report") 
													@if (str_replace("index","list",$name)=="reports.orderpembelian_isi"
														|| str_replace("index","list",$name)=="reports.persediaan_barang_isi"
														|| str_replace("index","list",$name)=="reports.pembelian_isi"
														|| str_replace("index","list",$name)=="reports.mutasi_stok_isi"
														|| str_replace("index","list",$name)=="reports.quotation_isi"
														|| str_replace("index","list",$name)=="reports.penjualan_isi"
														|| str_replace("index","list",$name)=="reports.returpembelian_isi"
														|| str_replace("index","list",$name)=="reports.returpenjualan_isi"
														|| str_replace("index","list",$name)=="reports.laba_rugi_isi"
														)
														@php
															goto lanjut;
														@endphp
													@endif
												@endif

												@if(jenis_langganan()=="POS")
													@if (str_replace("index","list",$name)=="reports.report_by_payee"
														|| str_replace("index","list",$name)=="reports.report_by_payer"
														|| str_replace("index","list",$name)=="reports.income_vs_expense"
														|| str_replace("index","list",$name)=="reports.transfer_report"
														|| str_replace("index","list",$name)=="reports.expense_report"
														|| str_replace("index","list",$name)=="reports.income_report"
														|| str_replace("index","list",$name)=="reports.returpenjualan_report"
														|| str_replace("index","list",$name)=="reports.returpembelian_report"
														|| str_replace("index","list",$name)=="reports.quotation_report"
														|| str_replace("index","list",$name)=="reports.orderpembelian_report"
														|| str_replace("index","list",$name)=="reports.account_statement"
														|| str_replace("index","list",$name)=="dashboard.current_day_expense"
														|| str_replace("index","list",$name)=="dashboard.current_month_expense"
														|| str_replace("index","list",$name)=="dashboard.financial_account_balance"
														)
														@php
															goto lanjut;
														@endphp
													@endif
												@endif
												<tr>
													<td>
														<div class="checkbox">
															<div class="custom-control custom-checkbox">
																<input type="checkbox" class="custom-control-input"
																	name="permissions[]" value="{{ $name }}"
																	id="customCheck{{ $i + 1 }}"
																	{{ array_search($name,$permission_list) !== FALSE ? "checked" : "" }}>
																<label class="custom-control-label"
																	for="customCheck{{ $i + 1 }}">{{ str_replace("index","list",$name) }}</label>
															</div>
														</div>
													</td>
												</tr>
												@php 
												lanjut:
												$i++; @endphp
												@endforeach
											</table>
										</div>
									</div>
								</div>
								@endforeach
							</div>
						</div>
    
                        <div class="col-md-12 mt-4">
                            <div class="form-group">
                                <button type="submit" class="btn btn-primary">{{ _lang('Save Permission') }}</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection