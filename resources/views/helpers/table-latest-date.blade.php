@extends('layouts.plain-allita')
@section('content')
<div class="uk-vertical-align uk-text-center uk-margin-large-bottom  uk-margin-top">
	<div class="uk-vertical-align-middle login-panel"  id="login-panel">
		<h2 style="margin-top:0px">Developer Page</h2>
		@if(!$error)
		<p>Change <code>{{ $table }}</code> table <code>last_edited time</code> to <code>{{ $decage_ago }}</code></p>
		<form class="uk-panel uk-panel-box uk-form"  id="developerForm" role="form" method="POST" action="{{ url('developer/last-edited-date-to-decade-ago') }}">
			@if (count($errors) > 0)
			<div class="alert alert-danger uk-text-danger">
				<ul>
					@foreach ($errors->all() as $error2)
					{{ $error2 }}<br>
					@endforeach
				</ul>
			</div>
			@endif
			{{ csrf_field() }}
			<div class="uk-width-1-1 uk-margin-top form-group">
				<input type="hidden" name="table" value="{{ $table }}">
			</div>
			<div class="uk-form-row" uk-scrollspy="target:.uk-button;cls:uk-animation-slide-top-small;">
				<button type="submit" class="uk-width-1-1 uk-button uk-button-primary uk-button-large">Change Last Edited Time</button>
			</div>
			<div class="uk-form-row uk-text-small" uk-scrollspy="target:.next-items;cls:uk-animation-fade;">
				<p class="uk-float-right uk-link-muted next-items">This process might take a while to complete...</p>
			</div>
		</form>
		@else
		<p>Given table <code>{{ $table }}</code>  doesn't exist, please check table name again.</p>
		@endif


	</div>
</div>
<script>
	$(document).on('submit', '#developerForm', function(submission){
		submission.preventDefault();
		// debugger;
		var form   = $(this),
		url    = form.attr('action'),
		submit = form.find('[type=submit]');
		var data        = form.serialize(),
		contentType = 'application/x-www-form-urlencoded; charset=UTF-8';
		var submitOriginal = submit.html();
		submit.html('Processing, please wait...');
		submit.attr("disabled", true);
  	// Request.
  	$.ajax({
  		type: "POST",
  		url: url,
  		data: data,
  		dataType: 'json',
  		cache: false,
  		contentType: contentType,
  		processData: false
    	// Response.
    }).always(function(response, status) {
      // Reset errors.
      // Check for errors.
      debugger;
      if(response.errors && response.errors.length > 0) {
      	$.each(response.errors, function(field, message) {
      		var formGroup = $('[name=table]', form).closest('.form-group');
            formGroup.addClass('has-error').append('<span class="help-block">'+message+'</span>');
          });
      	if (submit.is('button')) {
      		submit.html(submitOriginal);
      		submit.attr("disabled", false);
      	} else if (submit.is('input')) {
      		submit.val(submitOriginal);
      		submit.attr("disabled", false);
      	}
      } else {
      		if(response == 1)
        	submit.html('Changed Last Edited Time');
        	else
        		submit.html('Something went wrong');
        }
      });

  });

</script>
@endsection
