										@if($showLinks && count($allFindings))
										<li class="all" id="document-upload-findings-links">
											<div class="uk-width-1-1 uk-margin-bottom"  >
												
											{{$allFindings->links()}}
										</li>
										@endIf
										@forEach($allFindings as $uploadFinding)
										<li class="all upload-finding audit-{{$uploadFinding->audit_id}} @if($uploadFinding->building_id != NULL) building-{{$uploadFinding->building_id}} @endIf @if($uploadFinding->unit_id != NULL) building-{{$uploadFinding->unit->building_id}} unit-{{$uploadFinding->unit_id}} @endIf finding-{{$uploadFinding->id}}   @if($uploadFinding->auditor_approved_resolution) finding-resolved @else finding-unresolved @endIf" @if(!$showLinks)style="display: none;"@endIf>
											<input style="float: left; margin-top: 3px" name="finding-id-checkbox" class="uk-checkbox document-category-selection" id="upload-finding-id-{{ $uploadFinding->id }}" value="{{ $uploadFinding->id }}" type="checkbox">
											<label style="display: block; margin-left: 30px" for="finding-id-{{ $uploadFinding->id }}">
												@if($uploadFinding->auditor_approved_resolution)<span>RESOLVED @else <span class=" attention" style="color:red"><strong>UNRESOLVED</strong> @endIf </span> | {{strtoupper($uploadFinding->finding_type->type)}} | @if($uploadFinding->building_id) BUILDING {{$uploadFinding->building->building_name}} @elseIf($uploadFinding->unit_id) UNIT {{$uploadFinding->unit->unit_name}}  @elseIf($uploadFinding->site) SITE @endIf | AUDIT # {{$uploadFinding->audit_id}} | FINDING # {{$uploadFinding->id}}<br /> {{$uploadFinding->amenity->amenity_description}}: {{$uploadFinding->level_description()}}  @if($uploadFinding->comments != NULL && count($uploadFinding->comments))<br />Auditor Comment: "{{$uploadFinding->comments->first()->comment}}"@endIf
												<hr />
											</label>
										</li>
										@endforeach
										@if($showLinks)
										<li class="all" id="document-upload-findings-links2 ">
											
											
													{{$allFindings->links()}}
										</li>
										@endIf
										@if($showLinks && count($allFindings)<1)
										<li><h2>Sorry Your Filters Returned 0 results.</h2><p> Please Adjust Your Selections</p>
											{{$unresolved}} {{$audit_id}} {{$buSearchValue}}
											
													
													<hr />
												</li>
											
										@endIf
										<script type="text/javascript">
											$(document).ready(function(){
												   var tempdiv = '<div style="height:100px;text-align:center;"><div uk-spinner style="margin: 20px 0;"></div></div>';
												
												   $('#document-upload-findings-links .page-link').click(function(){
												   		$('#document-upload-findings-list').html(tempdiv);
													   	$('#document-upload-findings-list').load($(this).attr('href'));
													   	return false;
													});
												   $('#document-upload-findings-links2 .page-link').click(function(){
												   		$('#document-upload-findings-list').html(tempdiv);
													   	$('#document-upload-findings-list').load($(this).attr('href'));
													   	return false;
													});
											    });
										</script>