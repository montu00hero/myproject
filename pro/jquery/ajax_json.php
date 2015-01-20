<?php
// controller

  function add_markup_chk()
       {
       	 
       	$agent=$this->input->post('agent');
 	$country=$this->input->post('country');
       	$api=$this->input->post('api');
       	$res = $this->markup->add_markup_chk($agent,$country,$api);
       	 
       	if($res != 0){
       	//	$data['mid'] = $res->markup_id();
				$data['status'] = '0';
			}else{
				//$data['mid'] = $res->markup_id();
 			   $data['status'] = '1';
			}
			echo json_encode($data);
       }

?>



<!--   view page             -->
<script type="text/javascript">

$(document).ready(function(){
$("#agent").change(function(){
	var id="<?php echo $result->agent_id; ?>";
	//alert(id);
	 var country=$("#country").val();
	var agent=$("#agent").val();
	var api=$("#api").val();
  var aid=$("#aid").val();
 
  $("#msg").css({"color":"red"});
		if(agent != ''  && api != ''){
		 
	   $.ajax({
		type: "POST",
		url: "<?php echo base_url(); ?>markup_b2b/add_markup_chk",
	 	data: {agent :agent, country:country, api:api},
	 	dataType: 'json',
		success: function (msg){
			if(msg.status == '0' && agent != aid ){
				$("#msg").show();
				$("#msg").html('Already markup has been added to this agent');
		   	$('#tempStatus').val('false');
			}else{
				$('#tempStatus').val('true');
				$("#msg").hide();
				$("#msg").html('');
			} 
		 	
	 	}
	
	});
	}
	
	});
	

	

});

</script>