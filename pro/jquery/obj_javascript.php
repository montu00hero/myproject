<script>

//obj ect


var person = {
  firstName : 'Boaz',
  lastName : 'Sender',
  greet : function(greeting, punctuation) {
    log( greeting + ', ' + this.firstName + punctuation );
  }
};

var sayIt = person.greet;  //it will produce undefined

sayIt.call( person, 'Hello', '!!1!!1' );

sayIt.apply(person,['Hello','!$$$$$!']); //here arg is passed in array


//******************************Array*********************************//

     var myArray = [ 'a', 'b', 'c' ];
     var firstItem = myArray[ "0" ]; // access the first item

     var myArray = [ 'a', 'b', 'c' ];
     var firstItem = myArray[ 0 ];

     var secondItem = myArray[ 1 ]; // access the item at index 1
     log( secondItem ); // logs 'b'


    //length or size of array()
    
   var lent = myArray.length;
      alert(lent);

   //looping in array
    
    for(var i=0 ; i<=lent;i++)
    {
      log(myArray[i]);
    }  
         
   
   
   
   

//******************************logic and truthiness*********************************//
  /*
   As it turns out, most values in JavaScript are truthy — in fact,
      there are only five values in JavaScript that are falsy:

   1) undefined (the default value of declared variables that are not assigned a value)
   2) null.
   3) NaN ("not a number").
   4) 0 (the number zero).
   5) '' (an empty string).

   When we want to test whether a value is "falsy," we can use the ! operator: 

 */

//***************************************************************//





</script>
