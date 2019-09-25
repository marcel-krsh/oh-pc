	<div class="uk-modal-header">
		<div class="uk-modal-title uk-remove-margin"> <i class=" a-circle-plus"></i> FINDING #{{$from->id}} <small>ADD PHOTOS</small></div>
		
	</div>
	<form id="add-photo-finding-form" method="post">
		<input type="hidden" name="id" value="{{$from->id}}">
		<input type="hidden" name="fromtype" value="{{$fromtype}}">
		<input type="hidden" name="type" value="photo">
	<div class="form-default-followup uk-margin-top" uk-grid>

        @if(!is_null($project))
        <div class="uk-width-1-1">
        	
				<h4 class="uk-text-primary uk-text-uppercase">Upload new photos</h4>
				<div class="uk-form-row">
					<input class="uk-input uk-width-1-1" id="comment" type="text" name="comment" placeholder="Enter a brief note about those photos">
				</div>
				<div class="uk-form-row" id="list-item-upload-box">
					<div class="js-upload uk-placeholder uk-text-center">
						<span class="a-higher"></span>
						<span class="uk-text-middle"> Please upload your photos by dropping them here or</span>
						<div uk-form-custom>
							<input type="file" multiple accept="image/*;capture=camera">
							<span class="uk-link uk-text-primary">by browsing and selecting them here.</span>
						</div>
					</div>
					<progress id="js-progressbar" class="uk-progress" value="0" max="100" hidden></progress>
					<script>
						$(function(){
							var bar = document.getElementById('js-progressbar');
							settings    = {
								url: '{{ URL::route("photos.upload", $project->id) }}',
								multiple: true,
								allow : '*.(jpg|gif|png|pdf|doc|docx|xls|xlsx)',
								beforeSend: function () {
								},
			          			beforeAll: function (settings) {
									settings.params.comment = $("input[name=local-comment]").val();
									settings.params._token = '{{ csrf_token() }}';
									settings.params.finding_id = '{{$from->id}}';
								},
								load: function () {
								},
								error: function () {
								},
								complete: function (response) {
									var data = jQuery.parseJSON(response.response);

									setTimeout(function () {
										bar.setAttribute('hidden', 'hidden');
									}, 250);

						            if(data=='0'){
					            		UIkit.modal.alert("There was a problem getting the photos' information.",{stack: true});
					            	} else {
					            		var photo_info_array = data;

						            	for (var i = 0; i < photo_info_array.length; i++) {
					            			var pid = photo_info_array[i]['id'];
					            			var pname = photo_info_array[i]['filename'];

					            			newinput = '<li>'+
						            			'<input name="local_photos[]" id="list-photo-id-local-'+pid+'" value="'+pid+'" type="checkbox" checked  class="uk-checkbox" >'+
						            			'<label for="local-photo-id-'+pid+'">'+
						            			'    ' +pname+
						            			'</label>'+
					            				'</li>';
					            			$("#added-photos").append(newinput);
				            			}
					            	}
								},
			          			loadStart: function (e) {
									bar.removeAttribute('hidden');
									bar.max = e.total;
									bar.value = e.loaded;
								},
								progress: function (e) {
									bar.max = e.total;
									bar.value = e.loaded;
								},
								loadEnd: function (e) {
									bar.max = e.total;
									bar.value = e.loaded;
								},
								completeAll: function (response) {

						        }
						    };
						    var select = UIkit.upload('.js-upload', settings);
						});
					</script>
			    </div>

				<div class="uk-form-row">
					<ul id="added-photos" style="list-style-type: none;"></ul>
				</div>
        </div>
        @endif
        
        <div class="uk-width-1-1">

            
		    <hr class="dashed-hr uk-margin-bottom">
		    <div class="uk-margin-bottom" uk-grid>
		    	<!-- <div class="uk-width-1-2">
		    		<button onclick="saveBoilerplace();"><i class="a-file-text"></i> Save as new boilerplate for this finding</button>
		    	</div> -->
		    	
		    	<div class="uk-width-1-2">
		    		<a class="uk-button uk-button-primary uk-width-1-1" onclick="dynamicModalClose(2);"><i class="a-circle-cross"></i> CANCEL</a>
		    	</div>
		    	<div class="uk-width-1-2">
		    		<button class="uk-button uk-button-success uk-width-1-1" onclick="saveFindingPhoto(event);"><i class="a-file-pen"></i> SAVE PHOTOS</button>
		    	</div>
		    </div>
        </div>
    </div>
	</form>
	<script type="text/javascript">

	$( document ).ready(function() {
		$('.photos-list').slideToggle();
    	$('#add-photos-button').toggle();
    	$('#done-adding-photos-button').toggle();
	});

	function saveFindingPhoto(e){
		e.preventDefault();
		var form = $('#add-photo-finding-form');

		var no_alert = 1;
    	var selected_photos_array = [];
    	$("input[name='local_photos[]']:checked").each(function (){
    		selected_photos_array.push(parseInt($(this).val()));
    	});
    	if(selected_photos_array.length === 0){
    		no_alert = 0;
    		UIkit.modal.alert('You must select a photo.',{stack: true});
    	}

    	if(no_alert==1){
    		$.post("/findings/reply", {
	            'inputs' : form.serialize(),
	            '_token' : '{{ csrf_token() }}'
	        }, function(data) {
	            if(data!=1){ 
	                UIkit.modal.alert(data,{stack: true});
	            } else {
	                dynamicModalClose(2);
		            UIkit.notification('<span uk-icon="icon: check"></span> Photo(s) Saved', {pos:'top-right', timeout:1000, status:'success'});
		            $('#finding-modal-audit-stream-refresh').trigger('click');
	            
	            }
	        } );
    	}
		
	}
  </script>
