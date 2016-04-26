<?php

$str="hello world";

$len=  strlen($str);

/* In php string is treated as array */
$rev_str='';
for($i=$len;$i>=0;$i--)
{
    $rev_str.=$str[$i];
}

echo $str,'<br>';
echo $rev_str,'<br>';
