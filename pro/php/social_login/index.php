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
    }  
    ?> 

  <a href="index.php?types=Google">Google</a>
  <a href="index.php?types=Facebook">Facebook</a>
  <a href="index.php?types=LinkedIn">LinkedIn</a>