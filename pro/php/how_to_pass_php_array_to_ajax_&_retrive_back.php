<?php

$data_fare_rule=json_encode($flight);  // here $flight contain multi dim. array.


?>
<a onclick= 'return farerule(<?php echo $data_fare_rule ; ?>)' >Fare Rule</a>
<script>
           function farerule(data_fare_rule)
           {
              var str=data_fare_rule;
           //  alert(JSON.stringify(data_fare_rule));
               $.ajax({

                 url:'<?php echo $path;?>/flight_includes/Amadeus/farerules.php',
                 type:'POST',
                 data:'req='+(JSON.stringify(str)),   // JSON.stringify is the trick to pass 
                                                   // array in readable format  
                 success:function(msg){
                       
                   //alert(msg);

                   $('#res_msg').html(msg);

                 },
                 error:function(){} 


               });


           }



</script>

<?php 
   
 //accessing other pages

   print_r(json_decode($_POST['req']));
exit;


?>