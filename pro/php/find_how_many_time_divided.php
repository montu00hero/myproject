<?php
 $S='011100';
 $val=(int)bindec($S);
   $V=1;
   
   for($i=$val;$i>=$val;$i--)
    {
         $val=$val/2;
        if($val % 2==0)
          {  
            $V=$V+1; 
            
             if($val==0)
              {   $V=$V+1;
                 echo $V; exit;
              }
           } 

          if($val % 2!=0)
          {
               $V=$V+1;
            $val=ceil($val) -1;  

           }
   
         
   }   
   
//  echo $str = 'In My Cart : 11 12 items';
//preg_match_all('!\d+!', $str, $matches);
//print_r($matches);

/*
$str = '0/111)/ 111-1111';
$str = preg_replace('/\D/', '', $str);
//echo $str,'<br>';

 $len=strlen($str);   $val=  ceil($len/3);
$a=0;
 for($i=0;$i<$len;$i++){
     
   
     if( ( $val % 3 ) == 0){
     //echo substr($str, $a, 3);echo"</br>";
     //$a=$a+3;
     }
     
     if(($val % 3 ) == 1)
       { 
        if(substr($str, $a, 3)){ 
         $arr[]=substr($str, $a, 3);
        $a=$a+3;}
        
       }
       
       if(($val % 3 ) == 2)
       { 
        echo substr($str, $a, 3);echo"</br>";
         $a=$a+3;
       }
 }
    print_r($arr);
     echo $s =implode('-',$arr);

     
   

//count the no of ones in result of 

 //$n='18821471';
 $n='1331';

 $len=strlen($n);

for($i=0;$i<$len;$i++)
{
    $arr[]= substr($n, $i,1);
    
}

$arrs=(array_count_values($arr));

if(array_key_exists('1', $arrs))
{
   echo $arrs[1];    
}
 * 
 * 
 */