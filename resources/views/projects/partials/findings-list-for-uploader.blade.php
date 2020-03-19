										
										<li class="all" id="document-upload-findings-links">
											<div class="uk-width-1-1 uk-margin-bottom"  >
												
											{{$allFindings->links()}}
										</li>
										</ul>

										<ul id="findings-for-selection" style="overflow-x: hidden; max-height: 400px; min-height: 400px;">
										@forEach($allFindings as $uploadFinding)
										<li class="all upload-finding audit-{{$uploadFinding->audit_id}} @if($uploadFinding->building_id != NULL) building-{{$uploadFinding->building_id}} @endIf @if($uploadFinding->unit_id != NULL) building-{{$uploadFinding->unit->building_id}} unit-{{$uploadFinding->unit_id}} @endIf finding-{{$uploadFinding->id}}   @if($uploadFinding->auditor_approved_resolution) finding-resolved @else finding-unresolved @endIf" @if(!$showLinks)style="display: none;"@endIf>
											<input style="float: left; margin-top: 3px" name="" id="list-finding-id-{{$uploadFinding->id}}" value="{{$uploadFinding->id}}" type="checkbox" class="uk-checkbox" onclick="addFinding(this.value,'<i class=&quot;a-booboo&quot;></i>Finding-{{$uploadFinding->id}}')">

											<label style="display: block; margin-left: 30px" for="finding-id-{{ $uploadFinding->id }}">
												@if($uploadFinding->auditor_approved_resolution)<span>RESOLVED @else <span class=" attention" style="color:red"><strong>UNRESOLVED</strong> @endIf </span> | {{strtoupper($uploadFinding->finding_type->type)}} | @if($uploadFinding->building_id) BUILDING {{$uploadFinding->building->building_name}} @elseIf($uploadFinding->unit_id) UNIT {{$uploadFinding->unit->unit_name}}  @elseIf($uploadFinding->site) SITE @endIf | AUDIT # {{$uploadFinding->audit_id}} | FINDING # {{$uploadFinding->id}}<br /> {{$uploadFinding->amenity->amenity_description}}: {{$uploadFinding->level_description()}}  @if($uploadFinding->comments != NULL && count($uploadFinding->comments))<br />Auditor Comment: "{{$uploadFinding->comments->first()->comment}}"@endIf
												<hr />
											</label>
										</li>
										@endforeach
										</ul>
										
										
										@if(count($allFindings)<1)
										<ul>
										<li><h2>Sorry Your Filters Returned 0 results.</h2><p> Please Adjust Your Selections</p>
											<a class="help-badge" onclick="UIkit.modal.alert('<h1>Not sure why you are getting zero results?</h1><h3>Try removing the filters you applied by selecting the first option in each drop list, and click VIEW MATCHING FINDINGS again.</h3><h3>If you need technical assistance, this output will help the support staff resolve your problem: <ul><li>RESOLVED: {{$unresolved}} </li><li>AUDIT: {{$audit_id}} </li><li>BUILDING / UNIT SEARCH VALUE: {{$buSearchValue}}</li><li> IS BUILDING: {{$isBuilding}}</li></ul></h3>');" > HELP!
											</a>
												
													<hr />
												</li>
											</ul>
										@endIf
										<script type="text/javascript">
											$(document).ready(function(){
												   var tempdiv = '<li><div style="height:100px;text-align:center;"><div uk-spinner style="margin: 20px 0;"></div></div></li>';
												
												   $('#document-upload-findings-links .page-link').click(function(){
												   		$('#findings-for-selection').html(tempdiv);
													   	$('#document-upload-findings-list').load($(this).attr('href'));
													   	return false;
													});
												   
												   $("input:checkbox[name='findings[]']:checked").each(function(){
														//findingsSelectedArray.push($(this).val());
														$('#list-finding-id-'+$(this).val()).prop('checked',true);
													});
											    });
										</script>