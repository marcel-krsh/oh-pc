@php
	$selected_icon = null;
@endphp
<h4 class="uk-text-primary uk-text-uppercase">Apply to These Findings:</h4>
<!-- RECIPIENT LISTING -->
<div class="communication-selector uk-scrollable-box" style="height: 300px">
	<ul class="uk-list document-menu">
		@php
			$findings = session()->get('selected_findings');
		@endphp
		@if(count($findings) > 0)
		@foreach ($findings as $f)
		@if($f->finding_type->type == 'nlt')
			@php
				$f_icon = '<i class="a-booboo"></i>';
			@endphp
		@endif
		@if($f->finding_type->type == 'lt')
			@php
				$f_icon = '<i class="a-skull"></i>';
			@endphp
		@endif
		@if($f->finding_type->type == 'file')
			@php
				$f_icon = '<i class="a-folder"></i>';
			@endphp
		@endif
		<li class="findings-list-item finding-{{ $f->id }}">
			@php
				if($f->id == $all_findings)
					$selected_finding = $f;
				else
					$selected_finding = null;
			@endphp
			<input {{ $id == $f->id ? 'checked=checked' : '' }} name="findings[]" id="list-finding-id-{{ $f->id }}" value="{{ $f->id }}" type="checkbox" class="uk-checkbox" onClick="addFinding(this.value,'{{ $f_icon }}Finding-{{ ($f->id) }}')">
			<label for="finding-id-{{ $f->id }}">
				{!! $f_icon !!} Finding # {{ $f->id }} - @if(!is_null($f->building_id)) <strong>{{ $f->building->building_name }}</strong> @if(!is_null($f->building->address)) {{ $f->building->address->line_1 }} {{ $f->building->address->line_2 }} {{ $f->building->address->city }}, {{ $f->building->address->state }} {{ $f->building->address->zip }} @endif @endif
				<span uk-tooltip title="@if(!is_null($f->building_id))
					<strong>{{ $f->building->building_name }}</strong> <br />
					@if(!is_null($f->building->address))
					{{ $f->building->address->line_1 }} {{ $f->building->address->line_2 }}<br />
					{{ $f->building->address->city }}, {{ $f->building->address->state }} {{ $f->building->address->zip }}<br /><br />
					@endif
					@elseif(!is_null($f->unit_id))
					{{ $f->unit->building->building_name }} <br />
					@if(!is_null($f->unit->building->address))
					{{ $f->unit->building->address->line_1 }} {{ $f->unit->building->address->line_2 }}<br />
					{{ $f->unit->building->address->city }}, {{ $f->unit->building->address->state }} {{ $f->unit->building->address->zip }}
					@endif
					<br /><strong>Unit {{ $f->unit->unit_name }}</strong>
					@else
					<strong>Site Finding</strong><br />
					@if(!is_null($f->project->address))
					{{ $f->project->address->line_1 }} {{ $f->project->address->line_2 }}<br />
					{{ $f->project->address->city }}, {{ $f->project->address->state }} {{ $f->project->address->zip }}<br /><br />
					@else
					NA
					@endif
					@endif
					{{ $f->amenity->amenity_description }} <br />
					<strong> {{ $f->finding_type->name }}</strong><br>">
					<span class="a-info-circle"  style="color: #56b285;"></span>
				</span>
			</label>
		</li>
		@endforeach
		@endif
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
  }

  function removeFinding(id){
  	$("#finding-id-"+id+"-holder").slideUp();
  	$("#finding-id-"+id+"-holder").remove();
  	$("#list-finding-id-"+id).prop("checked",false)
  }

   function showFindings() {
    	$('.findings-list').slideToggle();
    	$('#add-findings-button').toggle();
    	$('#done-adding-findings-button').toggle();
    }

    $("document").ready(function() {
    	@if(!is_null($selected_finding))
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
    setTimeout(function() {
    		addFinding('{{ $all_findings }}','{{ $selected_icon }}Finding-{{ $all_findings }}');
        // $("list-finding-id-{{ $all_findings }}").trigger('click');
    },2);
});

</script>
<!-- END RECIPIENT LISTING -->
