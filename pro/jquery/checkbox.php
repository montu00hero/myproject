<?php
?>


<html>
    <title>Test</title>
    <head>
        <script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
    </head>
    <body>
    <label>select
        <input name="check" type="checkbox" id="ckd" value="1">
    </label>
    </body>
</html>

<script>
$(function(){
          // var ck=$('#ckd').is(':checked');
              $('#ckd').prop('checked','true')
        //$('#ckd').attr('checked',false);
     
     $('#ckd').on('click',function(){
        // alert($('#ckd').is(':checked')); //it will return true /false
       alert($('#ckd').prop('checked')); //it will return true /false
       //alert($('#ckd').attr('checked'));  //its not returing any vale
     }); 
});
</script>