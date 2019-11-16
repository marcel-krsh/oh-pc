@extends('modals.container')

@section('content')
	


	<h2>Success</h2>

	<p>Import id: {{ $import->id }}</p>
	<p>Inserted {{ $count_inserted }} rows</p>
	<p>Updated {{ $count_updated }} rows</p>

@stop