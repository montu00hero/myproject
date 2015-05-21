
<html>
<head>
<title>Online PHP Script Execution</title>
</head>
<body>
<?php
   $a="Execute PHP Script Online";
   $b="PHP";
   $c=str_replace('PHP','java',$a);
   
   echo $c;
   
   $d=strlen($c);
   echo'</br>';
   echo $d;
   $f=strchr($c,'S');
   echo'</br>';
   echo $f;
   echo"<br>";
   
   $e=substr($a,1);
   echo $e;
?>
</body>
</html>