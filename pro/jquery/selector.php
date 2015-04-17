
<html>
    <head>   
   <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
  
    </head>
    <body>
        <div>
            <input type="text" name="ll" />
            
        </div>
        <input class="aa" type="text" name="ac" />
        <input class="aa" type="text" name="bc" /> 
        <input type="button" name="btn" value="click" />
         <input type="button" name="btn1" value="click1" />  
        
        
        
        
        
     <script>
        
        $(function(){
          //  $(function(){ });    is equal to    $(document).ready(function(){ });
 
          $("input[name='ac']").css("background-color","orange");
          $("[name='bc']").css("background-color","green"); 
          $("[name='btn']").css("background-color","yellow");    
          
          
  
          });
        
          $("input[value='click']").click(function(){
             $("input[value='click']").removeAttr("style");
             $(".aa").removeAttr("style");
           
          $("input[name='bc']").append('The .append() method inserts the specified content as the last child of each element in the jQuery collection (To insert it as the first child, use .prepend())');
           $("div").append('<input type="button" name="inp" onclick="h(this);" />');  
          });
        
            
           $("input[name='btn1']").on('click',function(){
              $("input[name='inp']").remove();
               
           }); 
            function h(aa){
            
               //alert(aa);
                 // $(aa).remove();
            $(aa).css("background-color","green");
           }
        
        
        </script>   
    </body>
</html>

