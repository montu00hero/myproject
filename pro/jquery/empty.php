<html>
    <head>
        <script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>

        
    </head>
    <body>
        <label>
            this is empty() function test's
        </label>
        <div id="divTestArea1">
            <p>
                remove() method, remove the entire div, including any child elements.
            </p>
            
        </div>
        
        
        <button id="btn" >click to empty the label</button>
        <button id="ctn" >click to empty the label and add new text</button>
        <a href="javascript:void(0);" onclick="$('#divTestArea1').remove();">remove() div</a>

        
        <script>
            //The  empty() method , removing all the child elements.
            
            //remove() method, remove the entire div, including any child elements.
            
           $("#btn").click(function(){ 
               $('label').empty();
           });
           
           
           
           $("#ctn").click(function(){
               $('label').empty().text("1857'THE REVENGE'").css('color','red');
               
           });
        </script>
        
    </body>
</html>