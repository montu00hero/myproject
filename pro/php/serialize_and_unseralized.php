<?php

  $response = array(
                'status' => '1',
                'success' => 'true',
                'msg' => 'Successfully Registered!, please check your mail for login credentials',
                'rid' => 'agent_id',
                'fname' =>  'firstname'
            );
  
  
  $data=  serialize($response);
  
  echo "<font color='Green'>Serialize data (here transforming a array into serialize data):--</font>",'<br>';
  print_r($data);
  
  
  
  echo"<br>","<font color='green'>Unserialize the data</font>","<br>";
  
  $un_data=  unserialize($data);
  
  echo"<pre>",print_r($un_data);
  
  
  
  