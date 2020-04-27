@php
	$selected_icon = null;
@endphp
<link rel="stylesheet" href="/css/communications-tab.css{{ asset_version() }}">
<div class="uk-width-1-5 " style="padding:18px;"><div style="width:25px;display: inline-block;"><i uk-icon="users" class=""></i></div> &nbsp;FINDINGS: {{-- {{ $all_findings }} --}} </div>

<div class="uk-width-4-5 "  id="findings-box" style="border-bottom:1px #111 dashed;padding:18px; padding-left:25px;">
<div id="add-findings-button" class="uk-button uk-button-small" style="padding-top: 2px;" onClick="showFindings()"><i uk-icon="icon: plus-circle; ratio: .7"></i> &nbsp;ADD FINDING</div><div id="done-adding-findings-button" class="uk-button uk-button-success uk-button-small" style="padding-top: 2px; display: none;" onClick="showFindings()"><i class="a-circle-cross"></i> &nbsp;DONE ADDING FINDINGS</div>
<div id='finding-template' class="uk-button uk-button-small uk-margin-small-right uk-margin-small-bottom uk-margin-small-top" style="padding-top: 2px; display:none;"><i uk-icon="icon: cross-circle; ratio: .7"></i> &nbsp;<input name="" id="update-me" value="" type="checkbox" checked class="uk-checkbox finding-selector"><span class='finding-name'></span>
</div>
</div>
<div class="uk-width-1-5 findings-list" style="display: none;"></div>
<div class="uk-width-4-5 findings-list" id='findings' style="border-left: 1px #111 dashed; border-right: 1px #111 dashed; border-bottom: 1px #111 dashed; padding:18px; padding-left:25px; position: relative;top:0px; display: none">
<!-- RECIPIENT LISTING -->
<div class="communication-selector uk-scrollable-box">
	<ul class="uk-list document-menu">
		@php
			$findings = session()->get('selected_findings');
		@endphp
		@if(null !== $findings)
			@foreach ($findings as $f)
				@if($f->finding_type->type == 'nlt')
					@php
						$f_icon = '<i class="a-booboo"></i>';
					@endphp
				@endIf
				@if($f->finding_type->type == 'lt')
					@php
						$f_icon = '<i class="a-skull"></i>';
					@endphp
				@endIf
				@if($f->finding_type->type == 'file')
					@php
						$f_icon = '<i class="a-folder"></i>';
					@endphp
				@endIf
				<li class="findings-list-item finding-{{$f->id}}">
					@php
						if($f->id == $all_findings)
							$selected_finding = $f;
						else
							$selected_finding = null;
					@endphp
					<input {{ $all_findings == $f->id ? 'checked=checked' : '' }} name="" id="list-finding-id-{{$f->id}}" value="{{$f->id}}" type="checkbox" class="uk-checkbox" onClick="addFinding(this.value,'{{ $f_icon }}Finding-{{ ($f->id) }}')">
					<label for="finding-id-{{ $f->id }}">
						{!! $f_icon !!} Finding # {{$f->id}} - @if(!is_null($f->building_id)) <strong>{{$f->building->building_name}}</strong> @if(!is_null($f->building->address)) {{$f->building->address->line_1}} {{$f->building->address->line_2}} {{$f->building->address->city}}, {{$f->building->address->state}} {{$f->building->address->zip}} @endIf @endif
						<span uk-tooltip title="@if(!is_null($f->building_id))
							<strong>{{$f->building->building_name}}</strong> <br />
							@if(!is_null($f->building->address))
							{{$f->building->address->line_1}} {{$f->building->address->line_2}}<br />
							{{$f->building->address->city}}, {{$f->building->address->state}} {{$f->building->address->zip}}<br /><br />
							@endIf
							@elseIf(!is_null($f->unit_id))
							{{$f->unit->building->building_name}} <br />
							@if(!is_null($f->unit->building->address))
							{{$f->unit->building->address->line_1}} {{$f->unit->building->address->line_2}}<br />
							{{$f->unit->building->address->city}}, {{$f->unit->building->address->state}} {{$f->unit->building->address->zip}}
							@endIf
							<br /><strong>Unit {{$f->unit->unit_name}}</strong>
							@else
							<strong>Site Finding</strong><br />
							@if($f->project->address)
							{{$f->project->address->line_1}} {{$f->project->address->line_2}}<br />
							{{$f->project->address->city}}, {{$f->project->address->state}} {{$f->project->address->zip}}<br /><br />
							@endif
							@endIf
							{{$f->amenity->amenity_description}} <br />
							<strong> {{$f->finding_type->name}}</strong><br>">
							<span class="a-info-circle"  style="color: #56b285;"></span>
						</span>
					</label>
				</li>
			@endforeach
		@endIf
	</ul>
