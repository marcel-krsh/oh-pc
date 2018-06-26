@extends('layouts.allita')

@section('content')
<?php  
// if(extension_loaded('php_zip'))
// {
//       echo "<script>UIkit.modal.alert('php_zip');</script>";
// } else {
// 	echo "<script>UIkit.modal.alert('NO php_zip');</script>";
// }
// echo phpinfo();
?>
	<script>
		// Update Tab Title - note the refresh button in tab text script.
		$('.list-tab-text').html(' : Import HHF Retention Parcels');

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

<div class="uk-width-1-1 uk-width-2-3@m uk-align-center uk-margin-top">
    <div class="uk-panel uk-panel-box uk-text-center">
       
            <br /><div class="uk-border-circle uk-align-center" style=" width: 80px;
    height: 80px;
    font-size: 30px;"><span class="a-circle-from-bottom uk-icon-large uk-align-center uk-margin-top"></span></div><br /><h1 class="uk-panel-title">Let's Import The HHF Retention Parcels</h1>
        
   
	
			<form id="import-parcels" action="{{ route('import.hhf_retention_mappings') }}" method="post" enctype="multipart/form-data" class="uk-form-stacked">

			{{ csrf_field() }}
			<input type="hidden" name="table" id="table" value="parcels">
			
				

					<input type="hidden" name="program_id" value="1">
				
				
				
					<input type="hidden" name="account_id" value="1">
				
			
			<label for="file-upload" class="custom-file-upload">
			    <span class="a-file-copy-2"></span> &nbsp;I CAN ONLY ACCEPT A CSV FILE OF THE HHF DATA...
			</label>
			 
			<input id="file-upload" name="data" type="file"/><br />
			<span id="file-selected"></span>
			<br />
			
			<script>
			$('#file-upload').bind('change', function() { var fileName = ''; fileName = $(this).val(); 
				nameLength = fileName.length;
				extensionStart = nameLength - 3;
				if(fileName.substring(extensionStart,nameLength) == 'csv'
					|| fileName.substring(extensionStart,nameLength) == 'CSV'){
						$('#file-selected').html('OK, I\'m Uploading '+fileName.substring(12,500));
					 	$('#import-parcels').submit();
					} else {
						UIkit.modal.alert('OOPS! - That\'s not a CSV file. Please try again.');
						$('#file-upload').val('');
					}

				}) 
			</script>

			

			

		
		</form>
	 </div>
	</div>
	 <div class="uk-width-1-1">
	 <hr />
	
</div>

	@include('partials.helpers.landbank.reimbursement_steps');
@stop
