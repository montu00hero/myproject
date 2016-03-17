<?php
echo"https://developers.google.com/maps/documentation/javascript/examples/marker-simple";

?>


<!DOCTYPE>
<html>
    <head>
        
       <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <title>Map Marker</title>
       
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
        <br><br><br>
        <input type="text" placeholder="lat" id="lat">
        <input type="text" placeholder="long" id="long">
        <button type="submit" onclick="loadScript()" >submit</button>
        
        <div id="map"></div>
    </body>
    <script>
       
   function loadScript()
      {
        var script = document.createElement("script");
        script.type = "text/javascript";
//        script.src = "https://maps.googleapis.com/maps/api/js?callback=initMap";
        script.src = "https://maps.googleapis.com/maps/api/js?key=AIzaSyBjy1ZCvrG1zG4aJWCwI7AuF0XTbdSLUOk&callback=initMap";
        script.async=true;
        document.body.appendChild(script);
      }  
       
       
      function initMap()
         {
         var lat= $('#lat').val(); 
         var lng= $('#long').val(); 
        // alert(lat+'---'+lng);
        
        var myLatLng='';
        
        if(lat != '' && lng !='' )
           {
            myLatLng = {lat:parseInt(lat), lng:parseInt(lng)};
           }
       else{
            myLatLng = {lat: -25.363, lng: 131.044};
            }
       
       
       var mapDiv = document.getElementById('map');
        var map = new google.maps.Map(mapDiv, {
          center: myLatLng,
          zoom: 8,
          mapTypeId: google.maps.MapTypeId.TERRAIN
        });
       
        var marker = new google.maps.Marker({
           position: myLatLng,
           map: map,
           title: 'Hello World!'
         });
       }
      
      
   
</script>
  <script   src="//ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
    
<!--    <script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?callback=initMap"
        async defer></script>-->
</html>






