<?php // 'parcels','totalParcels', 'currentUser', 'svm_parcels_sorted_by_query','parcelsAscDesc', 'svmParcelsAscDescOpposite','programs' ?>
<?php setlocale(LC_MONETARY, 'en_US'); ?>
@if(Request::query('page') < 2)
		<?php
		session(['svmPreviousParcelID' => '']); 
	
		session(['svmPreviousAddress' => '']); 
		?>
		

<STYLE>
         		.date {
         			width: 40px;
         			background: #fff;
					text-align: center;
					font-family: 'Helvetica', sans-serif;
					position: relative;
         		}
         		.year {
         			display: block;
         			background-color: lightgray;
         			color: white;
         			font-size: 12px;
         		}
         		.m-d {
         			font-size: 14px;
         			line-height: 14px;
				    padding-top: 7px;
				    margin-top: 0px;
				    padding-bottom: 7px;
				    margin-bottom: 0px;

         		}

				.keepMe {
					background-color: #ffecec !important;
				}
				.deletedParcel {
					opacity: .25;
    				text-decoration: line-through;
				}
				.filter-drops{
					    -webkit-appearance: none;
					    -moz-appearance: none;
					    margin: 0;
					    border: none;
					    overflow: visible;
					    font: inherit;
					    color: #3a3a3a;
					    text-transform: none;
					    display: inline-block;
					    box-sizing: border-box;
					    padding: 0 12px;
					    background-color: #f5f5f5;
					    vertical-align: middle;
					    line-height: 28px;
					    min-height: 30px;
					    font-size: 1rem;
					    text-decoration: none;
					    text-align: center;
					    border: 1px solid rgba(0, 0, 0, 0.06);
					    border-radius: 4px;
					    text-shadow: 0 1px 0 #ffffff;
					    background: url(/images/select_icon.png) no-repeat;
					    background-position: 5px 7px;
					    text-indent: 13.01px;
					    background-size: 18px;
					    background-color: #f5f5f5;
				}
				select::-ms-expand {
				    display: none;
				}
				.countFlag{
					position: relative;
				    left: -2px;
				    top: -9px;
				    width: 10px;
				    height: 10px;
				    border-radius: 5px;
				    display: inline-block;
				    font-size: 9px;
				    text-align: center;
				    line-height: 9px;
				    color: white;
				    background-color: grey;
				}
				.gray-flag {
					background-color: lightgrey;
				}
	
         	</STYLE>
        
