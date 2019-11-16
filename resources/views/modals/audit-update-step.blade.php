<div class="modal-update-step">
    <div class="">
	    <div uk-grid>
	  		<div class="uk-width-1-1 uk-margin-small-top uk-margin-small-bottom">

	  			<h3>Audit {{ $audit->id }} Step Update</h3>
	  			@if($audit->current_step())
	  			<p>Current step is : {{ $audit->current_step()->guideStep->name }} </p>
	  			@endif
	  			<form id="modal-amenity-form">
					<fieldset class="uk-fieldset">
						<div uk-grid>
							<div class="uk-width-1-2 uk-padding-remove">
								<div class="uk-margin-small-top">
									<select id="audit-step" name="audit-step" class="uk-select">
										@foreach($steps as $step)
										<option value="{{ $step->id }}" @if($audit->step_id == $step->id) selected @endif>{{ $step->name }}</option>
										@endforeach
										@can('access_admin')

										<option value="delete">DELETE THIS AUDIT</option>
										@endCan
									</select>
						        </div>
							</div>
						    <div class="uk-width-1-2">
								<button onclick="saveStep()" class="uk-button uk-button-primary" style="margin-top: 6px;">Save & Close</button>
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
		@can('access_admin')
		if($('#audit-step').val() == 'delete'){
			UIkit.modal.confirm('<h1>Are You Sure?</h1><p>Are you sure you want to delete audit #{{ $audit->audit_id }}?</p> <p>This cannot be undone! The monitoring record will remain, only the Allita specific items will be deleted, including communications, findings, reports, documents, notes... ANYTHING that is associated with this audit id will be deleted.</p>').then(function() {
			    $.get("/tabs/audit/delete/{{ $audit->audit_id }}", {
	            '_token' : '{{ csrf_token() }}'
		        }, function(data) {

		            UIkit.notification('<span uk-icon="icon: trash"></span> Audit #{{ $audit->audit_id }} Deleted.', {pos:'top-right', timeout:1000, status:'success'});
		            if(1 == "{{ $detailsPage }}"){
		            	loadTab('/projects/{{ $audit->project_id }}/audit-details/0', '1', 0, 0, 'project-',1);window.tab_1=1;
		            }else{
		            	loadTab('{{ route('dashboard.audits') }}','1','','','',1);
		            }
		            dynamicModalClose();
		        } );

			}, function () {
			    console.log('Cancelled Deletion.')
			});



		}else{
			$.post("/audits/{{ $audit->id }}/saveStep", {
            'step' : $('#audit-step').val(),
            '_token' : '{{ csrf_token() }}'
	        }, function(data) {
	            dynamicModalClose();
	            UIkit.notification('<span uk-icon="icon: check"></span> Step Saved', {pos:'top-right', timeout:1000, status:'success'});
	            if(1 == "{{ $detailsPage }}"){
		            	//loadTab('/projects/{{ $audit->project_id }}/audit-details/0', '1', 0, 0, 'project-',1);window.tab_1=1;
		            }else{
		            	//loadTab('{{ route('dashboard.audits') }}','1','','','',1);
		        }
	        } );
		}
		@else
		$.post("/audits/{{ $audit->id }}/saveStep", {
            'step' : $('#audit-step').val(),
            '_token' : '{{ csrf_token() }}'
        }, function(data) {
            dynamicModalClose();
            UIkit.notification('<span uk-icon="icon: check"></span> Step Saved', {pos:'top-right', timeout:1000, status:'success'});
            if(1 == "{{ $detailsPage }}"){
		            	//loadTab('/projects/{{ $audit->project_id }}/audit-details/0', '1', 0, 0, 'project-',1);window.tab_1=1;
		            }else{
		            	//loadTab('{{ route('dashboard.audits') }}','1','','','',1);
		    }
        } );
		@endCan
	}

</script>