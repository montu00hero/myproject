<?php

$n='1331';

 $len=strlen($n);

for($i=0;$i<$len;$i++)
{
    $arr[]= substr($n, $i,1);
    
}

$arrs=(array_count_values($arr));

if(array_key_exists('1', $arrs))
{
   echo $arrs[1];    
}