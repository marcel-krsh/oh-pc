@extends('layouts.simplerAllita')
@section('head')
<title>Contacts to Project</title>


@stop
@section('content')
<!-- <script src="/js/components/upload.js{{ asset_version() }}"></script>
<script src="/js/components/form-select.js{{ asset_version() }}"></script>
<script src="/js/components/datepicker.js{{ asset_version() }}"></script>
<script src="/js/components/tooltip.js{{ asset_version() }}"></script> -->
<style>
	body, div, p {
		font-size: 13pt;
	}
	h1 {
		font-size: 24pt;

	}
	h2 {
		font-size: 20pt;
	}
	h3 {
		font-size: 16pt
	}
	h4,h5 {
		font-size: 14pt;
	}
	.crr-sections {
		width:1142px; min-height: 1502px; margin-left:auto; margin-right:auto; padding: 72px;

	}

	#crr-part {
		-webkit-transition: width 1s ease-out;
			-moz-transition: width 1s ease-out;
			-o-transition: width 1s ease-out;
			transition: width 1s ease-out;

	}
	#crr-sections {
		-webkit-transition: width 1s ease-out;
			-moz-transition: width 1s ease-out;
			-o-transition: width 1s ease-out;
			transition: width 1s ease-out;

	}

#crr-panel .uk-panel-box-white {background-color:#ffffff;}
#crr-panel .uk-panel-box .uk-panel-badge {}
#crr-panel .green {color:#82a53d;}
#crr-panel .blue {color:#005186;}
#crr-panel .uk-panel + .uk-panel-divider {
    margin-top: 50px !important;
}
#crr-panel table tfoot tr td {border: none;}
#crr-panel textarea {width:100%;}
#crr-panel .note-list-item:last-child { border: none;}
#crr-panel .note-list-item { padding: 10px 0; border-bottom: 1px solid #ddd;}
#crr-panel .property-summary {margin-top:0;}
#main-window { padding-top:0px !important; padding-bottom: 0px !important; max-width: 1142px !important; min-width: 1142px !important; }
body{ background-color: white; }
.crr-blocks { page-break-inside: avoid; }

@page {
   margin: .5in;
   size: portrait;
}

ul.leaders li:before, .leaders > div:before {
	content:"";
}
ul.leaders li {
	border-bottom: 1px dotted black;
    padding-bottom: 9px;
    padding-top: 7px;
}
</style>


<div uk-grid >




			<?php $row = 0;?>
            <div id="main-report-view" class="" style=" min-width: auto; padding:0px; background-color: currentColor;">

            	<div class="uk-shadow uk-card uk-card-default uk-card-body uk-align-center crr-sections" style="">
            		<h1>Contacts to Project</h1>
            		<hr class="uk-width-1-1">
            		<p>This list is output based on projects, contacts associated with those projects, and then a list of other projects those cotacts are on. The id numbers will be crucial to knowing who is a duplicate or a repeated user. Organization affiliation listed are those where they are the primary contact for that organization.</p>
            		<hr class="uk-width-1-1">


            		<table class="uk-table uk-striped">
            			<thead>
            				<tr>
            				<th style="width: 150px">
            					Project Id
            				</th>

            				<th>
            					Project Number
            				</th>

            				<th>
            					Project Name
            				</th>
            				<th>
            					Contacts (People)
            				</th>

            				</tr>
            			</thead>
            			<tbody>
            				@forEach($projects as $project)
            				<tr class="rows" id="project-{{$project->id}}">
            					<td>
            						{{$project->id}}
            					</td>
            					<td>
            						{{$project->project_number}}
            					</td>
            					<td>
            						{{$project->project_name}}
            					</td>
            					<td>
            						{{-- {{ dd($project->contactRoles->pluck('person')) }} --}}
            						@forEach($project->contactRoles as $contact)
            							{{$contact->person_id}} | @if($contact->person) {{$contact->person->first_name}} {{$contact->person->last_name}} | {{$contact->projectRole->role_name}} | User: @if($contact->person->user) {{$contact->person->user->id}} {{$contact->person->user->email}} @else NA @endIf @if($contact->person->email) | Contact Assigned Email: {{$contact->person->email->email_address}} @endIf | @if(count($contact->person->organizations) > 0) Associated Organizations: @forEach($contact->person->organizations as $org) @if($org) {{$org->organization_name}} <br />@endIf @endForEach @else No Organizations @endIf @endif<hr />
            						@endForEach
            					</td>
            				</tr>
            				@endForEach

	            	 	</tbody>
            		</table>
            	</div>

            </div>




</div>


@stop