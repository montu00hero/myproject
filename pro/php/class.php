<?php
class key {
    
 function lock()
 {
     //$var1="lock";
     echo"lock_parent_class";
 }
 
 function block()
 {
     $this->lock();
     
 }
    
}


class get extends key
{
    
    function made()
    {
        key::lock();   //to access the parent_class name :: function of parent class 
        
    }
    
    function made_easy()
    {
        key::block();
    }
    
    
}

class mort extends get{
    
    function red(){
        
     key::lock();   
    }
}


$k = new key();

//$k->lock();
//$k->block();
$ch=new get();
$mo=new mort();
$ch->made();
echo "</br>";
$ch->made_easy();
echo "</br>";
$mo->red();

?>