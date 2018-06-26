@extends('layout')

@section('header')
Parcel Number {{$parcel->parcelNumber}}
@stop
@section('names')
   
         <a href="/parcels/{{ $parcel->id }}">{{ $parcel->parcelNumber }}</a>
    
@stop