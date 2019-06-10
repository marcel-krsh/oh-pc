<?php setlocale(LC_MONETARY, 'en_US');?>
<!-- <script src="/js/components/upload.js{{ asset_version() }}"></script> -->
<style>
.uk-breadcrumb>li:nth-child(n+2):before {
    content: ">";
    display: inline-block;
    margin: 0 8px;
}
.uk-breadcrumb button{cursor: initial;}
.pickListButtons {
  padding: 10px;
  text-align: center;
}

.pickListButtons button {
  margin-bottom: 5px;
}

.pickListSelect {
  height: 100px !important;
  width: 100%;
}
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
</style>
<div uk-grid class="uk-grid-collapse">
	<div class="uk-width-1-1 uk-margin-top uk-margin-bottom">
		<div class="uk-panel">
			<ul class="uk-list uk-subnav-pill"  style="display:none;">
				<li><button id="disposition-start-button" onclick="step('disposition-start');" class="uk-button uk-button-default uk-button-primary uk-button-small button-step"><span class="a-home-2"></span> Start</button></li>

				@if($disposition)
					    <li><button id="disposition-upload-button" onclick="step('disposition-upload');" class="uk-button uk-button-default uk-button-small button-step" @if(!$disposition) disabled @endif><span class="a-file-plus"></span> Supporting Documents</button></li>
					    <li><button @if($disposition) onclick="window.open('/dispositions/{{$parcel->id}}/{{$disposition->id}}', '_blank')" @endif class="uk-button uk-button-default uk-button-small button-step" @if(!$disposition) disabled @endif>Open Disposition in New Window</button></li>


					    @if($parcel->associatedDisposition)
					    	@if($disposition->invoice)
					    		@if($disposition->invoice->disposition_invoice_id)
					                <li class="uk-margin-left">
					                	<button class="uk-button uk-button-small uk-button-success uk-width-1-1@m" type="button" onclick="window.open('/disposition_invoice/{{$disposition->invoice->disposition_invoice_id}}', '_blank')">Invoice #{{$disposition->invoice->disposition_invoice_id}}</button>
					                </li>
								@endif
						@endif

						@if($disposition->parcel->unpaidRetainages->count())
							<li class="uk-margin-left">
			                	<button class="uk-button uk-button-small uk-button-danger attention uk-width-1-1@m" type="button" disabled>Unpaid retainages</button>
			                </li>
						@endif

		                <li class="uk-margin-left">
		                	<span class="uk-text-small">Current disposition status:</span>
		                </li>

		                @php
		                	$latest_parcel_disposition = $parcel->associatedDisposition()->first();
		                @endphp

		                @if($latest_parcel_disposition->status_id == 1)
			                <li class="uk-margin-left">
			                	<div class="uk-badge uk-badge-success">Draft</div>
			                </li>
		                @elseif($latest_parcel_disposition->status_id == 2)
			                <li class="uk-margin-left">
			                	<div class="uk-badge uk-badge-success">Pending Landbank Approval</div>
			                </li>
		                @elseif($latest_parcel_disposition->status_id == 3)
			                <li class="uk-margin-left">
			                	<div class="uk-badge uk-badge-success">Pending HFA Approval</div>
			                </li>
		                @elseif($latest_parcel_disposition->status_id == 4)
			                <li class="uk-margin-left">
			                	<div class="uk-badge uk-badge-success">Pending Payment</div>
			                </li>
		                @elseif($latest_parcel_disposition->status_id == 5)
			                <li class="uk-margin-left">
			                	<div class="uk-badge uk-badge-warning">Declined</div>
			                </li>
		                @elseif($latest_parcel_disposition->status_id == 6)
			                <li class="uk-margin-left">
			                	<div class="uk-badge uk-badge-success">Paid</div>
			                </li>
		                @elseif($latest_parcel_disposition->status_id == 7)
			                <li class="uk-margin-left">
			                	<div class="uk-badge uk-badge-success">Approved</div>
			                </li>
		                @endif

	                @endif
				@endif
            </ul>
		</div>



	</div>

	@if(!$proceed)
	<div class="uk-width-1-1">
		<div class="uk-alert uk-alert-warning uk-width-1-2 uk-container-center">Disposition is not available at this time.</div>
	</div>
	@else
	<div id="disposition-start" class="uk-width-1-1 dispo-step">


        @if(count($parcel->associatedDispositions))
			<table class="uk-table uk-table-hover uk-table-striped uk-table-condensed small-table-text" >
			 	<thead>
			 		<th>
			 			<small>DATE CREATED</small>
			 		</th>
			 		<th>
			 			<small>TYPE</small>
			 		</th>
			 		<th>
			 			<small>RELEASE REQUESTED</small>
			 		</th>
			 		<th>
			 			<small>STATUS</small>
			 		</th>
			 		<th>
			 			<small>ACTIONS</small>
			 		</th>
			 	</thead>
			 	<tbody>
			 		@foreach($parcel->associatedDispositions as $associated_disposition)
					<tr>
						<td>
							@if($associated_disposition->created_at !== null && $associated_disposition->created_at != "-0001-11-30 00:00:00" && $associated_disposition->created_at != "0000-00-00 00:00:00")
							<div class="date">
								<p class="m-d">{{ date('m',strtotime($associated_disposition->created_at)) }}/{{ date('d',strtotime($associated_disposition->created_at)) }}</p><span class="year">{{ date('Y',strtotime($associated_disposition->created_at)) }}</span>
							</div>
							@else
							N/A
							@endif
						</td>
						<td><small>@if($associated_disposition->type){{$associated_disposition->type->disposition_type_name}}@endif</small></td>
						<td>@if($associated_disposition->date_release_requested !== null  && $associated_disposition->date_release_requested != "0000-00-00 00:00:00")
							<div class="date">
								<p class="m-d">{{ date('m',strtotime($associated_disposition->date_release_requested)) }}/{{ date('d',strtotime($associated_disposition->date_release_requested)) }}</p><span class="year">{{ date('Y',strtotime($associated_disposition->date_release_requested)) }}</span>
							</div>
							@else
							N/A
							@endif</td>
						<td><small>@if($associated_disposition->status) {{$associated_disposition->status->invoice_status_name}} @endif</small></td>
						<td>

			 				<a class="a-file-copy-2" onclick="window.open('/dispositions/{{$parcel->id}}/{{$associated_disposition->id}}', '_blank')" uk-tooltip="View Disposition"></a>
						@if(Auth::user()->isHFAAdmin() && 0)

			 				<a class="a-trash-4" onclick="" title="Delete" uk-tooltip="Delete Disposition"></a>
			 			@endif
						</td>
					</tr>
					@endforeach
			 	</tbody>
			</table>

			@endif

	@if(!$disposition)
		<div id="disposition-start-1" class="uk-panel uk-panel-box uk-width-1-1@s uk-width-1-2@m uk-container-center">
            <h3 class="uk-panel-title"><span class="a-home-2"></span> Ready to start</h3>
            <p class="uk-text-center">You are ready to start the NIP Early Lien Release Request Process.<br />Choose a disposition type below to proceed.</p>
            <form id="disposition-start-form" class="uk-container-center uk-width-1-2">
	            	<div class="uk-form-row">
	            		<select id="disposition-type" name="disposition-type" class="uk-select uk-width-1-1">
					    	<option>Choose a disposition type</option>
					    	@foreach($types as $type)
							<option name="disposition-type" value="{{$type->id}}">{{$type->disposition_type_name}}</option>
							@endforeach
					    </select>
					</div>
					<div class="uk-form-row">
						<button onclick="processStep('disposition-start');" class="uk-width-1-1 uk-button uk-button-success" type="button">Create New Disposition</button>
					</div>
            </form>
        </div>
        <div id="disposition-start-2" class="uk-panel uk-panel-box uk-width-1-1@s uk-width-1-2 uk-container-center uk-hidden">
            <h3 class="uk-panel-title"><span class="a-home-2"></span> Update your disposition</h3>
            <p>This disposition has been created and you can proceed to the next step.</p>
        </div>

        @else
        @if($latest_parcel_disposition->status_id == 5)
         <div class="uk-panel uk-panel-box uk-width-1-2 uk-container-center">
            <h3 class="uk-panel-title">The most recent disposition was declined</h3>
            <form id="disposition-start-form" class=" uk-container-center uk-width-1-2">
	            	<div class="uk-form-row">
	            		<select id="disposition-type" name="disposition-type" class="uk-select uk-width-1-1">
					    	<option>Choose a disposition type</option>
					    	@foreach($types as $type)
							<option name="disposition-type" value="{{$type->id}}">{{$type->disposition_type_name}}</option>
							@endforeach
					    </select>
					</div>
					<div class="uk-form-row">
						<button onclick="processStep('disposition-start');" class="uk-width-1-1 uk-button uk-button-success" type="button">Create New Disposition</button>
					</div>
            </form>
        </div>
        @else
        <div class="uk-panel uk-panel-box uk-width-1-2 uk-container-center">
            <h3 class="uk-panel-title"><span class="a-home-2"></span> Update your disposition</h3>
            <p>This disposition was created on {{ Carbon\Carbon::parse($disposition->created_at)->format('m/d/Y')}}.</p>
        </div>
        @endif
        @endif
	</div>

	<div id="disposition-upload" class="uk-width-1-1 dispo-step uk-hidden">
		<div class="uk-container uk-margin-top">
			<form id="uploadSupportingDocuments" >
				<div class="uk-container uk-container-center">
					<div class="uk-grid">

				        <div class="uk-width-1-2@m uk-width-1-1@s">
				        	@if($disposition)
				        	<h4>Required Support Documents For Early Release
				        	@if($disposition->disposition_type_id == 5)
				        	<a onclick="resetDocTabCategoryListVars();selectCategory('22')" uk-tooltip="Select corresponding categories">
			                    <span class="a-file-tool"></span></a><br />
				        	<small>Side Lot</small></h4>
							<ul class="uk-list">
								<li>Draft Lien Release</li>
							</ul>
							@elseif($disposition->disposition_type_id == 4)
							<a onclick="resetDocTabCategoryListVars();selectCategory('22,23')" uk-tooltip="Select corresponding categories">
			                    <span class="a-file-tool"></span></a><br />
							<small>Public Use</small></h4>
							<ul class="uk-list">
								<li>Draft Lien Release </li>
								<li>Disposition Use Affidavit</li>
							</ul>
							@elseif($disposition->disposition_type_id == 2)
							<a onclick="resetDocTabCategoryListVars();selectCategory('22,24,25,26')" uk-tooltip="Select corresponding categories">
			                    <span class="a-file-tool"></span></a><br />
							<small>Nonprofit Organization</small></h4>
							<ul class="uk-list">
								<li>Draft Lien Release</li>
								<li>Proof of Tax Exempt Status</li>
								<li>Proof the entity will commence operation/construction within one year (narrative allowable) </li>
								<li>Proof the property is zoned for its new use</li>
							</ul>
							@elseif($disposition->disposition_type_id == 1)
							<a onclick="resetDocTabCategoryListVars();selectCategory('22,27,28,29,25,26')" uk-tooltip="Select corresponding categories">
			                    <span class="a-file-tool"></span></a><br />
							<small>Residential or Business Development</small></h4>
							<ul class="uk-list">
								<li>Draft Lien Release</li>
								<li>Purchase Agreement</li>
								<li>Proof that proposed owner is current on all real estate taxes and assessments in the county</li>
								<li>Proof the owner was not a prior owner of foreclosed real property in the county since 1/1/10</li>
								<li>Proof the entity will commence operation/construction within one year (narrative allowable) </li>
								<li>Proof the property is zoned for its new use</li>
							</ul>
							@endif
							@endif
				        </div>

						<div class="uk-width-1-2@m uk-child-width-1-1@s ">
							<h4>Upload new documents</h4>
							<div class="communication-selector" style="height: 150px;">
								<ul class="uk-subnav document-category-menu">
			                        @foreach ($document_categories as $category)
			                        <li>
			                            <input name="category-id-checkbox" id="category-id-{{ $category->id }}" value="{{ $category->id }}" type="checkbox" class="uk-checkbox">
			                            <label for="category-id-{{ $category->id }}">
			                                {{ $category->document_category_name }}
			                            </label>
			                        </li>
			                        @endforeach
			                    </ul>
							</div>
							<div class="uk-form-row" id="list-item-upload-box">
			                    <div class="js-upload uk-placeholder uk-text-center">
			                        <span class="a-higher"></span>
			                        <span class="uk-text-middle"> Please upload your document by dropping it here or</span>
			                        <div uk-form-custom>
			                            <input type="file" multiple>
			                            <span class="uk-link">by browsing and selecting it here.</span>
			                        </div>
			                    </div>

			                    <progress id="js-progressbar" class="uk-progress" value="0" max="100" hidden></progress>

			                    <script>
			                    $(function(){
			                        var bar = document.getElementById('js-progressbar');

			                        settings    = {

			                            url: '{{ URL::route("documents.upload", $parcel->id) }}',
			                            multiple: true,
			                            allow : '*.(jpg|gif|png|pdf|doc|docx|xls|xlsx)',

			                            headers : {
			                                'enctype' : 'multipart/form-data'
			                            },

			                            beforeSend: function () {
			                                // console.log('beforeSend', arguments);
			                            },
			                            beforeAll: function (settings) {
			                                // console.log('beforeAll', arguments);
			                                var categoryArray = [];
		                                    $("input:checkbox[name=category-id-checkbox]:checked").each(function(){
		                                            categoryArray.push($(this).val());
		                                        });
		                                    settings.params.categories = categoryArray;
		                                    settings.params._token = '{{ csrf_token() }}';
		                                    categories = categoryArray;
		                                    if(categoryArray.length > 0){
		                                        console.log('Categories selected: '+categoryArray);
		                                    }else{
		                                        UIkit.modal.alert('You must select at least one category.',{stack: true});
		                                        return false;
		                                    }

			                            },
			                            load: function () {
			                                // console.log('load', arguments);
			                            },
			                            error: function () {
			                                // console.log('error', arguments);
			                            },
			                            complete: function () {
			                                // console.log('complete', arguments);
			                            },

			                            loadStart: function (e) {
			                                // console.log('loadStart', arguments);

			                                bar.removeAttribute('hidden');
			                                bar.max = e.total;
			                                bar.value = e.loaded;
			                            },

			                            progress: function (e) {
			                                // console.log('progress', arguments);

			                                bar.max = e.total;
			                                bar.value = e.loaded;
			                            },

			                            loadEnd: function (e) {
			                                // console.log('loadEnd', arguments);

			                                bar.max = e.total;
			                                bar.value = e.loaded;
			                            },

			                            completeAll: function (response) {

			                                var data = jQuery.parseJSON(response.response);
                                        	var documentids = data['document_ids'];

		                                    setTimeout(function () {
			                                    bar.setAttribute('hidden', 'hidden');
			                                }, 250);

		                                    // Submit form and make sure it responds back with 1 - otherwise it will output the response to a browser alert box.
		                                    UIkit.modal.prompt("I uploaded and categorized the document(s) accordingly. Please add your comment for the history record.<br /><br />Let's work on the request form next!",'',{stack: true}).then(function(val){
		                                        $.post('{{ URL::route("documents.uploadComment", $parcel->id) }}', {
		                                                'postvars' : documentids,
		                                                'comment' : val,
		                                                '_token' : '{{ csrf_token() }}'
		                                                }, function(data) {
		                                                    if(data!='1'){
		                                                        UIkit.modal.alert(data,{stack: true});
		                                                    } else {
		                                                        UIkit.modal.alert('Your comment has been saved.',{stack: true});
		                                                    }
		                                        });
		                                    });

			                            }

			                        };

			                        var select = UIkit.upload('.js-upload', settings);

			                    });
			                    </script>

			            	</div>
				        </div>

					</div>
				</div>
			</form>

		</div>
	</div>
	@endif

