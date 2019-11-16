<!-- THIS IS FOR DISPLAYING HISTORY!!! -->
<style>
      header{display:none !important;}
    </style>

@extends('layouts.app')
 
@section('content')

 

<div class="container">
<H1>New stuff is awesome</h1>
<div class="row">
    <div class="d-flex align-items-end">
        <div class="col-md-3"><img src="/img/st_logo.svg" alt="Smooth Transitions logo" width="218" height="80"></div>
        <div class="col-md-6"><h3 style="size:.8 rem; font-weight: normal;margin-bottom:0;">We customize our offerings by region.</h3> <h2 style="margin-bottom:0;">Please select your region.</h2></div>
        <div class="col-md-3"><form>
    <input id="location-filter" type="text" placeholder="City/State or zip" name="location" style="font-size:.9rem;float:left; width:9.5rem;">
    <button type="submit" style="padding:0 .5rem;font-size:.9rem;float:right;">go</button>
</form>
</div>
</div>
</div>
</div> 
<div class="row">
  <div class="d-flex">
    <div class="col-md-9">
      <div id="map" style="height:85vh; width:100%; margin-top:1em;"></div>
    </div>
    <div class="col-md-3">
        <ul>
          @foreach($companies as $company)
            @if(!is_null($company->name) && $company->name != '')
              @php $currentCity = ''; $currentCounty = ''; @endphp
              <li class="company-list-item @foreach($company->territories as $t) @if($currentCity != $t->City) {{$t->City}} @php $currentCity = $t->City; @endphp @endif @if($currentCounty != $t->County) {{$t->County}} @php $currentCounty = $t->County; @endphp @endif {{$t->zip_territory}} @endforeach">{{$company->name}}</li>
            @endIf
            
          @endforeach
      </ul>
    </div>
  </div>
</div>
   
<script>

  // Initialize and add the map
  // Brian how can we make the locations with consist of lat and lng data driven?
  function initMap() {
    var myLatLng = {
      lat: 35.9049874,
      lng: -78.1586372
    };

    @foreach($companies as $company)
      @if(!is_null($company->name) && $company->name != '' && count($company->territories)>0 && $company->territories[0]->lat !='' && $company->territories[0]->lng !='')
        var {{str_replace(',','',str_replace('.','',str_replace('&','',str_replace('&amp;','',str_replace(':','',str_replace('-','',str_replace('/','',str_replace(' ','',$company->name))))))))}} = {
          @php //dd($company->territories[0]); @endphp
          lat: {{$company->territories[0]->lat}},
          lng: {{$company->territories[0]->lng}}
        };
      @endif
    @endforeach
    //  var atlanta = {
    //   lat: 33.826317,
    //   lng: -84.3879382
    // };
    //   var littleton = {
    //   lat: 39.6166078,
    //   lng: -105.0696257,
    // };

    var map = new google.maps.Map(document.getElementById('map'), {
      zoom: 4,
      center: myLatLng
    });
    @foreach($companies as $company)
      @if(!is_null($company->name) && $company->name != '' && count($company->territories)>0 && $company->territories[0]->lat !='' && $company->territories[0]->lng !='')
        var marker = new google.maps.Marker({
          position: {{str_replace(',','',str_replace('.','',str_replace('&','',str_replace('&amp;','',str_replace(':','',str_replace('-','',str_replace('/','',str_replace(' ','',$company->name))))))))}},
          map: map,
          title: '{{addslashes($company->name)}}'
        });
      @endif
    @endforeach
    
    // var marker = new google.maps.Marker({
    //   position: atlanta, //position has to be set in variable, can't feed lat lng directly to marker
    //   map: map,
    //   title: 'Atlanta'
    // });

    // var marker = new google.maps.Marker({
    //   position: littleton,
    //   map: map,
    //   title: 'Colorado'
    // });

  }
</script>
<script async defer
    src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAAaNWSP3Vt3T7r86S-T1NdvPehLlkqfEk&callback=initMap">
</script>






@endsection