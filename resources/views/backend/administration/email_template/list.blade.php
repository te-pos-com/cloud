@extends('layouts.app')

@section('content')
<h4 class="page-title">{{ _lang('Email Templates') }}</h4>

<div class="row">
	<div class="col-12">
		<div class="card">
			<div class="card-body">
				<table class="table data-table">
					<thead>
						<tr>
							<th>{{ _lang('Name') }}</th>
							<th>{{ _lang('Subject') }}</th>
							<th>{{ _lang('Action') }}</th>
						</tr>
					</thead>
					<tbody>
						@foreach($emailtemplates as $emailtemplate)
						<tr id="row_{{ $emailtemplate->id }}">
							<td class='name'>{{ ucwords(str_replace('_',' ',$emailtemplate->name)) }}</td>
							<td class='subject'>{{ $emailtemplate->subject }}</td>
							<td>
								<a href="{{action('EmailTemplateController@edit', $emailtemplate['id'])}}" class="btn btn-warning btn-sm"><i class="ti-pencil-alt"></i> {{ _lang('Edit') }}</a>
								<a href="{{action('EmailTemplateController@show', $emailtemplate['id'])}}" class="btn btn-primary btn-sm"><i class="ti-eye"></i> {{ _lang('View') }}</a>
							</td>
						</tr>
						@endforeach
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>
@endsection