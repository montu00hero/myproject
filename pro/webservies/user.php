<?php

//add RESTED extionsion in firefox

//if($_SERVER['REQUEST_METHOD']=='GET')


if($_SERVER['REQUEST_METHOD']=='POST')
{  
    $arr=array(
      'PRICE'=>array('100','145','150','170'),
       'Tshirt'=>array('S','M','L','XL') 
    );
    
    //$user=$_GET['user'];
    $user=$_POST['user'];
    $email="sam@gmail.com";
    $address=array("Kochi","Amritsar","Agar");
    
    $add=  array_merge($address,$arr);
    
    $addr=$add;
    //sort($address);
    $data['user']=$user;
    $data['email']=$email;
    $data['address']=$addr;

   echo json_encode($data);  //sending result to caller
    
}  

