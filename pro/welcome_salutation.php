<?php

class s{
function welcome() {
    if (date("H") < 12) {
        echo "Good Morning";
    } elseif (date("H") > 11 && date("H") < 18) {
        echo "Good Afternoon";
    } elseif (date("H") > 17) {
        echo "Good Evening";
    }
   }
   
  function aa()
  {
      echo $_SERVER['HTTP_HOST'];   
  }
  
}
$sq= new s;
$sq->welcome();
$sq->aa();

?>