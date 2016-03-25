<?php

$conn=mysql_connect('localhost','root','');

var_dump($conn);

$database_name=GDS;

mysql_select_db($database_name);

$query=mysql_query('show table status ');


//print_r($conn);