<?php

/* max value   of array (19,1,9,7,4,2)*/
echo ' max value   of array (19,1,9,7,4,2)';
$arr1=[19,1,9,7,4,2];

$max1=array_rand($arr1,1);
 
$lent=count($arr1);

for($j=0;$j<$lent;$j++)
{
   if($arr1[$j]>$max1)
    {
     $max1=$arr1[$j];
  
    }
    if($arr1[$j]<$max1)
    {
     $max1=$max1;
    }
}

echo'<br> max value='.$max1;
