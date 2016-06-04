    <?php  
       if(isset($_REQUEST['types'])){
           
           $social_site_name=$_REQUEST['types'];
            
            if($_REQUEST['types']=='Google'){
               $config = array(
                  "base_url" => "http://localhost/tests/trunk/pro/php/social_login/hybridauth/hybridauth/",
                  "providers" => array (
                    "Google" => array (
                      "enabled" => true,
                      "keys"    => array ( "id" => "319688093166-u9rbosog1bla235kvhjeoasvb2fj24l1.apps.googleusercontent.com", "secret" => "HVESbB0zzCMLn93FlBQqcQdl" ),
                      "scope"           => "https://www.googleapis.com/auth/userinfo.profile ". // optional
                                           "https://www.googleapis.com/auth/userinfo.email"   , // optional
                      "access_type"     => "offline",   // optional
                      "approval_prompt" => "force"     // optional
                   //   "hd"              => "domain.com" // optional
                )));
         
              //   Callback URL(redirect url) : http://mywebsite.com/path_to_hybridauth/?hauth.done=Google    
       } 
       
         if($_REQUEST['types']=='Facebook'){
                   $config = array(
                    "base_url" => "http://localhost/tests/trunk/pro/php/social_login/hybridauth/hybridauth/",
                    "providers" => array (
                      "Facebook" => array (
                        "enabled" => true,
                        "keys"    => array ( "id" => "1692389697689191", "secret" => "79224a82d36da4a820d2b22f3f882943" ),
                        "scope"   => "email, user_about_me, user_birthday, user_hometown", // optional
                        "display" => "popup" // optional
                  )));
                   
            //   Callback URL(redirect url) : http://mywebsite.com/path_to_hybridauth/?hauth.done=Facebook    
      
       } 
        if($_REQUEST['types']=='LinkedIn'){
                   $config = array(
                    "base_url" => "http://localhost/tests/trunk/pro/php/social_login/hybridauth/hybridauth/",
                    "providers" => array (
                      "LinkedIn" => array (
                        "enabled" => true,
                        "keys"    => array ( "key" => "75qlcv72few29d", "secret" => "c9lxur7Rw4dveda9" )
                      
                  )));
                   
            //   Callback URL(redirect url) :  	http://mywebsite.com/path_to_hybridauth/?hauth.done=LinkedIn    
      
       } 
       
        require_once( "hybridauth/hybridauth/Hybrid/Auth.php" );

                $hybridauth = new Hybrid_Auth( $config );

                $adapter = $hybridauth->authenticate( $social_site_name );

                $user_profile = $adapter->getUserProfile();

                 echo"<pre>", print_r($user_profile); 
             
                 
    $identifier=$user_profile->identifier;
    $webSiteURL=$user_profile->webSiteURL;
    $profileURL=$user_profile->profileURL;
    $photoURL=$user_profile->photoURL;
    $displayName=$user_profile->displayName;
    $description=$user_profile->description;
    $firstName=$user_profile->firstName;
    $lastName=$user_profile->lastName;
    $gender=$user_profile->gender;
    $language=$user_profile->language;
    $age=$user_profile->age;
    $birthDay=$user_profile->birthDay;
    $birthMonth=$user_profile->birthMonth;
    $birthYear=$user_profile->birthYear;
    $email=$user_profile->email;
    $emailVerified=$user_profile->emailVerified;
    $phone=$user_profile->phone;
    $address=$user_profile->address; 
    $country=$user_profile->country;
    $region=$user_profile->region; 
    $city=$user_profile->city;
    $zip=$user_profile->zip; 
    
                 
                 $database_name="root";
                 
                 $host="localhost";
                 $user="root";
                 $pwd="";
                 
                 $conn=mysql_connect($host,$user,$pwd); 
                 mysql_select_db($database_name);
                 
                 $que="select email from social_login where email='$email' ";
                 $res=mysql_query($que) or die(mysql_error());
                
                  while($row=  mysql_fetch_array($res))
                  {
                    $ret=  $row['email'];     
                  }
                   
                 
                   
                 if($ret){
                  
                  echo "login to social";   
                 }
                 
                
                 
                 if(empty($ret)){ 
                  $query="insert into social_login values($identifier,'$webSiteURL','$profileURL','$photoURL','$displayName','$description','$firstName','$lastName','$gender','$language','$age','$birthDay','$birthMonth','$birthYear','$email','$emailVerified','$phone','$address','$country','$region','$city','$zip')";
                 $result=mysql_query($query) or die(mysql_error());
                  
                  if($result)
                  {
                      echo"insert the user info";
                  }
                 }
                 
  
    /*
        
CREATE TABLE IF NOT EXISTS `social_login` (
  `identifier` bigint(250) NOT NULL,
  `webSiteURL` varchar(500) NOT NULL,
  `profileURL` varchar(500) NOT NULL,
  `photoURL` varchar(500) NOT NULL,
  `displayName` varchar(100) NOT NULL,
  `description` varchar(500) NOT NULL,
  `firstName` varchar(50) NOT NULL,
  `lastName` varchar(50) NOT NULL,
  `gender` varchar(10) NOT NULL,
  `language` varchar(20) NOT NULL,
  `age` varchar(2) NOT NULL,
  `birthDay` varchar(2) NOT NULL,
  `birthMonth` varchar(2) NOT NULL,
  `birthYear` varchar(4) NOT NULL,
  `email` varchar(100) NOT NULL,
  `emailVerified` varchar(50) NOT NULL,
  `phone` varchar(15) NOT NULL,
  `address` varchar(200) NOT NULL,
  `country` varchar(50) NOT NULL,
  `region` varchar(50) NOT NULL,
  `city` varchar(50) NOT NULL,
  `zip` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

      */             
                 
                 
                 
    }  
    ?> 

  <a href="index.php?types=Google">Google</a>
  <a href="index.php?types=Facebook">Facebook</a>
  <a href="index.php?types=LinkedIn">LinkedIn</a>
  
  
  
  
  