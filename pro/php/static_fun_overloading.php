<?php

class static_fun_overloading{
    
    public function __callStatic($name, $arguments) {  // use for static function overloading
         
         if($name=='staticFunction')
         {
            $count= count($arguments);
             
            switch($count)
            {
              case 1:echo "this is static function overloading with one args";
                  break;
              case 2:echo" this is static function overloading with two args";
                  break;
              default:echo "this is static function overloading with no argument";
                  
            }
            
             
         }
         
         
         
     }  
     
     
     public function __call($name, $arguments) {  // use for  function overloading
         
         if($name=='Function')
         {
            $count= count($arguments);
             
            switch($count)
            {
              case 1:echo "one args";
                  break;
              case 2:echo" two args";
                  break;
              default:echo "no argument";
                  
            }
            
             
         }
         
         
         
     }  
    
    
}


static_fun_overloading::staticFunction();


$obj=new static_fun_overloading();

$obj->Function();

