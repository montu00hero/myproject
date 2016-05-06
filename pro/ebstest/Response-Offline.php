<?php
	error_reporting(0);
	session_start();
	//require_once("../Class/Database.php");
	//$DatabaseObj = new Database();
	include_once("ebs-offline.php");
	$secret_key = EBS_KEY;  // Your Secret Key
	if(isset($_GET['DR']))
	{
	    require('Rc43.php');
	    $DR = preg_replace("/\s/", "+", $_GET['DR']);
	
	    $rc4 = new Crypt_RC4($secret_key);
	    $QueryString = base64_decode($DR);
	    $rc4->decrypt($QueryString);
	    $QueryString = split('&', $QueryString);
	
	    $response = array(); 
	    //$QueryString3=decrypt($QueryString[0]); print_r($QueryString3);
	    foreach ($QueryString as $param)
	    {
	        $param = split('=', $param);
	        $response[$param[0]] = urldecode($param[1]);
	    }
	
	    $_SESSION['PG_RES'] = $response;   
	}
	//echo '<pre>'; print_r($_SESSION['PG_RES']); echo '</pre>'; //exit;  
   ?>
	<html>
	    <head>
	         <title><?php echo $SiteTitle;?></title>            	        
	         <style type="text/css">
				.tableborder{margin-top:100px;}
				.text{font-family:Arial, Helvetica, sans-serif;font-size:13px;color:#0066CC;}
				.load_page{ position:relative;  z-index:999; background:#fff; float:left; width:650px; font-family: Arial, Helvetica, sans-serif; padding:10px 0; border:1px solid #ccc; border-radius:10px; box-shadow:0 0 15px #666;}
				.mask {background: none repeat scroll 0 0 #64940D;height: 100%;left: 0;opacity: 0.3;position: fixed;top: 0;width: 100%;z-index:9;}
	        </style> 
	    </head>
	    <body>
			 <div class="mask"></div>
	        <center>
			  
			  
	        <table width="650" border="0" cellpadding="0" cellspacing="0" class="tableborder" align="center" style="display:block;">
				   <tr>
					  <td height="84" align="center" valign="middle">
						 <div class="load_page">
							<div align="center">
							   <a href="#"></a><img src="http://www.tripamazon.com/assets/images/logo.png" alt="" /></a>
							</div>
							<table>
							<?php
							foreach($response as $key => $value)
							{
								?>
								<td><?php echo $key; ?></td>
								<td>::</td>
								<td><?php echo $value; ?></td>
								</tr>
								<?php
							}
							?>						
							</table>
							
					   </div>
					  </td>
				   </tr>
           </table>
           </center> 
	    </body>
	</html>
