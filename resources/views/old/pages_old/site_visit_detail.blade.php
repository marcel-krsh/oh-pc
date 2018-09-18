<script>
resizeModal(95);
</script>
<style>
/* Style the tab */
.tab2 {
}

/* Style the buttons that are used to open the tab content */
.tab2 button {
    display: block;
    background-color: inherit;
    color: black;
    padding: 22px 16px;
    width: 100%;
    border: none;
    outline: none;
    text-align: left;
    cursor: pointer;
    transition: 0.3s;
}

/* Change background color of buttons on hover */
.tab2 button:hover {
    background-color: #ddd;
}

/* Create an active/current "tab button" class */
.tab2 button.active {
    background-color: #ccc;
}

/* Style the tab content */
.tab2content {
    padding: 0px 12px;
    border-left: none;
    margin-top:0 !important;
}
</style>
<script>
function openTab2(evt, id) {
    // Declare all variables
    var i, tab2content, tab2links;

    // Get all elements with class="tabcontent" and hide them
    tab2content = document.getElementsByClassName("tab2content");
    for (i = 0; i < tab2content.length; i++) {
        tab2content[i].style.display = "none";
    }

    // Get all elements with class="tablinks" and remove the class "active"
    tab2links = document.getElementsByClassName("tab2links");
    for (i = 0; i < tab2links.length; i++) {
        tab2links[i].className = tab2links[i].className.replace(" active", "");
    }

    // Show the current tab, and add an "active" class to the link that opened the tab
    document.getElementById(id).style.display = "block";
    evt.currentTarget.className += " active";
}
</script>
<div class="uk-container">
@if(Auth::user()->canViewSiteVisits())
	<?php 
		$parcel = $site_visit->parcel; 
		$comments = $site_visit->comments;
		$corrections = $site_visit->corrections;
		$photos = $site_visit->photos;
		//dd($parcel,$status,$comments,$corrections,$photos);
	?>
	<h2>{{$parcel->parcel_id}} Site Visit @if($site_visit->statusinfo): {{$site_visit->statusinfo->name}}@endif </h2>
	<small>SURVEY {{$site_visit->id}}</small>
	<hr />

	<div class="uk-child-width-1-2@s" uk-grid>
	    <div class="tab2 uk-width-1-3@m">
		  <button class="tab2links active" onclick="openTab2(event, 'result')">PASS FAILS</button>
		  <button class="tab2links" onclick="openTab2(event, 'corrections')">CORRECTIONS  <span class="uk-badge" style="padding-top: 3px;">@if($corrections) {{$corrections->count()}} @else 0 @endIf</button>
		  <button class="tab2links" onclick="openTab2(event, 'photos')">PHOTOS</button>
		  <button class="tab2links" onclick="openTab2(event, 'recapture')">RECAPTURE</button>
		  <button class="tab2links" onclick="openTab2(event, 'comments')">COMMENTS</button>
		</div>

		<div id="result" class="tab2content uk-width-2-3@m">
		  	<div class="uk-scrollable-box" style="min-height: 70vh;">
				<div uk-grid>
					<div class="uk-width-1-6@m" style="padding-top: 7px;">
						DATE OF VISIT:
					</div>
					<div class="uk-width-1-6@m" >
						<span id="visit-date-display">
							<span id="visit-date-display-value">{{date('Y-m-d', strtotime($site_visit->visit_date))}}</span> 
							@if(Auth::user()->isSiteVisitManager()) <a class="uk-link-mute" onclick="$('#visit-date').slideToggle();$('#visit-date-display').slideToggle();"><span class="a-pencil-2" id="visit-date-edit-icon"></span></a></span>
							<div id="visit-date" style="display: none;">
								<form id="visit-date-form">
									<div class="uk-inline">
									    <input type="text" id="visit-date-field" name="visit-date" class="uk-input uk-form-small uk-form-width-small flatpickr flatpickr-input active" onchange="autoSubmit('visit-date','site_visits'); $('#visit-date-spinner').slideToggle();" style="width:80px;" value="{{date('Y-m-d', strtotime($site_visit->visit_date))}}" data-id="dateformat"/>

									    
									</div>
									    <span id="visit-date-spinner" style="display: none"><span uk-icon="circle-o-notch" class="uk-icon-spin"></span> Saving Change</span>
								</form>
							</div>
							@else
						</span>
							@endIf
					</div>
					<div class="uk-width-1-3@m"></div>
					<div class="uk-width-1-3@m" style="padding-top: 7px;">
						LAST UPDATED: {{date('n/j/y g:h a', strtotime($site_visit->updated_at))}}
					</div>
				</div>
				<hr />
				<div uk-grid>
					<div class="uk-width-2-3@m">
						Are all structures removed?
					</div>
					<div class="uk-width-1-3@m">
						@if(is_null($site_visit->all_structures_removed)) <div class="uk-button uk-button-default uk-button-small ">NA</div> @elseIf($site_visit->all_structures_removed == 0) <div class="uk-button uk-button-small uk-button-danger">FAIL</div> @else <div class="uk-button uk-button-small uk-button-success">PASS</div>@endIf
					</div>
				
				</div>
				<hr />
				<div uk-grid>
					<div class="uk-width-2-3@m">
						Is there construction debris?
					</div>
					<div class="uk-width-1-3@m">
						 @if(is_null($site_visit->construction_debris_removed)) <div class="uk-button uk-button-default uk-button-small ">NA</div> @elseIf($site_visit->construction_debris_removed == 0) <div class="uk-button uk-button-small uk-button-danger">FAIL</div> @else <div class="uk-button uk-button-small uk-button-success">PASS</div>@endIf
					</div>
				
				</div>
				<hr />
				<div uk-grid>
					<div class="uk-width-2-3@m">
						Was the property graded and seeded?
					</div>
					<div class="uk-width-1-3@m">
						 @if(is_null($site_visit->was_the_property_graded_and_seeded)) <div class="uk-button uk-button-default uk-button-small ">NA</div> @elseIf($site_visit->was_the_property_graded_and_seeded == 0) <div class="uk-button uk-button-small uk-button-danger">FAIL</div>@else <div class="uk-button uk-button-small uk-button-success">PASS</div>@endIf
					</div>
				
				</div>
				<hr />
				<div uk-grid>
					<div class="uk-width-2-3@m">
						Is there any signage?
					</div>
					<div class="uk-width-1-3@m">
						 @if(is_null($site_visit->is_there_any_signage)) <div class="uk-button uk-button-default uk-button-small ">NA</div> @elseIf($site_visit->is_there_any_signage == 0) <div class="uk-button uk-button-small uk-button-danger">FAIL</div>@else <div class="uk-button uk-button-small uk-button-success">PASS</div>@endIf
					</div>
				
				</div>
				<hr />
				<div uk-grid>
					<div class="uk-width-2-3@m">
						Is grass growing consistently accross?
					</div>
					<div class="uk-width-1-3@m">
						 @if(is_null($site_visit->is_grass_growing_consistently_across)) <div class="uk-button uk-button-default uk-button-small ">NA</div> @elseIf($site_visit->is_grass_growing_consistently_across == 0) <div class="uk-button uk-button-small uk-button-danger">FAIL</div> @else <div class="uk-button uk-button-small uk-button-success">PASS</div>@endIf
					</div>
				
				</div>
				<hr />
				<div uk-grid>
					<div class="uk-width-2-3@m">
						Is grass mowed and weeded?
					</div>
					<div class="uk-width-1-3@m">
						@if(is_null($site_visit->is_grass_mowed_weeded)) <div class="uk-button uk-button-default uk-button-small ">NA</div> @elseIf($site_visit->is_grass_mowed_weeded == 0) <div class="uk-button uk-button-small uk-button-danger">FAIL</div> @else <div class="uk-button uk-button-small uk-button-success">PASS</div>@endIf
					</div>
				
				</div>
				<hr />
				<div uk-grid>
					<div class="uk-width-2-3@m">
						Was the property landscaped?
					</div>
					<div class="uk-width-1-3@m">
						 @if(is_null($site_visit->was_the_property_landscaped)) <div class="uk-button uk-button-default uk-button-small ">NA</div> @elseIf($site_visit->was_the_property_landscaped == 0) <div class="uk-button uk-button-small uk-button-danger">FAIL</div> @else <div class="uk-button uk-button-small uk-button-success">PASS</div>@endIf
					</div>
				
				</div>
				<hr />
				<div uk-grid>
					<div class="uk-width-2-3@m">
						Nuisance elements or code violations?
					</div>
					<div class="uk-width-1-3@m">
						 @if(is_null($site_visit->nuisance_elements_or_code_violations)) <div class="uk-button uk-button-default uk-button-small ">NA</div> @elseIf($site_visit->nuisance_elements_or_code_violations == 0) <div class="uk-button uk-button-small uk-button-danger">FAIL</div> @else <div class="uk-button uk-button-small uk-button-success">PASS</div>@endIf
					</div>
				
				</div>
				<hr />
				<div uk-grid>
					<div class="uk-width-2-3@m">
						Are there environmental conditions?
					</div>
					<div class="uk-width-1-3@m">
						 @if(is_null($site_visit->are_there_environmental_conditions)) <div class="uk-button uk-button-default uk-button-small ">NA</div> @elseIf($site_visit->are_there_environmental_conditions == 0) <div class="uk-button uk-button-small uk-button-danger">FAIL</div> @else <div class="uk-button uk-button-small uk-button-success">PASS</div>@endIf
					</div>
				
				</div>
				<hr />
				<div uk-grid>
					<div class="uk-width-2-3@m">
						Retainage released to contractor?
					</div>
					<div class="uk-width-1-3@m">
						 @if(is_null($site_visit->retainage_released_to_contractor)) <div class="uk-button uk-button-default uk-button-small ">NA</div> @elseIf($site_visit->retainage_released_to_contractor == 0) <div class="uk-button uk-button-small uk-button-danger">FAIL</div>@else <div class="uk-button uk-button-small uk-button-success">PASS</div>@endIf
					</div>
				
				</div>
				<hr />
				<div uk-grid>
					<div class="uk-width-2-3@m">
						Is recapture of maintenance owed?
					</div>
					<div class="uk-width-1-3@m">
						 @if(is_null($site_visit->is_a_recap_of_maint_funds_required)) <div class="uk-button uk-button-default uk-button-small ">NA</div> @elseIf($site_visit->is_a_recap_of_maint_funds_required == 0) <div class="uk-button uk-button-small uk-button-danger">FAIL</div>@else <div class="uk-button uk-button-small uk-button-success">PASS</div>@endIf
					</div>
				
				</div>
			</div>
		</div>

		<div id="corrections" class="tab2content uk-width-2-3@m" style="display:none;">
		  <div class="uk-scrollable-box" style="min-height: 85vh;">
			@forEach($corrections as $correction)
				<?php $correctionBy = \App\User::find($correction->user_id); ?>
				<div class="uk-panel  uk-panel-box uk-margin-bottom">
				    <div class="uk-panel-badge uk-badge @if(is_null($correction->corrected_date) ) uk-badge-danger @else uk-badge-success @endIF ">
				    	@if(is_null($correction->corrected_date)) NOT CORRECTED 
				    	@else 
				    	<span uk-tooltip = "Confirmed by {{$correction->corrected_user_id}} on {{$correction->corrected_date}} during survey {{$correction->corrected_site_visit_id}}"><span class="a-info-circle"></span> CORRECTED </span>@endIf
				    </div>
				    <h3 class="uk-panel-title">CORRECTION REQUIRED FROM SURVEY {{$correction->site_visit_id}}</h3>
				    <small>RECORDED {{date('n/j/Y g:h A', strtotime($correction->created_at))}} BY  {{strtoupper($correctionBy->name)}}</small>
				    <h4>{{$correction->notes}}</h4>
				    <?php $correctionPhotos = \App\Photo::where('correction_id',$correction->id)->get()->all(); ?>
				    @if($correctionPhotos)

				    <div class=" uk-child-width-1-2@s uk-child-width-1-4@m" data-uk-grid-margin="{gutter: 20}" uk-grid>
			    		<div class="uk-row-first">
			    		@forEach($correctionPhotos as $photo)
			    			<a href="/images/files/{{$photo->filename}}" data-uk-lightbox="{group:'group{{$correction->id}}'}" title="Correction {{$correction->id}} Photos">
				    			<img src="/images/files/{{$photo->filename}}" align="center" style="width: 800px;">
							</a>
			    		@endForEach
			    		</div>
					</div>
			
					@endif

				</div>
			@endForEach
			</div>
		</div>

		<div id="photos" class="tab2content uk-width-2-3@m" style="display:none;">
		  <div class="uk-scrollable-box" style="min-height: 70vh;">
			<?php $correctionPhotos = \App\Photo::where('parcel_id',$site_visit->parcel_id)->get()->all(); ?>
				@if($correctionPhotos)
				   
				    		@forEach($correctionPhotos as $photo)
				    			
					    			<img src="/images/files/{{$photo->filename}}" align="center">
								
				    		@endForEach
				   
				@endif
			</div>
		</div>

		<div id="recapture" class="tab2content uk-width-2-3@m" style="display:none;">
		  <table class="uk-table uk-table-hover uk-table-striped" style="width:100%">
				<thead>
					<tr>
						<th width="10%">DATE</th>
						<th width="50%">EXPENSE</th>
						<th width="20%">RECAPTURE OWED</th>
						<th width="20%">PAID</th>
					</tr>
				</thead>
				<tbody>
					<?php $recaptures = \App\RecaptureItem::select('recapture_items.*','expense_category_name')->where('parcel_id','$site_visit->parcel_id')->join('expense_categories','expense_categories.id','expense_category_id')->get()->all(); ?>
					
					@forEach($recaptures as $recapture)
					<tr>
						<td>{{date('n/j/y',strtotime($recapture->created_at))}}</td>
						<td>{{$recapture->expense_category_name}}<br /> <small>{{$recapture->description}}<small></td>
						<td>${{$recapture->amount}}</td>
						<td>STATUS TBD</td>
					</tr>

					@endForEach
				</tbody>
			</table>
		</div>

		<div id="comments" class="tab2content uk-width-2-3@m" style="display:none;">
		  <div class="uk-scrollable-box" style="min-height: 70vh;">
			@forEach($comments as $comment)
				<?php $commentBy = \App\User::find($comment->user_id); ?>
				<div class="uk-panel  uk-panel-box uk-margin-bottom">
				    
				    <h3 class="uk-panel-title">COMMENT FROM SURVEY {{$comment->site_visit_id}}</h3>
				    <small>RECORDED {{date('n/j/Y g:h A', strtotime($comment->created_at))}} BY  {{strtoupper($commentBy->name)}}</small>
				    <h4>{{$comment->comment}}</h4>
				    
				   
				    
				    <?php $commentPhotos = \App\Photo::where('comment_id',$comment->id)->get()->all(); ?>
				    @if($commentPhotos)
				    
				    @forEach($commentPhotos as $photo)
				    
				    	<img src="/images/files/{{$photo->filename}}" align="center">
				   
				    @endForEach
				   
				    @endif

				</div>
			@endForEach
			</div>
		</div>
	</div>

	
	<script>
		flatpickr.defaultConfig.animate = window.navigator.userAgent.indexOf('MSIE') === -1;
		flatpickr(".flatpickr");

		var configs = {
		    dateformat: {
		        dateFormat: "m/d/Y",
		    }
		}
	</script>
