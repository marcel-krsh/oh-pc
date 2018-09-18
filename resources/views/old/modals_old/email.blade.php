<script>
//resizeModal(95);
</script>
<div class="uk-container">
	<div class="uk-grid uk-grid-collapse open-communication-bottom-rules ">
		<div class="uk-width-1-1">
			<span class="communication-direction-text uk-margin-bottom">Email Detail sent to {{$message->recipient->name}}</span> &nbsp;&nbsp;&nbsp;
		</div>
	</div>
	
	<div class="uk-grid uk-grid-collapse open-communication-bottom-rules uk-margin-small-top uk-margin-bottom">
		<div class="uk-width-2-10 communication-type-and-who ">
			<span class=" communication-item-date-time">
				{{ date('F d, Y h:i', strtotime($message->created_at)) }}
			</span>
		</div>
	</div>
	<!-- Start content of communication -->
	<div class="uk-width-1-1"><!--used to be uk-width-9-10, but Linda changed it-->
		<div class="uk-grid">
			<div class="uk-width-1-1 uk-margin-bottom">
				{{$message->subject}}
			</div>
			<div class="uk-width-1-1 uk-margin-bottom">
				{!!$message->body!!}
			</div>		
		</div>
	</div>
</div>
