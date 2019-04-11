@extends('layouts.simpleAllita')

@section('content')
<div class="uk-grid">
	<div class="uk-alert uk-alert-{{$type}} uk-align-center uk-margin-top">{{$error}}</div>

</div>
<div class="uk-grid uk-align-center">
	<a  class="uk-align-center" href="{{ url('login') }}"> Go to Login</a>
</div>
@if(strlen($message)>0)
		<script>
			var message = '{!! $message !!}';
			UIkit.modal.alert(message);
		</script>
@endif
@stop
