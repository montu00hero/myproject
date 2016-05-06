   <?php	
	if($_SERVER['HTTP_HOST'] == '192.168.0.22' || $_SERVER['HTTP_HOST'] == 'localhost')
	{
		$SITE_URL = 'http://192.168.0.22/ebstest/';
		//$is_ebs_live_local = true; // live
		$is_ebs_live_local = false;  // test		
		if($is_ebs_live_local)
		{
			// LIVE MODE
			define('EBS_ACCOUNT_ID', '');
			define('EBS_RETURN_URL', $SITE_URL.'Response-Offline.php?DR={DR}'); // EBS Returnl url
			define('EBS_MODE', 'LIVE');   //TEST OR LIVE
			define('EBS_KEY', ''); // Secret Key
		}		
		else
		{
			// TEST MODE
		   define('EBS_ACCOUNT_ID', '5880');
			define('EBS_RETURN_URL', $SITE_URL.'Response-Offline.php?DR={DR}'); // EBS Returnl url
			define('EBS_MODE', 'TEST');   //TEST OR LIVE
			define('EBS_KEY', 'ebskey'); // Secret Key
	   }	
	}
	else
	{
		$SITE_URL = 'http://ebstest/';
		//$is_ebs_live_local = true;
		$is_ebs_live_local = false;	
		if($is_ebs_live_local)
		{
			// LIVE MODE
			define('EBS_ACCOUNT_ID', '');
			define('EBS_RETURN_URL', $SITE_URL.'Response-Offline.php?DR={DR}'); // EBS Returnl url
			define('EBS_MODE', 'LIVE'); //TEST OR TEST
			define('EBS_KEY', ''); // Secret Key
		}		
		else
		{
			// TEST MODE
		   define('EBS_ACCOUNT_ID', '5880');
			define('EBS_RETURN_URL', $SITE_URL.'Response-Offline.php?DR={DR}'); // EBS Returnl url
			define('EBS_MODE', 'TEST'); //TEST OR TEST
			define('EBS_KEY', 'ebskey'); // Secret Key
	   }	
	}	
	define('EBS_DESCRIPTION', 'EBS Offline Payment');
	?>