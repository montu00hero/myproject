<html>
    <body>
        <div class="row">
            <div class="containts">
                <input type="text" name="pname" placeholder="Product name">
                <input type="text" name="qut" placeholder="Product quntity">
                <input type="text" name="cp" placeholder="Product company">
                <input type="text" name="manuals" placeholder="Product manuals">
                <select>
                    <option val="">select</option>
                    <option val="fff">1234</option>
                    <option val="vvv">456</option>
                </select>
                <input type="button" onclick="show(this)" value="Show">
                <input type="button" onclick="rm(this)" value="Remove">
                
            </div>
        </div>
        <div><input type='submit'  name='Add' value='ADD MORE ROW'>
            <input type='submit'  name='remove' value='REMOVE ROW'>
        </div>
    </body>
</html>
<script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>

<script>
 $(function(){
     $('input[name="Add"]').on('click',function(){
         //$('div.row').children('.containts:first').children('input').val('');
         var a=$('div.row').children('.containts:first');
         $('div.row').append($(a).clone());  //adding the clone of object; 
         $('div.row').children('.containts:last').find('input:text').val("");  //removing the val of input type text 
            alert($('div.row').children().length);   //no of children in row div;
         
     });
     
     $('input[name="remove"]').on('click',function(){
         if($('div.row').children().length >1){
         $('div.row').children('.containts:last').remove();     //removing the last row
          }
     });
     
     
 });  
 
 function show(obj)
 {
     $(obj).siblings('input[name="pname"]').css('background-color','red');
 }
 function rm(obj)
 {
     //var s=$(obj).parent().prop('class');
     $(obj).parent().remove();
     //alert(s);
 }


</script>