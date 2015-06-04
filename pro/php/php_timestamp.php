<?php

$time=strtotime('12-04-2015 0:0:0');

$times=strtotime('12-04-2015 2:2:2');

echo $time;
echo'</br>';
echo $times;
echo'</br>';
echo $diff= $times-$time;
echo'</br>';
//echo $s=date('h:i:s',$diff); // not correct because we can't convert the time difference into date time 


echo $eh=$diff/3600;


echo'</br>';
echo "converting seconds into hours min sec:"; 
echo $diff=gmdate('h:i:s',$diff);

echo'</br>';



echo $today = date("Y-m-d H:i:s");    

//--------------second-----------------------------------
function secondsToTime($seconds) {
	$dtF = new DateTime("@0");
	$dtT = new DateTime("@$seconds");
	return $dtF->diff($dtT)->format('%a days, %h hours, %i minutes and %s seconds');
}


secondsToTime(253214);



//-------------------------------------------------------------

$seconds = strtotime("2010-10-20 08:10:00") - strtotime("2008-12-13 10:42:00");

$days    = floor($seconds / 86400);
$hours   = floor(($seconds - ($days * 86400)) / 3600);
$minutes = floor(($seconds - ($days * 86400) - ($hours * 3600))/60);
$seconds = floor(($seconds - ($days * 86400) - ($hours * 3600) - ($minutes*60)));

//----------------------------------------------------------------------






    /*  mktime  */ 
    
    list($a_yrs,$a_mon,$a_date)=explode('-',$date);
    list($a_hrs,$a_min,$a_sec)=explode(':',$time);
    
    $arrivalDateTime=mktime($a_hrs,$a_min,$a_sec,$a_mon,$a_date,$a_yrs);  
   // echo $arrivalDateTime;   
    
    /* ----------------------------------------- */


    
    /*mktime  */
    
    list($d_yrs,$d_mon,$d_date)=explode('-',$date);
    list($d_hrs,$d_min,$d_sec)=explode(':',$time);
    
    $departureDateTime=mktime($d_hrs,$d_min,$d_sec,$d_mon,$d_date,$d_yrs);  
   // echo $departureDateTime;   
  
    /*-------------------*/
    
    
    $mk_dur_diff=$arrivalDateTime-$departureDateTime;
    //$mk_dur_diff=$ArrivalDateTime-$DepartureDateTime;
    
  //  $mk_d_t=gmdate('Y-m-d H:i:s',($mk_dur_diff));
   // echo $mk_dur_diff;
   
// echo $ArrivalDateTime;die;
    
    
    
    
    //------------DateTime::setTimezone
// date_timezone_set----------------
    
    

$date = date_create('2000-01-01', timezone_open('Pacific/Nauru'));
echo date_format($date, 'Y-m-d H:i:sP') . "\n";

date_timezone_set($date, timezone_open('Pacific/Chatham'));
echo date_format($date, 'Y-m-d H:i:sP') . "\n";


?>
