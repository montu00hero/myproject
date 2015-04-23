
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
        
        <div>
           <select id="first" multiple>
                <option >select</option>
                <option>11</option>
                <option>12</option>
                <option>23</option>
                <option>24</option>
                <option>25</option>
                <option>36</option>
            </select>
        </div>
        <div>
            <button onclick="down()">shift down</button>
            <button onclick="up()">shift up</button>
            <button onclick="both()">shift up</button>
            
        </div>
        <div>
            <select id="second" multiple>
                <option >select</option>
                <option>1</option>
                <option>2</option>
                <option>3</option>
                <option>4</option>
                <option>5</option>
                <option>6</option>
            </select>
        </div>
        <div>
            <select id="third" multiple>
                <option >select</option>
                <option>100</option>
                <option>200</option>
                <option>300</option>
                <option>400</option>
                <option>500</option>
                <option>600</option>
            </select>
        </div>
        
        
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
        
           $("input[name='ll']").on('input',function(){
              alert("input");
               
           }); 
        
        
         function h(aa){
            
               //alert(aa);
                 // $(aa).remove();
            $(aa).css("background-color","green");
           }
        
        /* selectors   */
        
        
        function down()
        {
            $("#second").append($('#first').children(':selected'));
            
        }
        
        function up()
        {
             $("#first").append($('#second').children(':selected'));
            
        }
        
        function both()
        {
            $("#first").append($('#third').children(':selected'));
            $("#second").append($('#third').children(':selected'));
            
        }
        
        
        </script>   
    </body>
</html>

