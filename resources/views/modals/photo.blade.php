<div class="modal-photo-details">
  <div class="" uk-overflow-auto> 
  	<div uk-grid>
  		<div class="uk-width-1-2 uk-margin-medium-top" style="text-align: center">
  			<img src="{{$photo['url']}}" uk-img>
  			{{$photo['filename']}}
  		</div>
  		<div class="uk-width-1-2 uk-margin-medium-top" style="max-height: 500px; overflow-y: auto;">
  			@if(count($photo['comments']))

  			<h2>Comments <i class="a-comment-plus use-hand-cursor" onclick="addChildItem({{$photo['id']}}, 'comment','photo', 3)"></i></h2>
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
	    				<p style="color:#939598">{{$comment['date']}}: PIC#{{$comment['ref']}}<br />
	    					By {{$comment['auditor']['name']}}</p>
	    				<p>{{$comment['comment']}}</p>
	    			</div>
	    			@if($comment['comments'])
	    			@foreach($comment['comments'] as $subcomment)
	    			
					<div class="uk-width-1-1 uk-padding-remove-right " style="margin-top:10px">
						<div class="uk-width-1-1 uk-display-block uk-padding-remove inspec-tools-tab-finding-description">
		    				<p style="color:#939598">{{formatDate($subcomment->created_at)}}<br />
		    					By {{$subcomment->user->full_name()}}</p>
		    				<p>{{$subcomment->comment}}</p>
		    			</div>
		    		</div>
			    	
	    			@endforeach
	    			@endif
	    			<div class="uk-width-1-1 uk-display-block uk-padding-remove inspec-tools-tab-finding-description">
	    				<div class="inspec-tools-tab-finding-actions">
						    <button class="uk-button uk-link" onclick="addChildItem({{$comment['id']}}, 'subcommentfromphoto','comment', 3)"><i class="a-comment-plus"></i> REPLY</button>
	    				</div>
	    			</div>
	    		</div>
	    	</div>
  			@endforeach
  			@else
  			<h2>No comments yet. Click to add a comment: <i class="a-comment-plus use-hand-cursor" onclick="addChildItem({{$photo['id']}}, 'comment','photo', 3)"></i></h2>
  			@endif
  		</div>
  	</div>
  </div>
 </div>