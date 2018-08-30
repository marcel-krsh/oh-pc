@extends('layouts.allita')

@section('content')

	INDEX.BLADE

	<script>

	$('#{{$tab}}').trigger("click");
	$('#{{$tab}}').addClass('uk-active');

	</script>
		
@stop
