<?php
echo "waiting";
?>

<form id="target" name="forms" action="<?php echo WEB_URL.'/exp/exp2';?>" method="POST"></form>






<script>
$(function(){
    
  
   
 function shows(){
     $( "#target" ).submit(); 
   };
 
 window.setTimeout( shows, 5000 );
   
});






</script>