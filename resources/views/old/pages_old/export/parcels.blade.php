@extends('layouts.allita')

@section('content')
<script>
	// Update Tab Title - note the refresh button in tab text script.
	$('.list-tab-text').html(' : Import Parcels: Corrections Needed ');
	
	$('#list-tab > a > span').addClass('a-circle-from-bottom');
	// display the tab
	$('#list-tab').show();

	// Watch the file form
	
</script>
<div class="uk-grid">

	@if ($errors->any())
		<div class="uk-width-1-1">
		<h3>Errors</h3>
		@foreach ($errors->all() as $error)
			<div class="uk-alert uk-alert-danger">{{ $error }}</div>
		@endforeach
		</div>
	@endif

	<div class="uk-width-1-1 uk-width-2-3@m uk-align-center uk-margin-top">
	    <div class="uk-panel uk-panel-box uk-text-center">
	        <br />
	        <div class="uk-border-circle uk-align-center" style="background: white; border: 1px solid lightgray; width: 80px; height: 80px; font-size: 18px;">
	    		<span class="a-circle-from-bottom uk-icon-large uk-align-center uk-margin-top"></span>
	    	</div>
	    	<br />
	    	<h1 class="uk-panel-title">Parcels Export Reports</h1>
		</div>
	</div>
	@if(count($pending_reports))
	<div class="uk-width-1-1 uk-width-2-3@m uk-align-center">
		<div class="uk-alert uk-alert-primary uk-text-center">A report is being processed. Please come back in a few minutes.</div>
	</div>
	@endif

	<div class="uk-width-1-1">
		@if(count($files)>0)
		 	<h4 class="uk-text-left">{{count($files)}} FILES 
		 		<small>

		            <a href="/parcels/export" class="uk-button uk-button-default uk-button-small uk-align-right">
		                EXPORT PARCELS NOW
		            </a>
		        </small>
		    </h4>
	    	<hr />
	    	
		 	<table class="uk-table uk-table-hover uk-table-striped uk-table-condensed small-table-text">
		        <thead>
			        <tr>
			        	<th>
			        		<small>FILENAME</small>
			        	</th>
				        <th>
				            <small>DATE</small>
				        </th>
				        <th>
				            <small>SIZE</small>
				        </th>
				        <th>
				            <small>REQUESTED BY</small>
				        </th>
				        <th>
				            <small>DOWNLOADS</small>
				        </th>
				        <th>
				            <small>ACTION</small>
				        </th>
			        </tr>
			    </thead>
		        <tbody>
		        	@foreach($files as $file)
		        	<tr>    
                        <td>
                        	<small>{{$file['filename']}}</small>
                        </td>   
                        <td>
                        	<small>{{$file['humantime']}}</small>
                        </td>   
                        <td>
                        	<small>{{$file['size']}}</small>
                        </td>  
				        <td>
				            <small>{{$file['requestor']}}</small>
				        </td> 
				        <td>
				        	<small>
				        	@if($file['downloads'])	
				        	<a class="uk-link-muted " onclick="view_download_stats('{{$file['id']}}');" uk-tooltip="">
			                    <span class="a-info-circle"></span>
			                </a>
								{{$file['downloads']}}
							@endif
							</small>
				        </td>
                        <td>
                        	<a class="a-lower" href="/reports/export_parcels/{{$file['filename']}}/download" title="Download"></a>
                        </td>
                    </tr>
				 	@endforeach
		        </tbody>
		    </table>
		@else
			<h4 class="uk-text-left"> 0 FILES
		 		<small>

		            <a href="/parcels/export" class="uk-button uk-button-default uk-button-small uk-align-right">
		                EXPORT PARCELS NOW
		            </a>
		        </small>
		    </h4>
		    <hr />
		 	<div class="uk-alert">You do not have any available reports at the moment.</div>
		@endif
		<hr />
	</div>

	<script>
	function view_download_stats(reportid){
		var download_stats = [];
		@foreach($downloaders_array as $key => $stat)
		download_stats[{{$key}}] = '{!! $stat !!}';
		@endforeach

		UIkit.modal.alert("File Download History: <br />"+download_stats[reportid]);
	}
	</script>
@stop