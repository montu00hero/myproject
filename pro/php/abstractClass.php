<?php

/*Abstract classes are special because they can never be instantiated.
 *  Instead, you typically inherit a set of base functionality from them in a new class. 
 *  For that reason, they are commonly used as the base classes in a larger class hierarchy. 
 *  A method can be marked as abstract as well.
 *  As soon as you mark a class function as abstract,
 *  you have to define the class as abstract as well - only abstract classes can hold abstract functions.
 *  Another consequence is that you don't have to (and can't) write any code for the function - 
 *  it's a declaration only.
 *  You would do this to force anyone inheriting from your
 *  abstract class to implement this function and write the proper code for it.
 *  If you don't, PHP will throw an error.
 *  However, abstract classes can also contain non-abstract methods,
 *  which allows you to implement basic functionality in the abstract class. 
 *  */
abstract class par {
  
    function gt(){
        echo 'parent';
    }
   abstract function lt();
    
}

class child extends par{
   function mh(){
       parent::gt();
   } 
    
   function lt(){
       echo"kite";
   }
}
class set extends child{
    
    function jn()
    {
        parent::gt();
        parent::mh();
        par::gt();
        child::mh();
    }
}

/*
$t=new child();
$t->mh();
echo '<br/>';
$t->lt();

*/
$s=new set();
$s->jn();


