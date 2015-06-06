<?php

function bus_search(){
	$request='{ "SourceId": "1158","DestinationId": "1192", "DateOfJourney": "2015-06-15", "SourceName":"Rajkot",
	"DestinationName": "Jamnagar", "IsDomestic": true, "MemberMobileNo": "8652282111",
	"MemberMobilePin": "7972"}';

	$header=array('Content-Type:application/json', 'Accept-Encoding:gzip, deflate', 'x-Username:Gemoratti', 'x-Password:GEM123456');

	$cs = curl_init();
	curl_setopt($cs, CURLOPT_URL, 'http://api.jbspl.com/api/BusBooking/Search');
	curl_setopt($cs, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($cs, CURLOPT_POST, 1);
	curl_setopt($cs, CURLOPT_HTTPHEADER, $header);
	curl_setopt($cs, CURLOPT_POSTFIELDS, $request);

	$response = curl_exec($cs);
   
	curl_close($cs);

	echo "<pre>";
	print_r(json_decode($response));
}

function get_agency_balance(){
	$request='{	"isAirlineLLC": "true",
	"MemberMobileNo": "8652282111",
	"MemberMobilePin": "7972"}';

	$header=array('Content-Type:application/json', 'Accept-Encoding:gzip, deflate', 'x-Username:Gemoratti', 'x-Password:GEM123456');

	$cs = curl_init();
	curl_setopt($cs, CURLOPT_URL, 'http://api.jbspl.com/api/BusBooking/GetAgencyBalance');
	curl_setopt($cs, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($cs, CURLOPT_POST, 1);
	curl_setopt($cs, CURLOPT_HTTPHEADER, $header);
	curl_setopt($cs, CURLOPT_POSTFIELDS, $request);

	$response = curl_exec($cs);

	curl_close($cs);

	echo "<pre>";
	print_r(json_decode($response));
}
function GetAllCities(){
	
	$header=array('Content-Type:application/json', 'Accept-Encoding:gzip, deflate', 'x-Username:Gemoratti', 'x-Password:GEM123456');

	$cs = curl_init();
	curl_setopt($cs, CURLOPT_URL, 'http://api.jbspl.com/api/BusBooking/GetAllCities');
	curl_setopt($cs, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($cs, CURLOPT_HTTPHEADER, $header);
	
	$result = curl_exec($cs);
        
    $response=json_decode($result);
     
          echo '<pre>';print_r($response); exit;
           $i=0;
      foreach($response->WSBusCityList as $r)   
           {    
           	//echo count($r);
         
         
            echo '<pre>';print_r($r->CityId); 

                 $i++;
           }

exit;
    //echo "<pre>";
    // foreach($response as $v){
		//print_r($v);
	//}
    
    
	curl_close($cs);
    
	echo "<pre>";
	var_dump($response,true);
	
	$res = ["WSBusCityList"][0]["CityId"];
	echo $res;
}

function GetAllBusSourceCities(){
	
	$header=array('Content-Type:application/json', 'Accept-Encoding:gzip, deflate', 'x-Username:Gemoratti', 'x-Password:GEM123456');

	$cs = curl_init();
	curl_setopt($cs, CURLOPT_URL, 'http://api.jbspl.com/api/BusBooking/GetAllBusSourceCities');
	curl_setopt($cs, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($cs, CURLOPT_HTTPHEADER, $header);
	
	$response = curl_exec($cs);

	curl_close($cs);

	echo "<pre>";
	print_r(json_decode($response));
}

function GetBusDestinationBySourceCityCode(){
	
	$request='{ "sourceCityCode": "1158","MemberMobileNo": "8652282111","MemberMobilePin": "7972"}';
	
	$header=array('Content-Type:application/json', 'Accept-Encoding:gzip, deflate', 'x-Username:Gemoratti', 'x-Password:GEM123456');

	$cs = curl_init();
	curl_setopt($cs, CURLOPT_URL, 'http://api.jbspl.com/api/BusBooking/GetBusDestinationBySourceCityCode');
	curl_setopt($cs, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($cs, CURLOPT_POST, 1);
	
	curl_setopt($cs, CURLOPT_HTTPHEADER, $header);
	curl_setopt($cs, CURLOPT_POSTFIELDS, $request);
	
	$response = curl_exec($cs);

	curl_close($cs);

	echo "<pre>";
	print_r(json_decode($response));
}


GetAllCities();
//GetBusDestinationBySourceCityCode();
?>
