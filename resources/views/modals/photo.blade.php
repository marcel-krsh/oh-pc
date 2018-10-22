<div class="modal-photo-details">
  <div class="" uk-overflow-auto> 
  	<div uk-grid>
  		<div class="uk-width-1-2 uk-margin-medium-top">
  			<img src="{{$photo['url']}}" uk-img>
  		</div>
  		<div class="uk-width-1-2 uk-margin-medium-top">
  			<h2>Comments</h2>
  			@foreach($photo['comments'] as $comment)
  			<div class="uk-grid-match" uk-grid>
				<div class="uk-width-1-4 uk-padding-remove-left uk-first-column">
					<div class="uk-display-block">
	    				<i class="{{$comment['icon']}}"></i><br>
	    				<small class="auditinfo">AUDIT {{$comment['audit']}}</small><br />
	    				<small class="findinginfo">FND#: {{$comment['findingid']}}</small>
	    			</div>
	    			<div class="uk-display-block" style="margin: 15px 0;">
	    				
					</div>
				</div>
				<div class="uk-width-3-4 uk-padding-remove-right ">
					<div class="uk-width-1-1 uk-display-block uk-padding-remove inspec-tools-tab-finding-description">
	    				<p>{{$comment['date']}}: PIC#{{$comment['ref']}}<br />
	    					By {{$comment['auditor']['name']}}</p>
	    				<p>{{$comment['comment']}}</p>
	    				<div class="inspec-tools-tab-finding-actions">
						    <button class="uk-button uk-link"><i class="a-comment-plus"></i> REPLY</button>
	    				</div>
	    				<div class="inspec-tools-tab-finding-top-actions">
	    					<div uk-drop="mode: click" style="min-width: 315px;">
						        <div class="uk-card uk-card-body uk-card-default uk-card-small">
						    	 	<div class="uk-drop-grid uk-child-width-1-4" uk-grid>
						    	 		<div class="icon-circle use-hand-cursor" onclick="addChildItem(123, 'followup')"><i class="a-bell-plus"></i></div>
						    	 		<div class="icon-circle use-hand-cursor"  onclick="addChildItem(123, 'comment')"><i class="a-comment-plus"></i></div>
						    	 		<div class="icon-circle use-hand-cursor"  onclick="addChildItem(123, 'document')"><i class="a-file-plus"></i></div>
						    	 		<div class="icon-circle use-hand-cursor"  onclick="addChildItem(123, 'photo')"><i class="a-picture"></i></div>
						    	 	</div>
						        </div>
						    </div>
	    				</div>
	    			</div>
	    		</div>
	    	</div>
  			@endforeach
  		</div>
  	</div>
  </div>
 </div>