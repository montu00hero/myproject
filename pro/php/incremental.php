<?php
error_reporting(E_ALL);
$x=5;
echo 'x=5','<br>';
echo 'x++ + x++ =';echo $x++ + $x++,'<br>';
echo 'x-- - x-- =';echo $x-- - $x--,'<br>';
echo"---------------------------------------------",'<br>';
$arr=[10,1,9,7,4,2];

/*min valu in  of array (10,1,9,7,4,2) */
echo 'min valu in  of array (10,1,9,7,4,2)';

 //$min=  array_rand($arr,1);
 $min=  $arr[0];

$len=count($arr);

for($i=0;$i<$len;$i++)
{
   if($arr[$i]<$min)
    {
     $min=$arr[$i];
   //  print_r($min);
    }
    if($arr[$i]>$min)
    {
     $min=$min;
    }
}

echo'<br> min value='.$min;





?>