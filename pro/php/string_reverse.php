<?php
//--------------------------------reverse string-------------------//
echo $a="say hello world!";

$b=explode(' ',$a);
//$d=array();
foreach($b as $c)
{
   $d[]=  strrev($c) ;
    
    
}
asort($d);
echo'<pre>',print_r($d);
$e=  implode(' ', $d);


echo $e;


 //--------------array count duplicate values-------------------//

$array = array('apple', 'orange', 'pear', 'banana', 'apple',
'pear', 'kiwi', 'kiwi', 'kiwi');

echo'<pre>',print_r(array_count_values($array));

/******** will output *********/
/*
Array
(
   [apple] => 2
   [orange] => 1
   [pear] => 2
   
)
*/

//---------------------------------------------//



