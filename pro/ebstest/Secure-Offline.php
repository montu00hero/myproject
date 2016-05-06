<?php
   //include_once("../Class/Database.php");
	//$DatabaseObj = new Database();
	include_once("ebs-offline.php");
	$hash = EBS_KEY."|" . $_REQUEST['account_id'] . "|" . $_REQUEST['amount'] . "|" . $_REQUEST['reference_no'] . "|" . $_REQUEST['return_url'] . "|" . $_REQUEST['mode'];
	$secure_hash = md5($hash);
	?>
	<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
	<html xmlns="http://www.w3.org/1999/xhtml">
	    <head> 
	    <title><?php echo $SiteTitle;?></title>             
       <script type="text/javascript">
       function ForwardData()
       {
          document.frmTransaction.submit();
       }
       </script>
       <style type="text/css">
			.tableborder{margin-top:100px;}
			.text{font-family:Arial, Helvetica, sans-serif;font-size:13px;color:#0066CC;}
			.load_page{ position:relative;  z-index:999; background:#fff; float:left; width:650px; font-family: Arial, Helvetica, sans-serif; padding:10px 0; border:1px solid #ccc; border-radius:10px; box-shadow:0 0 15px #666;}
			.mask {background: none repeat scroll 0 0 #64940D;height: 100%;left: 0;opacity: 0.3;position: fixed;top: 0;width: 100%;z-index:9;}
       </style> 
	    </head>
	    <body onload="ForwardData();">
			 <div class="mask"></div>
	        <form  method="post" action="https://secure.ebs.in/pg/ma/sale/pay" name="frmTransaction" id="frmTransaction">
	            <input type="hidden" name="account_id"  value="<?php echo $_REQUEST['account_id'] ?>">
	            <input type="hidden" name="return_url" value="<?php echo $_REQUEST['return_url'] ?>" />
	            <input type="hidden" name="mode" value="<?php echo $_REQUEST['mode'] ?>" />
	            <input type="hidden" name="reference_no" value="<?php echo $_REQUEST['reference_no'] ?>" />
	            <input type="hidden" name="amount" value="<?php echo $_REQUEST['amount'] ?>"/>
	            <input type="hidden" name="description" value="<?php echo $_REQUEST['description'] ?>" /> 
	            <input type="hidden" name="name" maxlength="255" value="<?php echo $_REQUEST['name'] ?>" />
	            <input type="hidden" name="address" maxlength="255" value="<?php echo $_REQUEST['address'] ?>" />
	            <input type="hidden" name="city" maxlength="255" value="<?php echo $_REQUEST['city'] ?>" />
	            <input type="hidden" name="state" maxlength="255" value="<?php echo $_REQUEST['state'] ?>" />
	            <input type="hidden" name="postal_code" maxlength="255" value="<?php echo $_REQUEST['postal_code'] ?>" />
	            <input type="hidden" name="country" maxlength="255" value="<?php echo $_REQUEST['country'] ?>" />
	            <input type="hidden" name="phone" maxlength="255" value="<?php echo $_REQUEST['phone'] ?>" />
	            <input type="hidden" name="email" value="<?php echo $_REQUEST['email'] ?>" />
	            <input type="hidden" name="ship_name" maxlength="255" value="<?php echo $_REQUEST['ship_name'] ?>" />
	            <input type="hidden" name="ship_address" maxlength="255" value="<?php echo $_REQUEST['ship_address'] ?>" />
	            <input type="hidden" name="ship_city" maxlength="255" value="<?php echo $_REQUEST['ship_city'] ?>" />
	            <input type="hidden" name="ship_state" maxlength="255" value="<?php echo $_REQUEST['ship_state'] ?>" />
	            <input type="hidden" name="ship_postal_code" maxlength="255" value="<?php echo $_REQUEST['ship_postal_code'] ?>" />
	            <input type="hidden" name="ship_country" maxlength="255" value="<?php echo $_REQUEST['ship_country'] ?>" />
	            <input type="hidden" name="ship_phone" maxlength="255" value="<?php echo $_REQUEST['ship_phone'] ?>" />
	            <input type="hidden" name="secure_hash" value="<?php echo $secure_hash; ?>" />            
	        </form>
	        <table width="650" border="0" cellpadding="0" cellspacing="0" class="tableborder" align="center">
				   <tr>
					  <td height="84" align="center" valign="middle">
						 <div class="load_page">
							<div align="center">
							   <img src="http://www.tripamazon.com/assetes/images/logo.png" alt=""/>
							</div>
							<div align="center" class="text1 style1" style="font-family:Verdana, Geneva, sans-serif; margin-top:10px; font-size:11px; color:#666; line-height:18px;">You are currently redirecting to the payment gateway page.</div>
							<div>&nbsp;</div>
							<img src="http://www.tripamazon.com/assets/images/loading_bar_animated.gif" width="160" height="20" alt="" title="" />
							<div align="center" valign="baseline" class="text1 style1" style="font-size:11px; color:#666; margin-top:10px; padding-left:50px; padding-right:50px;font-family:verdana;"><strong>Almost there !!</strong> your search results are being loaded, do not refresh the screen .</div>
						 </div>
					  </td>
				   </tr>
				</table>
	    </body>
	</html>
