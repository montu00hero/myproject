<?php

echo $a="say hello world! ";

$b=explode(' ',$a);
//$d=array();
foreach($b as $c)
{
   $d[]=  strrev($c) ;
    
    
}
print_r($d);
$e=  implode(' ', $d);


echo $e;