</div>
<script type="text/javascript">

	function step(id){
		$(".dispo-step").addClass("uk-hidden");
		$(".button-step").removeClass("uk-button-primary");
		$("#"+id).removeClass("uk-hidden");
		$("#"+id+"-button").addClass("uk-button-primary");
	}
	$("#disposition-form-form").submit(function(e) {
	    e.preventDefault();
	});

	function processStep(id){
		// gather field data
		switch (id) {
		    case "disposition-start":
		        var form = $('#disposition-start-form');
		        break;
		    case "disposition-upload":
		        var form = $('#disposition-upload-form');
		        break;
		    case "disposition-form":
		        var form = $('#disposition-form-form');
		        break;
		    default:
		}
		// post to processor
		$.post('{{ URL::route("disposition.processStep", [$parcel]) }}', {
			'inputs' : form.serialize(),
			'step' : id,
			'_token' : '{{ csrf_token() }}'
		}, function(data) {
			if(!data['next']){
				UIkit.modal.alert(data['message']);
			}else{
				$('#disposition-tab-content').load('/dispositions/{{$parcel->id}}/all/tab');
				step(data['next']);
				UIkit.modal.alert(data['message']);
				if(data['next'] == "disposition-open"){
					var dispid = data['disposition'];
					window.open('/dispositions/{{$parcel->id}}/'+dispid, '_blank');
				}
			}
		} );
	}

</script>
