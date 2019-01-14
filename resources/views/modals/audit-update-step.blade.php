<div class="modal-update-step">
    <div class="">
	    <div uk-grid> 
	  		<div class="uk-width-1-1 uk-margin-small-top uk-margin-small-bottom">
	  			
	  			<h3>Audit {{$audit->id}} Step Update</h3>
	  			@if($audit->current_step())
	  			<p>Current step is : {{$audit->current_step()->guideStep->name}} </p>
	  			@endif
	  			<form id="modal-amenity-form" onsubmit="saveStep()">
					<fieldset class="uk-fieldset">
						<div uk-grid>
							<div class="uk-width-1-2 uk-padding-remove">
								<div class="uk-margin-small-top">
									<select id="audit-step" name="audit-step" class="uk-select">
										@foreach($steps as $step)
										<option value="{{$step->id}}" @if($audit->step_id == $step->id) selected @endif>{{$step->name}}</option>
										@endforeach
									</select>
						        </div>
							</div>
						    <div class="uk-width-1-2">
								<button class="uk-button uk-button-primary" style="margin-top: 6px;">Save & Close</button>
							</div>
					    </div>
					</fieldset>
				</form>
	  		</div>
	    </div>
	</div>
</div>
<script>
	function saveStep() {
		event.preventDefault();

		console.log('saving step');
		
		$.post("/audits/{{$audit->id}}/saveStep", {
            'step' : $('#audit-step').val(),
            '_token' : '{{ csrf_token() }}'
        }, function(data) {
            dynamicModalClose();
            UIkit.notification('<span uk-icon="icon: check"></span> Step Saved', {pos:'top-right', timeout:1000, status:'success'});
            loadTab('{{ route('dashboard.audits') }}','1','','','',1);
        } );
	}

</script>