@extends('layouts.allita')

@section('content')
<div class="uk-grid">
   @foreach($logs as $log)
   <div class='log'>
     @if($log->getExtraProperty('EventType') == 'user')
        <h3>User Log Entry</h3>
        <p>From User: {{ $log->causer->email}}</p>
        <p>To User: {{ $log->subject->email}}</p>
        <?php 
        $userParams = $log->properties['userParams'];
        if(isset($userParams['changed'])) { 
        	$changed = userParams['changed'];
        	?>
        <p>Changed Fields:</p>
        <ul>
          @foreach($changed as $item=>$value)
          <li>{{$item}}- Newvalue {{$value[0]}}  OldValue: {{$value[1]}}</li>
          @endforeach
        </ul>
        <?php } ?>
       <?php
        if(isset($userParams['roles'])) { 
        	$roles = $userParams['roles'];
       	?>
        <p>Roles:</p>
        <ul>
          @foreach($roles as $item)
          <li>{{$item}}</li>
          @endforeach
        </ul>
        <?php } ?>
     @elseif($log->getExtraProperty('EventType') == 'entity')
        <h3>Entity Log Entry</h3><br>
     @elseif($log->getExtraProperty('EventType') == 'parcel')
        <h3>Parcel Log Entry</h3><br>
     @else
        <h3>Raw Log Entry</h3><br>
     @endif
   </div>
   @endforeach
</div>
@stop
