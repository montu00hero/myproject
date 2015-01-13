<?php

$wor=$_REQUEST['data'];




$user="root";
$host="localhost";
$pwd="";
$db="page";


$conn=mysql_connect($host,$user,$pwd);

mysql_select_db($db);

 $query="select cityName from cities where cityName LIKE '%$wor%' ";


$result=mysql_query($query,$conn);



$outp = "[";
while($rs=mysql_fetch_assoc($result,MYSQLI_ASSOC))
{      if ($outp != "[") {$outp .= ",";}
 
     $outp .= '{"city":"'  . $rs["cityName"] . '"}';
}
$outp .="]";
//$outp = "[";
//while($rs = mysql_fetch_assoc($result,MYSQLI_ASSOC)or die(mysql_error())) {
//    if ($outp != "[") {$outp .= ",";}
//    $outp .= $rs["cityName"];
//}
//$outp .="]";

mysql_close();

echo $outp;

//echo(json_encode($outp));

















?>