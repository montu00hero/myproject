
<html>
    <head>   
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
    </head>
    <body>
        
        <input class="aa" type="text" name="ac" />
        <input class="aa" type="text" name="bc" /> 
        <input type="button" name="btn" value="click" />
   
        
        
        
        
        
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
             
          });
        
        </script>   
    </body>
</html>

