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
        <label>Choose</label>  
        <input type="radio" name="gr" id="gr1" value="10" checked>
            <input type="radio" name="gr" id="gr2" value="100">
            <div>
                <select id="sel" name="sele">
                    <option value="10" selected >Item1</option>
                    <option value="9">Item2</option>
                    <option value="8">Item3</option>
                </select>
            </div>
    </body>
</html>

<script>
$(function(){
          // var ck=$('#ckd').is(':checked');
              $('#ckd').prop('checked','true')
         // alert(ck);    
               var s= $("input[type='checkbox']:checked").val();
               console.log('Checkbox Value:'+s);  
            // document.write('Checkbox Value:'+s);    
    //-------------------------Radio------------------------//         
             var k=$("input[type='radio']:checked").val();
             
             console.log('radio Value:'+k);
            // document.write('radio value'+k);
    //--------------------------select-box-------------------------//         
            var h=$("#sel option:selected").val(); 
             console.log("select-box:" +h);
             
           // var g=$('#sel').prop('option','selected');
            // var g=$('#sel').is('option:selected');
            
        $('#sel').on('click',function(){
         
        if($('#sel option:selected').length > 0) //for checking whether a option of selectbox is selected or not.
         {
             var R=$('#sel').val() 
              console.log("select-box:" +R);
              
             var g=$('#sel option:selected').val();
              console.log("select-box:" +g); 
            //alert(g);
             
         }
            
        });
                 
                 
    //--------------------------------------------------//          
     //    alert(s);
        //$('#ckd').attr('checked',false);
     
     $('#ckd').on('click',function(){
        // alert($('#ckd').is(':checked')); //it will return true /false
       alert($('#ckd').prop('checked')); //it will return true /false
       //alert($('#ckd').attr('checked'));  //its not returing any vale
     }); 
});
</script>