@extends('layouts.simplerAllita')
@section('head')
<title>DISPOSITION {{$disposition->id ?? ''}}</title>
@stop
@section('content')
<?php setlocale(LC_MONETARY, 'en_US');?>
<script>window.notSigned = 0</script>
<!-- <script src="/js/components/upload.js{{ asset_version() }}"></script>
<script src="/js/components/tooltip.js{{ asset_version() }}"></script> -->
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
.uk-table-strong-footer tfoot{
	font-style: inherit;
	font-size: inherit;
	font-weight: bold;
}
</style>
<div uk-grid>

	@if(!$proceed || !$disposition)
	<div class="uk-width-1-1">
		<div class="uk-alert uk-alert-warning uk-width-1-2 uk-container-center">Disposition is not available at this time.</div>
	</div>
	@else

	<div id="disposition-form" class="uk-width-1-1 dispo-step">


		<div id="releaseformpanel">
			<form id="disposition-form-form">
				<div class="uk-panel uk-panel-box uk-panel-box-white">
					<div class="uk-panel uk-panel-header">
						<div class="uk-panel-badge"><img src="/images/ni-program.jpg" alt="NIP Logo" style="height:70px" /></div>
						<h6 class="uk-panel-title uk-text-center uk-text-left-small" style="padding-bottom: 15px;"><span class="blue uk-text-bold	">OHIO HOUSING FINANCE AGENCY</span><br /><span class="green">EARLY LIEN RELEASE REQUEST FORM</span><br /></h6>
					</div>
					<div class="uk-panel no-print">
						<div uk-grid>
							<div class="uk-panel uk-width-1-2@m uk-width-1-1@s uk-container-center uk-margin-top uk-text-center">
								<div class="uk-alert">
									This disposition's status is "{{$step}}".
									@if($disposition->date_release_requested !== null && $disposition->release_date == null)
									Release was requested on {{ Carbon\Carbon::parse($disposition->date_release_requested)->format('m/d/Y') }}.
									@elseif($disposition->date_release_requested !== null && $disposition->release_date !== null)
									Released on {{ Carbon\Carbon::parse($disposition->release_date)->format('m/d/Y') }}.
									@endif
								</div>
							</div>
						</div>
					</div>

					<div class="uk-panel uk-panel-divider no-padding-bottom">
						<div class="guidesteps uk-grid-small" uk-height-match uk-grid >
							<div class="uk-width-1-1 uk-width-1-6@l uk-margin-bottom">
								<div class="uk-panel active">
								    <h3 class="uk-panel-title uk-text-center">STEP 1 - (LANDBANK)</h3>
								    <p class="uk-text-center uk-text-primary uk-text-bold">Complete Disposition Form & SUBMIT IT</p>
								    <ul class="uk-list uk-list-space">
									    <li uk-tooltip="{{$guide_help[2]}}"><span class="@if(guide_check_step(2, $disposition->id) || guide_check_step(1, $disposition->id)) a-checkbox-checked @else a-checkbox @endif"></span> @if(guide_check_step(2, $disposition->id) || guide_check_step(1, $disposition->id)){{$guide_name[2]['name_completed']}} @else {{$guide_name[2]['name']}}@endif</li>
									    <li><span  uk-tooltip="{{$guide_help[3]}}"><span class="@if(guide_check_step(3, $disposition->id) || guide_check_step(1, $disposition->id)) a-checkbox-checked @else a-checkbox @endif" ></span> @if(guide_check_step(3, $disposition->id) || guide_check_step(1, $disposition->id)){{$guide_name[3]['name_completed']}} @else {{$guide_name[3]['name']}}@endif</span> <a onclick="window.open('/viewparcel/{{$disposition->parcel_id}}', '_blank')" class="uk-link-muted" uk-tooltip="OPEN PARCEL IN NEW WINDOW"><span class="a-upload uk-text-muted"></span></a></li>
									    <li uk-tooltip="{{$guide_help[4]}}"><span class="@if(guide_check_step(4, $disposition->id) || guide_check_step(1, $disposition->id)) a-checkbox-checked @else a-checkbox @endif" ></span> @if(guide_check_step(4, $disposition->id) || guide_check_step(1, $disposition->id)){{$guide_name[4]['name_completed']}} @else {{$guide_name[4]['name']}}@endif</li>
									    <li uk-tooltip="{{$guide_help[5]}}"><span class="@if(guide_check_step(5, $disposition->id) || guide_check_step(1, $disposition->id)) a-checkbox-checked @else a-checkbox @endif" ></span> @if(guide_check_step(5, $disposition->id) || guide_check_step(1, $disposition->id)){{$guide_name[5]['name_completed']}} @else {{$guide_name[5]['name']}}@endif</li>
									</ul>
								</div>
							</div>
							<div class="uk-width-1-1 uk-width-1-6@l uk-margin-bottom">
								<div class="uk-panel @if(guide_check_step(1, $disposition->id)) active @endif">
								    <h3 class="uk-panel-title uk-text-center">STEP 2 - (HFA)</h3>
								    <p class="uk-text-center uk-text-primary uk-text-bold">Review & Approve Disposition Request & Add to Quartely Disposition Invoice</p>
								    <ul class="uk-list uk-list-space">
									    <li @if($current_user->isFromEntity(1))uk-tooltip="{{$guide_help[7]}}"@endif><span class="@if(guide_check_step(7, $disposition->id) || guide_check_step(6, $disposition->id)) a-checkbox-checked @else a-checkbox @endif"></span> @if(guide_check_step(7, $disposition->id) || guide_check_step(6, $disposition->id)){{$guide_name[7]['name_completed']}} @else {{$guide_name[7]['name']}}@endif</li>
									    <li @if($current_user->isFromEntity(1))uk-tooltip="{{$guide_help[8]}}"@endif><span class="@if(guide_check_step(8, $disposition->id)) a-checkbox-checked @else a-checkbox @endif"></span> @if(guide_check_step(8, $disposition->id)){{$guide_name[8]['name_completed']}} @else {{$guide_name[8]['name']}}@endif</li>
									    <li @if($current_user->isFromEntity(1))uk-tooltip="{{$guide_help[9]}}"@endif><span class="@if(guide_check_step(9, $disposition->id) || guide_check_step(6, $disposition->id)) a-checkbox-checked @else a-checkbox @endif"></span> @if(guide_check_step(9, $disposition->id) || guide_check_step(6, $disposition->id)){{$guide_name[9]['name_completed']}} @else {{$guide_name[9]['name']}}@endif</li>
									    <li @if($current_user->isFromEntity(1))uk-tooltip="{{$guide_help[10]}}"@endif><span class="@if(guide_check_step(10, $disposition->id) || guide_check_step(6, $disposition->id)) a-checkbox-checked @else a-checkbox @endif"></span> @if(guide_check_step(10, $disposition->id) || guide_check_step(6, $disposition->id)){{$guide_name[10]['name_completed']}} @else {{$guide_name[10]['name']}}@endif</li>
									    <li @if($current_user->isFromEntity(1))uk-tooltip="{{$guide_help[11]}}"@endif><span class="@if(guide_check_step(11, $disposition->id) || guide_check_step(6, $disposition->id)) a-checkbox-checked @else a-checkbox @endif"></span> @if(guide_check_step(11, $disposition->id) || guide_check_step(6, $disposition->id)){{$guide_name[11]['name_completed']}} @else {{$guide_name[11]['name']}}@endif</li>
									    <li @if($current_user->isFromEntity(1))uk-tooltip="{{$guide_help[12]}}"@endif><span class="@if(guide_check_step(12, $disposition->id) || guide_check_step(6, $disposition->id)) a-checkbox-checked @else a-checkbox @endif"></span> @if(guide_check_step(12, $disposition->id) || guide_check_step(6, $disposition->id)){{$guide_name[12]['name_completed']}} @else {{$guide_name[12]['name']}}@endif</li>
									</ul>
								</div>
							</div>
							<div class="uk-width-1-1 uk-width-2-3@l " uk-grid>

									<div class="uk-width-1-1 uk-width-1-3@l uk-margin-bottom">
										<div class="uk-panel @if(guide_check_step(1, $disposition->id) && guide_check_step(6, $disposition->id)) active @endif">
										    <h3 class="uk-panel-title uk-text-center">STEP 3 - (HFA)</h3>
										    <p class="uk-text-center uk-text-primary uk-text-bold">Review and Submit Invoice for Approval</p>
										    <ul class="uk-list uk-list-space">
											    <li @if($current_user->isFromEntity(1))uk-tooltip="{{$guide_help[56]}}"@endif><span class="@if(guide_check_step(56, $disposition->id) || guide_check_step(13, $disposition->id)) a-checkbox-checked @else a-checkbox @endif"></span> @if(guide_check_step(56, $disposition->id) || guide_check_step(13, $disposition->id)){{$guide_name[56]['name_completed']}} @else {{$guide_name[56]['name']}}@endif @if($disposition->invoice)<a onclick="window.open('/disposition_invoice/{{$disposition->invoice->disposition_invoice_id}}', '_blank')" class="uk-link-muted" uk-tooltip="OPEN DISPOSITION INVOICE IN NEW WINDOW"><span class="a-upload uk-text-muted"></span></a>@endif</li>
											</ul>
										</div>
									</div>
									<div class="uk-width-1-1 uk-width-2-3@l uk-margin-bottom">
										<div class="uk-panel @if(guide_check_step(1, $disposition->id) && guide_check_step(6, $disposition->id)) active @endif">
										    <h3 class="uk-panel-title uk-text-center">ANY POINT AFTER STEP 3 - (HFA OR FISCAL AGENT)</h3>
										    <p class="uk-text-center uk-text-primary uk-text-bold doubleheight">Finalize Documentation, Review Release Request, Release Lien</p>
										    <ul class="uk-list uk-list-space uk-text-center">
										    	<li @if($current_user->isFromEntity(1))uk-tooltip="{{$guide_help[14]}}"@endif><span class="@if(guide_check_step(14, $disposition->id) || guide_check_step(13, $disposition->id)) a-checkbox-checked @else a-checkbox @endif"></span> @if(guide_check_step(14, $disposition->id) || guide_check_step(13, $disposition->id)){{$guide_name[14]['name_completed']}} @else {{$guide_name[14]['name']}}@endif</li>
											    <li><span @if($current_user->isFromEntity(1))uk-tooltip="{{$guide_help[17]}}"@endif><span class="@if(guide_check_step(17, $disposition->id)) a-checkbox-checked @else a-checkbox @endif"></span> @if(guide_check_step(17, $disposition->id)){{$guide_name[17]['name_completed']}} @else {{$guide_name[17]['name']}}@endif </span> <a onclick="window.open('/viewparcel/{{$disposition->parcel_id}}', '_blank')" class="uk-link-muted" uk-tooltip="OPEN PARCEL IN NEW WINDOW"><span class="a-upload uk-text-muted"></i></a></li>
											</ul>
										</div>
									</div>



									<div class="uk-width-1-1 uk-width-1-3@l uk-margin-bottom uk-first-column" >

											<div class="uk-width-1-1 uk-width-1-1@l">
												<div class="uk-panel  @if(guide_check_step(1, $disposition->id) && guide_check_step(6, $disposition->id)  && guide_check_step(13, $disposition->id)) active @endif">
												    <h3 class="uk-panel-title uk-text-center">STEP 3 - (LANDBANK)</h3>
												    <p class="uk-text-center uk-text-primary uk-text-bold doubleheight">Sell/Dispose Parcel</p>
												    <ul class="uk-list uk-list-space">
													    <li><span uk-tooltip="{{$guide_help[16]}}"><span class="@if(guide_check_step(16, $disposition->id) || guide_check_step(13, $disposition->id)) a-checkbox-checked @else a-checkbox @endif" ></span> @if(guide_check_step(16, $disposition->id) || guide_check_step(13, $disposition->id)){{$guide_name[16]['name_completed']}} @else {{$guide_name[16]['name']}}@endif  </span><a onclick="window.open('/viewparcel/{{$disposition->parcel_id}}', '_blank')" class="uk-link-muted" uk-tooltip="OPEN PARCEL IN NEW WINDOW"><span class="a-upload uk-text-muted"></span></a></li>
													</ul>
												</div>
											</div>

									</div>
									<div class="uk-width-1-1 uk-width-1-3@l uk-margin-bottom uk-first-column" >

											<div class="uk-width-1-1 uk-width-1-1@l">
												<div class="uk-panel @if(guide_check_step(1, $disposition->id) && guide_check_step(6, $disposition->id) && guide_check_step(18, $disposition->id)) active @endif">
												    <h3 class="uk-panel-title uk-text-center">STEP 4 - (HFA)</h3>
												    <p class="uk-text-center uk-text-primary uk-text-bold doubleheight">Approve Disposition Invoice, Send to Landbank</p>
												    <ul class="uk-list uk-list-space">
													    <li><span @if($current_user->isFromEntity(1))uk-tooltip="HFA approvers {{$invoice_hfa_approvers_list}} must approve the disposition invoice."@endif><span class="@if(guide_check_step(19, $disposition->id) || guide_check_step(18, $disposition->id)) a-checkbox-checked @else a-checkbox @endif"></span> @if(guide_check_step(19, $disposition->id) || guide_check_step(18, $disposition->id)){{$guide_name[19]['name_completed']}} @else {{$guide_name[19]['name']}}@endif</span> @if($disposition->invoice)<a onclick="window.open('/disposition_invoice/{{$disposition->invoice->disposition_invoice_id}}', '_blank')" class="uk-link-muted" uk-tooltip="OPEN DISPOSITION INVOICE IN NEW WINDOW"><span class="a-upload uk-text-muted"></span></a>@endif</li>
													    <li @if($current_user->isFromEntity(1))uk-tooltip="{{$guide_help[20]}}"@endif><span class="@if(guide_check_step(20, $disposition->id) || guide_check_step(18, $disposition->id)) a-checkbox-checked @else a-checkbox @endif"></span> @if(guide_check_step(20, $disposition->id) || guide_check_step(18, $disposition->id)){{$guide_name[20]['name_completed']}} @else {{$guide_name[20]['name']}}@endif</li>
													</ul>
												</div>
											</div>

									</div>

									<div class="uk-width-1-1 uk-width-1-3@l uk-margin-bottom" >

											<div class="uk-width-1-1 uk-width-1-1@l">
												<div class="uk-panel @if(guide_check_step(1, $disposition->id) && guide_check_step(6, $disposition->id) && guide_check_step(13, $disposition->id) && guide_check_step(18, $disposition->id)) active @endif">
												    <h3 class="uk-panel-title uk-text-center">STEP 5 - (HFA)</h3>
												    <p class="uk-text-center uk-text-primary uk-text-bold doubleheight">Finalize Disposition</p>
												    <ul class="uk-list uk-list-space">
													    <li @if($current_user->isFromEntity(1))uk-tooltip="{{$guide_help[22]}}"@endif><span class="@if(guide_check_step(21, $disposition->id) || guide_check_step(22, $disposition->id)) a-checkbox-checked @else a-checkbox @endif"></span> @if(guide_check_step(21, $disposition->id) || guide_check_step(22, $disposition->id)){{$guide_name[22]['name_completed']}} @else {{$guide_name[22]['name']}}@endif</li>
													</ul>
												</div>
											</div>

									</div>

							</div>
						</div>
					</div>

					<div class="uk-panel uk-panel-divider no-padding-bottom">
						<div uk-grid>
							<div class="uk-width-1-1 uk-width-1-1@l">
								<p>Instructions: NIP Partners may complete this form to release the HHF lien prior to the three-year term in accordance with the NIP guidelines. Partners must attach any supporting documents identified in Section 8(D) of the Guidelines. The decision of whether to grant a release is within OHFA’s discretion. You may wish to contact the NIP Program Manager before completing this form for guidance on eligible dispositions and required attachments.</p>
							</div>
						</div>
					</div>

					@if($disposition->parcel->unpaidRetainages->count())
					<div class="uk-panel no-padding-bottom uk-panel-divider">
						<div uk-grid>
							<div class="uk-panel uk-width-1-2@m uk-width-1-1@s uk-container-center uk-text-center">
								<div class="uk-alert uk-alert-danger">This disposition has unpaid retainages!</div>
							</div>
						</div>
					</div>
					@endif

					<div class="uk-panel uk-panel-divider">
						<div uk-grid>
							<div class="uk-width-1-1 uk-width-1-1@l uk-margin-bottom no-print">
								<h3 class="uk-panel-title">Supporting Documents</h3>
								<p class="no-print">You can upload copies of all your supporting documents in a single file here. It will be added to your parcel's documents under the supporting documents tab.</p>
							</div>

							<div class="uk-width-1-1@s uk-width-1-1@m no-print">
								<div class="uk-display-inline no-print" id="doc-upload-box">
				                    <div id="upload-drop-disposition" class="js-upload uk-placeholder uk-text-center">
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

				                            url: '{{ URL::route("disposition.uploadSupportingDocuments", $parcel->id) }}',
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
				                                settings.params._token = '{{ csrf_token() }}';
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
				                                var documentids = response.response;

			                                    setTimeout(function () {
				                                    bar.setAttribute('hidden', 'hidden');
				                                }, 250);

			                                    // Submit form and make sure it responds back with 1 - otherwise it will output the response to a browser alert box.
			                                    UIkit.modal.prompt("I uploaded and categorized the document(s) accordingly. Please add your comment for the history record.",'',{
	                                                stack: true}).then(function(val){
			                                        $.post('{{ URL::route("disposition.uploadSupportingDocumentsComments", $parcel->id) }}', {
			                                                'postvars' : documentids,
			                                                'comment' : val,
			                                                '_token' : '{{ csrf_token() }}'
			                                                }, function(data) {
			                                                    if(data!='1'){
			                                                        UIkit.modal.alert(data,{stack: true});
			                                                    } else {
			                                                        UIkit.modal.alert('Your comment has been saved.',{stack: true});
			                                                        $.post('{{ URL::route("disposition.getUploadedDocuments", $parcel->id) }}', {
							                                                '_token' : '{{ csrf_token() }}'
							                                                }, function(data) {
							                                                    if(data['message']!=''){
							                                                    	$('#sent-document-list').empty();
							                                                    	var index;
							                                                    	for (index = 0; index < data.length; ++index) {
																					    $('#sent-document-list').append('<tr><td>'+data[index]['filedate']+'</td><td>'+data[index]['filename']+'</td><td><a class="uk-link-muted " uk-tooltip="'+data[index]['comment']+'"><span class="a-info-circle"></span></a>&nbsp;&nbsp;|&nbsp;&nbsp;<a href="'+data[index]['filelink']+'" target="_blank" class="uk-link-muted "  uk-tooltip="Download file"><span class="a-lower"></span></a>&nbsp;&nbsp;|&nbsp;&nbsp;<a class="uk-link-muted " onclick="deleteFile('+data[index]['id']+');" uk-tooltip="Delete this file"><span class="a-trash-4"></span></a></td></tr>');
																					}
																				}else{
																					UIkit.modal.alert('Something went wrong.',{stack: true});
																				}
							                                        });
			                                                    }
			                                        });
			                                    });
				                            }

				                        };

				                        var select = UIkit.upload('.js-upload', settings);

				                    });
				                    </script>

				                    <!-- <script>
				                        $(function(){
				                            var categories = [];
				                            var progressbar = $("#progressbar-disposition"),
				                                bar         = progressbar.find('.uk-progress-bar-disposition'),
				                                settings    = {
				                                before: function (settings) {},
				                                single: true,
				                                filelimit: 1,
				                                multiple: false,
				                                action: '{{ URL::route("disposition.uploadSupportingDocuments", $parcel->id) }}',
				                                allow : '*.(jpg|gif|png|pdf|doc|docx|xls|xlsx)',
				                                params : {
				                                    '_token' : '{{ csrf_token() }}'
				                                },
				                                headers : {
				                                    'enctype' : 'multipart/form-data'
				                                },
				                                loadstart: function() {
				                                    bar.css("width", "0%").text("0%");
				                                    progressbar.removeClass("uk-hidden");
				                                },
				                                progress: function(percent) {
				                                    percent = Math.ceil(percent);
				                                    bar.css("width", percent+"%").text(percent+"%");
				                                },
				                                allcomplete: function(response) {
				                                    var documentids = response;
				                                    bar.css("width", "100%").text("100%");
				                                    setTimeout(function(){
				                                        progressbar.addClass("uk-hidden");
				                                    }, 250);
				                                    // Submit form and make sure it responds back with 1 - otherwise it will output the response to a browser alert box.
				                                    UIkit.modal.prompt("I uploaded and categorized the document(s) accordingly. Please add your comment for the history record.",'',function(val){
				                                        $.post('{{ URL::route("disposition.uploadSupportingDocumentsComments", $parcel->id) }}', {
				                                                'postvars' : documentids,
				                                                'comment' : val,
				                                                '_token' : '{{ csrf_token() }}'
				                                                }, function(data) {
				                                                    if(data!='1'){
				                                                        UIkit.modal.alert(data);
				                                                    } else {
				                                                        UIkit.modal.alert('Your comment has been saved.');
				                                                        $.post('{{ URL::route("disposition.getUploadedDocuments", $parcel->id) }}', {
								                                                '_token' : '{{ csrf_token() }}'
								                                                }, function(data) {
								                                                    if(data['message']!=''){
								                                                    	$('#sent-document-list').empty();
								                                                    	var index;
								                                                    	for (index = 0; index < data.length; ++index) {
																						    $('#sent-document-list').append('<tr><td>'+data[index]['filedate']+'</td><td>'+data[index]['filename']+'</td><td><a class="uk-link-muted " uk-tooltip="'+data[index]['comment']+'"><span class="a-info-circle"></span></a>&nbsp;&nbsp;|&nbsp;&nbsp;<a href="'+data[index]['filelink']+'" target="_blank" class="uk-link-muted "  uk-tooltip="Download file"><span class="a-lower"></span></a>&nbsp;&nbsp;|&nbsp;&nbsp;<a class="uk-link-muted " onclick="deleteFile('+data[index]['id']+');" uk-tooltip="Delete this file"><span class="a-trash-4"></span></a></td></tr>');
																						}
																					}else{
																						UIkit.modal.alert('Something went wrong.');
																					}
								                                        });
				                                                    }
				                                        });
				                                    });
				                                }
				                            };
				                            var select = UIkit.uploadSelect($("#upload-select-disposition"), settings),
				                                drop   = UIkit.uploadDrop($("#upload-drop-disposition"), settings);
				                        }); // end function
				                    </script>  -->
				            	</div>
							</div>

							<div class="uk-width-1-1@s uk-width-1-1@m">
								<table class="uk-table uk-table-striped uk-table-condensed uk-table-hover gray-link-table" id="">
							        <tbody id="sent-document-list">
							        	@if(count($supporting_documents))
							        	@foreach($supporting_documents as $supporting_document)
							            <tr>
								            <td>{{ date('m/d/Y',strtotime($supporting_document['date'])) }}</td>
								            <td>{{ $supporting_document['category'] }}
								            </td>
								            <td>
								            	@if($supporting_document['approved'])
								            	<span class="a-circle-checked" uk-tooltip="Approved"></span>
								            	@elseif($supporting_document['notapproved'])
								            	<span class="a-circle-cross" uk-tooltip="Not approved"></span>
								            	@else
								            	<span class="a-circle" uk-tooltip="Received - not yet reviewed"></span>
								            	@endif

								            	{{ $supporting_document['filename'] }}
								            </td>
								            <td>
								            	<a class="uk-link-muted " uk-tooltip="{{ $supporting_document['comment'] }}">
								                    <span class="a-info-circle"></span>
								                </a>
								                &nbsp;&nbsp;|&nbsp;&nbsp;
								             	<a href="{{ URL::route('documents.downloadDocument', [$parcel->id, $supporting_document['id']]) }}" target="_blank"  class="uk-link-muted "  uk-tooltip="Download file">
								                    <span class="a-lower"></span>
								                </a>
								                &nbsp;&nbsp;|&nbsp;&nbsp;
								                <a class="uk-link-muted " onclick="deleteFile({{ $supporting_document['id'] }});" uk-tooltip="Delete this file">
								                    <span class="a-trash-4"></span>
								                </a>

								            </td>
							        	</tr>
							        	@endforeach
							        	@endif
							    	</tbody>
								</table>
							</div>

						</div>
					</div>

					@if(!$legacy)
					<div class="uk-panel uk-panel-divider">
						<div uk-grid>
							@if($current_user->isFromEntity(1) || ($isApproved_hfa))
							<div id="pl" class="uk-width-2-5@m uk-width-2-5 uk-width-1-1@s">
							@else
							<div class="uk-width-1-1@m uk-width-1-1@s">
							@endif
								<dl class="uk-description-list-horizontal uk-form">
		                            <dt>Partner</dt>
		                            <dd>{{$entity->entity_name}}</dd>
		                            <dt>Full Property Address</dt>
		                            <dd>@if($parcel->street_address) {{$parcel->street_address}} <br />@endif
											{{$parcel->city}} @if($parcel->state->state_acronym) {{$parcel->state->state_acronym}}@endif @if($parcel->zip) {{$parcel->zip}} @endif</dd>
		                        </dl>
		                        <dl class="uk-description-list-horizontal uk-form">
		                            <dt style="margin-top: 5px;">Program Income</dt>
		                            <dd><input type="text"  name="income" value="{{$calculation['income']}}"  class="uk-input"/></dd>
		                            @if((Auth::user()->isHFADispositionApprover()) || Auth::user()->isHFAAdmin())
		                            <dt style="margin-top: 15px;" >Imputed Cost/Parcel</dt>
		                            <dd><input type="text" name="transaction_cost" value="{{$calculation['transaction_cost']}}" style="margin-top: 10px;" class="uk-input"/> <span class="uk-text-small uk-text-muted" style="vertical-align: -webkit-baseline-middle;">min {{$calculation['rule_min_cost_formatted']}}</span></dd>
		                            @endif
		                            <dt style="margin-top: 15px;" >Permanent Parcel #</dt>
		                            <dd><input type="text" name="permanentparcelid" @if($disposition)@if($disposition->permanent_parcel_id != '') value="{{$disposition->permanent_parcel_id}}" @else value="{{$parcel->parcel_id}}"@endif @endif style="margin-top: 10px;" class="uk-input" />
		                            	<span class="uk-text-small uk-text-muted" style="vertical-align: -webkit-baseline-middle;">
		                            		<span class="a-info-circle" uk-tooltip="ORIGINAL PARCEL ID {{$parcel->parcel_id}}"></span>&nbsp;
		                            		<a onclick="window.open('/viewparcel/{{$disposition->parcel_id}}', '_blank')" class="uk-link-muted" uk-tooltip="OPEN PARCEL IN NEW WINDOW"><span class="a-upload uk-text-muted"></span></a>
		                            	</span>
		                            </dd>
		                        </dl>
		                        <hr class="dashed-hr"/>

								<div class="uk-grid uk-form uk-margin-top">
									<div class="uk-width-1-1@m uk-width-1-1@s">
										<p>Please Paste in the Text of the Parcel's Legal Description. <br />
										<input type="checkbox" name="legal_description_in_documents" value="1" id="legal_description_in_documents" class="uk-checkbox" @if($disposition) @if($disposition->legal_description_in_documents) checked @endif @endif> <small>PARCEL LEGAL DESCRIPTION INCLUDED IN SUPPORTING DOCUMENTS</small></p>
										<div class="uk-form-controls">
											<textarea id="full_description" class="uk-textarea" name="full_description" style="width:100%; @if($disposition) @if($disposition->legal_description_in_documents) display:none; @endif @endif >" rows="6">@if($disposition) {{$disposition->full_description}} @endif</textarea>
			                            </div>
									</div>
								</div>
							</div>
							@if($current_user->isFromEntity(1) || ($isApproved_hfa))
							<div  id="pr" class="uk-width-3-5@m uk-width-1-1@s">
								<div class="uk-panel uk-panel-box">
									<h3 class="uk-panel-title">Calculations</h3>
									@if($disposition)
									<table class="uk-table uk-table-strong-footer">
										<thead>
											<th>Line Item</th>
											@if($current_user->isFromEntity(1))
											<th class="uk-text-right">Estimated</th>
											<th class="uk-text-right">Adjustments</th>
											@endif
											<th class="uk-text-right">Actual</th>
										</thead>
										<tbody>
											<tr>
												<td>Income</td>
												@if($current_user->isFromEntity(1))
												<td class="uk-text-right">{{$calculation['income_formatted']}}</td>
												<td class="uk-text-right">
													@if(Gate::check('hfa-review-disposition') || Gate::check('hfa-sign-disposition') || Gate::check('hfa-release-disposition'))
													@if($disposition->hfa_calc_income === null && $calculation['income'] != null)
														$<input type="text" name="hfa_calc_income" value="{{$calculation['income']}}" id ="hfa_calc_income"  class="uk-input uk-form-small "/>
													@elseif($disposition->hfa_calc_income === null && $calculation['income'] == null)
														$<input type="text" name="hfa_calc_income" value="" id ="hfa_calc_income"  class="uk-input uk-form-small "/>
													@else
														$<input type="text" name="hfa_calc_income" value="{{$disposition->hfa_calc_income}}" id ="hfa_calc_income"  class="uk-input uk-form-small "/>
													@endif
													@else
													{{$disposition->hfa_calc_income}}
													@endif</td>
												@endif
												<td class="uk-text-right">@if($disposition->hfa_calc_income){{$actual['income_formatted']}}@else {{$calculation['income_formatted']}} @endif</td>
											</tr>
											<tr>
												<td><span uk-tooltip="Minimum {{$calculation['rule_min_cost_formatted']}}">Imputed Cost Per Parcel  <span class="a-info-circle uk-text-muted"></span></span></td>
												@if($current_user->isFromEntity(1))
												<td class="uk-text-right">{{$calculation['transaction_cost_formatted']}}</td>
												<td class="uk-text-right">
													@if(Gate::check('hfa-review-disposition') || Gate::check('hfa-sign-disposition') || Gate::check('hfa-release-disposition'))

													@if($disposition->hfa_calc_trans_cost === null && $calculation['transaction_cost'] != null)
														$<input type="text" name="hfa_calc_trans_cost" value="{{$calculation['transaction_cost']}}" id ="hfa_calc_trans_cost"  class="uk-input uk-form-small "/>
													@elseif($disposition->hfa_calc_trans_cost === null && $calculation['transaction_cost'] == null)
														$<input type="text" name="hfa_calc_trans_cost" value="" id ="hfa_calc_trans_cost"  class="uk-input uk-form-small "/>
													@else
														$<input type="text" name="hfa_calc_trans_cost" value="{{$disposition->hfa_calc_trans_cost}}" id ="hfa_calc_trans_cost"  class="uk-input uk-form-small "/>
													@endif

													@else
													{{$disposition->hfa_calc_trans_cost}}
													@endif</td>
												@endif
												<td class="uk-text-right">@if($disposition->hfa_calc_trans_cost){{$actual['transaction_cost_formatted']}}@else {{$calculation['transaction_cost_formatted']}} @endif</td>
											</tr>
											<tr>
												<td><span uk-tooltip="Total maintenance advance">Maintenance Total  <span class="a-info-circle uk-text-muted"></span></span></td>
												@if($current_user->isFromEntity(1))
												<td class="uk-text-right">{{$calculation['maintenance_total_formatted']}}</td>
												<td class="uk-text-right">
													@if(Gate::check('hfa-review-disposition') || Gate::check('hfa-sign-disposition') || Gate::check('hfa-release-disposition'))
													@if($disposition->hfa_calc_maintenance_total === null && $calculation['maintenance_total'] != null)
														$<input type="text" name="hfa_calc_maintenance_total" value="{{$calculation['maintenance_total']}}" id ="hfa_calc_maintenance_total"  class="uk-input uk-form-small "/>
													@elseif($disposition->hfa_calc_maintenance_total === null && $calculation['maintenance_total'] == null)
														$<input type="text" name="hfa_calc_maintenance_total" value="" id ="hfa_calc_maintenance_total"  class="uk-input uk-form-small "/>
													@else
														$<input type="text" name="hfa_calc_maintenance_total" value="{{$disposition->hfa_calc_maintenance_total}}" id ="hfa_calc_maintenance_total"  class="uk-input uk-form-small "/>
													@endif

													@else
													{{$disposition->hfa_calc_maintenance_total}}
													@endif</td>
												@endif
												<td class="uk-text-right">@if($disposition->hfa_calc_maintenance_total){{$actual['maintenance_total_formatted']}} @else {{$calculation['maintenance_total_formatted']}} @endif</td>
											</tr>

											<tr>
												<td>
													<span uk-tooltip="Number of months used to compute the monthly maintenance rate (default is 36)">Number of Months Prepaid <span class="a-info-circle uk-text-muted"></span></span><br />
												</td>
												@if($current_user->isFromEntity(1))
												<td class="uk-text-right">{{$calculation['months_prepaid']}}</td>
												<td class="uk-text-right">
													@if(Gate::check('hfa-review-disposition') || Gate::check('hfa-sign-disposition') || Gate::check('hfa-release-disposition'))

													@if($disposition->hfa_calc_months_prepaid === null && $calculation['months_prepaid'] != null)
														<input type="text" name="hfa_calc_months_prepaid" value="{{$calculation['months_prepaid']}}" id ="hfa_calc_months_prepaid"  class="uk-input uk-form-small "/>
													@elseif($disposition->hfa_calc_months_prepaid === null && $calculation['months_prepaid'] == null)
														<input type="text" name="hfa_calc_months_prepaid" value="" id ="hfa_calc_months_prepaid"  class="uk-input uk-form-small "/>
													@else
														<input type="text" name="hfa_calc_months_prepaid" value="{{$disposition->hfa_calc_months_prepaid}}" id ="hfa_calc_months_prepaid"  class="uk-input uk-form-small "/>
													@endif

													@else
													{{$disposition->hfa_calc_months_prepaid}}
													@endif</td>
												@endif
												<td class="uk-text-right">@if($disposition->hfa_calc_months_prepaid){{$actual['months_prepaid']}}@else {{$calculation['months_prepaid']}}@endif</td>
											</tr>
											<tr>
												<td>
													<span uk-tooltip="Monthly maintenance rate equals the total maintenance divided by the number of months prepaid">Monthly Maintenance Rate <span class="a-info-circle uk-text-muted"></span></span><br />
												</td>
												@if($current_user->isFromEntity(1))
												<td class="uk-text-right">{{$calculation['monthly_maintenance_rate']}}</td>
												<td class="uk-text-right">
													@if(Gate::check('hfa-review-disposition') || Gate::check('hfa-sign-disposition') || Gate::check('hfa-release-disposition'))

													@if($disposition->hfa_calc_monthly_rate === null && $calculation['monthly_maintenance'] != null)
														$<input type="text" name="hfa_calc_monthly_rate" value="{{$calculation['monthly_maintenance']}}" id ="hfa_calc_monthly_rate"  class="uk-input uk-form-small "/>
													@elseif($disposition->hfa_calc_monthly_rate === null && $calculation['monthly_maintenance_rate'] == null)
														$<input type="text" name="hfa_calc_monthly_rate" value="" id ="hfa_calc_monthly_rate"  class="uk-input uk-form-small "/>
													@else
														$<input type="text" name="hfa_calc_monthly_rate" value="{{$disposition->hfa_calc_monthly_rate}}" id ="hfa_calc_monthly_rate"  class="uk-input uk-form-small "/>
													@endif

													@else
													{{$disposition->hfa_calc_monthly_rate}}
													@endif</td>
												@endif
												<td class="uk-text-right">@if($disposition->hfa_calc_monthly_rate){{$actual['monthly_maintenance_rate']}}@else {{$calculation['monthly_maintenance_rate']}}@endif</td>
											</tr>
											<tr>
												<td><span uk-tooltip="Using disposition date ({{Carbon\Carbon::parse($disposition_date)->format('m/d/Y')}}) minus invoice payment clear date ({{Carbon\Carbon::parse($invoice_payment_date)->format('m/d/Y')}})">Months Maintained  <span class="a-info-circle uk-text-muted"></span></span></td>
												@if($current_user->isFromEntity(1))
												<td class="uk-text-right">
													@if($calculation['month_unused']){{$calculation['month_unused']}} @else <span class="a-warning"></span> Missing payment date<br />
													@if(Gate::check('hfa-review-disposition') || Gate::check('hfa-sign-disposition') || Gate::check('hfa-release-disposition'))
													    <input type="text" id="missing_date" name="missing_date" class="uk-input uk-form-small uk-form-width-small" style="width:80px;"/> <button onclick="save_missing_date();" class="uk-button uk-button-default uk-button-small flatpickr flatpickr-input active" data-id="dateformat" >Add</button>
													@endif
													@endif
												</td>
												<td class="uk-text-right">
													@if(Gate::check('hfa-review-disposition') || Gate::check('hfa-sign-disposition') || Gate::check('hfa-release-disposition'))

													@if($disposition->hfa_calc_months === null && $calculation['month_unused'] != null)
														<input type="text" name="hfa_calc_months" value="{{$calculation['month_unused']}}" id ="hfa_calc_months"  class="uk-input uk-form-small "/>
													@elseif($disposition->hfa_calc_months === null && $calculation['month_unused'] == null)
														<input type="text" name="hfa_calc_months" value="" id ="hfa_calc_months"  class="uk-input uk-form-small "/>
													@else
														<input type="text" name="hfa_calc_months" value="{{$disposition->hfa_calc_months}}" id ="hfa_calc_months"  class="uk-input uk-form-small "/>
													@endif

													@else
													{{$disposition->hfa_calc_months}}
													@endif</td>
												@endif
												<td class="uk-text-right">@if($disposition->hfa_calc_months){{$actual['month_unused']}}@else {{$calculation['month_unused']}}@endif</td>
											</tr>
											<tr>
												<td>
													<span uk-tooltip="Maintenance to repay equals the total maintenance minus the number of months multiplied by the maintenance rate">Maintenance To Repay <span class="a-info-circle info-circle uk-text-muted"></span></span><br />
												</td>
												@if($current_user->isFromEntity(1))
												<td class="uk-text-right">{{$calculation['maintenance_unused_formatted']}}</td>
												<td class="uk-text-right">
												@if(Gate::check('hfa-review-disposition') || Gate::check('hfa-sign-disposition') || Gate::check('hfa-release-disposition'))

													@if($disposition->hfa_calc_maintenance_due === null && $calculation['maintenance_unused'] != null)
														$<input type="text" name="hfa_calc_maintenance_due" value="{{$calculation['maintenance_unused']}}" id ="hfa_calc_maintenance_due"  class="uk-input uk-form-small "/>
													@elseif($disposition->hfa_calc_maintenance_due === null && $calculation['maintenance_unused'] == null)
														$<input type="text" name="hfa_calc_maintenance_due" value="" id ="hfa_calc_maintenance_due"  class="uk-input uk-form-small "/>
													@else
														$<input type="text" name="hfa_calc_maintenance_due" value="{{$disposition->hfa_calc_maintenance_due}}" id ="hfa_calc_maintenance_due"  class="uk-input uk-form-small "/>
													@endif

												@else
													{{$disposition->hfa_calc_maintenance_due}}
													@endif</td>
												@endif
												<td class="uk-text-right">@if($disposition->hfa_calc_maintenance_due){{$actual['maintenance_unused_formatted']}}@else {{$calculation['maintenance_unused_formatted']}}@endif</td>
											</tr>
											<tr>
												<td><span uk-tooltip="Total demolition cost (does not include maintenance)">Demolition Reimbursement  <span class="a-info-circle uk-text-muted"></span></span></td>
												@if($current_user->isFromEntity(1))
												<td class="uk-text-right">{{$calculation['demolition_cost_formatted']}}</td>
												<td class="uk-text-right">
												@if(Gate::check('hfa-review-disposition') || Gate::check('hfa-sign-disposition') || Gate::check('hfa-release-disposition'))

													@if($disposition->hfa_calc_demo_cost === null && $calculation['demolition_cost'] != null)
														$<input type="text" name="hfa_calc_demo_cost" value="{{$calculation['demolition_cost']}}" id ="hfa_calc_demo_cost"  class="uk-input uk-form-small "/>
													@elseif($disposition->hfa_calc_demo_cost === null && $calculation['demolition_cost'] == null)
														$<input type="text" name="hfa_calc_demo_cost" value="" id ="hfa_calc_demo_cost"  class="uk-input uk-form-small "/>
													@else
														$<input type="text" name="hfa_calc_demo_cost" value="{{$disposition->hfa_calc_demo_cost}}" id ="hfa_calc_demo_cost"  class="uk-input uk-form-small "/>
													@endif

												@else
													{{$disposition->hfa_calc_demo_cost}}
													@endif</td>
												@endif
												<td class="uk-text-right">@if($disposition->hfa_calc_demo_cost){{$actual['demolition_cost_formatted']}}@else {{$calculation['demolition_cost_formatted']}}@endif</td>
											</tr>
											<tr>
												<td>
													<span uk-tooltip="Eligible property income equals the income minus the imputed cost per parcel">Eligible Property Income <span class="a-info-circle uk-text-muted"></span></span><br />
												</td>
												@if($current_user->isFromEntity(1))
												<td class="uk-text-right">{{$calculation['eligible_income_formatted']}}</td>
												<td class="uk-text-right">
													@if(Gate::check('hfa-review-disposition') || Gate::check('hfa-sign-disposition') || Gate::check('hfa-release-disposition'))

													@if($disposition->hfa_calc_epi === null && $calculation['eligible_income'] != null)
														$<input type="text" name="hfa_calc_epi" value="{{$calculation['eligible_income']}}" id ="hfa_calc_epi"  class="uk-input uk-form-small "/>
													@elseif($disposition->hfa_calc_epi === null && $calculation['eligible_income'] == null)
														$<input type="text" name="hfa_calc_epi" value="" id ="hfa_calc_epi"  class="uk-input uk-form-small "/>
													@else
														$<input type="text" name="hfa_calc_epi" value="{{$disposition->hfa_calc_epi}}" id ="hfa_calc_epi"  class="uk-input uk-form-small "/>
													@endif

													@else
													{{$disposition->hfa_calc_epi}}
													@endif</td>
												@endif
												<td class="uk-text-right">@if($disposition->hfa_calc_epi){{$actual['eligible_income_formatted']}}@else {{$calculation['eligible_income_formatted']}}@endif</td>
											</tr>
											<tr>
												<td>
													<span uk-tooltip="Total capital gain equals the eligible property income minus demolition reimbursement minus maintenance to repay">Total Capital Gain For The Landbank <span class="a-info-circle uk-text-muted"></span></span><br />
												</td>
												@if($current_user->isFromEntity(1))
												<td class="uk-text-right">{{$calculation['gain_formatted']}}</td>
												<td class="uk-text-right">
												@if(Gate::check('hfa-review-disposition') || Gate::check('hfa-sign-disposition') || Gate::check('hfa-release-disposition'))
													@if( $disposition->hfa_calc_gain === null && $calculation['gain'] != null)
														$<input type="text" name="hfa_calc_gain" value="{{$calculation['gain']}}" id ="hfa_calc_gain"  class="uk-input uk-form-small " />
													@elseif($disposition->hfa_calc_gain === null && $calculation['gain'] == null)
														$<input type="text" name="hfa_calc_gain" value="" id ="hfa_calc_gain"  class="uk-input uk-form-small " />
													@else
														$<input type="text" name="hfa_calc_gain" value="{{$disposition->hfa_calc_gain}}" id ="hfa_calc_gain"  class="uk-input uk-form-small " />
													@endif

												@else
													{{$disposition->hfa_calc_gain}}
												@endif</td>
												@endif
												<td class="uk-text-right">@if($disposition->hfa_calc_gain){{$actual['gain_formatted']}}@else {{$calculation['gain_formatted']}}@endif</td>
											</tr>
										</tbody>
										<tfoot>

											<tr>
												<td>
													<span uk-tooltip="When the eligible property income is greater than the demolition reimbursement, the payback equals demolition reimbursement plus maintenance to repay. Otherwise the paypack equals the eligible property income plus the maintenance to repay.">Total Recapture Owed To The HFA <span class="a-info-circle uk-text-muted"></span></span><br />
												</td>
												@if($current_user->isFromEntity(1))
												<td class="uk-text-right">{{$calculation['payback_formatted']}}</td>
												<td class="uk-text-right">
												@if(Gate::check('hfa-review-disposition') || Gate::check('hfa-sign-disposition') || Gate::check('hfa-release-disposition'))

													@if($disposition->hfa_calc_payback === null && $calculation['payback'] != null)
														$<input type="text" name="hfa_calc_payback" value="{{$calculation['payback']}}" id ="hfa_calc_payback"  class="uk-input uk-form-small "/>
													@elseif($disposition->hfa_calc_payback === null && $calculation['payback'] == null)
														$<input type="text" name="hfa_calc_payback" value="" id ="hfa_calc_payback"  class="uk-input uk-form-small "/>
													@else
														$<input type="text" name="hfa_calc_payback" value="{{$disposition->hfa_calc_payback}}" id ="hfa_calc_payback"  class="uk-input uk-form-small "/>
													@endif

												@else
													{{$disposition->hfa_calc_payback}}
													@endif</td>
												@endif
												<td class="uk-text-right">@if($disposition->hfa_calc_payback){{$actual['payback_formatted']}}@else {{$calculation['payback_formatted']}}@endif</td>
											</tr>
										</tfoot>
									</table>
									@if(!$current_user->isFromEntity(1))
									<p class="uk-text-right">This is not a bill. You will receive an invoice for amounts to be recaptured.</p>
									@endif
									@endif
								</div>
							</div>
							@endif
						</div>
					</div>
					@else
					<div class="uk-panel">
						<div uk-grid>
							<div class="uk-panel uk-width-1-2@m uk-width-1-1@s uk-container-center uk-margin-top uk-text-center">
								<div class="uk-alert">This is a legacy disposition. The calculations were done manually. Please see supporting documents for more information.</div>
							</div>
						</div>
					</div>
					@endif

					<div class="uk-panel uk-panel-divider">
						<div class="uk-grid uk-form">
							<div class="uk-width-2-5@m uk-width-1-1@s">
								<p>Special Circumstance Justifying Lien Release:</p>
								<div class="uk-form-controls">
									<select id="special" name="special" class="uk-select uk-width-1-1">
								    	<option>Choose a disposition type</option>
								    	@foreach($types as $type)
										<option name="disposition-type" value="{{$type->id}}" @if($disposition) @if($disposition->disposition_type_id == $type->id) selected @endif @endif>{{$type->disposition_type_name}}</option>
										@endforeach
								    </select>

	                            </div>
							</div>
							<div class="uk-width-3-5@m uk-width-1-1@s">
								<p>Description of Proposed New Use and “Special Circumstance”:<br />
								<input type="checkbox" name="description_use_in_documents" value="1" id="description_use_in_documents" class="uk-checkbox" @if($disposition) @if($disposition->description_use_in_documents) checked @else class="no-print"  @endif @endif> <small>INCLUDED IN SUPPORTING DOCUMENTS</small></p>

								<textarea name="special_circumstance" id="special_circumstance" class="uk-textarea" style="width:100%; @if($disposition) @if($disposition->description_use_in_documents) display:none; @endif @endif" rows="3">@if($disposition) {{$disposition->special_circumstance}} @endif</textarea>
							</div>
						</div>
					</div>

					@if(Gate::check('create-disposition') || Gate::check('authorize-disposition-request') || Gate::check('submit-disposition'))
					@if($step == "Draft")
					<div class="uk-panel uk-panel-divider">
						<div uk-grid>
							<div class="uk-width-1-1@s uk-width-1-3@m ">
								<button class="uk-button uk-margin-right uk-width-1-1@m uk-width-1-1@s uk-button-success" type="button" onclick="processStep('disposition-form');">Save Disposition</button>
							</div>
							<div class="uk-width-1-1@s uk-width-1-3@m ">
								<button class="uk-button uk-width-1-1@m uk-width-1-1@s uk-button-success" type="button" onclick="processStep('disposition-submitted');">Save & Submit Disposition Request for Internal Approval</button>
							</div>
						</div>
					</div>
					@endif
					@endif



					@if($step == "Pending Landbank Approval" || $step == "Pending HFA Approval" || $step == "Pending Payment" || $step == "Paid" || $step == "Submitted to Fiscal Agent")
					<div class="uk-panel uk-panel-header uk-panel-divider uk-margin-top">
						<h6 class="uk-panel-title">LANDBANK SIGNATURES</h6>
					</div>

					<div class="uk-panel">
						<div class="uk-grid approvals">
							<div class="uk-width-1-1@s uk-width-1-1@m">
								<p>I am a duly authorized representative of the Partner with the authority to execute this certification on behalf of the Partner. I have read and understand the NIP Guidelines and other governing documents related to this program. I certify that the special circumstance listed above complies with all NIP guidelines and governing regulations. I agree that Partner will remit any and all Program Income or Net Proceeds as may be required by the guidelines in a timely manner as prescribed by OHFA.</p>
							</div>
							<div class="uk-width-1-1">
					            <table class="uk-table uk-overflow-container" id="hfa-approvers-list">
									<thead>
										<th><small>Name</small></th>
										<th><small>Decision</small></th>
										@if( $isApprover || Auth::user()->isHFAAdmin() || Auth::user()->isLandbankDispositionApprover())

										<th class="no-print">
											@if($disposition->parcel->legacy != 1 && $step == "Pending Landbank Approval" && $step != "Pending HFA Approval")
												<small>Action</small>
											@endif
										</th>
										@endif
									</thead>
									@if($disposition->parcel->legacy != 1 && $step == "Pending Landbank Approval" && $step != "Pending HFA Approval")
									<tfoot class="no-print">
										<tr>
											<td colspan="3">
												@if( $isApprover )
												<div class="uk-width-4-5 uk-align-center uk-margin-top">
													<h4>If an approver is not able to login and click "approve", please print the disposition using the print button below, and they can physically sign the printed copy of the document on their respective signature line.<br /><button class="uk-button uk-button-small uk-button-warning uk-margin-top uk-width-1-1" onclick="window.print()"><span class="a-print"></span> PRINT DISPOSITION FOR MANUAL @if(count($approvals)>1) SIGNATURES @else SIGNATURE @endif</button>
													</h4>
													<h4>Select approvers who signed the document you are about to upload.</h4>
													<div class="communication-selector">
											            <ul class="uk-subnav document-menu">
											            	@foreach ($approvals as $approval)
								                            <li  id="approver-id-{{ $approval->approver->id }}-li">
								                                <input name="approvers-id-checkbox" id="approver-id-{{ $approval->approver->id }}" value="{{ $approval->approver->id }}" type="checkbox" class="uk-checkbox">
								                                <label for="approver-id-{{ $approval->approver->id }}">
								                                    {{$approval->approver->name}}
								                                </label>
								                            </li>
								                            @endforeach
								                        </ul>
								                    </div>
												</div>
													<div class="uk-width-4-5 uk-align-center uk-margin-bottom" id="list-item-upload-box">
									                    <div id="upload-drop" class="js-upload-2 uk-placeholder uk-text-center">
									                        <span class="a-higher"></span>
									                        <span class="uk-text-middle"> Please upload your document by dropping it here or</span>
									                        <div uk-form-custom>
									                            <input type="file" multiple>
									                            <span class="uk-link">by browsing and selecting it here.</span>
									                        </div>
									                    </div>

									                    <progress id="js-progressbar-2" class="uk-progress" value="0" max="100" hidden></progress>

									                    <script>
									                    $(function(){
									                        var bar2 = document.getElementById('js-progressbar-2');

									                        settings2    = {

									                            url: '{{ URL::route("disposition.uploadSignature", $parcel) }}',
									                            multiple: false,
									                            allow : '*.(jpg|gif|png|pdf|doc|docx|xls|xlsx)',

									                            headers : {
									                                'enctype' : 'multipart/form-data'
									                            },

									                            beforeSend: function () {
									                                // console.log('beforeSend', arguments);
									                            },
									                            beforeAll: function (settings2) {

									                                var approverArray = [];
							                                        $("input:checkbox[name=approvers-id-checkbox]:checked").each(function(){
							                                                approverArray.push($(this).val());
							                                            });
							                                        settings2.params.approvers = approverArray;
									                                settings2.params._token = '{{ csrf_token() }}';
							                                        approvers = approverArray;
							                                        if(approverArray.length > 0){
							                                            console.log('Approvers selected: '+approverArray);
							                                        }else{
							                                            UIkit.modal.alert('You must select at least one approver.',{stack: true});
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

									                                bar2.removeAttribute('hidden');
									                                bar2.max = e.total;
									                                bar2.value = e.loaded;
									                            },

									                            progress: function (e) {
									                                // console.log('progress', arguments);

									                                bar2.max = e.total;
									                                bar2.value = e.loaded;
									                            },

									                            loadEnd: function (e) {
									                                // console.log('loadEnd', arguments);

									                                bar2.max = e.total;
									                                bar2.value = e.loaded;
									                            },

									                            completeAll: function (response) {
									                                var documentids = response.response;

									                                setTimeout(function () {
									                                    bar2.setAttribute('hidden', 'hidden');
									                                }, 250);

									                                // Submit form and make sure it responds back with 1 - otherwise it will output the response to a browser alert box.
								                                    UIkit.modal.prompt("I uploaded and categorized the document(s) accordingly. Please add your comment for the history record.",'',{stack: true}).then(function(val){
								                                        $.post('{{ URL::route("disposition.uploadSignatureComments", $parcel) }}', {
								                                                'postvars' : documentids,
								                                                'comment' : val,
								                                                '_token' : '{{ csrf_token() }}'
								                                                }, function(data) {
								                                                    if(data!='1'){
								                                                        UIkit.modal.alert(data,{stack: true});
								                                                    } else {
								                                                        UIkit.modal.alert('Your comment has been saved.',{stack: true});
								                                                            location.reload();
								                                                    }
								                                        });
								                                    });

									                            }

									                        };

									                        var select = UIkit.upload('.js-upload-2', settings2);

									                    });
									                    </script>


									            	</div>
									            <hr class="dashed-hr uk-margin-top uk-margin-bottom">
									            	@endif
											</td>
										</tr>
									</tfoot>
									@endif
									<tbody>
										@php $hfa_approver_count = 0; $user_is_in_approvers_list =0 ; @endphp
										@foreach ($approvals as $approval)
										@php $hfa_approver_count = $hfa_approver_count + 1; @endphp
										<tr id="ap_{{$approval->approver->id}}" @if(!count($approval->actions)) class="no-print" @endif >
											<td class="uk-width-2-5">{{$approval->approver->name}}</td>
											<td class="uk-width-2-5" id="action_{{$approval->approver->id}}">
												@if(!count($approval->actions))
												<small class="no-action no-print">Has Not Signed.</small>
												<script type="text/javascript">window.notSigned++;</script>
												@endif
												@foreach($approval->actions as $action)
												@if($action->action_type->id == 1)
												<div class="uk-badge uk-badge-success">Approved</div> <small>on {{$action->created_at}}</small><br />
												@elseif($action->action_type->id == 4)
												<div class="uk-badge uk-badge-danger">Declined</div> <small>on {{$action->created_at}}</small><br />
												@elseif($action->action_type->id == 5)
												<div class="uk-badge uk-badge-success">Approved by proxy</div> <small>on {{$action->created_at}}</small><br />
												@endif
												@endforeach
											</td>

											<td class="uk-width-1-5 no-print">
												@if( $isApprover || Auth::user()->isHFAAdmin())
												@if(Auth::user()->isLandbankDispositionApprover() || Auth::user()->isHFAAdmin())
													@if($disposition->parcel->legacy != 1 && $step == "Pending Landbank Approval" && $step != "Pending HFA Approval")
														@if( Auth::user()->id == $approval->approver->id )
														@php $user_is_in_approvers_list = 1; @endphp
														<button style="margin-top:5px" class="uk-button uk-button-small uk-button-success" onclick="approve();">Approve</button>

														<button style="margin-top:5px" class="uk-button uk-button-small uk-button-warning" onclick="decline();">Decline</button>
														@endif
														@if( $isApprover )
								 						<button style="margin-top:5px" onclick="remove({{$approval->approver->id}});" class="uk-button uk-button-small uk-button-warning"><span class="a-trash-4"></span></button>
							 							@endif
						 							@endif
						 						@endif
						 						@endif
						 					</td>

										</tr>
										@endforeach
										@if($disposition->parcel->legacy != 1 && $step == "Pending Landbank Approval" && $step != "Pending HFA Approval")
											@if($pending_approvers)
											@foreach($pending_approvers as $pending_approver)
											<tr class="no-print">
												<td class="uk-text-muted">
													{{$pending_approver->name}}
												</td>
												<td></td>
												<td>
													<button class="uk-button uk-button-default uk-button-small " onclick="addApprover({{$pending_approver->id}});">Add as approver</button>
												</td>
											</tr>
											@endforeach
											@endif
										@endif
									@if(Auth::user()->isHFAAdmin() && !Auth::user()->isLandbankDispositionApprover() && $user_is_in_approvers_list == 0 && $step == "Pending Landbank Approval" && $step != "Pending HFA Approval")
										<tr class="no-print">
											<td class="uk-text-muted">
												[ADMIN] {{Auth::user()->name}}
											</td>
											<td></td>
											<td>
												<button class="uk-button uk-button-small uk-button-success" onclick="addApprover({{Auth::user()->id}});">Add me as approver</button>
											</td>
										</tr>
									@endif
									</tbody>
								</table>
					        </div>

						</div>
					</div>
					@if((Auth::user()->isLandbankDispositionApprover()) || Auth::user()->isHFAAdmin())
						@if($disposition->parcel->legacy != 1 && $step == "Pending Landbank Approval" && $step != "Pending HFA Approval")
							<div class="uk-panel uk-panel-header uk-panel-divider print-only">
								<h6 class="uk-panel-title">SIGNATURES</h6>
							</div>
							<div class="uk-panel uk-margin-top print-only">
								<div uk-grid>
									<div class="uk-width-1-1">
										<p></p>
									</div>
								</div>
								@foreach ($approvals as $approval)
								<div class="uk-panel uk-panel-header">
									<div class="uk-width-1-1 uk-margin-top ">
										<br /><br /><br />
										<div style="border-top:1px solid #333;padding-top:10px;" class=" uk-width-1-1">
											<p>Name: {{$approval->approver->name}} <span style="float:right;margin-right:20%;">Date:</span>
											</p>
										</div>
									</div>
								</div>
								@endforeach
							</div>
						@endif
					@endif
					@if($step == "Pending Landbank Approval" && $step != "Pending HFA Approval")
					<div class="uk-panel no-print">
						<div uk-grid>
							<div class="uk-width-1-1@s uk-width-1-2@m">
							@if(Gate::check('create-disposition') || Gate::check('authorize-disposition-request') || Gate::check('submit-disposition'))
								<button class="uk-button uk-width-1-1@m uk-button-success" type="button" onclick="processStep('disposition-form');"><span class="a-floppy"></span> Save Disposition</button>
							</div>

							<div class="uk-width-1-1@s uk-width-1-2@m">
								@if($isApproved)
								<button class="uk-button uk-width-1-1@m uk-button-success" type="button" onclick="processStep('disposition-submit-to-hfa');"><span class="a-mail-out"></span> Save & Submit To HFA For Approval</button>
								@endif
							@endif
							</div>
						</div>
					</div>

					<hr class="dashed-hr">
					@endif
					@endif
					{{-- End if step Pending Landbank Approval --}}

				@if($step == "Pending HFA Approval" ||  $step == "Pending Payment" || $step == "Paid" || $step == "Submitted to Fiscal Agent")
					<div class="uk-panel uk-panel-header uk-margin-large-top">
						<h6 class="uk-panel-title">HFA APPROVALS</h6>
					</div>

					<div class="uk-panel">
					@if($disposition->hfa_calc_trans_cost === null)
						<h3>Please Save Your Adjustments</h3><p>Sorry, you have to save your adjustments first, then you can enter your approvals.</p>
					@else
						<div class="uk-grid approvals">
							<div class="uk-width-1-1">
					            <table class="uk-table uk-overflow-container" id="hfa-approvers-list">
									<thead>
										<th><small>Name</small></th>
										<th><small>Decision</small></th>
										@if( $isApprover_hfa || Auth::user()->isHFAAdmin() || Auth::user()->isHFADispositionApprover())
										<th class="no-print">
											@if($disposition->parcel->legacy != 1  && ( $step != "Pending Payment" && $step != "Paid" && $step != "Submitted to Fiscal Agent" ))
												<small>Action</small>
											@endif</th>
										@endif
									</thead>
									@if($disposition->parcel->legacy != 1 && $step != "Pending Payment")
										<tfoot class="no-print">
											<tr>
												<td colspan="3">
													@if( $isApprover_hfa )
														<div class="uk-width-4-5 uk-align-center uk-margin-top">
															<h4 class="uk-margin-top-large">If an approver is not able to login and click "approve", please print the disposition using the print button below, and they can physically sign the printed copy of the document on their respective signature line.<br /><button style="margin-top:5px" class="uk-button uk-button-small uk-button-warning" onclick="window.print()">Print Disposition</button>
															</h4>
															<h4>Select approvers who signed the document you are about to upload.</h4>
															<div class="communication-selector">
													            <ul class="uk-subnav document-menu">
													            	@foreach ($approvals_hfa as $approval)
										                            <li  id="approver-hfa-id-{{ $approval->approver->id }}-li">
										                                <input name="approvers-hfa-id-checkbox" id="approver-hfa-id-{{ $approval->approver->id }}" value="{{ $approval->approver->id }}" type="checkbox" class="uk-checkbox">
										                                <label for="approver-id-{{ $approval->approver->id }}">
										                                    {{$approval->approver->name}}
										                                </label>
										                            </li>
										                            @endforeach
										                        </ul>
										                    </div>
														</div>
														<div class="uk-width-4-5 uk-align-center uk-margin-bottom-large" id="list-item-upload-box">
										                    <div id="upload-drop-hfa" class="js-upload-3 uk-placeholder uk-text-center">
										                        <span class="a-higher"></span>
										                        <span class="uk-text-middle"> Please upload your document by dropping it here or</span>
										                        <div uk-form-custom>
										                            <input type="file" multiple>
										                            <span class="uk-link">by browsing and selecting it here.</span>
										                        </div>
										                    </div>

										                    <progress id="js-progressbar-3" class="uk-progress" value="0" max="100" hidden></progress>

										                    <script>
										                    $(function(){
										                        var bar3 = document.getElementById('js-progressbar-3');

										                        settings3    = {

										                            url: '{{ URL::route("disposition.uploadHFASignature", $parcel) }}',
										                            multiple: false,
										                            allow : '*.(jpg|gif|png|pdf|doc|docx|xls|xlsx)',

										                            headers : {
										                                'enctype' : 'multipart/form-data'
										                            },

										                            beforeSend: function () {
										                                // console.log('beforeSend', arguments);
										                            },
										                            beforeAll: function (settings3) {

										                            	var approverArray = [];
								                                        $("input:checkbox[name=approvers-hfa-id-checkbox]:checked").each(function(){
								                                                approverArray.push($(this).val());
								                                            });
								                                        settings3.params.approvers = approverArray;
										                                settings3.params._token = '{{ csrf_token() }}';
								                                        approvers = approverArray;
								                                        if(approverArray.length > 0){
								                                            console.log('Approvers selected: '+approverArray);
								                                        }else{
								                                            UIkit.modal.alert('You must select at least one approver.',{stack: true});
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

										                                bar3.removeAttribute('hidden');
										                                bar3.max = e.total;
										                                bar3.value = e.loaded;
										                            },

										                            progress: function (e) {
										                                // console.log('progress', arguments);

										                                bar3.max = e.total;
										                                bar3.value = e.loaded;
										                            },

										                            loadEnd: function (e) {
										                                // console.log('loadEnd', arguments);

										                                bar3.max = e.total;
										                                bar3.value = e.loaded;
										                            },

										                            completeAll: function (response) {
										                                var documentids = response.response;

										                                setTimeout(function () {
										                                    bar3.setAttribute('hidden', 'hidden');
										                                }, 250);

									                                    // Submit form and make sure it responds back with 1 - otherwise it will output the response to a browser alert box.
									                                    UIkit.modal.prompt("I uploaded and categorized the document(s) accordingly. Please add your comment for the history record.",'',{stack: true}).then(function(val){
									                                        $.post('{{ URL::route("disposition.uploadSignatureComments", $parcel) }}', {
									                                                'postvars' : documentids,
									                                                'comment' : val,
									                                                '_token' : '{{ csrf_token() }}'
									                                                }, function(data) {
									                                                    if(data!='1'){
									                                                        UIkit.modal.alert(data,{stack: true});
									                                                    } else {
									                                                        UIkit.modal.alert('Your comment has been saved.',{stack: true});
									                                                            location.reload();
									                                                    }
									                                        });
									                                    });

										                            }

										                        };

										                        var select = UIkit.upload('.js-upload-3', settings3);

										                    });
										                    </script>
										            	</div>
										            @endif
												</td>
											</tr>
										</tfoot>
									@endif
									<tbody>
										@php $hfa_approver_count = 0; $user_is_in_approvers_list =0 ; @endphp
										@foreach ($approvals_hfa as $approval)
										@php $hfa_approver_count = $hfa_approver_count + 1; @endphp
										<tr id="ap_{{$approval->approver->id}}" @if(!count($approval->actions)) class="no-print" @endif>
											<td class="uk-width-2-5">{{$approval->approver->name}}</td>
											<td class="uk-width-2-5" id="actionhfa_{{$approval->approver->id}}">
												@if(!count($approval->actions))
												<small class="no-action">Has Not Signed.</small>
												<script>window.notSigned = window.notSigned + 1</script>
												@endif
												@foreach($approval->actions as $action)
												@if($action->action_type->id == 1)
												<div class="uk-badge uk-badge-success">Approved</div> <small>on {{$action->created_at}}</small><br />
												@elseif($action->action_type->id == 4)
												<div class="uk-badge uk-badge-danger">Declined</div> <small>on {{$action->created_at}}</small><br />
												@elseif($action->action_type->id == 5)
												<div class="uk-badge uk-badge-success">Approved by proxy</div> <small>on {{$action->created_at}}</small><br />
												@endif
												@endforeach
											</td>

											<td class="uk-width-1-5 no-print">
												@if( $isApprover_hfa || Auth::user()->isHFAAdmin())
												@if(Auth::user()->isHFADispositionApprover() || Auth::user()->isHFAAdmin())
													@if($disposition->parcel->legacy != 1  && $step != "Pending Payment" && $step != "Paid" && $step != "Submitted to Fiscal Agent")
														@if( Auth::user()->id == $approval->approver->id )
														@php $user_is_in_approvers_list = 1; @endphp
														<button style="margin-top:5px" class="uk-button uk-button-small uk-button-success" onclick="approveHFA();">Approve</button>

														<button style="margin-top:5px" class="uk-button uk-button-small uk-button-warning" onclick="declineHFA();">Decline</button>
														@endif
														@if( $isApprover_hfa )
								 						<button style="margin-top:5px" onclick="removeHFA({{$approval->approver->id}});" class="uk-button uk-button-small uk-button-warning"><span class="a-trash-4"></span></button>
							 							@endif
						 							@endif
						 						@endif
						 						@endif
						 					</td>

										</tr>
										@endforeach
										@if(Auth::user()->isHFADispositionApprover() || Auth::user()->isHFAAdmin())
										@if($disposition->parcel->legacy != 1 && $step != "Pending Payment" && $step != "Paid" && $step != "Submitted to Fiscal Agent")
											@if($pending_approvers_hfa)
											@foreach($pending_approvers_hfa as $pending_approver)
											<tr class="no-print">
												<td class="uk-text-muted">
													{{$pending_approver->name}}
												</td>
												<td></td>
												<td>
													<button class="uk-button uk-button-default uk-button-small " onclick="addHFAApprover({{$pending_approver->id}});">Add as approver</button>
												</td>
											</tr>
											@endforeach
											@endif
										@endif
										@endif
									</tbody>
								</table>
					        </div>

						</div>
					@endif
					</div>
					@if((Auth::user()->isHFADispositionApprover()) || Auth::user()->isHFAAdmin())
						@if($disposition->parcel->legacy != 1 && $disposition->hfa_calc_trans_cost !== null)
							<div class="uk-panel uk-panel-header uk-panel-divider print-only">
								<h6 class="uk-panel-title">SIGNATURES</h6>
							</div>
							<div class="uk-panel uk-margin-top print-only">
								<div uk-grid>
									<div class="uk-width-1-1">
										<p></p>
									</div>
								</div>
								@foreach ($approvals_hfa as $approval)
								<div class="uk-panel uk-panel-header">
									<div class="uk-width-1-1 uk-margin-top ">
										<br /><br /><br />
										<div style="border-top:1px solid #333;padding-top:10px;" class=" uk-width-1-1">
											<p>Name: {{$approval->approver->name}} <span style="float:right;margin-right:20%;">Date:</span>
											</p>
										</div>
									</div>
								</div>
								@endforeach
							</div>
						@endif
					@endif

					@if($step != "Paid" && $step != "Submitted to Fiscal Agent")
					@if($step != "Pending Payment")
						<div class="uk-panel no-print">
							<div uk-grid>
								<div class="uk-width-1-1@s uk-width-1-2@m">
									@if(Gate::check('hfa-review-disposition') || Gate::check('hfa-sign-disposition') || Gate::check('hfa-release-disposition'))
										<button class="uk-button uk-button-success uk-width-1-1@m" type="button" onclick="processStep('disposition-under-review');"><span class="a-floppy"></span> Confirm Calculations &amp; Save Disposition</button>
									@endif
								</div>

								<div class="uk-width-1-1@s uk-width-1-2@m">
									@if($isApproved_hfa)
										@can('hfa-release-disposition')
										<button class="uk-button uk-button-success uk-width-1-1@m " type="button" onclick="processStep('disposition-approve');" id="create-invoice"><span class="a-file-left"></span> Create&thinsp;&thinsp;&thinsp;/&thinsp;Add to a Disposition Invoice</button>
										@endcan
									@else
										@can('hfa-release-disposition')
										<button class="uk-button uk-button-default uk-width-1-1@m" type="button" id="create-invoice" uk-tooltip="Please Approve the Disposition First" onclick="UIkit.modal.alert('OOPSIE! Please approve the disposition first.');"><span class="a-file-left"></span> Create&thinsp;&thinsp;&thinsp;/&thinsp;Adjustments to a Disposition Invoice</button>
										@endcan
									@endif

								</div>

							</div>
						</div>
					@else
					<div class="uk-panel no-print">
						<div uk-grid>
							<div class="uk-width-1-1@a uk-width-1-2@m">
							@if(Gate::check('hfa-review-disposition') || Gate::check('hfa-sign-disposition') || Gate::check('hfa-release-disposition'))
								<button class="uk-button uk-button-success uk-width-1-1@m" type="button" onclick="processStep('disposition-under-review');"><span class="a-floppy"></span>  Save Disposition Changes</button>
							@endif
							</div>
						</div>
					</div>
					@endif
					@endif

				@endif
				{{-- End if step Pending HFA Approval --}}


					@if($disposition->invoice)
						@if($disposition->invoice->disposition_invoice_id)
						<div class="uk-panel uk-panel-divider no-print">
							<div uk-grid>
								<div class="uk-width-1-2@m uk-width-1-1@s uk-container-center ">
									<button class="uk-button uk-button-success uk-width-1-1@m" type="button" onclick="window.open('/disposition_invoice/{{$disposition->invoice->disposition_invoice_id}}', '_blank')"><span class="a-file-left"></span> View Disposition Invoice {{$disposition->invoice->disposition_invoice_id}}</button>
								</div>
							</div>
						</div>
						@endif
					@endif

					@can('hfa-review-disposition')
						@if($isDeclined == 0)

							@if($disposition->date_release_requested == null && $disposition->release_date == null)
								@if(Auth::user()->isHFAFiscalAgent() || Auth::user()->isHFAAdmin())
								<div class="uk-panel uk-panel-divider">
									<div uk-grid>
										<div class="uk-panel uk-width-1-2@m uk-width-1-1@s  uk-text-center no-print uk-margin-top">

											<button class="uk-button uk-button-success uk-width-1-1@s" type="button" onclick="processStep('disposition-release-requested');"><span class="a-mail-out"></span> Release Lien</button>
										</div>
									</div>
								</div>
								@endif
							@endif
							@if($disposition->date_release_requested !== null && $disposition->release_date == null)
								@if(Auth::user()->isHFAFiscalAgent() || Auth::user()->isHFAAdmin())
								<div class="uk-panel uk-panel-divider">
									<div uk-grid>
										<div class="uk-panel uk-width-1-1@m uk-width-1-1@s uk-panel-header uk-panel-divider uk-margin-top no-print">
											<h6 class="uk-panel-title">RELEASE DISPOSITION</h6>
											Release Date <input type="text" id="release_date" name="release_date" class="uk-input uk-form-small uk-form-width-small" style="width:80px;" value="{{ Carbon\Carbon::now()->format('Y-m-d') }}"/> <button type="button" onclick="save_release_date();" class="uk-button uk-button-success uk-button-small flatpickr flatpickr-input active" data-id="dateformat" ><span class="a-checkbox-checked"></span> Release Lien</button>
										</div>
									</div>
								</div>
								@endif
							@endif

						@else

							@if($step != "Declined")
							<div class="uk-panel uk-panel-divider">
								<div uk-grid>
									<div class="uk-panel uk-width-1-2@m uk-width-1-1@s  uk-text-center no-print uk-margin-top">

										<button class="uk-button uk-button-success uk-width-1-1@s" type="button" onclick="processStep('disposition-decline');"><span class="a-checkbox"></span> Confirm Disposition Declined</button>
									</div>
								</div>
							</div>
							@endif

						@endif
					@endcan

					@can('hfa-review-disposition')
					<div class="uk-panel uk-panel-divider">
						<div uk-grid>
							<div class="uk-width-1-1@s">
								<p>For OHFA use only:</p>
								<dl class="uk-description-list-horizontal uk-form">
		                            <dt>Public Use:</dt>
		                            <dd>
		                            	<input type="checkbox" class="uk-checkbox" name="public_use_political" value="1" id="public_use_political" @if($disposition) @if($disposition->public_use_political) checked @endif @endif> Political Subdivision
		                            	<input type="checkbox" class="uk-checkbox"  class="uk-margin-left" name="public_use_community" value="1" id="public_use_community" @if($disposition)  @if($disposition->public_use_community) checked @endif @endif> Community Benefit
		                            	<input type="checkbox" class="uk-checkbox"  class="uk-margin-left" name="public_use_oneyear" value="1" id="public_use_oneyear" @if($disposition)  @if($disposition->public_use_oneyear) checked @endif @endif> Construction/Operation 1 Year
		                            	<input type="checkbox" class="uk-checkbox"  class="uk-margin-left" name="public_use_facility" value="1" id="public_use_facility" @if($disposition)  @if($disposition->public_use_facility) checked @endif @endif> Public Facility
		                            </dd>
		                            <hr class="dashed-hr uk-margin-bottom">
		                            <dt>Nonprofit:</dt>
		                            <dd>
		                            	<input type="checkbox" class="uk-checkbox" name="nonprofit_taxexempt" value="1" id="nonprofit_taxexempt" @if($disposition)  @if($disposition->nonprofit_taxexempt) checked @endif @endif> Tax Exempt Status
		                            	<input type="checkbox" class="uk-checkbox"  class="uk-margin-left" name="nonprofit_community" value="1" id="nonprofit_community" @if($disposition)  @if($disposition->nonprofit_community) checked @endif @endif> Community Use
		                            	<input type="checkbox" class="uk-checkbox" class="uk-margin-left"  name="nonprofit_oneyear" value="1" id="nonprofit_oneyear" @if($disposition)  @if($disposition->nonprofit_oneyear) checked @endif @endif> Construction/Operation 1 Year
		                            	<input type="checkbox" class="uk-checkbox" class="uk-margin-left"  name="nonprofit_newuse" value="1" id="nonprofit_newuse" @if($disposition)  @if($disposition->nonprofit_newuse) checked @endif @endif> Zoned for New Use
		                            </dd>
		                            <hr class="dashed-hr uk-margin-bottom">
		                            <dt>Bus/Res Development</dt>
		                            <dd>
		                            	<input type="checkbox" class="uk-checkbox" name="dev_fmv" value="1" id="dev_fmv" @if($disposition)  @if($disposition->dev_fmv) checked @else class="no-print" @endif @endif> FMV
		                            	<input type="checkbox" class="uk-checkbox" class="uk-margin-left"  name="dev_oneyear" value="1" id="dev_oneyear" @if($disposition) @if($disposition->dev_oneyear) checked @else class="no-print" @endif @endif> Construction/Operation 1 Year
		                            	<input type="checkbox" class="uk-checkbox" class="uk-margin-left"  name="dev_newuse" value="1" id="dev_newuse" @if($disposition) @if($disposition->dev_newuse) checked @else class="no-print"  @endif @endif> Zoned for New Use
		                            	<input type="checkbox" class="uk-checkbox" class="uk-margin-left"  name="dev_purchaseag" value="1" id="dev_purchaseag" @if($disposition) @if($disposition->dev_purchaseag) checked @endif @endif> Purchase Ag
		                            	<input type="checkbox" class="uk-checkbox" class="uk-margin-left"  name="dev_taxescurrent" value="1" id="dev_taxescurrent" @if($disposition) @if($disposition->dev_taxescurrent) checked @else class="no-print" @endif @endif> Taxes Current
		                            	<input type="checkbox" class="uk-checkbox" class="uk-margin-left"  name="dev_nofc" value="1" id="dev_nofc" @if($disposition) @if($disposition->dev_nofc) checked @else class="no-print"  @endif @endif> No FC
		                            </dd>
		                        </dl>

							</div>

						</div>
					</div>
					@endcan

				</div>
			</form>
		</div>

	</div>
	@endif

</div>
<script type="text/javascript">

	function deleteFile(id){
	    UIkit.modal.confirm("Are you sure you want to delete this file? This is permanent.").then(function() {
	        $.post('{{ URL::route("documents.deleteDocument", $parcel->id) }}', {
	            'id' : id,
	            '_token' : '{{ csrf_token() }}'
	        }, function(data) {
	            if(data!='1'){
	                UIkit.modal.alert(data);
	            } else {
	                UIkit.modal.alert('The document has been deleted.');
	                $.post('{{ URL::route("disposition.getUploadedDocuments", $parcel->id) }}', {
								                                                '_token' : '{{ csrf_token() }}'
                            }, function(data) {
                                if(data['message']!=''){
                                	$('#sent-document-list').empty();
                                	var index;
                                	for (index = 0; index < data.length; ++index) {
									    $('#sent-document-list').append('<tr><td>'+data[index]['filedate']+'</td><td>'+data[index]['filename']+'</td><td><a class="uk-link-muted " uk-tooltip="'+data[index]['comment']+'"><span class="a-info-circle"></span></a>&nbsp;&nbsp;|&nbsp;&nbsp;<a href="'+data[index]['filelink']+'" target="_blank" class="uk-link-muted "  uk-tooltip="Download file"><span class="a-lower"></span></a>&nbsp;&nbsp;|&nbsp;&nbsp;<a class="uk-link-muted " onclick="deleteFile('+data[index]['id']+');" uk-tooltip="Delete this file"><span class="a-trash-4"></span></a></td></tr>');
									}
								}else{
									UIkit.modal.alert('Something went wrong.');
								}
                    });
	            }
	        });
	    });

	}

	function approve(){
		UIkit.modal.confirm("Are you sure you want to approve?").then(function() {
	        $.post('{{ URL::route("disposition.approve", [$parcel]) }}', {
				'_token' : '{{ csrf_token() }}'
			}, function(data) {
				if(data['message']!=''){
					UIkit.modal.alert(data['message']);
					$(".no-action").remove();
					$('#action_'+data['id']).append('<div class="uk-badge uk-badge-success">Approved</div> <small></small><br />');
					location.reload();
				}else{
					UIkit.modal.alert('Something went wrong.');
				}
			} );
	    });
	}

	function approveHFA(){
		UIkit.modal.confirm("Are you sure you want to approve?").then(function() {
	        $.post('{{ URL::route("disposition.approveHFA", [$parcel]) }}', {
				'_token' : '{{ csrf_token() }}'
			}, function(data) {
				if(data['message']!=''){
					UIkit.modal.alert(data['message']);
					$(".no-action").remove();
					$('#actionhfa_'+data['id']).append('<div class="uk-badge uk-badge-success">Approved</div> <small></small><br />');
					location.reload();
				}else{
					UIkit.modal.alert('Something went wrong.');
				}
			} );
	    });
	}

	function decline(){
		UIkit.modal.confirm("Are you sure you want to decline?").then(function() {
	        $.post('{{ URL::route("disposition.decline", [$parcel]) }}', {
				'_token' : '{{ csrf_token() }}'
			}, function(data) {
				if(data['message']!=''){
					UIkit.modal.alert(data['message']);
					$(".no-action").remove();
					$('#action_'+data['id']).append('<div class="uk-badge uk-badge-danger">Declined</div> <small></small><br />');
					location.reload();
				}else{
					UIkit.modal.alert('Something went wrong.');
				}
			} );
	    });
	}

	function declineHFA(){
		UIkit.modal.confirm("Are you sure you want to decline?").then(function() {
	        $.post('{{ URL::route("disposition.declineHFA", [$parcel]) }}', {
				'_token' : '{{ csrf_token() }}'
			}, function(data) {
				if(data['message']!=''){
					UIkit.modal.alert(data['message']);
					$(".no-action").remove();
					$('#actionhfa_'+data['id']).append('<div class="uk-badge uk-badge-danger">Declined</div> <small></small><br />');
					location.reload();
				}else{
					UIkit.modal.alert('Something went wrong.');
				}
			} );
	    });
	}

	function addApprover(id){
		UIkit.modal.confirm("Make sure you saved your changes before proceeding!").then(function() {
			$.post('{{ URL::route("disposition.addapprover", [$parcel]) }}', {
				'_token' : '{{ csrf_token() }}',
				'user_id' : id
			}, function(data) {
				if(data['message']!=''){
					UIkit.modal.alert(data['message']);
					location.reload();
				}else{
					UIkit.modal.alert('Something went wrong.');
				}
			} );
		});
	}

	function addHFAApprover(id){
		UIkit.modal.confirm("Make sure you saved your changes before proceeding!").then(function() {
			$.post('{{ URL::route("disposition.addHFAApprover", [$parcel]) }}', {
				'_token' : '{{ csrf_token() }}',
				'user_id' : id
			}, function(data) {
				if(data['message']!=''){
					UIkit.modal.alert(data['message']);
					location.reload();
				}else{
					UIkit.modal.alert('Something went wrong.');
				}
			} );
		});
	}

	function remove(id){
		UIkit.modal.confirm("Are you sure you want to remove this approver?").then(function() {
	        $.post('{{ URL::route("disposition.removeapprover", [$parcel]) }}', {
				'id' : id,
				'_token' : '{{ csrf_token() }}'
			}, function(data) {
				if(data['message']!=''){
					UIkit.modal.alert(data['message']);
				}else{
					$("#ap_"+data['id']).remove();
					$("#approver-id-"+data['id']+"-li").remove();
					window.notSigned = window.notSigned - 1;
					if(window.notSigned < 1){
						location.reload();
					}
				}
			} );
	    });
	}
	function removeHFA(id){
		UIkit.modal.confirm("Are you sure you want to remove this approver?").then(function() {
	        $.post('{{ URL::route("disposition.removeHFAapprover", [$parcel]) }}', {
				'id' : id,
				'_token' : '{{ csrf_token() }}'
			}, function(data) {
				if(data['message']!=''){
					UIkit.modal.alert(data['message']);
				}else{
					$("#ap_"+data['id']).remove();
					$("#approver-hfa-id-"+data['id']+"-li").remove();
					window.notSigned = window.notSigned - 1;
					if(window.notSigned < 1){
						location.reload();
					}else{
						console.log('There are '+window.notSigned+' unsigned approvers left.');
					}

				}
			} );
	    });
	}

	function step(id){
		//$(".dispo-step").addClass("uk-hidden");
		$(".button-step").removeClass("uk-button-primary");
		if(id == 'disposition-submitted'){
			$("#disposition-form").removeClass("uk-hidden");
			$("#"+id+"-button").addClass("uk-button-primary");
		}else if(id == 'disposition-under-review'){
			$("#disposition-form").removeClass("uk-hidden");
			$("#disposition-requested-button").addClass("uk-button-primary");
		}else if(id == 'disposition-release-requested' || id == 'disposition-released'){
			$("#disposition-form").removeClass("uk-hidden");
			$("#disposition-requested-button").addClass("uk-button-primary");
		}else{
			$("#"+id).removeClass("uk-hidden");
			$("#"+id+"-button").addClass("uk-button-primary");
		}
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
		    case "disposition-submitted":
		        var form = $('#disposition-form-form');
		        break;
		    case "disposition-submit-to-hfa":
		        var form = $('#disposition-form-form');
		        break;
		    case "disposition-requested":
		        var form = $('#disposition-form-form');
		        break;
		    case "disposition-under-review":
		        var form = $('#disposition-form-form');
		        break;
		    case "disposition-approve":
		        var form = $('#disposition-form-form');
		        break;
		    case "disposition-release-requested":
		        var form = $('#disposition-released-form');
		        break;
		    case "disposition-released":
		        var form = $('#disposition-released-form');
		        break;
		    case "disposition-decline":
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
				$('#disposition-tab-content').load('/dispositions/{{$parcel->id}}');
				step(data['next']);
				location.reload();
				UIkit.modal.alert(data['message']);
			}
		} );
	}


	$(document).ready(function(){

		$('#legal_description_in_documents').change(function(){
	        if(this.checked){
	        	$('#full_description').fadeOut('slow');
	        }else{
	        	$('#full_description').fadeIn('slow');
	        }
	    });

	    $('#description_use_in_documents').change(function(){
	        if(this.checked){
	        	$('#special_circumstance').fadeOut('slow');
	        }else{
	        	$('#special_circumstance').fadeIn('slow');
	        }
	    });

		var next_step = '';
        $.get( "/session/next_step", function( data ) {
                next_step = data;

		        if(next_step != ''){
		        	step(next_step);
		        }
            });
	});

