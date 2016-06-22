


 <input type="text" class="dp_cal sprite" onblur="check(this);"  name="to_depart[]"  id="flight_arr">

onblur="check(this)";

<script>
    
 function check(e)
      {
         var form =$(e).closest("form").attr("id");
            var status="no";
                $('#'+form+' input[type="text"]').each(
                function(index){  
                    var input = $(this);
                    //alert('Type: ' + input.attr('type') + 'Name: ' + input.attr('name') + 'Value: ' + input.val());
                   var input_val= $.trim(input.val());
                        if(input_val)
                         { //alert('Type: ' + input.attr('type') + 'Name: ' + input.attr('name') + 'Value: ' + input.val());
                              //$('#'+form+' input[type="submit"]').css('background','green');
                             status='ys';
                            // console.log(status);
                         }
                         else
                         {  //alert('Type: ' + input.attr('type') + 'Name: ' + input.attr('name') + 'Value: ' + input.val());
                             status='no'; 
                            // console.log(status);
                             return false;
                         }

                     });
     
            if(status=='ys')
                {
                    $('#'+form+' input[type="submit"]').css('background','green');
                }
            else
                {
                    $('#'+form+' input[type="submit"]').css('background','orange');
                }
  
      }
    
</script>    