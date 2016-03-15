<?php


?>


<!DOCTYPE>
<html>
    <head>
        
       <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <title>Google Map</title>
       
        <style>
            #map{
                height: 50%;
                width:  50%;
                border-radius: 30px;
                border-color: red;
                margin-left: 30%;
                margin-right: 30%;
                margin-top: 5%;
                background-color: gray;
            }
            
        </style>
        
    </head>
    <body>
        
        <input type="text" placeholder="lat" id="lat">
        <input type="text" placeholder="long" id="long">
        <button type="submit" onclick="loadScript()" >submit</button>
        
        <div id="map"></div>
    </body>
    <script>
       
        function loadScript() {
      var script = document.createElement("script");
      script.type = "text/javascript";
      script.src = "https://maps.googleapis.com/maps/api/js?callback=initMap";
      script.async=true;
      document.body.appendChild(script);
    }  
       
       
      function initMap() {
         var lat= $('#lat').val(); 
         var lng= $('#long').val(); 
        // alert(lat+'---'+lng);
        var mapDiv = document.getElementById('map');
        
        if(lat != '' && lng !='' ){
        var map = new google.maps.Map(mapDiv, {
          center: {lat:parseInt(lat), lng:parseInt(lng)},
          zoom: 8
        });
       }
       else{
          var map = new google.maps.Map(mapDiv, {
          center: {lat: 44.540, lng: -78.546},
          zoom: 8
        });
       }
      }
      
      
   
</script>
  <script   src="//ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
    
<!--    <script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?callback=initMap"
        async defer></script>-->
</html>