</div>
{{-- <div class="uk-form-row">
	<input type="text" id="finding-filter" class="uk-input uk-width-1-1" placeholder="Filter Findings">
</div> --}}
<script>
	// addFinding({{ $all_findings }},'Finding-{{ $all_findings }}');
  // CLONE RECIPIENTS
  function addFinding(formValue,name){
    //alert(formValue+' '+name);
    if($("#list-finding-id-"+formValue).is(':checked')){
    	var recipientClone = $('#finding-template').clone();
    	recipientClone.attr("id", "finding-id-"+formValue+"-holder");
    	recipientClone.prependTo('#findings-box');

    	$("#finding-id-"+formValue+"-holder").slideDown();
    	$("#finding-id-"+formValue+"-holder input[type=checkbox]").attr("id","finding-id-"+formValue);
    	$("#finding-id-"+formValue+"-holder input[type=checkbox]").attr("name","findings[]");
    	$("#finding-id-"+formValue+"-holder input[type=checkbox]").attr("onClick","removeFinding("+formValue+");");

    	$("#finding-id-"+formValue+"-holder input[type=checkbox]").val(formValue);
    	$("#finding-id-"+formValue+"-holder span").html('&nbsp; '+name+' ');
    } else {
    	$("#finding-id-"+formValue+"-holder").slideUp();
    	$("#finding-id-"+formValue+"-holder").remove();
    }
    updateMessage();
    return true;
  }

  function removeFinding(id){
  	$("#finding-id-"+id+"-holder").slideUp();
  	$("#finding-id-"+id+"-holder").remove();
  	$("#list-finding-id-"+id).prop("checked",false)
    updateMessage();
  }

   function showFindings() {
    	$('.findings-list').slideToggle();
    	$('#add-findings-button').toggle();
    	$('#done-adding-findings-button').toggle();
    }

    $("document").ready(function() {
    	@if(isset($selected_finding) && !is_null($selected_finding))
	    	@if($selected_finding->finding_type->type == 'nlt')
					@php
						$selected_icon = '<i class="a-booboo"></i>';
					@endphp
				@endIf
				@if($selected_finding->finding_type->type == 'lt')
					@php
						$selected_icon = '<i class="a-skull"></i>';
					@endphp
				@endIf
				@if($selected_finding->finding_type->type == 'file')
					@php
						$selected_icon = '<i class="a-folder"></i>';
					@endphp
				@endIf
			@else
				$selected_icon = '';
			@endif
			@if(is_numeric($all_findings))
	    setTimeout(function() {
	    		addFinding('{{ $all_findings }}','{{ $selected_icon }}Finding-{{ $all_findings }}');
	        // $("list-finding-id-{{ $all_findings }}").trigger('click');
	    },2);
	    @endif
});

</script>
<!-- END RECIPIENT LISTING -->
</div>








