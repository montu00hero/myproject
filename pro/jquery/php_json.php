<?php

$json = '{"a":1,"b":2,"c":3,"d":4,"e":5}';

var_dump(json_decode($json));
var_dump(json_decode($json, true));




$a = array("'foo'","'bar'","'baz'","'dded'", "'dd'");

echo "Normal: ",  json_encode($a), "\n";

//var_dump(json_decode($a));

?>

<script>
window.confirm("Do you realy want to close the window ?");


</script>