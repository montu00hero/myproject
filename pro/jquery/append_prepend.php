
<html>
    
    <head>
          <script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>

    </head>
    <body>
       <a href="javascript:void(0);" onclick="$('#olTestList1').append('<li>Appended item</li>').css('color','green');">Append</a>   
       <a href="javascript:void(0);" onclick="$('#olTestList1').prepend('<li>Prepended item</li>').css('color','red');">Prepend</a>
      
      <ol id="olTestList1">
        <li>Existing item</li>
        <li>Existing item</li>
     </ol>        
    </body>    
</html>

