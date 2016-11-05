<?php

/*********** list **************/

echo "The list() function is used to assign values to a list of variables in one operation.
<br>
Note: This function only works on numerical arrays.";


$info=array('a','b','d','c');
list($t,$q,$e,$p)=$info;

echo $t;
echo $q;
echo $e;
echo $p;


/************array map********************/


echo" array_map() returns an array containing all the elements of array1 after "
. "applying the callback function to each one. The number of parameters that the "
. "callback function accepts should match the number of arrays passed to the array_map() ";


function cube($n)
{
    return($n * $n * $n);
}

$a = array(1, 2, 3, 4, 5);
$b = array_map("cube", $a);
print_r($b);

/****************************************/


function show_Spanish($n, $m)
{
    return("The number $n is called $m in Spanish");
}

function map_Spanish($n, $m)
{
    return(array($n => $m));
}

$a = array(1, 2, 3, 4, 5);
$b = array("uno", "dos", "tres", "cuatro", "cinco");

$c = array_map("show_Spanish", $a, $b);
print_r($c);

$d = array_map("map_Spanish", $a , $b);
print_r($d);






?>