@if(Auth::user()->isSiteVisitManager())
<script>
	function submitInfo(v){console.log(v);
		// setTimeout(function afterTwoSeconds() {
		//   $('#'+v).slideUp();
		//   $('#'+v+'-display-value').html('');
		//   $('#'+v+'-display-value').html($('#'+v+'-field').val());
		//   $('#'+v+'-display').slideToggle();
		//   $('#'+v+'-spinner').toggle();
		//   $('#'+v+'-field').css('opacity', '1');
		  

		// }, 2000);

		var form = $('#visit-date-form');

		$.post('{{ URL::route("site_visit.saveDate", [$site_visit->parcel_id,$site_visit->id]) }}', {
			'_token' : '{{ csrf_token() }}',
			'inputs' : form.serialize()
		}, function(data) {
			  $('#'+v).slideUp();
			  $('#'+v+'-display-value').html('');
			  $('#'+v+'-display-value').html($('#'+v+'-field').val());
			  $('#'+v+'-display').slideToggle();
			  $('#'+v+'-spinner').toggle();
			  $('#'+v+'-field').css('opacity', '1');
			if(data['message']!='' && data['error']!=1){
				
			}else if(data['message']!='' && data['error']==1){
				
			}else{
				UIkit.modal.alert('Something went wrong.');
			}
		} );
	}
	function autoSubmit(v,t,datePicker = 0){
		//alert('Submitting form '+v);
		
		$('#'+v+'-field').css('opacity', '0.6');
		if(datePicker == 0){
			submitInfo(v);
			$('.uk-datepicker').hide();
		} else {
			$('#'+v+'-field').on('change', function() {
		        submitInfo(v);
		    });
		}	
		
	}
</script>
@endif

@else
<br />
<hr>
<h2>Sorry, but you don't have permission to view site visits. Please contact your admin for help.</h2>
<hr>
@endIf

</div>
