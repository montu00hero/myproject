<html>
    <head>
           <script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
        
        <script type="text/javascript">
            // .bind() method help in binding event to the elements. 
            
            
            $(function()
                {
                        $("a").bind("click", function() {
                                    $("#sid").val('Hello, world!');
                        });
                        
                        
                        
                        $("#id").bind("click",function(){
                            
                            $("a,input").css("color","green");
                            
                        });
                        
                        
                        
                                  
          $("#divArea").bind('mousemove',function(event){
              $(this).text(event.pageX+','+event.pageY);
              
          });     
             
                        
         $("#btns").click(function(){
            $("a").unbind("click");
             
         });          
                   
                   
            
                });
                
               
        
                
        </script>
    </head>
   <body>
       <a href="#"  >CLICK</a>
       <button id="id"  >change</button>
       
       <input id="sid" type="text" />
       
       
       
       <div id="divArea" style="background-color: silver; width: 100px; height: 100px;">
</div>

       <input id="btns" type="button" value="unbind event " />
   </body>

</html>