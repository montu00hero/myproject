<?php
   session_start();
  if(isset($_POST['name']))
  {
      $hours=time()+3600;
      
      setcookie('Username',$_POST['name'], $hours,'/');
      setcookie('Password',$_POST['password'], $hours,'/');
      
      echo $_COOKIE['Username'];
      echo $_COOKIE['Password'];
  }
  
   echo session_id();

?>


<html>
    <head>
        <title>Cookies And Session</title>
    </head>
    <body>
        <div style="border:1px">
            <form action="<?php echo $_SERVER['PHP_SELF']?>" Method="POST" name="myform">
                <input type="text" name="name" placeholder="Enter name" />
                <input type="text" name="password" placeholder="Enter password" />
                <button type="submit">Submit</button>
            </form>
         </div>   
    </body>
</html>

<h6>NOTE:session uses the cookies to store its values. 
    but session can also work without cookies if cookies are disabled in the web browser.
    Every time when user enter into a website a Unique session id is generated for that user.
    Session either store the session id in cookies or that session id is propagated in the URL.
</h6>

<h5>For testing disable the cookie in browser the check the session id.</h5>
