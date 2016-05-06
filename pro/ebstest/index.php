<?php
	error_reporting(0);
	session_start();
	include_once("ebs-offline.php");	
	?>
	<html>
	    <head>
	        <title>Title</title>
	        <META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=iso-8859-1" />
	         <style type="text/css">
				.tableborder{margin-top:100px;}
				.text{font-family:Arial, Helvetica, sans-serif;font-size:13px;color:#0066CC;}
				.load_page{ position:relative;  z-index:999; background:#fff; float:left; width:650px; font-family: Arial, Helvetica, sans-serif; padding:10px 0; border:1px solid #ccc; border-radius:10px; box-shadow:0 0 15px #666;}
				.mask {background: none repeat scroll 0 0 #64940D;height: 100%;left: 0;opacity: 0.3;position: fixed;top: 0;width: 100%;z-index:9;}
	        </style>  
	        <style type="text/css">
	            .tableborder{
	                border:#333333 solid 1px;
	                margin-top:150px;
	            }
	            .text{
	                font-family:Arial, Helvetica, sans-serif;
	                font-size:13px;
	                color:#0066CC;
	            }
	        </style>
	        <script type="text/javascript">
	            function submitform()
	            {          	
				          document.frmTransaction.submit();		                     
	            }
	        </script>
	    </head>    
	    <body onload="submitform()">    	   
	<?php
	//1. EBS Account Details
	$account_id  = EBS_ACCOUNT_ID; // ACCOUNT ID
	$return_url  = EBS_RETURN_URL; // RETURN URL
	$mode        = EBS_MODE; //TEST OR LIVE
	$description = EBS_DESCRIPTION;      // SIMPLE DESCRIPTION
	//2. Transaction Details
	//$fetch_max_no = mysql_fetch_array(mysql_query("select max(id) as max_no from payment_gateway"));
	//$max_no = $fetch_max_no['max_no'];
	//$max_no++;
	$fetch_new_payment = $_REQUEST;
	$max_no       = time();
	$pg_ref_no    = 'PG'. date('d'). date('m'). date('Y').$max_no;  // REFERENCE NO i.e order id should be match in MerchantRefNo
	$amount       = '1000';//$fetch_new_payment['amount']; // AMOUNT TO PASS
	$source                    = 'Offline Payment';
	$_SESSION['pg_ref_no']     = $pg_ref_no;
	//3. Customer Billing address
	$billing_name    = 'Rajeev';//$fetch_new_payment['name'];
	$billing_email   = 'provab.rajeev@gmail.com';//$fetch_new_payment['email'];
	$billing_phone   = '9620415992';//$fetch_new_payment['contact_no'];
	$billing_address = 'BTM';       
	$billing_city    = 'Bangalore'; 
	$billing_state   = 'Karnataka'; 
	$billing_zip     = '560100'; 
	$billing_country = 'IN';
	//4. Customer Shipping address
	$shipping_name    = $billing_name;
	$shipping_email   = $billing_email;
	$shipping_phone   = $billing_phone;
	$shipping_address = $billing_address;
	$shipping_city    = $billing_city;
	$shipping_state   = $billing_state;
	$shipping_zip     = $billing_zip;
	$shipping_country = $billing_country;
	$IP = $_SERVER['REMOTE_ADDR'];
	/*$browser = getBrowserInfo();
	$sql = "insert into payment_gateway set pg_ref_no='$pg_ref_no',source='$source',source_ref_no='$ref_no',status='Pending',
	        amount='$amount',name='$billing_name',email='$billing_email',phone='$billing_phone',address='$billing_address',
	        city='$billing_city',state='$billing_state',zip='$billing_zip',country='$billing_country',user_ip='$IP',
	        browser='".$browser[name]."',ebs_mode='$mode',date=now(),datetime=now()";
	        if($row_count==1)
	        {
	          mysql_query($sql, $DatabaseObj->link) or die(mysql_error());
	        }	 */
	?>            
	<div class="mask"> </div>
	          <form  method="post" action="Secure-Offline.php" name="frmTransaction" id="frmTransaction">			
	            <input type="hidden" name="account_id" value="<?=$account_id?>" />
	            <input type="hidden" name="return_url" value="<?=$return_url?>" />
	            <input type="hidden" name="mode" value="<?=$mode?>" />
	            <input type="hidden" name="reference_no"  value="<?=$pg_ref_no?>" />
	            <input type="hidden" name="amount" value="<?=$amount?>" />
	            <input type="hidden" name="description"  value="<?=$description?>" />
	            <!--Billing address start-->
	            <input type="hidden" name="name" maxlength="255" value="<?=$billing_name?>" />
	            <input type="hidden" name="address" value="<?=$billing_address?>" />
	            <input type="hidden" name="city" value="<?=$billing_city?>" />
	            <input type="hidden" name="state" value="<?=$billing_state?>" />
	            <input type="hidden" name="postal_code" value="<?=$billing_zip?>" />
	            <input type="hidden" name="country" value="<?=$billing_country?>" />
	            <input type="hidden" name="email" value="<?=$billing_email?>" />
	            <input type="hidden" name="phone" value="<?=$billing_phone?>" />
	            <!--Billing address end -->
	            <!--Shipping address start-->
	            <input type="hidden" name="ship_name"  value="<?=$shipping_name?>" />
	            <input type="hidden" name="ship_address"  value="<?=$shipping_address?>" />
	            <input type="hidden" name="ship_city"  value="<?=$shipping_city?>" />
	            <input type="hidden" name="ship_state"  value="<?=$shipping_state?>" />
	            <input type="hidden" name="ship_postal_code"  value="<?=$shipping_zip?>" />
	            <input type="hidden" name="ship_country"  value="<?=$shipping_country?>" />
	            <input type="hidden" name="ship_phone"  value="<?=$shipping_phone?>" />
	            <!--Shipping address end-->
	            <table width="650" border="0" cellpadding="0" cellspacing="0" class="tableborder" align="center">
				   <tr>
					  <td height="84" align="center" valign="middle">
						 <div class="load_page">
							<div align="center">
							   <img src="http://www.tripamazon.com/assets/images/logo.png" alt="">
							</div>
							<div align="center" class="text1 style1" style="font-family:Verdana, Geneva, sans-serif; margin-top:10px; font-size:11px; color:#666; line-height:18px;">You are currently redirecting to the payment gateway page.</div>
							<div>&nbsp;</div>
							<img src="http://www.tripamazon.com/assets/images/loading_bar_animated.gif" width="160" height="20" alt="" title="" />
							<div align="center" valign="baseline" class="text1 style1" style="font-size:11px; color:#666; margin-top:10px; padding-left:50px; padding-right:50px;font-family:verdana;">
							<strong>Almost there!!</strong> Your Payment Gateway being loaded, do not refresh the screen .</div>
						 </div>
					  </td>
				   </tr>
				</table>	 
	        </form>  
	    </body>
	</html>