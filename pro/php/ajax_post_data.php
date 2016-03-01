<?php
?>

<html>
    <head>
        <script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script> 
    </head>
    <body>
        
        <h6>See the result in Console</h6>
        
        <script type="text/javascript">
              $(function(){
                  var str='Sam';
                  var str1=34;
                  $.ajax({
                      
                      url:'ajax_data_recieve_in_php.php',
                      data:'name='+str+'&roll='+str1,
                      type:'POST',
                      success:function(msg){
                          console.log(JSON.stringify(msg));  //JSON.stringify used to see the value 
                                                             //inside the object returned
                      },
                      error:function(xhr){}
                      
                  });
                 
              });
             
        </script> 
        
        
        
        <script type="text/javascript">
              $(function(){
                  var str='Sam';
                  var str1=34;
                  $.ajax({
                      
                      url:'ajax_data_recieve_in_php.php?name='+str+'&roll='+str1,
                      //data:'name='+str+'&roll='+str1,
                      type:'POST',
                      success:function(msg){
                          console.log(JSON.stringify(msg));
                      },
                      error:function(){}
                      
                  });
                 
              });
                  
              
           
        
        
        </script>  
        
        
            <script type="text/javascript">
              $(function(){
                  var str='Sam';
                  var str1=34;
                  $.ajax({
                      
                      url:'ajax_data_recieve_in_php.php',
                      data:'name='+str+'&roll='+str1,
                      type:'GET',
                      success:function(msg){
                          console.log(JSON.stringify(msg));
                      },
                      error:function(){}
                      
                  });
                 
              });
                  
              
           
        
        
        </script>  
        
        
    </body>
</html>