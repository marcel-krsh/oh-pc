
	<div id="dynamic-modal-content">

		<form name="newReportForm" id="newReportForm" method="post">
			
			<!-- begin communication opened template -->
			<div class="" uk-grid>

				<!--BEGIN top row-->
				<div class="uk-width-1-1">
					<h1>Generate a New Report</h1>
				</div>
				<hr class="dashed-hr uk-width-1-1 uk-margin-bottom">
				<div class="uk-width-1-1">
					<div class="" style="min-height:3em;" uk-grid>
						<label class="uk-width-1-5">TYPE</label>
						<select name="template_id" id="templates" onChange="showOptions(this.value);" class="uk-select filter-drops uk-width-4-5">
							<option>PLEASE SELECT A REPORT TYPE</option>
							@forEach($templates as $template)
								<option value="{{$template->id}}">{{$template->template_name}}</option>
							@endForEach
						</select>

						<hr class="dashed-hr uk-width-1-1 uk-margin-bottom">
						
							<label class="uk-width-1-5 audit-list" style="display: none;">FOR AUDIT</label>
						<select name="audit_id" class="uk-select filter-drops uk-width-4-5 audit-list" style="display: none;" onChange="$('#new-report-errors').html('');">
							<option>PLEASE SELECT AN AUDIT (ORDERED BY MODIFICATION DATE)</option>
							@forEach($audits as $audit)
								<option value="{{$audit->id}}">{{$audit->project->project_name}} : {{$audit->id}} | @if($audit->lead_user_id)LEAD: {{$audit->lead->person->first_name}} {{$audit->lead->person->last_name}} @endIf | LAST MODIFIED: {{ ucfirst($audit->updated_at->diffForHumans()) }}</option>
							@endForEach

						</select>
						<hr class="dashed-hr uk-width-1-1 uk-margin-bottom audit-list" style="display: none;">
					</div>
				</div>

					
				<div id="new-report-errors" class="uk-width-1-1 uk-margin-top uk-margin-bottom"></div>
				<div class="uk-width-1-2">
				</div>
				<div class="uk-width-1-4">
					<a class="uk-button uk-button-default uk-width-1-1" onclick="dynamicModalClose()"><span uk-icon="times-circle"></span> CANCEL</a>
				</div>
				<div class="uk-width-1-4 ">
					<a class="uk-button uk-width-1-1 uk-button-primary" onclick="submitNewReport()"><span uk-icon="save"></span> GO</a>
				</div>
			</div>
		</form>
	</div>

	
	<script type="text/javascript">
	function showOptions(templateId){
		if(templateId == 1 || templateId == 2  ){
			$('.audit-list').slideDown();
			$('#new-report-errors').html('');
		}else{
			$('.data-options').slideDown();
			$('#new-report-errors').html('');
		}
	}

	function submitNewReport() {
		console.log('Submitting Request for New Report.');
		var form = $('#newReportForm');

		data = form.serialize(); 
		$.post('{{ URL::route("report.create") }}', {
	            'template_id' : form.find( "select[name='template_id']" ).val(),
	            'audit_id' : form.find( "select[name='audit_id']" ).val(),
	            '_token' : '{{ csrf_token() }}'
	            }, function(data) {
	                if(data!=1){ 
	                    $('#new-report-errors').html('<div class="attention"><h2>!!'+data+'</h2></div>');
	                } else {
	                    UIkit.modal.alert('Your report has been created.');
	                    $('#detail-tab-3').trigger("click");
						dynamicModalClose();                                                                           
	                }
		} );
	        
		// by nature this note is it's history note - so no need to ask them for a comment.
		
		
	}	
	</script>