<div class="uk-grid">
	
	<div class="uk-width-1-1">
		

		<hr class="dashed-hr">
		<div class="uk-overflow-container" class="margin:4px;">

		<table class="uk-table uk-table-hover uk-table-striped" style="min-width: 1420px;">
		<thead >
			<tr >
				<th width="60" colspan="2">
				<a class="uk-link-muted" onClick="loadSiteVisitManagerSubTab('site_visit_manager','visit_list?svm_parcels_sort_by=12&svm_parcels_asc_desc={{ $svmParcelsAscDescOpposite }}');" >Visit Date <span uk-icon="sort-{{ $svmParcelsAscDesc }}" class="date-arrow-{{ $svm_parcels_sorted_by_query }}"></span>
				</th>
				<th width="135"><span uk-icon="info-circle"></span> <a class="uk-link-muted" onClick="loadSiteVisitManagerSubTab('site_visit_manager','visit_list?svm_parcels_sort_by=1&svm_parcels_asc_desc={{ $svmParcelsAscDescOpposite }}');" uk-tooltip="Sorting by parcel id will highlight duplicate parcels by their parcel id." >Parcel Id <span uk-icon="sort-{{ $svmParcelsAscDesc }}" class="parcel-arrow-{{ $svm_parcels_sorted_by_query }}"></span></a></th>
				<th width="300"><span uk-icon="map"></span> <a class="uk-link-muted" onClick="loadSiteVisitManagerSubTab('site_visit_manager','visit_list?svm_parcels_sort_by=2&svm_parcels_asc_desc={{ $svmParcelsAscDescOpposite }}');" uk-tooltip="Sorting by address will highlight duplicate parcels by their street address.">Address <span uk-icon="sort-{{ $svmParcelsAscDesc }}" class="address-arrow-{{ $svm_parcels_sorted_by_query }}"></span></a></th>
				<th width="90"> 

					
					<a class="uk-link-muted" onClick="loadSiteVisitManagerSubTab('site_visit_manager','visit_list?svm_parcels_sort_by=14&svm_parcels_asc_desc={{ $svmParcelsAscDescOpposite }}');" >Target Area <span uk-icon="sort-{{ $svmParcelsAscDesc }}" class="target-area-arrow-{{ $svm_parcels_sorted_by_query }}"></span></a>
				 </th>
				<th width="90"> 

					
					<a class="uk-link-muted" onClick="loadSiteVisitManagerSubTab('site_visit_manager','visit_list?svm_parcels_sort_by=6&svm_parcels_asc_desc={{ $svmParcelsAscDescOpposite }}');" >Program <span uk-icon="sort-{{ $svmParcelsAscDesc }}" class="program-arrow-{{ $svm_parcels_sorted_by_query }}"></span></a>
				 </th>
				<th width=""><a class="uk-link-muted" onClick="loadSiteVisitManagerSubTab('site_visit_manager','visit_list?svm_parcels_sort_by=11&svm_parcels_asc_desc={{ $svmParcelsAscDescOpposite }}');" >Status <span uk-icon="sort-{{ $svmParcelsAscDesc }}" class="status-arrow-{{ $svm_parcels_sorted_by_query }}"></span></a></th>
				@if(Auth::user()->entity_type == 'hfa')
				<th width="90"><a class="uk-link-muted" onClick="loadSiteVisitManagerSubTab('site_visit_manager','visit_list?svm_parcels_sort_by=13&svm_parcels_asc_desc={{ $svmParcelsAscDescOpposite }}');" >HFA Status <span uk-icon="sort-{{ $svmParcelsAscDesc }}" class="hfa-status-arrow-{{ $svm_parcels_sorted_by_query }}"></span></a></th>
				@endIf
				
				
				<th width="200">
					
					<span uk-icon="user"></span> Inspector
				
			</tr>
		</thead>
		<tbody id="results-list">
	@endif
	<?php 
	if(Request::query('page')){
		$i = (Request::query('page')-1) * 100;
	} 
	
	// we use sessions for these because of the infinite scroll - and only set them if they are not already set
	
	
	?>
	@if($svmTotalParcels > 0)
	    @foreach ($svmParcels as $svmParcel ) 

	    	<?php $i = $i + 1; ?>
	    	<tr id="sv-{{$svmParcel->id}}" >
	    
	         <td width="1">
	         <td width="59">
	         
	         	<div class="date">
					
					<?php 

						/// SITE VISIT SUPPORTING DATA

						// $lastSiteVisit = $svmParcel->siteVisits->first();
						// $totalSiteVisits = count($svmParcel->siteVisits);
						// $inProgressVisits = $svmParcel->siteVisitLists()->where('status',1)->count();
					?>
					@if(date('Y',strtotime($svmParcel->visit_date)) == "-0001" || date('Y',strtotime($svmParcel->visit_date)) == "0001" || date('Y',strtotime($svmParcel->visit_date)) == "0000" )
					<h2 uk-tooltip="A date was not able to be determined for this parcel's site visit. Most likely this was an error carried over from sales force.">NA</h2>
					@else
					<p class="m-d">{{ date('m',strtotime($svmParcel->visit_date)) }}/{{ date('d',strtotime($svmParcel->visit_date)) }}</p><span class="year">{{ date('Y',strtotime($svmParcel->visit_date)) }}</span>
					@endIf
				</div>
			 </td>
	         <td width="">

	         <a onClick="loadDetailTab('/parcel/','{{ $svmParcel->id }}','1',0,0)"><span uk-tooltip="System ID: {{$svmParcel->id}} @if(isset($svmParcel->importId->id)) , a parcel in import # {{$svmParcel->importId->import_id}} on {{date('n/j/y \a\t g:h a', strtotime($svmParcel->importId->created_at))}} by {{$svmParcel->importId->import->imported_by->name}} @endif. ">{{ $svmParcel->parcel_id }}</span></a><br /><a onclick="dynamicModalLoad('site_visit/{{$svmParcel->site_visit_id}}',1);"><small style="color:black;">@if($svmParcel->status == 1) <span uk-icon="spinner" class="uk-icon-spin"></span> In Progress @else {{$svmParcel->passFail()}} @endIf </small> </td>
	         <?php 
	         session(['previousSvmParcelID' => str_replace('-','',strtoupper($svmParcel->parcel_id))]);
	         session(['previousSvmParcelSystemID' => $svmParcel->id]);
	         session(['previousSvmAddress' => strtoupper($svmParcel->street_address)]);
	         ?>
	         <td width=""><a href="{{ $svmParcel->google_map_link}}" target="_blank">{{ $svmParcel->street_address }}, {{ $svmParcel->city }}, {{$svmParcel->state_acronym }} {{ $svmParcel->zip }}</a></td>
	         <td width="220"> {{ $svmParcel->target_area_name }}
	         </td>
	         <td width="220"> {{ $svmParcel->program_name }}
	         </td>
	         <td width=""> {{ $svmParcel->lb_property_status_name }}
	         </td>
		     @if(Auth::user()->entity_type == 'hfa')
		     <td width=""> {{ $svmParcel->hfa_property_status_name }}
		     </td>
	         @endIf
	            
		         <td>
		         
		          {{ $svmParcel->name }}
		          
		          </td>
		         
	         
	         </tr>
	   
			 

	    @endforeach

    <!-- PARCEL PAGINATION LINKS??? -->
    <?php /*{{ $svmParcels->links() }} */ ?>
    @endIf

    @if(Request::query('page')<2)
    
		</tbody>
		</table>

		
		</div>
	</div>
</div>

<div id="svm-results-pagination">
<a name="svm-bottom"></a>
</div>
<div id="list-tab-bottom-bar" class="uk-vertical-align">
<a  href="#top" uk-scroll="{offset: 90}" class="uk-button uk-button-default uk-button-small uk-align-right uk-margin-top uk-margin-right"><span uk-icon="toggle-up" class="uk-text-small  uk-vertical-align-middle"></span> SCROLL TO TOP</a> 

</div>

@endif
