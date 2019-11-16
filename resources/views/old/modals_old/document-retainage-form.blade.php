
		<script>
		resizeModal(60);
		</script>

		<form name="documentRetainageForm" id="documentRetainageForm" method="post">
			<div class="uk-container uk-container-center">
				<!--BEGIN top row-->
				<div class="uk-grid uk-grid-small">
					<div class="uk-width-8-10">You have uploaded some Retainage Payment Documents. Please select one or more retainages.</div>
				</div>
				<div class="uk-grid ">
					<!--END second row-->		
					<div class="uk-width-1-1">
						<div class="uk-grid">
							<div class="uk-width-1-1 uk-margin-bottom">
								<div class="uk-width-1-1">
									<div style="min-height:3em;">
										<div class="uk-grid uk-grid-collapse">
											<div class="uk-width-1-10"><label>Retainages</label></div>
											<div class="uk-width-9-10">
												@if($parcel->retainages)
												<ul class="uk-nav">
													@foreach($parcel->retainages as $retainage)
													<li>
							                            <input name="retainage-checkbox" id="retainage-id-{{ $retainage->id }}" value="{{ $retainage->id }}" type="checkbox">
							                            <label for="retainage-id-{{ $retainage->id }}">
							                                @if($retainage->cost_item){{$retainage->cost_item->description}}@endif | ${{$retainage->retainage_amount}}
							                            </label>
							                        </li>
							                        @endforeach
							                    </ul>
												@endif
											</div>
										</div>
									</div>
								</div>
							</div>
							<div class="uk-width-1-2">
							</div>
							<div class="uk-width-1-4">
								<a class="uk-button uk-button-default uk-width-1-1" onclick="dynamicModalClose()"><i uk-icon="times-circle"></i> CANCEL</a>
							</div>
							<div class="uk-width-1-4 ">
								<a class="uk-button uk-width-1-1 uk-button-primary" onclick="associateRetainage()"><i uk-icon="save"></i> SAVE</a>
							</div>
						</div>
					</div>
				</div>
			</div>
		</form>
	
	
	<script type="text/javascript">
	function associateRetainage() {
		var form = $('#documentRetainageForm');
		var retainageArray = [];
        $("input:checkbox[name=retainage-checkbox]:checked").each(function(){
            retainageArray.push($(this).val());
        });
        if(retainageArray.length === 0){
        	UIkit.modal.alert('You must select at least one retainage.');
        }else{
        	var documentids = "{{$documentids}}";
        	var documentids_array = documentids.split(',');
        	$.post('{{ URL::route("documentretainage.save", $parcel->id) }}', {
				'retainages' : retainageArray,
				'documentids' : documentids_array,
	            '_token' : '{{ csrf_token() }}'
	            }, function(data) {
	                UIkit.modal.prompt("I uploaded and categorized the document(s) accordingly. Please add your comment for the history record.",'',function(val){
                        $.post('{{ URL::route("documents.uploadComment", $parcel->id) }}', {
                            'postvars' : documentids,
                            'comment' : val,
                            '_token' : '{{ csrf_token() }}'
                        }, function(data) {
                            if(data!='1'){ 
                                UIkit.modal.alert(data);
                            } else {
                                UIkit.modal.alert('Your comment has been saved.');                               loadParcelSubTab('documents',{{$parcel->id}});
                            }
                        });
                    });
			} );
		        
			dynamicModalClose();
        }

	}	
	</script>