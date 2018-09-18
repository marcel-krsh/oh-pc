@extends('layouts.allita')

@section('content')
	<script>
		// Update Tab Title - note the refresh button in tab text script.
		$('.list-tab-text').html(' : Import Costs: Corrections Needed ');

		$('#list-tab > a > span').attr('uk-icon','cloud-upload');
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

<div class="uk-width-1-1 uk-width-2-3@m uk-align-center uk-margin-top">
    <div class="uk-panel uk-panel-box uk-text-center">
       
            <br /><div class="uk-border-circle uk-align-center" style="background: white; border: 1px solid lightgray; width: 80px;
    height: 80px;font-size: 18px;"><span class="a-circle-from-bottom uk-icon-large uk-align-center uk-margin-top"></span></div><br /><h1 class="uk-panel-title">Let's Import Some Parcels</h1>
        
   
	
			<form id="import-parcels" action="{{ route('import.mappings') }}" method="post" enctype="multipart/form-data" class=" uk-form-stacked">

			{{ csrf_field() }}
			<input type="hidden" name="table" id="table" value="parcels">
			
				@if(count($programs)>1)
				<select name="program_id" class="uk-select uk-width-1-1 uk-margin-bottom">
					<option value="">Please Select the Program to Assign to the Imported Parcels</option>
					@foreach($programs as $data)
					<option value="{{$data->program_id}}">{{$data->program_name}}</option>
					@endForEach
					</select>
				@else
					<input type="hidden" name="program_id" value="{{$programs[0]->program_id}}">
				@endIf
				
				@if(count($accounts)>1)
					<select name="account_id" class="uk-select uk-width-1-1 uk-margin-bottom">
					<option value="">Please Select the Account to Assign to the Imported Parcels</option>
					@foreach($accounts as $data)
					<option value="{{$data->account_id}}">{{$data->account_name}}</option>
					@endForEach
					</select>
				@else
					<input type="hidden" name="account_id" value="{{$accounts[0]->account_id}}">
				@endIf
			
			<label for="file-upload" class="custom-file-upload">
			    <span class="a-file-chart"></span> &nbsp;SELECT YOUR CSV OR XLS PARCEL FILLED FILE
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
						$('#file-selected').html('OK, I\'m Uploading '+fileName.substring(12,500));
					 	$('#import-parcels').submit();
					} else {
						UIkit.modal.alert('OOPS! - That\'s not a CSV, XLSX or XLS file. Please try again.');
						$('#file-upload').val('');
					}

				}) 
			</script>

			

			

		
		</form>
	 </div>
	</div>
	 <div class="uk-width-1-1">
	 <a class="uk-button uk-button-default uk-button-large uk-align-center uk-width-2-3@m" href="/import_parcels_template" target="_blank"><span class="a-higher"></span> DOWNLOAD EXCEL IMPORT TEMPLATE</a>
	 <hr />
	
</div>

@include('partials.helpers.landbank.reimbursement_steps');

@stop
