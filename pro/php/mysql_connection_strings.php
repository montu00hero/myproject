<?php

$conn=mysql_connect('localhost','root','');

var_dump($conn);

$database_name='GDS';

mysql_select_db($database_name);

$query=mysql_query('show table status ');  //give the info related to table

while ( $row=  mysql_fetch_assoc($query))
{

echo"<pre>",print_r($row);
//print_r($conn);
}
