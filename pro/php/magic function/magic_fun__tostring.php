<?php
/*
   
 __toString()

Definitely saving the best until last, the __toString method is a very handy addition to our toolkit.
This method can be declared to override the behaviour of an object which is output as a string,
for example when it is echoed. For example if you wanted to just be able to echo an object in a template,
you can use this method to control what that output would look like. Let's look at our Penguin again:  


*/
class Penguin {

  public function __construct($name) {
      $this->species = 'Penguin';
      $this->name = $name;
  }

  public function __toString() {
      return $this->name . " (" . $this->species . ")\n";
  }
}



$obj=new Penguin('Tuci');

echo $obj;