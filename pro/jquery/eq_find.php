<html>
    <head>
        <script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
    </head>
    <body>
        <input id="finds" type="button" value="Find" />
        
        <br>
        <button>1</button>
        <button>2</button>
        <button>3</button>
        <button>4</button>
        
        
        <script>
            var i=0;
            $("#finds").click(function(){ 
                
                $("body").find("button").eq(i).css("color","red");
                  i++;
                
           });
          
        </script>
        
        
        
    </body>
</html>