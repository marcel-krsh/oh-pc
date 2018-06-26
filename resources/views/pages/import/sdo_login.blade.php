@extends('layouts.allita')

@section('content')
<div class="uk-grid">
	
	
	@if($step == 1)
	<div class="uk-width-1-1 uk-width-1-3@m uk-push-1-3" data-uk-scrollspy="{cls:'uk-animation-slide-bottom',delay:900}">	
		<h1 class="uk-margin-top"><img src="https://savethedreamohio.gov/Areas/Admin/Content/Icons/apple-icon-57x57.png" > Allita HHF Access</h1>
		<hr />
		<form action="/login_sdo" method="post">
			<h4 class="uk-margin-bottom">Please provide your Allita HHF credentials:<br /><br /></h4>
			<div class="uk-form-row">
				<div class="uk-grid">
				<label for="userName" class="uk-width-1-1 uk-width-1-2@m">ALLITA HHF USERNAME: </label>
				<input type="text" name="userName" value="{{Auth::user()->email}}" class="uk-input uk-width-1-1 uk-width-1-2@m"></div>
				</div>
			<div class="uk-form-row">
				<div class="uk-grid">
					<label for="password" class="uk-width-1-1 uk-width-1-2@m">ALLITA HHF PASSWORD: </label>
					<input type="password" name="password" value=""  class="uk-input uk-width-1-1 uk-width-1-2@m">
				</div>
			</div>
			<div class="uk-form-row">
				<div class="uk-grid">
					<button class="uk-button uk-button-default uk-width-1-1 uk-width-1-2@m uk-push-1-2" type="submit"><span class="a-avatar-key"></span> LOGIN</button>
				</div>
			</div>
			{{ csrf_field() }}
		</form>
		<hr class="uk-margin-large-top" />
	</div>
	@endIf

	@if($step == 2)
	<div class="uk-width-1-1 uk-width-1-3@m uk-push-1-3" data-uk-scrollspy="{cls:'uk-animation-fade',delay:900}">	
		<h1 class="uk-margin-top"><img src="https://savethedreamohio.gov/Areas/Admin/Content/Icons/apple-icon-57x57.png" > Allita HHF Access</h1>
		<hr />
		<form action="/verify_sdo" method="post" class="uk-form">
			<h4 class="uk-margin-bottom">Please enter the verification code:<br /><br /></h4>
			<div class="uk-form-row">
				<div class="uk-grid">
				<label for="userName" class="uk-width-1-1 uk-width-1-2@m">ALLITA HHF V-CODE: </label>
				<input type="text" name="userName" value="" class="uk-input uk-width-1-1 uk-width-1-2@m"></div>
			</div>
			<div class="uk-form-row">
				<div class="uk-grid">
					<button class="uk-button uk-button-default uk-width-1-1 uk-width-1-2@m uk-push-1-2" type="submit"><span class="a-locked-focus"></span> SUBMIT CODE</button>
				</div>
			</div>
			{{ csrf_field() }}
		</form>
		<hr class="uk-margin-large-top" />
	</div>
	@endIf

	@if($step == 3)
	
	<div class="uk-width-1-1 uk-width-1-3@m uk-push-1-3" data-uk-scrollspy="{cls:'uk-animation-fade',delay:900}">	
		<h1 class="uk-margin-top"><img src="https://savethedreamohio.gov/Areas/Admin/Content/Icons/apple-icon-57x57.png" > Initiate HHF Import</h1>
		<hr />
		
			<h4 class="uk-margin-bottom">Please enter the verification code:<br /><br /></h4>
			<div class="uk-grid">
				<div class="uk-width-1-1" id="import-results">
					<!-- insert progress here -->
				</div>
			</div>
			<div class="uk-form-row">
				<div class="uk-grid">
					<a onclick="checkImportProgress();" class="uk-button uk-button-default uk-width-1-1 uk-width-1-2@m uk-push-1-2" type="submit"><span class="a-lower"></span> START IMPORT</a>
				</div>
			</div>
		
		<hr class="uk-margin-large-top" />
	</div>
	<script>
		function checkImportProgress(){
			$('#import-results').load('/sdo_import_progress', function(response, status, xhr) {
								  if (status == "error") {
								  	if(xhr.status == "401") {
								  		var msg = "<h2>SERVER ERROR 401 :(</h2><p>Looks like your login session has expired. Please refresh your browser window to login again.</p>";
								  	} else if( xhr.status == "500"){
								  		var msg = "<h2>SERVER ERROR 500 :(</h2><p>I ran into trouble checking the progress of your import. Please contact support and let them know.</p>";
								  	} else {
								  		var msg = "<h2>"+xhr.status + " " + xhr.statusText +"</h2><p>Sorry, but there was an error and it isn't one I was expecting. Please contact support and let them know how you came to this page and what you clicked on to trigger this message.";
								  	}
								    
								    UIkit.modal.alert(msg);
								  }
								});
		}
	</script>	
	@endIf

</div>
@if(strlen($message)>0)
		<script>
			UIkit.modal.alert('{{$message}}');
		</script>
	@endif
@stop