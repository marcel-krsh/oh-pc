<html>
  <head>
    <meta name="viewport" content="initial-scale=1.0, user-scalable=no">
    <meta charset="utf-8">
    <title>@if($request->sdo == 1)Save the Dream Ohio Parcels @else Neighborhood Initiative Program Parcels @endif</title>
    <style>
      /* Always set the map height explicitly to define the size of the div
       * element that contains the map. */
      #map {
        height: 100%;
      }
      /* Optional: Makes the sample page fill the window. */
      html, body {
        height: 100%;
        margin: 0;
        padding: 0;
      }
    </style>
  </head>
  <body>
    <div id="map"></div>
    <script>

      function initMap() {

        var map = new google.maps.Map(document.getElementById('map'), {
          zoom: 8,
          center: {lat: 40.4057533, lng: -82.7450926}
        });

        var layer = new google.maps.FusionTablesLayer({
            query: {
              select: '\'Geocodable address\'',
              from: '1Hky8qXEOcJQmTbndHmrHWo8-yhRBLV3U31HwEg'
            },
            styles: [{
              polygonOptions: {
                fillColor: '#00FF00',
                fillOpacity: 0.1
              }
            }]
          });
          layer.setMap(map);

        // Create an array of alphabetical characters used to label the markers.
        var labels = '';

        // Add some markers to the map.
        // Note: The code uses the JavaScript Array.prototype.map() method to
        // create an array of markers based on a given "locations" array.
        // The map() method here has nothing to do with the Google Maps API.
        var markers = locations.map(function(location, i) {
          return new google.maps.Marker({
            position: location,
            label: labels[i % labels.length]
          });
        });

        // Add a marker clusterer to manage the markers.
        var markerCluster = new MarkerClusterer(map, markers,
            {imagePath: 'https://developers.google.com/maps/documentation/javascript/examples/markerclusterer/m'});
      }
      var locations = [
        @foreach($points as $d)
            @if(strlen($d->latitude > 0))
                {lat: {{$d->latitude}}, lng: {{$d->longitude}}},
            @endif
        @endforeach
      ]
    </script>
    <script src="/js/components/markers.js{{ asset_version() }}">
    </script>
    <script async defer
    src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAMB5fHlZyAet2TnsuU3bBX7miYyDMBLSg&callback=initMap">
    </script>
    </body>
    </html>
