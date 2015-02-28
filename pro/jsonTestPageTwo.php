<?php
     $a=$_POST['data1'];
     $b=$_POST['data2'];
      if($a=='1')
      {
          $k=array('a'=> 1,
              'b'=>2,
              'c'=>3,
              'd'=>4,
              'e'=>5,
              'f'=>6,
              );
          
             echo $n=json_encode($k);
          
      }
          



?>