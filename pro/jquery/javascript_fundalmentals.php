
<script>

    
  var sam=function aa(s){
  var a=s;
  var b=14;
  return a+b;
 }
    
    var a=sam(34);   
    alert(a);
    
  //objects in javascripts(http://jqfundamentals.com/chapter/javascript-basics)
  /*
   
    As it turns out, most everything we work with in JavaScript is an object — in fact,
    there are only five kinds of values that are not objects:

    1.strings (text)
    2.booleans (true/false)
    3.numbers
    4.undefined
    5.null

    These values are called primitives
   
     */
  
  
  var person={
       
        name:'Ram',
        address:'ecity',
        city:function(){
            alert('blr');
            }
      
  };
        alert(person.name);  
        person.city();  
  
  
    
</script>    