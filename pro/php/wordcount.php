
<!docType>
<html>
    <head></head>
    <body>
      
        <form name='form1' action='<?php echo $_SERVER[SELF]?>' method='GET' >
        <input type='text' name='textbox' placeholder="Please enter your string" >
        <button type='submit'>Submit</button>
        </form>
    </body>
</html>




<?php

class wordcount{
    
    function count_word($str)
    {
        echo"string:$str","<br/>";
        $count=str_word_count($str);
        echo "No. of word in string(str_word_count()):$count ","<br/>";
        $arr=explode(' ',$str);
        $dup_word_finder=array_count_values($arr);
        
        echo"To find the duplicate values in array use [array_count_values()]","<br/>";
        
        echo"<table border='1'><tr><th>Word</th><th>Occurence</th></tr>";
        foreach($dup_word_finder as $key => $value){
            echo"<tr><td>$key</td><td>$value</td></tr>";
        }
        echo"</table>";
        
    }
   
}

$obj=new wordcount();
//$str="hi hi why why u u and i";
//$obj->count_word($str);



if(!empty($_REQUEST['textbox']))
{
  $obj->count_word($_REQUEST['textbox']);  
    
}      

