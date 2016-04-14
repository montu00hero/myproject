<?php

echo '$_SERVER["SERVER_NAME"]='. $_SERVER['SERVER_NAME'];
echo '<br>';echo '<br>';


echo '$_SERVER["HTTPS"]='. $_SERVER['HTTPS'];
echo '<br>';echo '<br>';

echo '$_SERVER["HTTP_HOST"]='. $_SERVER['HTTP_HOST'];
echo '<br>';echo '<br>';

echo '$_SERVER["HTTP_USER_AGENT"]='. $_SERVER['HTTP_USER_AGENT'];
echo '<br>';echo '<br>';

echo '$_SERVER["REMOTE_ADDR"]='. $_SERVER['REMOTE_ADDR'];
echo '<br>';echo '<br>';

echo '$_SERVER["HTTP_REFERER"]='.$_SERVER['HTTP_REFERER'];
echo "<br>","This will return the path of the requested script";
echo '<br>';echo '<br>';

echo'parse_url($_SERVER["HTTP_REFERER"]';
print_r(parse_url($_SERVER['HTTP_REFERER']));
echo '<br>';echo '<br>';

echo '$_SERVER["REQUEST_URI"]='. $_SERVER['REQUEST_URI'];
echo '<br>';echo '<br>';

echo"<pre>",print_r($_SERVER);

echo"<br>";
echo"<br>";


echo"How to make a page which can be access by only allowed ip address ";
echo "<br>";echo '<br>';
echo 'fist get the path of script $_SERVER["HTTP_REFERER"],then use parse_url,the extract host and convert in ip address using "
. "gethostbyname(host)';
echo '<br>';
echo '<br>','$parseurl = parse_url($_SERVER["HTTP_REFERER"])=';
  
  $parseurl = (parse_url($_SERVER['HTTP_REFERER']));
  print_r($parseurl); 
  
  echo '<br>';echo '<br>';
  
  echo 'gethostbyname($parseurl["host"])=';
  
  
  echo $ip = gethostbyname($parseurl['host']);
  
  
/*
$query = "SELECT agent_id, agent_type, company_name, address, city, country, postal_code, agent_logo FROM `b2b` WHERE md5(`agent_id`)='$b2b2c_id' AND `permitted_ip`='$ip' LIMIT 1";
            $res = $this->db->query($query)->row_array();
            if(!isset($res['agent_id'])){
                $this->session->unset_userdata('b2b2c');
                exit;
            }
  */      
        
