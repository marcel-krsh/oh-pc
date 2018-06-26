@extends('layouts.allita')

@section('content')
	<script>
		// Update Tab Title - note the refresh button in tab text script.
		$('#list-tab-text').html(' : Import Parcels: Please Map Your Columns ');

		$('#detail-tab-1-icon').attr('uk-icon','shuffle');
		// display the tab
		$('#list-tab').show();
	</script>

	@if ($errors->any())
		<div class="uk-width-1-1">
		<h3>Errors</h3>
		@foreach ($errors->all() as $error)
			<div class="uk-alert uk-alert-danger">{{ $error }}</div>
		@endforeach
		</div>
	@endif
	@if($skipMapping == 1)
	<h2 class="uk-text-center">Looks like everything lines up...</h2>
	<p class="uk-text-center">Someone used a handy template!</p>
	<a href="javascript:;" class=" import-smart-mapping" id="smart-mapping"></a>
	
	@else
	<h2 class="uk-text-center">Please Double Check I'm Putting Things in the Right Places</h2>
	<a href="javascript:;" class="uk-width-1-2 uk-button uk-button-default uk-button-small uk-align-center import-smart-mapping" id="smart-mapping">RESET TO MY MATCHES</a>
	@endIf
	<hr />
	
	<form action="{{ route('import.corrections') }}" method="post">
		<div class="uk-overflow-container">
		<table id="import-mapping-table" class="uk-table uk-table-condensed uk-table-striped">
			<thead>
				<tr>
					<th>YOUR DATA</th>
					<th>WHERE I'M PUTTING IT</th>
				</tr>
			</thead>
			<tbody>
				@foreach ($excel->first()->keys()->all() as $excel_column_name)
					<tr>
						<td>{{ $excel_column_name }}</td>
						<td>
							<select name="mappings[]" id="" class="uk-select uk-width-1-1">
								<option value="">IGNORE</option>
								@foreach ($columns as $key => $column)
									@if (!in_array($column['name'], $autofill))
										<option value="{{ $key }}">{{ $column['name'] }}</option>
									@endif
								@endforeach
							</select>
						</td>
					</tr>
				@endforeach
			</tbody>
		</table>
		</div>

		{{ csrf_field() }}
		<input type="hidden" name="table" value="{{ $table }}">
		<input type="hidden" name="filename" value="{{ $filename }}">

		<input type="hidden" name="program_id" value="{{ $program_id }}">
		<input type="hidden" name="account_id" value="{{ $account_id }}">

		
		
		<div class="uk-form-row">
			<button id="submit-mapping" type="submit" name="submit" class="uk-button uk-button-default uk-button-large uk-margin-top uk-align-center uk-width-1-2">GOOD TO GO!</button>
		</div><!-- uk-form-row -->
	</form>

	<script src="{{ asset('/js/jquery.js') }}"></script>
	<script>
		
			$(".import-smart-mapping").click(function(e) {
				e.preventDefault();
				$("#import-mapping-table tbody tr").each(function(i) {
					var row = $(this);
					row.find("select option").each(function(i) {
						var option = $(this);
						if (option.text() == row.find('td:first-child').text()) {
							option.prop('selected', true);
						}
					});
				});
			});
		
	</script>
	<script type="text/javascript">
		$('#smart-mapping').trigger('click');
		@if($skipMapping == 1)
		$('#submit-mapping').trigger('click');
		@endIf
	</script>
@include('partials.helpers.landbank.reimbursement_steps')
@stop
