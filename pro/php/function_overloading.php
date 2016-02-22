<?php

class fun_overloading{
      
    
    function __call($name, $arguments) {
     if($name=='fun_overload')
        {
         // print_r($arguments);  
         $count=count($arguments);
        
         switch($count)
         {
           case 1:echo"hello---";
               break;
           case 2: echo $sum=$this->print_ov($arguments);   
               break;
           default : echo"more than 2 args";  
             
         }
         
         
        }
            
        
    }
    
  function print_ov($arguments)
   {
     return $sum=$arguments[0]+$arguments[1];
   }
    
    
}



$obj = new fun_overloading();

$obj->fun_overload(1);
$obj->fun_overload(1,2);
$obj->fun_overload(1,2,5);
