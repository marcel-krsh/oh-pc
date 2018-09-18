@extends('layouts.allita')

@section('content')

<?php // set some base vars for the page
 $selectedProgram = "";
?>

	<script>
		// Update Tab Title - note the refresh button in tab text script.
		$('.list-tab-text').html(' : Import Parcels: Corrections Needed ');

		$('#list-tab > a > span').addClass('a-circle-from-bottom');
		// display the tab
		$('#list-tab').show();

		// Watch the file form
		
	</script>
<div class="uk-grid">

	@if ($errors->any())
		<div class="uk-width-1-1">
		<h3>Errors</h3>
		@foreach ($errors->all() as $error)
			<div class="uk-alert uk-alert-danger">{{ $error }}</div>
		@endforeach
		</div>
	@endif

<div class="uk-width-1-1 uk-width-2-3@m uk-align-center uk-margin-top no-padding-left">
    <div class="uk-panel uk-panel-box uk-text-center">
		<br>
		<div class="uk-border-circle uk-align-center" style=" width: 80px; height: 80px; font-size: 30px;">
			<span class="a-circle-from-bottom uk-icon-large uk-align-center uk-margin-top"></span>
		</div><br>
		<h1 class="uk-panel-title">Let's Import Some Parcels</h1>

			<form id="import-parcels" action="{{ route('import.mappings') }}" method="post" enctype="multipart/form-data" class=" uk-form-stacked">

			{{ csrf_field() }}
			<input type="hidden" name="table" id="table" value="parcels">
				
				@if(count($programs)>1)
				<?php $selectedProgram = 0; ?>
				<select name="program_id" class="uk-select uk-width-1-1 uk-margin-bottom" id="select-program-id" data-select="account-id">
					<option value="">Please Select the Program to Assign to the Imported Parcels</option>
					@foreach($programs as $data)
						@if($data->program_id != 1)
							<option value="{{$data->program_id}}"
									@if(Auth::user()->entity_id == $data->entity_id && Auth::user()->entity_id != 1 )
										<?php $selectedProgram = $data->program_id; ?>
										SELECTED
									@endIf >PROGRAM: {{$data->program_name}}
							</option>
						@endIf
					@endForEach
					</select>

				@elseIf(count($programs)== 1)
					<input type="hidden" name="program_id" value="{{$programs[0]->program_id}}" id="select-program-id">
				@else
					<div class="uk-alert uk-alert-danger"><h2>No program ID was selected!</h2>The programs table returned no active programs for your organization:<pre><?php print_r($programs);?></pre></div>
				@endIf
				
				@if(count($accounts)>1)
					<select name="account_id" id="select-account-id" class="uk-select uk-width-1-1 uk-margin-bottom" data-select="select-program-id">
					<option value="">Please Select the Account to Assign to the Imported Parcels</option>
					@foreach($accounts as $data)
						<option value="{{$data->account_id}}" @if(isset($data->program_id))data-program="{{$data->program_id}}"@endif
								@if(isset($data->program_id) && $selectedProgram == $data->program_id) SELECTED @endIf
						>
							ACCOUNT: {{$data->account_name}}
						</option>
					@endForEach
					</select>
				@elseIf(count($accounts)==1)
					<input type="hidden" name="account_id" value="{{$accounts[0]->account_id}}" id="select-account-id">
				@else
					<div class="uk-alert uk-alert-danger"><h2>No account ID was selected!</h2>The accounts table returned no active accounts for your program:<pre><?php print_r($accounts);?></pre></div>
				@endIf
			@if(count($programs)>0 && count($accounts)>0)
			<label for="file-upload" class="custom-file-upload">
			    <span class="a-file-copy-2"></span> &nbsp;SELECT YOUR CSV OR XLS PARCEL FILLED FILE
			</label>
			 
			<input id="file-upload" name="data" type="file"/><br />
			<span id="file-selected"></span>
			<br />
			
			<script>
			$('#file-upload').bind('change', function() { var fileName = ''; fileName = $(this).val(); 
				nameLength = fileName.length;
				extensionStart = nameLength - 3;
				if(fileName.substring(extensionStart,nameLength) == 'csv'
					|| fileName.substring(extensionStart,nameLength) == 'CSV'
					|| fileName.substring(extensionStart,nameLength) == 'xls'
					|| fileName.substring(extensionStart,nameLength) == 'XLS'
					|| fileName.substring(extensionStart,nameLength) == 'LSX'
					|| fileName.substring(extensionStart,nameLength) == 'lsx'){
						if($('#select-program-id').val() > 0 && $('#select-account-id').val() >0){
							$('#file-selected').html('OK, I\'m Uploading '+fileName.substring(12,500));
						 	$('#import-parcels').submit();
					 	} else {
					 		UIkit.modal.alert('<h2>OOPS!</h2><p>Please select a program and account to associate to the upload!</p><p> You\'ll need to select your document again to upload it.');
					 		$('#file-upload').val('');
					 	}
					} else {
						UIkit.modal.alert('OOPS! - That\'s not a CSV, XLSX or XLS file. Please try again.');
						$('#file-upload').val('');
					}

				}) 
			</script>
			@endif
			

			

		
		</form>
	 </div>
	</div>
	 <div class="uk-width-1-1 no-padding-left">
	 @if(count($programs)>1)
	 	<script>
		 	function getTemplate(){
		 	
		 		
		 		$programId = $('#program-id').val();
		 		
		 		if($programId < 1){
					UIkit.modal.alert('<h2>Oops!</h2><p>Please select which program for which you\'re creating this template.</p><small>I have great grammar, don\'t I? ;) </small>');
				} else {
					$('#getTemplate').submit();
				}

		 	}
		 	@if(count($accounts)>1)
		 	/// Auto select matching Account.
		 	$('#select-program-id').change(function(e) {
			    
			   $('#program-id').val($('#select-program-id').val());
			   $('#select-account-id').find("[data-program='"+$('#select-program-id').val()+"']").attr('selected','selected');

			}); //.attr('selected','selected');        
			
			@endIf

	 	</script>
	 	<form method="GET" action="/import_parcels_template" id="getTemplate">
	 		<input id="program-id" type="hidden" name="program_id" value="{{$selectedProgram}}">
	 		
	 		<a class="uk-button uk-button-default uk-button-large uk-align-center uk-width-2-3@m" onclick="getTemplate();" target="_blank"><span class="a-lower"></span> DOWNLOAD EXCEL IMPORT TEMPLATE</a>
	 	</form>
	 @elseif(count($programs)==1 && count($accounts)>0)
	 <a class="uk-button uk-button-default uk-button-large uk-align-center uk-width-2-3@m" href="/import_parcels_template?program_id={{$programs[0]->program_id}}" target="_blank"><span class="a-lower"></span> DOWNLOAD EXCEL IMPORT TEMPLATE</a>
	 @else
	 <div class="uk-alert">Sorry, you cannot upload any parcels unless you have both an active Program and an active Account.</div>
	 @endif
	 <hr />
	
</div>

	@include('partials.helpers.landbank.reimbursement_steps')
@stop
