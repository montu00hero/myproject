<?php

/* 
 __call

There are actually two methods which are similar enough that they don't get their own title in this post!
The first is the __call method, which gets called, if defined, 
when an undefined method is called on this object. 
The second is __callStatic which behaves in exactly the same way but responds to 
undefined static method calls instead (this was added in PHP 5.3). 
Probably the most common thing I use __call for is polite error handling, 
and this is especially useful in library code where other people might need to be integrating with 
your methods. So for example if a script had a Penguin object called $penguin and it 
contained $penguin->speak() ... the speak() method isn't defined so under normal circumstances we'd see:
/

PHP Fatal error: Call to undefined method Penguin::speak() in ...

What we can do is add something to cope more nicely with this kind of failure 
 * than the PHP fatal error you see here, by declaring a method __call. For example:
*/
class Animal {
}
class Penguin extends Animal {

  public function __construct($id) {
    $this->getPenguinFromDb($id);
  }

  public function getPenguinFromDb($id) {
    // elegant and robust database code goes here
  }

  public function __get($field) {
    if($field == 'name') {
      return $this->username;
    }
  }

  public function __set($field, $value) {
    if($field == 'name') {
      $this->username = $value;
    }
  }

  public function __call($method, $args) {
      echo "unknown method " . $method;
      return false;
  }
}
/*
This will catch the error and echo it.
In a practical application it might be more appropriate to log a message, redirect a user,
or throw an exception, depending on what you are working on - but the concept is the same. 
Any misdirected method calls can be handled here however you need to, 
you can detect the name of the method and respond differently accordingly - 
for example you could handle method renaming in a similar way to how we handled 
the property renaming above.
*/