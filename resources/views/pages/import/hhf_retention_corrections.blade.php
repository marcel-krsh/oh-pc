@extends('layouts.allita')

@section('content')
	<script>
		// Update Tab Title - note the refresh button in tab text script.
		$('#list-tab-text').html(' : Import Parcels: Corrections Needed ');

		$('#detail-tab-1-icon').attr('uk-icon','shuffle');
		// display the tab
		$('#list-tab').show();
	</script>

	<script>
		UIkit.modal.alert('<h1 >Whoops!</h1><p>It looks like some of the information I received doesn\'t match up with my database\'s fields.</p><p>Don\'t worry though, you can make the needed corrections on this page.</p><p><strong>Be sure you scroll left and right on the list of corrections as this is a really wide table!</strong></p>');
	</script>
	<div class="uk-grid">
		<div class="uk-width-1-1">
			<form action="" method="post">
				<input type="hidden" name="program_id" value="{{$program_id}}">
				<input type="hidden" name="account_id" value="{{$account_id}}">

				@foreach ($corrections as $r => $row)
					@foreach ($row as $c => $value)
						<input type="hidden" name="corrections_{{ $r }}_{{ $c }}" value="{{ $value }}">
					@endforeach
				@endforeach
				<div class="uk-article">
					<div class="uk-block uk-block-primary uk-dark uk-light">
                        <div class="uk-container">
                        	<div class="uk-grid">
	                        	<div class="uk-width-1-5@m">
	                        		<p class="uk-text-center"><span class="a-circle-from-bottom big-blue-shadow" style="font-size: 150px;"></span></p>
	                        	</div>
	                            <div class="uk-width-4-5@m"><h1 class="blue-shadow">IMPORT SUMMARY</h1><hr style="border-top: 2px dotted;" /><br />
		                            <div class="uk-grid ">
		                            	
		                                <div class="uk-width-1-3@m uk-row-first">
		                                    <div class="uk-panel">
		                                        <p>{{ count($ic) }} @if(count($ic) > 1) ROWS @else ROW @endIf TO IMPORT </p><p> {{ count($ie) }} @if(count($ie) > 1) ROWS @else ROW @endIf THAT @if(count($ic) > 1) NEED @else NEEDS @endIf CORRECTED</p>
		                                    </div>
		                                </div>
		                                <div class="uk-width-1-3@m">
		                                    <div class="uk-panel">
		                                        <p>I went ahead and highligted the columns that need corrected in red for you.</p>
		                                    </div>
		                                </div>
		                                <div class="uk-width-1-3@m">
		                                    <div class="uk-panel">
		                                        <p>If you need to reference your excel file, the number on the left of each row is its corresponding number in your excel file.</p>
		                                    </div>
		                                </div>
		                            </div>
	                            </div>
	                        </div>
                        </div>
                    </div>
                </div>

				
				@if ($ie)
					
					
					<div class="uk-overflow-container">
					<table class="uk-table uk-table-condensed uk-table-striped">
						<thead>
							<tr>
								<th><small>#</small></th>
								<?php $totalColumns = 1; ?>
								@foreach (current($ic) as $th)
									<th><small>{{ strtoupper($th['excel_col']) }}</small></th>
									<?php $totalColumns++; ?>
								@endforeach
							</tr>
						</thead>
						<tbody>
							@foreach ($ie as $r => $error_row)
									
									<tr>
										<td>
											<small>{{ current($error_row)['excel_row_label'] }}</small>
											
										</td>
										@foreach ($ic[$r] as $c => $cell)
											<td class="{{ isset($ie[$r][$c]) ? 'error' : '' }}">


											
												@if (isset($ie[$r][$c]['options']))
													<select name="corrections_{{ $r}}_{{ $cell['db_col_num'] }}" class="uk-select {{ isset($ie[$r][$c]) ? 'uk-form-danger' : 'no-border no-background auto-width' }}" {{ !isset($ie[$r][$c]) ? 'readonly="true"' : '' }}>
														@foreach ($ie[$r][$c]['options'] as $o_k => $o_v)
															<option value="{{ $o_v }}">{{ $o_v }}</option>
														@endforeach
													</select>

												@else
													<input type="text" name="corrections_{{ $r}}_{{ $cell['db_col_num'] }}" value="uk-input {{ $cell['excel_val'] }}" class="{{ isset($ie[$r][$c]) ? 'uk-form-danger' : 'no-border no-background auto-width' }}" {{ !isset($ie[$r][$c]) ? 'readonly="true"' : '' }}>
												@endif


												@if (isset($ie[$r][$c]))
													<p class="uk-text-danger">{{ $ie[$r][$c]['message'] }}</p>
												@endif
											</td>
										@endforeach
									</tr>
									<tr>
										<td colspan="{{$totalColumns}}" style="padding:0px;">
											<small>ROW {{ current($error_row)['excel_row_label'] }} @if(count($error_row) < 3) ONLY @endIf NEEDS {{ count($error_row) }} @if(count($error_row) > 1) CORRECTIONS @else CORRECTION @endIf MADE</p>
										</td>
									<tr>
							@endforeach
						</tbody>
					</table>
				</div>
				@endif


				

				{{ csrf_field() }}

				@foreach ($mappings as $mapping)
					<input type="hidden" name="mappings[]" value="{{ $mapping }}">
				@endforeach
				<input type="hidden" name="table" value="{{ $table }}">
				<input type="hidden" name="filename" value="{{ $filename }}">

				<div class="uk-form-row">
					<button type="submit" name="submit" class="uk-margin-top uk-button uk-button-default blue-button uk-button-large uk-dark uk-light uk-width-1-1">SUBMIT CORRECTIONS</button>
					<p>
					<input type="checkbox"  class="uk-checkbox" name="skip_errors" id="skip_errors" value="1" onclick="if($('#skip_errors').is(':checked')) { UIkit.modal.alert('By checking this, I will NOT check your changes for errors, and ignore all rows that have errors in them.');}">
					<label for="skip_errors">Skip rows with errors. (Warning this will also skip any rows you attempted to fix, but did not do so correctly).</label>
				</p>
				</div><!-- uk-form-row -->
			</form>
		</div>
	@include('partials.helpers.landbank.reimbursement_steps')
@stop