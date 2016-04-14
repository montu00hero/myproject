

<!--  @@@@  http to https using HTMLS @@@ -->

<!--< meta http-equiv="Refresh" content="0;URL=https://www.example.com" />   -->




<?php




/*    http to https using php 
       
$redirect_url = "https://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    header("Location: $redirect_url");




/* http to https using htaccess   */

echo'The easiest way is surely, to make your whole site HTTPS only (you can use relative links then).<br>
    Some providers offer this option in their control panel. If there is no such option, you can write a.<br>
    htaccess file and place it in the root direcotry.<br>
    This lines will redirect any HTTP requests to HTTPS requests:<br><br>

RewriteEngine On        <br>
RewriteCond %{HTTPS} off    <br> 
RewriteRule ^(.*)$ https://www.example.com/$1 [R=301,L]    <br><br>

Of course you should replace example.com with your own domain.<br>';

echo"OR";

echo '<Location /buyCrap.php>    <br>
RewriteEngine On      <br>
RewriteCond %{HTTPS} off      <br>
RewriteRule (.*) https://%{HTTP_HOST}%{REQUEST_URI}        <br>
</Location>          <br>';




