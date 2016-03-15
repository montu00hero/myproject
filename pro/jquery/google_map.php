<?php
?>


<!DOCTYPE>
<html>
    <head>
        <title>Google Map</title>
       
        <style>
            #map{
                height: 400px;
                width:  400px;
                border-radius: 30px;
                border-color: black;
            }
            
        </style>
        
    </head>
    <body>
        <div id="map"></div>
    </body>
    <script>
      function initMap() {
        var mapDiv = document.getElementById('map');
        var map = new google.maps.Map(mapDiv, {
          center: {lat: 44.540, lng: -78.546},
          zoom: 8
        });
      }

    </script>
    <script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?callback=initMap"
        async defer></script>
</html>