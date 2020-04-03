<?php $signature = $bladeData?>
@if(!is_null($report->signature))
<div uk-grid>
	<div class="uk-width-1-1">
		<img src="{{$report->signature}}">
		<hr >
		<p>Signed: {{date('m/d/Y g:h a', strtotime($report->date_signed))}} By {{$report->signed_by}}</p>
	</div>

	@if($auditor_access)<button onClick="jsFunctionToDelete"><i class="a-trash-can"></i> DELETE SIGNATURE </button>@endIf
</div>
@else

<div uk-grid style="margin-bottom:340px;">
	@if($print == 1)
	<div class="uk-width-1-1">
		<hr style="margin-top: 300px;" >
	</div>
	<div class="uk-width-1-3">PRINT NAME</div>
	<div class="uk-width-1-2">SIGNATURE </div>
	<div class="uk-width-1-6">DATE </div>
</div>
@else
<div id="signature-pad" class="signature-pad uk-width-1-1" style="min-height: 350px;">
	<div class="signature-pad--body">
		<canvas></canvas>
	</div>

	<div class="signature-pad--footer">
		<div class="description">BY SIGNING ABOVE I CERTIFY I HAVE REVIEWED THIS DOCUMENT.</div>
		<form id="singnatureForm">
			<div class="signature-pad--actions">
				<div>
					<button type="button" class="uk-button clear" data-action="clear">Clear</button>
					<button type="button" class="uk-button uk-hidden" data-action="change-color">Change color</button>
					<button type="button" class="uk-button" data-action="undo">Undo</button>
				</div>
				<div class="uk-grid">
					<div class="uk-width-1-3">
						<select  id="signing_user" name="signing_user" class="uk-select" style="width: 500px">
							<option value="">Who is Signing?</option>
							@foreach($users as $user)
							<option value="{{ $user->person_id }}">{{ $user->person->first_name }} {{ $user->person->last_name }}</option>
							@endforeach
							<option value="other">Other</option>
						</select>
					</div>
					<div class="uk-width-2-3">
						<input id="other_name"  class="uk-input" type="text" name="other_name" style="display: none" placeholder="Enter your name">
						<button type="button" class="uk-button uk-hidden" data-action="change-color">Change color</button>
					</div>
				</div>
				<div>
					<a onclick="submitSignature()" class="uk-button save">Confirm Signature</a>
					<button type="button" class="uk-button save uk-hidden" data-action="save-png">CONFIRM SIGNATURE</button>
					<button type="button" class="button save uk-hidden" data-action="save-jpg">Save as JPG</button>
					<button type="button" class="button save uk-hidden" data-action="save-svg">Save as SVG</button>
				</div>
			</div>
		</form>
	</div>
</div>
<script src="/js/signature/signature_pad.umd.js{{ asset_version() }}"></script>
<script src="/js/signature/app.js{{ asset_version() }}"></script>
@endIf
</div>
@endIf

<script type="text/javascript">

signaturePad = new SignaturePad(canvas);
//if other is selected show the input box to enter name
$('#signing_user').change(function() {
	var value = $("#signing_user").val();
	if(value == 'other'){
		document.getElementById('other_name').style.display='inline';
	} else {
		document.getElementById('other_name').style.display='none';
		$('#api_token').val('');
	}
});
//Form submision,
function submitSignature() {
	jQuery.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': '{{ csrf_token() }}'
		}
	});

	// alert(signaturePad.toDataURL());

	var form = $('#singnatureForm');

	var data = { };
	var signing_user = $('#signing_user').val();
	var signed_version = '{{$report->version}}';
	var other_name = $('#other_name').val();
	if (signaturePad.isEmpty()) {
		UIkit.modal.alert('Please provide a signature first.',{stack: true});
    return;
  }
	$.each($('form').serializeArray(), function() {
		data[this.name] = this.value;
	});
	data['signature'] = signaturePad.toDataURL();

	if(data['signing_user'] == '') {
		UIkit.modal.alert('Select who is signing');
    return;
	} else if(data['signing_user'] == 'other' && data['other_name'] == '') {
		UIkit.modal.alert('Enter your name');
    return;
	}

	jQuery.ajax({
		url: "{{ url('report/'. $report->id . '/digital-signature') }}",
		method: 'post',
		data: {
			signature: data['signature'],
			signing_user: data['signing_user'],
			date_signed: '{{date('Y-m-d',time())}}',
			other_name: data['other_name'],
      '_token' : '{{ csrf_token() }}'
    },
    success: function(data){
    	$('.alert-danger' ).empty();
    	if(data == 1) {
    		UIkit.modal.alert('Signature has been saved.',{stack: true});
    		location.reload();
    	}
    	jQuery.each(data.errors, function(key, value){
    		jQuery('.alert-danger').show();
    		jQuery('.alert-danger').append('<p>'+value+'</p>');
    	});
    }
  });
}
</script>