{{--


<div class="uk-width-4-5 "  id="findings-box" style="border-bottom:1px #111 dashed;padding:18px; padding-left:25px;">
<div class="uk-width-1-5" style="display: none;"></div>
<div class="uk-width-5-5 uk-grid documents-list" id='recipients' style="border-top: 1px #111 dashed; border-left: 1px #111 dashed; border-right: 1px #111 dashed; border-bottom: 1px #111 dashed; padding:10px 2px 2px 2px; position: relative;top:0px; display: none">
	<div class="uk-width-1-2@m uk-width-1-1@s">
		<h4>Select from exising documents</h4>
		<div class="communication-selector  uk-scrollable-box">
			<ul class="uk-list document-menu" id="existing-documents">
				@php
				$findings = session()->get('selected_findings');
				@endphp
				@foreach ($findings as $f)
				<li class="document-list-item uk-margin-small-bottom finding-{{$f->id}}">
					<input style="float: left; margin-top: 3px" name="docuware_documents[]" id="list-document-id-docuware-{{$f->id}}" value="docuware-{{ $f->id }}" type="checkbox"  class="uk-checkbox" onClick="addDocuwareDocument(this.value,'{{$f->id}}')">
					<label style="display: block; margin-left: 20px" for="docuware-document-id-{{ $f->id }}">
						Finding # {{$f->id}}
						<span uk-tooltip title="@if(!is_null($f->building_id))
							<strong>{{$f->building->building_name}}</strong> <br />
							@if(!is_null($f->building->address))
							{{$f->building->address->line_1}} {{$f->building->address->line_2}}<br />
							{{$f->building->address->city}}, {{$f->building->address->state}} {{$f->building->address->zip}}<br /><br />
							@endIf

							@elseIf(!is_null($f->unit_id))
							{{$f->unit->building->building_name}} <br />
							@if(!is_null($f->unit->building->address))
							{{$f->unit->building->address->line_1}} {{$f->unit->building->address->line_2}}<br />
							{{$f->unit->building->address->city}}, {{$f->unit->building->address->state}} {{$f->unit->building->address->zip}}
							@endIf
							<br /><strong>Unit {{$f->unit->unit_name}}</strong>
							@else
							<strong>Site Finding</strong><br />
							{{$f->project->address->line_1}} {{$f->project->address->line_2}}<br />
							{{$f->project->address->city}}, {{$f->project->address->state}} {{$f->project->address->zip}}<br /><br />
							@endIf">
							<span class="a-info-circle"  style="color: #56b285;"></span>
						</span>
					</label>
				</li>
				@endforeach
			</ul>
		</div>
		<div class="uk-form-row">
			<input type="text" id="document-filter" class="uk-input uk-width-1-1" placeholder="Filter Documents" >
		</div>
	</div>
</div>
<script>
  // CLONE RECIPIENTS
  function addDocuwareDocument(formValue,name){
    //alert(formValue+' '+name);
    if($("#list-document-id-"+formValue).is(':checked')){
    	var documentClone = $('#documents-template').clone();
    	documentClone.attr("id", "document-id-"+formValue+"-holder");
    	documentClone.prependTo('#documents-box');
    	$("#document-id-"+formValue+"-holder").slideDown();
    	$("#document-id-"+formValue+"-holder input[type=checkbox]").attr("id","document-id-"+formValue);
    	$("#document-id-"+formValue+"-holder input[type=checkbox]").attr("name","docuware_documents[]");
    	$("#document-id-"+formValue+"-holder input[type=checkbox]").attr("onClick","removeDocuwareDocument('"+formValue+"');");
    	$("#document-id-"+formValue+"-holder input[type=checkbox]").val(formValue);
    	$("#document-id-"+formValue+"-holder span").html('&nbsp; '+name+' ');
    } else {
    	$("#document-id-"+formValue+"-holder").slideUp();
    	$("#document-id-"+formValue+"-holder").remove();
    }
  }
  function removeDocuwareDocument(id){
  	$("#document-id-"+id+"-holder").slideUp();
  	$("#document-id-"+id+"-holder").remove();
  	$("#list-document-id-"+id).prop("checked",false)
  }

  function addLocalDocument(formValue,name){
        //alert(formValue+' '+name);
        if($("#list-document-id-"+formValue).is(':checked')){
        	var documentClone = $('#documents-template').clone();
        	documentClone.attr("id", "document-id-"+formValue+"-holder");
        	documentClone.prependTo('#documents-box');
        	$("#document-id-"+formValue+"-holder").slideDown();
        	$("#document-id-"+formValue+"-holder input[type=checkbox]").attr("id","document-id-"+formValue);
        	$("#document-id-"+formValue+"-holder input[type=checkbox]").attr("name","local_documents[]");
        	$("#document-id-"+formValue+"-holder input[type=checkbox]").attr("onClick","removeLocalDocument('"+formValue+"');");
        	$("#document-id-"+formValue+"-holder input[type=checkbox]").val(formValue);
        	$("#document-id-"+formValue+"-holder span").html('&nbsp; '+name+' ');
        } else {
        	$("#document-id-"+formValue+"-holder").slideUp();
        	$("#document-id-"+formValue+"-holder").remove();
        }
      }
      function removeLocalDocument(id){
      	$("#document-id-"+id+"-holder").slideUp();
      	$("#document-id-"+id+"-holder").remove();
      	$("#list-document-id-"+id).prop("checked",false)
      }
    </script>

    <script type="text/javascript">
	    // filter documents based on class
	    $('#document-filter').on('keyup', function () {
	    	var searchString = $(this).val().toLowerCase();
	    	if(searchString.length > 0){
	    		$('.document-list-item ').hide();
	    		$('.document-list-item[class*="' + searchString + '"]').show();
	    	}else{
	    		$('.document-list-item ').show();
	    	}
	    });
	  </script>
 --}}
