<?php

$time=strtotime('12-04-2015 0:0:0');

$times=strtotime('12-04-2015 2:2:2');

echo $time;
echo'</br>';
echo $times;
echo'</br>';
echo $diff= $times-$time;
echo'</br>';
//echo $s=date('h:i:s',$diff); // not correct because we can't convert the time difference into date time 


echo $eh=$diff/3600;


echo'</br>';
echo "converting seconds into hours min sec:"; 
echo $diff=gmdate('h:i:s',$diff);