</script>

	<script>
		flatpickr.defaultConfig.animate = window.navigator.userAgent.indexOf('MSIE') === -1;
		flatpickr(".flatpickr");

		var configs = {
		    dateformat: {
		        dateFormat: "m/d/Y",
		    }
		}
	</script>
<script>

function save_release_date(){
	var form = $('#disposition-released-form');

	$.post('{{ URL::route("disposition.processStep", [$parcel]) }}', {
			'inputs' : form.serialize(),
			'step' : 'disposition-released',
			'release_date' : $('#release_date').val(),
			'_token' : '{{ csrf_token() }}'
		}, function(data) {
			if(!data['next']){
				UIkit.modal.alert(data['message']);
			}else{
				$('#disposition-tab-content').load('/dispositions/{{$parcel->id}}');
				step(data['next']);
				location.reload();
				UIkit.modal.alert(data['message']);
			}
		} );
}

function save_missing_date(){
	UIkit.modal.confirm("Are you sure you want to add this payment date?").then(function() {
        $.post('{{ URL::route("disposition.addmissingdate", [$parcel]) }}', {
			'_token' : '{{ csrf_token() }}',
			'date' : $('#missing_date').val(),
		}, function(data) {
			if(data['message']!=''){
				UIkit.modal.alert(data['message']);
				$('#disposition-tab-content').load('/dispositions/{{$parcel->id}}');
			}else{
				UIkit.modal.alert('Something went wrong.');
			}
		} );
    });
}


</script>
<style>
@media print
{
	#main-window {
		padding:30px;
	}
	#pl {
		width:30%;
	}
	#pr {
		width:70%;
	}
	input.uk-form-small {
	    width: 50px;
	}

}
</style>

@stop
