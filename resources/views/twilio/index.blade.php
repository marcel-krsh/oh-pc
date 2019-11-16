@extends('layouts.simplerAllita')

@section('head')

@endsection

@section('content')
<div class="uk-text-center">
	<h2>Twilio test<br /><small>{{$status}} - test code is 12345 - tries {{$tries}}</small></h2>
	<p><small>(unless you comment out controller line 55, a new code will be sent on refresh)</small></p>
	<div class="uk-grid-small uk-flex-center uk-text-center" uk-grid>
	    <div class="uk-width-4-5@s ">
	        <div class="uk-card uk-card-default uk-card-body">
	        	<img src="/images/apple-touch-icon.png" alt="Allita" width="120" height="120" style="margin-bottom:20px;"/>

	        	@if($error !== null)
	        	<div id="codeerror" class="uk-alert-error" uk-alert>
	        		<p>{{$error}}</p>
	        	</div>
	        	@else
				<div id="codeform">
					@if($user)
					<p>Hi {{$user->name}}, I'm sending you a security code via text message to verify your device. Please enter it below.</p>
					@else
					<p>Hi, I'm sending you a security code via text message to verify your device. Please enter it below.</p>
					@endif
					<input class="uk-input" name="code" type="text" id="code" value="" placeholder="Enter code here" />
					<a class="uk-button uk-button-primary blue-button" onclick="submitCode()">SUBMIT</a>
					<p><small>Still haven't receive the text message? <a href="{{ route('device.code.check.form', ['resend' => 1]) }}">Send the code again</a></small></p>
				</div>
				<div id="codeconfirm" style="display:none;">
					<p>Good to go!</p>
					<a class="uk-button uk-button-primary blue-button" onclick="">NEXT <span class="a-arrow-right-2_1"></span></a></p>
				</div>
				@endif
	        </div>
	    </div>
	</div>

	<script type="text/javascript">
		function submitCode() {
			var no_alert = 1;
			var code = $('#code').val();

			if($("#codeerror").length){
				UIkit.alert('#codeerror').close();
			}

			if(code == ''){
				no_alert = 0;
			    alert('Please enter a code.');
			}

			if(no_alert){
				$.post('{{ URL::route("device.code.check") }}', {
					'code' : code,
					'_token' : '{{ csrf_token() }}'
				}, function(data) {
					if(data['error'] == 1){ 
						$('#code').val('');
						var error = '<div id="codeerror" style="display:none" class="uk-alert-warning" uk-alert><a class="uk-alert-close" style="top: 12px;" uk-close></a><p>'+data['message']+'</p></div>';
						$('#code').before(error);
						$('#codeerror').fadeIn("slow");
					} else if(data['error'] == 2){
						$('#code').val('');
						var error = '<div id="codeerror" style="display:none" class="uk-alert-error" uk-alert><a class="uk-alert-close" style="top: 12px;" uk-close></a><p>'+data['message']+'</p></div>';
						$('#codeform').before(error);
						$('#codeform').remove();
						$('#codeerror').fadeIn("slow");
					} else {
						$('#codeform').fadeOut("slow", function(){
						    var div = $('#codeconfirm');
						    $(this).replaceWith(div);
						    $('#codeconfirm').fadeIn("slow");
						});
						// UIkit.modal.alert('Your message has been saved.');
					}
				} );
			}
		}	
		</script>
</div>
@endsection