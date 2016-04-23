<?php
error_reporting(E_ALL);


echo $str="hello world!"; echo"<br>";

$len=strlen($str);

/*  reversing string*/
for($i=0;$i<=$len;$i++ )
{
  $new_str=substr($str,0,$len-$i);    
  $k.=substr($new_str,-1);    
}

echo "reverse string:".$k;
echo"<br>";




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







 