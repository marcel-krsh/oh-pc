
<STYLE>
         		.date {
         			width: 70px;
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
				.deleteddevice {
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
				<th width="80" >
				Registered
				</th>
				<th width=""><span uk-icon="barcode"></span> Device UUID</th>
				<th width=""> 
					<span uk-icon="gears"></span> Tools
				</th>
				<th width="80">Last Used</th>				
			</tr>
		</thead>
		<tbody id="device-list">

	
	@if(count($devices) > 0)
	    @foreach ($devices as $device ) 

	    	
	    	<tr id="device-{{$device->id}}">
	    	
	         <td width="80">
	         
	         	<div class="date">
					<p class="m-d">{{ date('n',strtotime($device->created_at)) }}/{{ date('d',strtotime($device->created_at)) }}/{{ date('y',strtotime($device->created_at)) }}</p><span class="year">{{ date('g:h a',strtotime($device->created_at)) }}</span>
				</div>
			 </td>
			 
	         <td width="">{{ $device->device_id }}
	     	</td>
	         
	        <td>
	        	@if(is_null($device->last_wiped))
	        		@if($device->remote_wipe == 0)
	        			<a onclick="wipeDevice({{$device->id}})"><span uk-icon="exclamation-triangle"></span> <span uk-tooltip="Wiping the device clears all data, inlcuding its UUID.">Wipe Device</span></a> | <a class="uk-muted" onclick="dynamicModalLoad('devices/users?device_id={{$device->device_id}}')"><span uk-icon="user"></span> View Users Registered on Device</a>
	        		@else
	        			<span uk-icon="exclamation-triangle"></span> <span uk-tooltip="Wiping the device clears all data, inlcuding its UUID.">Wipe Requested</span> by {{ $device->wipeUser->name }} | <a class="uk-muted" onclick="dynamicModalLoad('devices/users?device_id={{$device->device_id}}')"><span uk-icon="user"></span> View Users Registered on Device</a>
	        		@endIf
	        	@else
	        	This device was wiped on {{date('n/d/y g:h a', strtotime($device->last_wiped))}} by {{ $device->wipeUser->name }}.  <br />The app itself may need to be uninstalled and reinstalled to work again on the physical device. | <a class="uk-muted" onclick="dynamicModalLoad('devices/users?device_id={{$device->device_id}}')"><span uk-icon="user"></span> View Users Registered on Device</a>
	        	@endIf
		    </td>
		    <td width="80">
	         
	         	<div class="date">
					
					<p class="m-d">{{ date('n',strtotime($device->updated_at)) }}/{{ date('d',strtotime($device->updated_at)) }}/{{ date('y',strtotime($device->updated_at)) }}</p><span class="year">{{ date('g:h a',strtotime($device->updated_at)) }}</span>
				</div>
			 </td> 
	         
	         </tr>
	   
			 

	    @endforeach
	@endif
    
    
		</tbody>
		</table>

		
		</div>
	</div>
</div>

<script>
 function wipeDevice(deviceId){
 	// id to refresh: svm-subtab-3
 	UIkit.modal.confirm("Are you sure you want to request this device's information be wiped? Any changes that have not been sync'd will be lost.").then(function() {
				console.log('User confirmed to wipe device.');
				dynamicModalLoad('wipe_device/'+deviceId);
			}); 

 }
</script>

<div id="-results-pagination">
<a name="-bottom"></a>
</div>
<div id="list-tab-bottom-bar" class="uk-vertical-align">
<a  href="#top" uk-scroll="{offset: 90}" class="uk-button uk-button-default uk-button-small uk-align-right uk-margin-top uk-margin-right"><span uk-icon="toggle-up" class="uk-text-small  uk-vertical-align-middle"></span> SCROLL TO TOP</a> 

</div>

