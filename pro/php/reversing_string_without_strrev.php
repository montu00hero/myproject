<?php
error_reporting(E_ALL);


echo $str="hello world!"; echo"<br>";

$len=strlen($str);

echo"/*  reversing string*/   //way 1 ",'<br>';



for($i=0;$i<=$len;$i++ )
{
  $new_str=substr($str,0,$len-$i);    
  $k.=substr($new_str,-1);    
}




echo "reverse string:substr(str,0,len-i) & substr(new_str,-1):--".$k;
echo"<br>";



echo"/*---------way 2--- str_split(str,1) return array of single words -----------*/";

$splited_str= str_split($str,1);

//echo"<pre>",print_r($splited_str);

krsort($splited_str);

//echo"<pre>",print_r($splited_str);

$s=  implode('',$splited_str);


echo $s;


/*------------------------*/

/*  for understanging  purpose*/
$a=array();
$b=array();
for($i=0;$i<=$len;$i++ )
{
   $b[]=substr($str,0,$len-$i);    
  $a[]=substr($b[$i],-1);    
}


echo"<pre>",print_r($b); 
echo"<pre>",print_r($a); 
echo"<br>";







 