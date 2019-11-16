
		<script>
		resizeModal(60);
		</script>

		<form name="documentAdvanceForm" id="documentAdvanceForm" method="post">
			<div class="uk-container uk-container-center">
				<!--BEGIN top row-->
				<div class="uk-grid uk-grid-small">
					<div class="uk-width-8-10">You have uploaded some Advance Payment Documents. Please select one or more cost items.</div>
				</div>
				<div class="uk-grid ">
					<!--END second row-->		
					<div class="uk-width-1-1">
						<div class="uk-grid">
							<div class="uk-width-1-1 uk-margin-bottom">
								<div class="uk-width-1-1">
									<div style="min-height:3em;">
										<div class="uk-grid uk-grid-collapse">
											<div class="uk-width-1-10"><label>Advance Items</label></div>
											<div class="uk-width-9-10">
												@if($parcel->costItemsWithAdvance)
												<ul class="uk-nav">
													@foreach($parcel->costItemsWithAdvance as $advance)
													<li>
							                            <input name="advance-checkbox" id="advance-id-{{ $advance->id }}" value="{{ $advance->id }}" type="checkbox">
							                            <label for="advance-id-{{ $advance->id }}">
							                                @if($advance->expense_category){{$advance->expense_category->expense_category_name}}@endif | {{$advance->description}} | ${{$advance->amount}}
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
								<a class="uk-button uk-width-1-1 uk-button-primary" onclick="associateAdvance()"><i uk-icon="save"></i> SAVE</a>
							</div>
						</div>
					</div>
				</div>
			</div>
		</form>

	
	<script type="text/javascript">
	function associateAdvance() {
		var form = $('#documentAdvanceForm');
		var advanceArray = [];
        $("input:checkbox[name=advance-checkbox]:checked").each(function(){
            advanceArray.push($(this).val());
        });
        if(advanceArray.length === 0){
        	UIkit.modal.alert('You must select at least one cost item.');
        }else{
        	var documentids = "{{$documentids}}";
        	var documentids_array = documentids.split(',');
        	$.post('{{ URL::route("documentadvance.save", $parcel->id) }}', {
				'advances' : advanceArray,
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
