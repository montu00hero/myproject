<?php




$server_url = $_SERVER["SERVER_NAME"]; 
$doc_root = $_SERVER["DOCUMENT_ROOT"]; 


 $url = $server_url.'/'."<br />"; 
 $dir = $doc_root.'/';



$files=array();

$files=  scandir($doc_root);

foreach($files as $files_seak){
  
    $ext = pathinfo($files_seak, PATHINFO_EXTENSION);
    
    if($ext=='php')
     {
        echo $files_seak;
     }
    if($ext==''){ 
         $files=$files=  scandir($doc_root.'/'.$files_seak);
     }
   
    ($ext);
}




//echo'<pre>',print_r($files);
