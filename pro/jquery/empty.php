<html>
    <head>
        <script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>

        
    </head>
    <body>
        <label>
            this is empty() function test's
        </label>
        <button id="btn" >click to empty the label</button>
        <button id="ctn" >click to empty the label and add new text</button>
        
        
        <script>
           $("#btn").click(function(){ 
               $('label').empty();
           });
           
           
           
           $("#ctn").click(function(){
               $('label').empty().text("1857'THE REVENGE'").css('color','red');
               
           });
        </script>
        
    </body>
</html>