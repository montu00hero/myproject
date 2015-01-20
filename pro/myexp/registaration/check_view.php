<?php
 

echo'<form id="form1" action="javascript:void(0)" >';

foreach($sqls as $a)
{
   if(isset($a->f_id))
   {
       echo'<label>'.$a->f_name.'</label><input name="'.$a->f_name.'" type="text" data-cid="'.$a->f_name.'" value="" required></br>';
       
   }
}

echo'<input type="submit" value="submit" > </form>';



?>

<script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
<script>
  $('#form1').submit(function(){
    var str=$('#form1').serialize();
    
    $.ajax({
      url:"<?php echo base_url();?>index.php/check/agent_data/",
      data:str,
      type:'POST',
      success:function(msg){
          alert(msg);
      },
      error:function(){
          alert("Server error !");
      }
        
    });
    
    
  });
</script>