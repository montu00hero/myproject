<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>

<html>
    <head>
        <title>Json</title>   
    </head>
    <body>
        <form id="form1" action="javascript:void(0)" >
            <input type="text" name="data1" value="1" />
            <input type="text" name="data2" value="2" />
            <input type="submit" value="submit"/>
        </form>
        <div>
            <input id="output" type="text" />
        </div>
        
    </body>
</html>


<script>
$(document).ready(function(){
  $('#form1').submit(function(){
   var str = $("#form1").serialize();
       
       $.ajax({
           type:'POST', 
            url:'jsonTestPageTwo.php',
             data:str,
             dataType:'json',
            success:function(msg){
               // alert(msg.a);
                
              // var j=JSON.parse(msg); 
             // var j = JSON.parse(msg);    
                // alert(j);
               $('#output').val(msg.a+','+msg.b+','+msg.c+','+msg.d+','+msg.e+','+msg.f);
                
                
            },
            error:function(){
                
            }
       });
       });
});




</script>