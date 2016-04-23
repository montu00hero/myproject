<?php
echo"--------------------------------reverse string-------------------//";

echo '<br>';

echo $a="say hello world!";

echo '<br>';

echo 'strrev(say hello world!) <br>';
echo $g=strrev($a) ;

echo '<br>';

$b=explode(' ',$a);
//$d=array();
foreach($b as $c)
{
   $d[]=  strrev($c) ;
    
    
}
asort($d);
echo'<pre>',print_r($d),'<br>';
$e=  implode(' ', $d);


echo $e;


echo '<br>';

 echo "--------------array count duplicate values-------------------"," <br>";

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



