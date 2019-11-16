@extends('layouts.allita')

@section('content')
<div class="uk-grid">
	<div class="uk-alert uk-alert-{{$type}} uk-align-center uk-margin-top">{{$error}}</div>

</div>
@if(strlen($message)>0)
		<script>
			UIkit.modal.alert('@php echo $message; @endphp');
		</script>
@endif
@stop