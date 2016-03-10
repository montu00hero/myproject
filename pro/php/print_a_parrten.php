<?php

$str = '0/111)/ 111-1111';
$str = preg_replace('/\D/', '', $str);
//echo $str,'<br>';

 $len=strlen($str);   $val=  ceil($len/3);
$a=0;
 for($i=0;$i<$len;$i++){
     
   
     if( ( $val % 3 ) == 0){
     //echo substr($str, $a, 3);echo"</br>";
     //$a=$a+3;
     }
     
     if(($val % 3 ) == 1)
       { 
        if(substr($str, $a, 3)){ 
         $arr[]=substr($str, $a, 3);
        $a=$a+3;}
        
       }
       
       if(($val % 3 ) == 2)
       { 
        echo substr($str, $a, 3);echo"</br>";
         $a=$a+3;
       }
 }
    print_r($arr);
     echo $s =implode('-',$arr);