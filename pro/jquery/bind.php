<html>
    <head>
           <script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
        
        <script type="text/javascript">
            $(function()
                {
                        $("a").bind("click", function() {
                                    $("#sid").val('Hello, world!');
                        });
                        
                        
                        
                        $("#id").bind("click",function(){
                            
                            $("a,input").css("color","green");
                            
                        });
                });
        </script>
    </head>
   <body>
       <a href="#"  >CLICK</a>
       <button id="id"  >change</button>
       
       <input id="sid" type="text" />
       
   </body>

</html>