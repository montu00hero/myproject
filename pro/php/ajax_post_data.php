<?php
?>

<html>
    <head>
        <script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script> 
    </head>
    <body>
        
        <h6>See the result in Console or net for all</h6>
        
        <h6> .serialize() function used for form element </h6>
        
        <form name="myfrom"  action="javascript:void(0)" >
            <input name="student" placeholder="Enter Student Name" type="text" >
            <input name="rollno" type="text" placeholder="Enter Student Roll">
            <button type="submit">Submit</button>
        </form>    
        
        
        
        <script type="text/javascript">
              $(function(){
                  var str='Sam';
                  var str1=34;
                  $.ajax({
                      
                      url:'ajax_data_recieve_in_php.php',
                      data:'name='+str+'&roll='+str1,
                      type:'POST',   //Post Method
                      success:function(msg){
                          console.log(JSON.stringify(msg));  //JSON.stringify used to see the value 
                                                             //inside the object returned
                      },
                      error:function(xhr){}
                      
                  });
                 
              });
             
        </script> 
        
        
        
             <?php /* this script used for serialize() */ ?>
             <script type="text/javascript">
              $(function(){
                 $('[name="myfrom"]').submit(function(){
                        var str= $('[name="myfrom"]').serialize();
                        alert(str);
                        
                  $.ajax({
                      
                      url:'ajax_data_recieve_in_php.php',
                      data:str,
                      type:'POST',
                      success:function(msg){
                          console.log(JSON.stringify(msg));
                      },
                      error:function(){}
                      
                  });   
                     
                     
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
                      type:'GET',   //Get Method
                      success:function(msg){
                          console.log(JSON.stringify(msg));
                      },
                      error:function(){}
                      
                  });
                 
              });
                  
              
           
        
        
        </script>  
        
 
        
        
        
        
    </body>
</html>