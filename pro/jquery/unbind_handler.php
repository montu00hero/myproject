<?php
?>


<html>
    <head>
        <script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
        <script>
    // unbind method used to remove the event associated the a particular element.       
    
    $(function(){
           var handler=function(){
               alert("handler1");
               $("#first").unbind("click",handler);
               
           }
           
           var handler2=function(){
               alert("handler2");
                $("#second").unbind("click",handler2);
           }
           
          $("#first").click( function(){
              $("#first").bind("click",handler); 
          });
            
           $("#second").click( function(){
               $("#second").bind("click",handler2);
           });
           
           });
           
        </script>
       
        
    </head>
    <body>
        <input id="first" type="button" value="submit1" />
        <input id="second" type="button" value="submit2" />
    </body>    
    
</html>