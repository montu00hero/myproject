<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Hotel extends CI_Controller {

    public function __construct() {
        parent::__construct();

        $this->load->model('Auth_Model');
        $current_url = $_SERVER['QUERY_STRING'] ? '?' . $_SERVER['QUERY_STRING'] : '';
        $current_url = $this->config->site_url() . $this->uri->uri_string() . $current_url;
        $url = array(
            'continue' => $current_url,
        );
        $this->session->set_userdata($url);
        $this->helpMenuLink = "";
        $this->load->model('Help_Model');
        $this->helpMenuLink = $this->Help_Model->fetchHelpLinks();
        $this->load->model('example_model');
        $this->load->model('Hotel_Model');
	$this->load->model('flight_model');
        $this->load->helper('hotel_helper');
        $this->load->model('booking_model');
        $this->load->model('email_model');
	$this->load->model('xml_model');
        $this->load->library('xml_to_array');
		
	}

    public function index(){
        $request = $this->input->get();
        //echo '<pre>';print_r($request);die;
        $data['request_array'] = $request;
        $request = json_encode($request);
        $data['req'] = $req = json_decode($request);
        $data['request'] = $request = base64_encode($request);
        $data['city_data'] = $this->hotel_model->get_city_byId($req->city_code)->row();
        //echo '<pre>';print_r($req);die;
        $this->load->view('hotel/results', $data);
    }

    public function home(){
    	$language = $this->uri->segment('3');
    	$current_url = $this->uri->segment('4');
    	if($language){
    		$this->lang->load('home', $language);
    		$language = array('language' => $language);
    		$this->session->set_userdata($language);
    		$current = base64_decode($current_url);
    		redirect($current);
    	}else{
    		$this->lang->load('home', 'english');
    	}
    	$data['banners'] = $this->Help_Model->getHomeSettings();
    	$data['portfolio'] = $this->Help_Model->getAllPortfolio();
    	$this->load->view('hotel/hotel_index',$data);	
    }
    public function hotel_index(){
		$current_url = $this->uri->segment('4');
		
		$data['banners'] = $this->Help_Model->getHomeSettings();
		$data['portfolio'] = $this->Help_Model->getAllPortfolio();

		//Transfer Data Starts
		$data['time'] = $this->transfer_model->get_transfer_time();
		$data['country'] = $this->transfer_model->get_country();
		$this->load->view('hotel/hotel_index_v1',$data);	
	}
    public function search() {
        $request = $this->input->get();
        #echo '<pre>';print_r($request);
        #exit;
        $checkin_explode_date = explode('-', $_GET['hotel_checkin']);
        $checkout_explode_date = explode('-', $_GET['hotel_checkout']);

        $check_in = $checkin_explode_date['2'] . '-' . $checkin_explode_date['1'] . '-' . $checkin_explode_date['0'];
        $check_out = $checkout_explode_date['2'] . '-' . $checkout_explode_date['1'] . '-' . $checkout_explode_date['0'];
        $city_data = $this->hotel_model->get_city_data($_GET['city'])->row();
        $data['city'] = $_GET['city'];
        $data['city_code'] = $city_data->city_id;
        $data['countrycode'] = $city_data->country_code;
        $data['check_in'] = $check_in;
        $data['check_out'] = $check_out;
        

        
        $this->session->set_userdata('check_in', $check_in);
        $this->session->set_userdata('check_out', $check_out);
        
        
        $data['rooms'] = $_GET['rooms'];
        $data['adult'] = $_GET['adult'];
        $data['child'] = $_GET['child'];
		$c1=array();
		if(isset($_GET['childAge_1']))
		{
        $c1[]= $_GET['childAge_1'];
		}
		else
		{
		$c1[]='';
		}
		if(isset($_GET['childAge_2']))
		{
        $c1[]= $_GET['childAge_2'];
		}
		else
		{
					$c1[]='';
		}
		if(isset($_GET['childAge_3']))
		{
        $c1[]= $_GET['childAge_3'];
		}
		else
		{		$c1[]='';
		}
		 $data['childAges'] = $c1;
        $data['adult_count'] = array_sum($_GET['adult']);
        $data['child_count'] = array_sum($_GET['child']);

        $checkin_date = strtotime($check_in);
        $checkout_date = strtotime($check_out);
        $absDateDiff = abs($checkout_date - $checkin_date);

        $data['nights'] = floor($absDateDiff/(60*60*24));
        // $data['childAge_1'] = $_GET['childAge_1'];
        // $data['childAge_2'] = $_GET['childAge_2'];
        // $data['childAge_3'] = $_GET['childAge_3'];

      //  echo '<pre>';print_r($data);die;
        $query = http_build_query($data);
       
               
        $url = WEB_URL.'/hotel/?'.$query;
        redirect($url);
		
    }

    public function GetResults($request=''){
		
		
        $request = base64_decode($request);
        $data['request'] = $request = json_decode($request);
        
       # echo "<pre>";
       # print_r($data['request']);
       # exit;
		
		$country_m = explode(",",$request->city);
		$m_country = '';
		if(isset($country_m[1]))
		{
			$m_country = trim($country_m[1]);
		}
		
		 
      //  $getHotelAvailabilityRQ_RS = getHotelAvailabilityRQ($request);
        $HotelValuedAvailRQ_RS = HotelValuedAvailRQ($request);
        
      # echo '<pre>';print_r($HotelValuedAvailRQ_RS);exit;
        
        $aMarkup_hb = $this->account_model->get_markup('TEK',$m_country); //get markup
        $aMarkuphb = $aMarkup_hb['markup'];
		
		// $aMarkup_b = $this->account_model->get_markup('Booking.com',$m_country); //get markup
      //  $aMarkupb = $aMarkup_b['markup'];
		
          /* get agent markup  */                                                   
        $MyMarkup = $this->account_model->get_my_markup(); 
        $myMarkup = $MyMarkup['markup'];
        $nType=$MyMarkup['type'];
        
       //    print_r($MyMarkup); EXIT;
        
       /* Agent Commission  */ 
         $agent_id=$this->session->userdata('b2b_id');
         $agentCommission=$this->account_model->get_agent_commission($agent_id);
          
         /*echo "<pre>";
          print_r($agentCommission); exit;
         */
        
        
        //echo '<pre>';print_r($b_results);die;
        $hb_results = $this->formatResponse($HotelValuedAvailRQ_RS, $aMarkuphb, $myMarkup, $nType, $agentCommission); //Format Hotelsbed Response
        if(!$hb_results){
            $hb_results = array();
        }
		
        //$hb_results = array();        
       // echo '<pre>';print_r($hb_results);exit;
	   //  $results = array_merge($b_results,$hb_results);
        $results = $hb_results;
      #  echo '<pre>';print_r($results);die;
        if($results){
            $data['hotels'] = $results;
            $response['result'] = $this->load->view('hotel/ajax_results', $data, true);
            $response['status'] = 1;
        }else{
            $response['result'] = $this->load->view('hotel/no_result',$data,true);
            $response['status'] = 0;
        }
        echo json_encode($response);
    }


    public function formatBookingDotcomResponse($request, $getHotelAvailabilityRQ_RS, $aMarkup, $myMarkup){
        $getHotelAvailabilityRS = $getHotelAvailabilityRQ_RS['getHotelAvailabilityRS'];
        if(empty($getHotelAvailabilityRS)){
            return false;
        }
        $getHotelAvailabilityRS = new SimpleXMLElement($getHotelAvailabilityRS);
        if(isset($getHotelAvailabilityRS->fault)){
            return false;
        }
        $Hotels = $getHotelAvailabilityRS->result;
        $api_data = $this->hotel_model->get_api_credentials('Booking.com')->row(); //get api credintials
        $credintials = $api_data->username.':'.$api_data->password;
        $i=1;$hs = array();
        foreach($Hotels as $hotel){
            $hotel_id = $hotel->hotel_id;
            //if(!empty($hotel_id)){
                $hurl = "https://".$credintials."@distribution-xml.booking.com/xml/bookings.getHotels?hotel_ids=".$hotel_id;
                //$hsXML = curl($hurl);
                // $hoXML = new SimpleXMLElement($hsXML);
                // foreach ($hoXML as $hxml){
                //     $hotel_name = (string) $hxml->name;
                //     $address = (string) $hxml->address;
                //     $city = (string) $hxml->city;
                //     $ranking = (string) $hotel->ranking;
                //     $url = (string) $hxml->url;
                //     $pagename = (string) $hxml->pagename;
                //     $preferred = (string) $hxml->preferred;
                // }
                $CurrencyCode = (string) $hotel->currencycode;
                $TotalPrice_API = (string) $hotel->minrate;
                $TotalFare = $hotel->minrate;
                $TotalFare = $this->flight_model->currency_convertor($TotalFare,$CurrencyCode,CURR);
                $TotalFare = $this->account_model->PercentageToAmount($TotalFare,$aMarkup);
                $Markup = $this->account_model->PercentageAmount($TotalFare,$myMarkup);
                $TotalFare = $this->account_model->PercentageToAmount($TotalFare,$myMarkup);

                $hotel_id = 'BCOM'.$i;
                $hs[$i]['Hotel_Id'] = $hotel_id;
                $hs[$i]['TotalFare'] = $TotalFare;
                $hs[$i]['TotalPrice_API'] = $TotalPrice_API;
                $hs[$i]['APICurrencyType'] = $CurrencyCode;
                $hs[$i]['SITECurrencyType'] = CURR;
                $hs[$i]['MyMarkup'] = $Markup;
                $hs[$i]['API'] = 'Booking.com';
                $hs[$i]['Status'] = 'Available';
                $hs[$i]['HotelName'] = (string) $hotel->name;
                $hs[$i]['HotelCode'] = (string) $hotel->hotel_id;
                $hs[$i]['Address'] = (string) $hotel->address;
                $hs[$i]['City'] = (string) $hotel->city;
                $hs[$i]['Ranking'] = (string) $hotel->ranking;
                $hs[$i]['URL'] = (string) $hotel->url;
                $hs[$i]['Pagename'] = (string) $hotel->pagename;
                $hs[$i]['Preferred'] = (string) $hotel->preferred;

				$cinn = date("Y-n-j",strtotime($request->check_in));
				$coutt = date("Y-n-j",strtotime($request->check_out));
				$cin = explode("-",$cinn);
				$cout = explode("-",$coutt);
                $Booking = array(
                    'aid' => '398698',
                    'checkin_monthday' => $cin[2],
                    'checkin_year_month' => $cin[0].'-'.$cin[1],
                    'checkout_monthday' => $cout[2],
                    'checkout_year_month' => $cout[0].'-'.$cout[1]
                    
                );
                //$BookingURL = "https://".$credintials."@secure-distribution-xml.booking.com/xml/bookings.processBooking?";
                $BookingURL = (string) $hotel->url."?";
                $query = http_build_query($Booking);
                $BookingURL = $BookingURL.$query;
                $hs[$i]['BookingURL'] = $BookingURL;
            //}
          
          
            /*$gblockURL = "https://qodariyah1:4805@distribution-xml.booking.com/xml/bookings.getBlockAvailability?arrival_date=".$arrival."&departure_date=".$departure."&hotel_ids=".$xml->hotel_id;
            $gblocksXML = $this->curl($gblockURL);
            $gblockoXML = new SimpleXMLElement($gblocksXML);

            $processBookingURL = "https://qodariyah1:4805@secure-distribution-xml.booking.com/xml/bookings.processBooking?affiliate_id=392546&begin_date=".$arrival."&end_date=".$departure."&guest_country=".$b."&guest_email=sreenath.veer@gmail.com&guest_telephone=8095369525&hotel_id=98251";*/
            $i++;
        }
        //echo '<pre>';print_r($hs);die;
        return $hs;
    }

    public function formatResponse($HotelValuedAvailRQ_RS, $aMarkup, $myMarkup, $nType, $agentCommission){
        $HotelValuedAvailRS = $HotelValuedAvailRQ_RS['HotelValuedAvailRS'];
          
        if(isset( $agentCommission->type)){
           $ctype = $agentCommission->type; 
        }    
        if(isset($agentCommission->Gold))
        {
            $agent_commission=$agentCommission->Gold;
        }
        if(isset($agentCommission->Silver))
        {
            $agent_commission=$agentCommission->Silver;
        }
        if(isset($agentCommission->Platinum))
        {
            $agent_commission=$agentCommission->Platinum;
        }
        if(isset($agentCommission->Default))
        {
            $agent_commission=$agentCommission->Default;
        }
        if(isset($agentCommission->Bronze))
        {
            $agent_commission=$agentCommission->Bronze;
        }  
        
       // echo $agent_commission;exit;
        #echo "<pre>";
      #  print_r($HotelValuedAvailRS);
       #die;
      $i=0;
	  if(!isset($HotelValuedAvailRS['HotelSearchResult']['HotelResults'][0]))
	  {
		  return false;
	  }
	  
	   $TraceId = $HotelValuedAvailRS['HotelSearchResult']['TraceId'];
	  	$hs = array();
	  	 
        foreach ($HotelValuedAvailRS['HotelSearchResult']['HotelResults'] as $key => $ServiceHotel) {
          
				  $hotel_code = $ServiceHotel['HotelCode'];
  $hotel_name = $ServiceHotel['HotelName'];
   $StarRating = $ServiceHotel['StarRating'];
    $HotelDescription = $ServiceHotel['HotelDescription'];
    $Latitude = $ServiceHotel['Latitude'];
    $Longitude = $ServiceHotel['Longitude'];
	 $HotelPicture = base64_encode($ServiceHotel['HotelPicture']);
	  $HotelAddress = $ServiceHotel['HotelAddress'];
 $ResultIndex = $ServiceHotel['ResultIndex'];
                $TotalPrice_API = $ServiceHotel['Price']['PublishedPriceRoundedOff'];
                
                
                $TotalFare =$ServiceHotel['Price']['PublishedPriceRoundedOff'];
		$CurrencyCode=$ServiceHotel['Price']['CurrencyCode'];
                $TotalFare = $this->flight_model->currency_convertor($TotalFare,$CurrencyCode,CURR);
                
               if(isset($ctype)){
                /*calculating netrate*/
                 if($ctype=='0')
                   {/*calculating percent commission*/
                    $Commission_amount = $this->account_model->PercentageAmount($TotalFare,$agent_commission);
                    $netRate = $TotalFare-$Commission_amount;
                     
                   }
                if($ctype=='1')
                { /*calculating addition commission*/
                 $Commission_amount = $this->flight_model->currency_convertor($agent_commission,$CurrencyCode,CURR);
                 $netRate = $TotalFare-$Commission_amount;
                }
               }
               //  print_r($TotalFare);
               // print_r($Commission_amount);exit;
                /*   */
               
                $TotalFare = $this->account_model->PercentageToAmount($TotalFare,$aMarkup);
                
                //$Markup = $this->account_model->PercentageAmount($TotalFare,$myMarkup);
                //$TotalFare = $this->account_model->PercentageToAmount($TotalFare,$myMarkup);
//echo $hotel_code.'--';
//echo '<pre/>';
//print_r($hs);   
                if($nType=='0'){  
                    /*calculating percent markup*/
                $Markup = $this->account_model->PercentageAmount($TotalFare,$myMarkup);
                $TotalFare = $this->account_model->PercentageToAmount($TotalFare,$myMarkup);
                // echo"%"; print_r($TotalFare);exit;
                }
                if($nType=='1')
                {
                   /*calculating addition markup*/
                $Markup = $this->flight_model->currency_convertor($myMarkup,$CurrencyCode,CURR);
                $TotalFare = $TotalFare + $Markup;
                // echo"rs"; print_r($TotalFare);exit;
                }
                
                
                
                
                if (!$this->in_array_r($hotel_code, $hs)) {
                    $hotel_id = 'TEK'.$i;
                    $hs[$i]['Hotel_Id'] = $hotel_code;
                    $hs[$i]['TotalFare'] = $TotalFare;
                    $hs[$i]['TotalPrice_API'] = $TotalPrice_API;
                    $hs[$i]['APICurrencyType'] = $CurrencyCode;
                    $hs[$i]['SITECurrencyType'] = CURR;
                    $hs[$i]['MyMarkup'] = $Markup;
                    $hs[$i]['API'] = 'TEK';
                    $hs[$i]['Status'] = 'Available';
                    $hs[$i]['HotelCode'] = $hotel_code;
                    $hs[$i]['HotelName'] = $hotel_name;
					$hs[$i]['HotelDescription'] = $HotelDescription;
					$hs[$i]['HotelPicture'] = $HotelPicture;
					$hs[$i]['HotelAddress'] = $HotelAddress;
                    $hs[$i]['Star'] = $StarRating;
					$hs[$i]['TraceId'] = $TraceId;
					$hs[$i]['ResultIndex'] = $ResultIndex;
					$hs[$i]['TokenId'] =$HotelValuedAvailRQ_RS['TokenId'];
                    $hs[$i]['netrate']=$netRate;                    
                                        
					
                }
              $check_in =  $this->session->userdata('check_in');
               $check_out = $this->session->userdata('check_out');
                
                
                 $insertion_data[] = array(
							'ResultIndex' =>$ResultIndex,
                            'hotel_code' => $hotel_code,
                            'hotelname' => $hotel_name,
                            'star' => $StarRating,
                            't_markup' => $Markup,
                            'longitude' => $Longitude,
                            'latitude' => $Latitude,
                            'description' => $HotelDescription,
                            'image' => $HotelPicture,
                            'TraceId' => $TraceId,
                            'TokenId' => $HotelValuedAvailRQ_RS['TokenId'],
                            'check_in' => $check_in,
                            'check_out' => $check_out
                            );
                       
               
              
				//echo '<pre/>';
				//print_r($hs);
           
            $i++;
        }
        
        $this->hotel_model->delete_api_hotel_data_t();
                if (isset($insertion_data[0])) {
                $this->db->insert_batch('api_hotel_data_t', $insertion_data);
            }
        
      //  echo '<pre/>';print_r($hs);die;
		$hs= array_values($hs);
        return $hs;
        //echo '<pre>';print_r($response->ServiceHotel);die;
    }
	function in_array_r($needle, $haystack, $strict = false) {
    foreach ($haystack as $item) {
        if (($strict ? $item === $needle : $item == $needle) || (is_array($item) && $this->in_array_r($needle, $item, $strict))) {
            return true;
        }
    }
 
    return false;
}
		public function nearbyhotels($lat, $lang,$request,$hotelcode,$response)
		{
			 $data['hotel_search_result'] = $this->hotel_model->get_nearby_hotels($lat,$lang);
		
	 $data['hotel_code'] = base64_encode(base64_encode(json_encode($hotelcode)));
        $data['request'] = $request;
		 $data['response'] = $response;
        $hotel_search_result = $this->load->view('hotel/nearbyhotel', $data, true);
        print json_encode(array(
            'hotel_search_result' => $hotel_search_result
        ));
		}
    public function detail($hotel_id='',$request='',$traceid,$resultid,$TokenId,$response='') {
        if (!empty($hotel_id)) {
			
			
            $hotel_id = json_decode(base64_decode(base64_decode($hotel_id)));
                       
          $hotel_data = json_decode(base64_decode($response));
          
          $HotelValueddetailsRQ_hotel_id_RS = HotelValueddetailsRQ_hotel_id($request,$hotel_id,$traceid,$resultid,$TokenId);
		
		#echo "<pre>";
		# print_r($HotelValueddetailsRQ_hotel_id_RS);
        # exit;
          
           if ($HotelValueddetailsRQ_hotel_id_RS['HotelValuedAvailRS'] != '') {
			
			$HotelValueDetails = $HotelValueddetailsRQ_hotel_id_RS['HotelValuedAvailRS']['HotelInfoResult']['HotelDetails'];
			
			$data['Description'] = $HotelValueDetails['Description'];
			$data['Attractions'] = $HotelValueDetails['Attractions'];
			$data['HotelFacilities'] = $HotelValueDetails['HotelFacilities'];
			$data['Images'] = $HotelValueDetails['Images'];
			$data['HotelContactNo'] = $HotelValueDetails['HotelContactNo'];
			$data['Latitude'] = $HotelValueDetails['Latitude'];
			$data['Longitude'] = $HotelValueDetails['Longitude'];
			$data['StarRating'] = $HotelValueDetails['StarRating'];
			
		}
          
          
		    $data['hotel_code'] = $hotel_id;
           
            $data['hotel_image'] = $images = base64_decode($hotel_data->HotelPicture);
            $data['request'] = json_decode(base64_decode($request));
			$data['request_data'] = $request;
			$data['response_data'] = $response;
            $data['response'] = json_decode(base64_decode($response));
            $data['HotelName'] = $hotel_data->HotelName;
            $data['HotelDescription'] = $hotel_data->HotelDescription;
            $data['HotelAddress'] = $hotel_data->HotelAddress;
			$data['Star'] = $hotel_data->Star;
			$data['traceid'] = $traceid;
			$data['resultid'] = $resultid;$data['TokenId'] = $TokenId;
          #  echo '<pre>';print_r($data);die;
            $this->load->view('hotel/hotel_details', $data);
        } else {
            redirect(WEB_URL);
        }
        
    }

     public function rooms($api,$hotel_code,$request_data,$traceid,$resultid,$TokenId){
        $session_id = $this->session->userdata('session_id');
        $this->hotel_model->delete_temp_results($session_id, $hotel_code);
        $request_data_v1 = json_decode(base64_decode($request_data));
		$no_of_rooms = $request_data_v1->rooms;
		#exit;
		
		$hotel_code = str_replace('%7C','|',$hotel_code);
		
		$HotelValuedAvailRQ_hotel_id_RS = HotelValuedAvailRQ_hotel_id($request_data_v1,$hotel_code,$traceid,$resultid,$TokenId);
		#echo "<pre>";
		#print_r($HotelValuedAvailRQ_hotel_id_RS);
		
		#exit;
		
		
		
		if ($HotelValuedAvailRQ_hotel_id_RS['HotelValuedAvailRS'] != '') {
            
			$HotelRoomsDetails = $HotelValuedAvailRQ_hotel_id_RS['HotelValuedAvailRS']['GetHotelRoomResult']['HotelRoomsDetails'];
			
			#echo "<pre>";
			#print_r($HotelValuedAvailRQ_hotel_id_RS);
			#exit;
            $ii = 0;
            $cvv = 0;
            $uniq_id = rand(10000,9999999);
            $ResultIndex = $HotelValuedAvailRQ_hotel_id_RS['HotelValuedAvailRQ']['ResultIndex'];
            $RoomCombinations = $HotelValuedAvailRQ_hotel_id_RS['HotelValuedAvailRS']['GetHotelRoomResult']['RoomCombinations'];
            $InfoSource = $RoomCombinations['InfoSource'];
            $room_cobinations = "";
           foreach($RoomCombinations['RoomCombination'] as $value)
				{
				   	$room_cobinations .= json_encode($value);
				   	$room_cobinations .= "|";
				}
				
         
                         $agent_id=$this->session->userdata('b2b_id');
                         $agentCommission=$this->account_model->get_agent_commission($agent_id);
      
                        if(isset( $agentCommission->type)){
                           $ctype = $agentCommission->type; 
                        }    
                        if(isset($agentCommission->Gold))
                        {
                            $agent_commission=$agentCommission->Gold;
                        }
                        if(isset($agentCommission->Silver))
                        {
                            $agent_commission=$agentCommission->Silver;
                        }
                        if(isset($agentCommission->Platinum))
                        {
                            $agent_commission=$agentCommission->Platinum;
                        }
                        if(isset($agentCommission->Default))
                        {
                            $agent_commission=$agentCommission->Default;
                        }
                        if(isset($agentCommission->Bronze))
                        {
                            $agent_commission=$agentCommission->Bronze;
                        }
           
                        /* get agent markup */ 
                        $MyMarkup = $this->account_model->get_my_markup(); 
                        $myMarkup = $MyMarkup['markup'];
                        $nType=$MyMarkup['type'];
                           //  echo"dd"; echo($myMarkup);
                        
                                
                                
                        $i=0;       
            foreach ($HotelRoomsDetails as $val) {
                
                $currencyva1 = $val['Price']['CurrencyCode'];
           		 $currencyv1 = $val['Price']['CurrencyCode'];
		          
			       
                        $RatePlanCode = $val['RatePlanCode'];
                        $roomTypeVal = $val['RoomTypeCode'];

                        $charVal = $val['SequenceNo'];

                      
                        $roomv1 =  $val['RoomTypeName'];

                      if(empty($val['Amenities']))
                      {
						  $boardv1 =  "No Amenities";
					  }
					  else
					  {
                        $boardv1 =  $val['Amenities'][0];
                       } 
                        
                        $CancellationPolicies = $val['CancellationPolicies'];
                        $LastCancellationDate = $val['LastCancellationDate'];
                        $CancellationPolicy = $val['CancellationPolicy'];
                        $des_offer_value = $val['Price']['Discount'];
                        $day_price = $val['DayRates'][0]['Amount'];
                        
                        
                      //echo $currencyv1;
                        //echo $amount->item(0)->nodeValue;exit;
						
		$country_m = explode(",",$request_data_v1->city);
		$m_country = '';
		if(isset($country_m[1]))
		{
			$m_country = trim($country_m[1]);
		}
                        $aMarkup1 = $this->account_model->get_markup('TEK',$m_country); //get markup
                        $aMarkup = $aMarkup1['markup'];

                        // $MyMarkup = $this->account_model->get_my_markup(); //get agent markup
                        //$myMarkup = $MyMarkup['markup'];
                        
                        
                         /* agent commission */ 
                         
                        
                        
                        
                        
                        $org_amt = $val['Price']['PublishedPriceRoundedOff'];
                       // $total_cost = $val['Price']['PublishedPriceRoundedOff'];
                          $org_api_amt = $val['Price']['PublishedPriceRoundedOff'];

                        $org_amt = $this->flight_model->currency_convertor($org_amt,$currencyv1,CURR);
                        $org_amt = $this->account_model->PercentageToAmount($org_amt,$aMarkup);
                        
                        if(isset($ctype)){
                         /*calculating  netrate*/
                         if($ctype=='0')
                             {/*calculating percent commission*/
                               $Commission_amount = $this->account_model->PercentageAmount($org_amt,$agent_commission);
                               $netrate = $org_amt-$Commission_amount;  //net rate for agent
                              }
                          if($ctype=='1')
                           { /*calculating addition commission*/
                            $Commission_amount = $this->flight_model->currency_convertor($agent_commission,$currencyv1,CURR);
                            $netrate = $org_amt-$Commission_amount;  //net rate for agent
                           }
                          }
                       else {$netrate=0;}
                       /*  
                        echo'<pre>';  print_r($agentCommission);
                        echo'<pre>'; print_r($org_amt);
                        echo'<pre>';  print_r($MyMarkup); exit;   */
                      
               if($nType=='0'){  
                    /*calculating % markup*/
                $Markup = $this->account_model->PercentageAmount($org_amt,$myMarkup);
                $org_amt = $this->account_model->PercentageToAmount($org_amt,$myMarkup);
                // echo"%"; print_r($TotalFare);exit;
                }
                if($nType=='1')
                {
                   /*calculating + markup*/
                  $Markup = $this->flight_model->currency_convertor($myMarkup,$currencyv1,CURR);
                  $org_amt = $org_amt + $Markup;
                }
                          
                        //    echo'<pre>';  print_r($agentCommission);
                          // echo'<pre>'; print_r($org_amt);
                          //echo'<pre>';  print_r($MyMarkup); 
                        
                  //      $org_amt = $this->account_model->PercentageToAmount($org_amt,$myMarkup);

                    /*    $total_cost = $this->flight_model->currency_convertor($total_cost,$currencyv1,CURR);
                        $total_cost = $this->account_model->PercentageToAmount($total_cost,$aMarkup);
                        $Markup = $this->account_model->PercentageAmount($total_cost,$myMarkup);
                        $total_cost = $this->account_model->PercentageToAmount($total_cost,$myMarkup);
                      */
                $total_cost=$org_amt;
                        
                       $RoomIndex = $val['RoomIndex'];
                       $RatePlanCode = $val['RatePlanCode'];
                       $RatePlanName = "";
                       $RoomTypeCode = $val['RoomTypeCode'];
                       $RoomTypeName = $val['RoomTypeName'];
                       $CurrencyCode = $val['Price']['CurrencyCode'];
                       $RoomPrice = $val['Price']['RoomPrice'];
                       $Tax = $val['Price']['Tax'];
                       $ExtraGuestCharge = $val['Price']['ExtraGuestCharge'];
                       $ChildCharge = $val['Price']['ChildCharge'];
                       $OtherCharges = $val['Price']['OtherCharges'];
                       $Discount = $val['Price']['Discount'];
                       $PublishedPrice = $val['Price']['PublishedPrice'];
                       $PublishedPriceRoundedOff = $val['Price']['PublishedPriceRoundedOff'];
                       $OfferedPrice = $val['Price']['OfferedPrice'];
                       $OfferedPriceRoundedOff = $val['Price']['OfferedPriceRoundedOff'];
                       $AgentCommission = $val['Price']['AgentCommission'];
                       $AgentMarkUp = $val['Price']['AgentMarkUp'];
                       $ServiceTax = $val['Price']['ServiceTax'];
                       $TDS = $val['Price']['TDS'];
                       
                       
                      
                        $insertion_data[$i] = array(
                            'session_id' => $this->session->userdata('session_id'),
                            'api' => $api,
                            'request' => $request_data,
                            'hotel_code' => $hotel_code,
                            'room_code' => $roomTypeVal,
                            'room_type' => $roomv1,
                            'total_cost' => $total_cost,
                            'w_markup' => $Markup,
                            'status' => 'Available',
                            'inclusion' => $boardv1,
                            'shurival' => $RatePlanCode,
                            'charval' => $charVal,
                           
                            'org_amt' => $org_amt,
                            'xml_currency' => $currencyv1,
                            
                            'des_offer_value' => $des_offer_value,
                            'uniq_id'   => $uniq_id,
                            'cancel_policy' => $CancellationPolicy,
                            'noofrooms' => $no_of_rooms,
                            'roomcombinations' => $room_cobinations,
                            'infosource' => $InfoSource,
                            'RoomIndex'  => $RoomIndex,
                            'RatePlanCode'=>$RatePlanCode,
                            'RatePlanName'=>$RatePlanName,
                            'RoomTypeCode'=>$RoomTypeCode,
                            'RoomTypeName'=>$RoomTypeName,
                            'CurrencyCode'=>$CurrencyCode,
                            'RoomPrice'=>$RoomPrice,
                            'Tax'=>$Tax,
                            'ExtraGuestCharge'=>$ExtraGuestCharge,
                            'ChildCharge'=>$ChildCharge,
                            'OtherCharges'=>$OtherCharges,
                            'Discount'=>$Discount,
                            'PublishedPrice'=>$PublishedPrice,
                            'PublishedPriceRoundedOff'=>$PublishedPriceRoundedOff,
                            'OfferedPrice'=>$OfferedPrice,
                            'OfferedPriceRoundedOff'=>$OfferedPriceRoundedOff,
                            'AgentCommission'=>$AgentCommission,
                            'AgentMarkUp'=>$AgentMarkUp,
                            'ServiceTax'=>$ServiceTax,
                            'TDS'=>$TDS,
                            'NetRate'=>$netrate
                            );
                     $i++;
            }
            

    // echo"<pre>";print_r($insertion_data); 
           $this->hotel_model->delete_api_hotel_detail_t();
            // $this->Hotel_Model->insert_hotelsbed_temp_result($this->sec_id,$api,$codev1,$roomTypeVal,$roomv1,$total_cost,'Available',$boardv1,$shruiVal,$charVal,$adult,$child,$boardTypeVal,$token,$inoffcode,$contractnameVal,$destCodeVal,$shortname,$RoomCountval,$total_cost,$org_amt,$currencyv1,$c_val,$total_cost,$org_city,$date_final,$Promotionsaa,$total_cost,$ShortNameaa,$Classification_val,$des_offer_value,$Remarksaa);  	
            if (isset($insertion_data[0])) {
                $this->db->insert_batch('api_hotel_detail_t', $insertion_data);
            }  
        }
       
        
        
        $data['room_cobinations'] =$room_cobinations;
        $data['CancellationPolicies']=$CancellationPolicies;
        $data['LastCancellationDate']=$LastCancellationDate;
        $data['CancellationPolicy']=$CancellationPolicy;
        $data['hotel_code'] = $hotel_code;
        $data['request_data'] = $request_data;
        $data['day_price']=$day_price;
        $data['uniq_id'] =$uniq_id;
        $data['no_of_rooms'] =$no_of_rooms;
        $data['room_cobinations'] =$room_cobinations;
        $data['InfoSource'] =$InfoSource;
     #  echo "<pre>";
      # print_r($data);
    #  exit;
        $hotel_search_result = $this->load->view('hotel/TEK_room', $data, true);
        print json_encode(array(
            'hotel_search_result' => $hotel_search_result
        ));
    }

    public function AddToCart($temp_id=''){
        if(!empty($temp_id)){
            $temp_id = json_decode(base64_decode($temp_id));
    
    
      
            
            $session_id = $this->session->userdata('session_id');
            $result_data = array();
           // $split_room = explode("-", $temp_id);
                     

                      
          #  foreach ($split_room as $key => $room_id) {
            #   $results[] = $this->hotel_model->getHotelTempDetails($session_id,$room_id)->row();
          #  }
          
          if(is_array($temp_id))
          {
            for($i=0;$i<count($temp_id);$i++)
            {
				 $results[] = $this->hotel_model->getHotelTempDetails($session_id,$temp_id[$i])->row();
			}
		 }
		 else
		 {
			 $results[] = $this->hotel_model->getHotelTempDetails($session_id,$temp_id)->row();
		 }
		
           
                    
                    
				//forming room array for room blocking
				for($i=0;$i<count($results);$i++)
				{
				
				
				$room_array[] = Array('RoomIndex' => $results[$i]->RoomIndex, 'RatePlanCode' => $results[$i]->RatePlanCode, 'RatePlanName' => $results[$i]->RatePlanName,
                                                        'RoomTypeCode' => $results[$i]->RoomTypeCode, 'RoomTypeName' => $results[$i]->RoomTypeName, 'BedTypeCode' => '',
                                                        'SmokingPreference' => 0, 'Supplements' => '', 
                                                        'Price' => Array('CurrencyCode' => $results[$i]->CurrencyCode, 'RoomPrice' => $results[$i]->RoomPrice, 'Tax' => $results[$i]->Tax,
                                                        'ExtraGuestCharge' => $results[$i]->ExtraGuestCharge, 'ChildCharge' => $results[$i]->ChildCharge, 'OtherCharges' => $results[$i]->OtherCharges,
                                                        'Discount' => $results[$i]->Discount, 'PublishedPrice' => $results[$i]->PublishedPrice, 'PublishedPriceRoundedOff' => $results[$i]->PublishedPriceRoundedOff,
                                                        'OfferedPrice' => $results[$i]->OfferedPrice, 'OfferedPriceRoundedOff' => $results[$i]->OfferedPriceRoundedOff,
                                                        'AgentCommission' => $results[$i]->AgentCommission, 'AgentMarkUp' => $results[$i]->AgentMarkUp, 'ServiceTax' => $results[$i]->ServiceTax, 'TDS' => $results[$i]->TDS));
				// forming array that needs to sent for room blocking
				}
				
				
				$room_block_array = Array('ResultIndex' => $results[0]->ResultIndex, 'HotelCode' => $results[0]->hotel_code, 
					  'HotelName' => $results[0]->hotelname, 'GuestNationality' => 'IN', 'NoOfRooms' => $results[0]->noofrooms, 'ClientReferenceNo' => time(), 
					  "IsVoucherBooking" => true, 'HotelRoomsDetails' => $room_array, 'EndUserIp' => $_SERVER['REMOTE_ADDR'], 'TokenId' => $results[0]->TokenId, 'TraceId' => $results[0]->TraceId);
				
				
			#echo "<pre>";
             #  print_r($room_block_array);
             #   exit;
				
				$json_room_data = json_encode($room_block_array);
                    
                    
                    
                    
                    $HotelRoomBlock_RS = HotelRoomBlock_FN($json_room_data);
                    
                    
                    
                    
              # echo "<pre>";
           #  print_r($HotelRoomBlock_RS);
               # exit;
                
$BlockRoomResult = $HotelRoomBlock_RS['HotelBlockRS']['BlockRoomResult'];



if(isset($BlockRoomResult['AvailabilityType']) and $BlockRoomResult['ResponseStatus'] == '1') {
		if($BlockRoomResult['IsPriceChanged'] == '1' or $BlockRoomResult['IsCancellationPolicyChanged'] == '1') {
	
	       #echo "inside if";
      #print_r($BlockRoomResult);
#exit;
for($i=0;$i<count($BlockRoomResult['HotelRoomsDetails']);$i++)
{
	   
	    $RoomPrice = $BlockRoomResult['HotelRoomsDetails'][$i]['Price']['RoomPrice']; 
	    $Tax = $BlockRoomResult['HotelRoomsDetails'][$i]['Price']['Tax']; 
	    $ExtraGuestCharge = $BlockRoomResult['HotelRoomsDetails'][$i]['Price']['ExtraGuestCharge']; 
	    $ChildCharge = $BlockRoomResult['HotelRoomsDetails'][$i]['Price']['ChildCharge']; 
	    $OtherCharges = $BlockRoomResult['HotelRoomsDetails'][$i]['Price']['OtherCharges']; 
	    $Discount = $BlockRoomResult['HotelRoomsDetails'][$i]['Price']['Discount']; 
	    $PublishedPrice = $BlockRoomResult['HotelRoomsDetails'][$i]['Price']['PublishedPrice']; 
	    $PublishedPriceRoundedOff = $BlockRoomResult['HotelRoomsDetails'][$i]['Price']['PublishedPriceRoundedOff']; 
	    $OfferedPrice = $BlockRoomResult['HotelRoomsDetails'][$i]['Price']['OfferedPrice']; 
	    $OfferedPriceRoundedOff = $BlockRoomResult['HotelRoomsDetails'][$i]['Price']['OfferedPriceRoundedOff']; 
	    $AgentCommission = $BlockRoomResult['HotelRoomsDetails'][$i]['Price']['AgentCommission']; 
	    $AgentMarkUp = $BlockRoomResult['HotelRoomsDetails'][$i]['Price']['AgentMarkUp']; 
	    $ServiceTax = $BlockRoomResult['HotelRoomsDetails'][$i]['Price']['ServiceTax']; 
	    $TDS = $BlockRoomResult['HotelRoomsDetails'][$i]['Price']['TDS']; 
	    
	}  
}
else
{
for($i=0;$i<count($BlockRoomResult['HotelRoomsDetails']);$i++)
{
	   $RoomPrice = $BlockRoomResult['HotelRoomsDetails'][$i]['Price']['RoomPrice']; 
	    $Tax = $BlockRoomResult['HotelRoomsDetails'][$i]['Price']['Tax']; 
	    $ExtraGuestCharge = $BlockRoomResult['HotelRoomsDetails'][$i]['Price']['ExtraGuestCharge']; 
	    $ChildCharge = $BlockRoomResult['HotelRoomsDetails'][$i]['Price']['ChildCharge']; 
	    $OtherCharges = $BlockRoomResult['HotelRoomsDetails'][$i]['Price']['OtherCharges']; 
	    $Discount = $BlockRoomResult['HotelRoomsDetails'][$i]['Price']['Discount']; 
	    $PublishedPrice = $BlockRoomResult['HotelRoomsDetails'][$i]['Price']['PublishedPrice']; 
	    $PublishedPriceRoundedOff = $BlockRoomResult['HotelRoomsDetails'][$i]['Price']['PublishedPriceRoundedOff']; 
	    $OfferedPrice = $BlockRoomResult['HotelRoomsDetails'][$i]['Price']['OfferedPrice']; 
	    $OfferedPriceRoundedOff = $BlockRoomResult['HotelRoomsDetails'][$i]['Price']['OfferedPriceRoundedOff']; 
	    $AgentCommission = $BlockRoomResult['HotelRoomsDetails'][$i]['Price']['AgentCommission']; 
	    $AgentMarkUp = $BlockRoomResult['HotelRoomsDetails'][$i]['Price']['AgentMarkUp']; 
	    $ServiceTax = $BlockRoomResult['HotelRoomsDetails'][$i]['Price']['ServiceTax']; 
	    $TDS = $BlockRoomResult['HotelRoomsDetails'][$i]['Price']['TDS']; 
	}
}


	$hotel_blocking_id = rand(100000,9999999);
	$this->session->set_userdata('hotel_blocking_id', $hotel_blocking_id);
	$insertion_data[]= array(
		             'blockdata' =>$json_room_data,
                            'sessionid' => $session_id,
                            'blockid' => $hotel_blocking_id
                            
                            );
	
	$this->hotel_model->delete_api_hotel_block_data();
	
	  $this->db->insert_batch('api_hotel_block_data', $insertion_data);
	
	
	
       
      # echo $this->session->userdata('hotel_blocking_id');
      # exit;

	#echo $BlockRoomResult['AvailabilityType'];
	
	#$HotelRoomsDetails = $BlockRoomResult['HotelRoomsDetails'];
	/*if($BlockRoomResult['IsPriceChanged'] == '1' or $BlockRoomResult['IsCancellationPolicyChanged'] == '1') {
		//change in price or change in cancellation policy as per rule need to block the room once again
		unset($price);
		foreach($HotelRoomsDetails as $key => $value)
		{
			$price[$value['RoomIndex']] = $value['Price'];
		}
		$stored_room_arr = json_decode($inserted_data['room_array'], true);
		foreach($stored_room_arr['HotelRoomsDetails'] as $stored_key => $stored_value)
		{
			$RoomIndex = $stored_value['RoomIndex'];
			$stored_value['price'] = $price[$RoomIndex];
		}
		echo "<pre>";
		print_r($stored_room_arr);
		
		$new_array = json_encode($stored_room_arr);
		mysql_query("UPDATE hotel_room_blocking SET room_array='".$new_array."', created_datetime = NOW() WHERE pk='".$hotel_room."'");
?>

<?php
	} */
	
	

                    
           $api_temp_hotel_id_key = ''; $room_code = ''; $room_type = ''; $inclusion = ''; $shurival = ''; $charval = ''; $adult = ''; $child = ''; $board_type = ''; $token = ''; $inoffcode = ''; $contractnameVal = ''; $room_count = ''; $rate_typeval = ''; $total_cost = 0; $destCodeVal = ''; $shortname = ''; $child_age = '';
            $api_temp_hotel_id_key_v=array(); $room_code_v=array(); $room_type_v=array(); $inclusion_v=array(); $shurival_v=array(); $charval_v=array(); $adult_v=array(); $child_v=array(); $board_type_v=array(); $token_v=array(); $inoffcode_v=array(); $contractnameVal_v=array(); $room_count_v=array(); $rate_typeval_v=array(); $destCodeVal_v=array();$shortname_v=array(); $child_age_v=array();
            foreach ($results as $key => $result) {
                $api_temp_hotel_id_key_v[]= $result->api_temp_hotel_id ;
                $room_code_v[]= $result->room_code ;
                $room_type_v[]= $result->room_type ;
                $inclusion_v[]= $result->inclusion ;
                $shurival_v[]= $result->shurival ;
                $charval_v[]= $result->charval ;
                $adult_v[]= $result->adult ;
                $child_v[]= $result->child ;
                $board_type_v[]= $result->board_type ;
                $token_v[]= $result->token ;
                $total_cost = $total_cost + $PublishedPriceRoundedOff;
                $inoffcode_v[]= $result->inoffcode ;
                $contractnameVal_v[]= $result->contractnameVal ;
                $room_count_v[]= $result->noofrooms ;
                $rate_typeval_v[]= $result->rate_typeval ;
                $destCodeVal_v[]= $result->destCodeVal ;
                $shortname_v[]= $result->shortname ;
                $child_age_v[]= $result->child_age;
            }
            $api_temp_hotel_id_key=implode("<br>",$api_temp_hotel_id_key_v); $room_code=implode("<br>",$room_code_v); $room_type=implode("<br>",$room_type_v); $inclusion=implode("<br>",$inclusion_v); $shurival=implode("<br>",$shurival_v); $charval=implode("<br>",$charval_v); $adult=implode("<br>",$adult_v); $child=implode("<br>",$child_v); $board_type=implode("<br>",$board_type_v); $token=implode("<br>",$token_v); $inoffcode=implode("<br>",$inoffcode_v); $contractnameVal=implode("<br>",$contractnameVal_v); $room_count=implode("<br>",$room_count_v); $rate_typeval=implode("<br>",$rate_typeval_v); $destCodeVal=implode("<br>",$destCodeVal_v);$shortname=implode("<br>",$shortname_v); $child_age=implode("<br>",$child_age_v);
$image_url2 = '';
if(isset($results[0]->image) && $results[0]->image!='')
{
	$image_url = explode(",",$results[0]->image);
	if(isset($image_url[0]) && $image_url[0]!='')
	{
		$image_url2 = 'http://photos.hotelbeds.com/giata/'.$image_url[0];
	}
}


    if(is_array($temp_id))
    {
		$tmp_id = json_encode($temp_id);
	}
	else
	{
		$tmp_id = $temp_id;
	}
	


            $cart_hotel = array(
                'user_type' => '1',
                'parent_cart_id' => $tmp_id,
                'request' => $results[0]->request,
                'api_temp_hotel_id_key' => $api_temp_hotel_id_key,
                'user_id' => '1',
                'hotel_name' => $results[0]->hotelname, //$results[0]->hotel_name,
                'session_id' => $results[0]->session_id,
                'hotel_code' => $results[0]->hotel_code,
                'api' => $results[0]->api,
                'room_code' => $room_code,
                'room_type' => $room_type,
                'inclusion' => $inclusion,
                'total_cost' => $total_cost,
                'MyMarkup' => $results[0]->t_markup,
                'status' => $results[0]->status,
                'shurival' => $shurival,
                'charval' => $charval,
                'adult' => $adult,
                'child' => $child,
                'board_type' => $board_type,
                'token' => $token,
                'inoffcode' => $inoffcode,
                'contractnameVal' => $contractnameVal,
                'destCodeVal' => $destCodeVal,
                'shortname' => $shortname,
                'child_age' => $child_age,
                'room_count' => $room_count,
                'city' => $results[0]->city,
                'Promotionsaa' => $results[0]->Promotionsaa,
                'ShortNameaa' => $results[0]->ShortNameaa,
                'Classification_val' => $results[0]->Classification_val,
                'des_offer_value' => $results[0]->des_offer_value,
                'Remarksaa' => $results[0]->Remarksaa,
               'star' => $results[0]->star,
                'image' => $results[0]->image,
                'description' => $results[0]->description, //$results[0]->hotel_description,
                'rate_typeval' => $rate_typeval,
                'longitude' => 78.0000, //$results[0]->longitude,
                'latitude' => 21.0000,  //$results[0]->latitude
                'cancel_policy' => $results[0]->cancel_policy
                
            );
         # echo "<pre>";
          #print_r($cart_hotel);
          
            $booking_cart_id = $this->hotel_model->insert_cart_hotel($cart_hotel);
            $session_id = $this->session->userdata('session_id');
            if($this->session->userdata('b2c_id')){
                $user_type = 3;
                $user_id = $this->session->userdata('b2c_id');
            }else if($this->session->userdata('b2b_id')){
                $user_type = 2;
                $user_id = $this->session->userdata('b2b_id');
            }else{
                $user_type = '';
                $user_id = '';
            }
            $cart_global = array(
                'parent_cart_id' => $tmp_id,
                'ref_id' => $booking_cart_id,
                'module' => 'HOTEL',
                'user_type' => $user_type,
                'user_id' => $user_id,
                'session_id' => $session_id,
                'site_curr' => CURR,
                'total' => $total_cost,
                'ip' =>  $this->input->ip_address(),
                'timestamp' => date('Y-m-d H:i:s')
            );
            $cart_global_id = $this->cart_model->insert_cart_global($cart_global);
            //Cancellation Policy Starts Here
			//$HotelValuedAvailRQ_hotel_id_RS = HotelCancellation_Policy($results[0]->request,$results[0]->hotel_code,$booking_cart_id,$cart_global_id);
	
            $id = $cart_global_id.'(:.:)';
            $id = base64_encode(base64_encode(json_encode($id)));
            $URL = WEB_URL.'/booking/'.$session_id.'/'.$id;
            redirect($URL);
	
	
	
	
	
} else {
	$data['Error_msg'] ="Room Unavailable or your Session has Expired. Please initiate new search <a href='http://zingatrip.com'>Back to Home</a>";
	$this->load->view('hotel/no_result_room', $data);
}


        }
	}
	
    function get_random_hotel_images($sid, $hotelid) {
        $data['sid'] = $sid;
        $data['hotelid'] = $hotelid;
        $hotel_image_results = get_hotel_image_results($data);

        $xml_to_array_image = $this->xml_to_array->XmlToArray($hotel_image_results);
        $hotel_images = $xml_to_array_image['SOAP:Body']['hotel:HotelMediaLinksRsp']['hotel:HotelPropertyWithMediaItems']['common_v28_0:MediaItem'];


        $hotel_images_val = array();
        if (!empty($hotel_images)) {
            for ($k = 0; $k < count($hotel_images); $k++) {
                if (isset($hotel_images[$k]['@attributes']['sizeCode'])) {
                    $sizecode = $hotel_images[$k]['@attributes']['sizeCode'];
                    if ($sizecode == 'M') {

                        $hotel_images_val[] = $hotel_images[$k]['@attributes']['url'];
                    }
                }
            }
        }
        if (isset($hotel_images_val[0]) && $hotel_images_val[0] != '') {
            print json_encode(array(
                'hotel_images_val1' => $hotel_images_val
            ));
        } else {
            print json_encode(array(
                'hotel_images_val1' => ASSETS.'images/ftrlogo.png'
            ));
        }
    }

    public function hotel_search_sanjay() {


        $data['city_code'] = $_POST['city_code'];
        $data['check_in'] = $_POST['check_in'];
        $data['check_out'] = $_POST['check_out'];
        $data['rooms'] = $_POST['rooms'];
        $data['adult'] = $_POST['adult'];



        $diff = abs(strtotime($data['check_out']) - strtotime($data['check_in']));

        $years = floor($diff / (365 * 60 * 60 * 24));
        $months = floor(($diff - $years * 365 * 60 * 60 * 24) / (30 * 60 * 60 * 24));
        $days = floor(($diff - $years * 365 * 60 * 60 * 24 - $months * 30 * 60 * 60 * 24) / (60 * 60 * 24));

        $data['days'] = $days;

        $hotel_search_results = get_hotel_search_results($data);

        $xml_to_array = $this->xml_to_array->XmlToArray($hotel_search_results);

        if (!empty($xml_to_array['SOAP:Body']['hotel:HotelSearchAvailabilityRsp']['hotel:HotelSearchResult'])) {
            $hotel_search_nodes = $xml_to_array['SOAP:Body']['hotel:HotelSearchAvailabilityRsp']['hotel:HotelSearchResult'];

            $provider_code = array();
            $VendorCode = array();
            $VendorLocationID = array();
            $Key = array();
            $hotel_address = array();
            $direction_units = array();
            $direction_value = array();
            $direction = array();

            $hotel_chain = array();
            $hotel_code = array();
            $hotel_location = array();
            $hotel_name = array();
            $hotel_vendorlocation_key = array();
            $hotel_transportation = array();
            $hotel_reserverequirement = array();
            $partipationlevel = array();


            if (!empty($hotel_search_nodes)) {
                foreach ($hotel_search_nodes as $key => $value) {

                    if (!empty($value['hotel:HotelSearchError']['@attributes']['Code']) && $value['hotel:HotelSearchError']['@attributes']['Code'] != 5000) {
                        
                    } else {


                        $provider_code[] = $value['common_v28_0:VendorLocation']['@attributes']['ProviderCode'];
                        $VendorCode[] = $value['common_v28_0:VendorLocation']['@attributes']['VendorCode'];

                        $VendorLocationID[] = $value['common_v28_0:VendorLocation']['@attributes']['VendorLocationID'];
                        $Key[] = $value['common_v28_0:VendorLocation']['@attributes']['Key'];
                        $hotel_address[] = $value['hotel:HotelProperty']['hotel:PropertyAddress']['hotel:Address'];

                        $direction_units[] = $value['hotel:HotelProperty']['common_v28_0:Distance']['@attributes']['Units'];
                        $direction_value[] = $value['hotel:HotelProperty']['common_v28_0:Distance']['@attributes']['Value'];
                        $direction[] = $value['hotel:HotelProperty']['common_v28_0:Distance']['@attributes']['Direction'];

                        $hotel_chain[] = $value['hotel:HotelProperty']['@attributes']['HotelChain'];
                        $hotel_code[] = $value['hotel:HotelProperty']['@attributes']['HotelCode'];
                        $hotel_location[] = $value['hotel:HotelProperty']['@attributes']['HotelLocation'];
                        $hotel_name[] = $value['hotel:HotelProperty']['@attributes']['Name'];
                        $hotel_vendorlocation_key[] = $value['hotel:HotelProperty']['@attributes']['VendorLocationKey'];
                        $hotel_transportation[] = $value['hotel:HotelProperty']['@attributes']['HotelTransportation'];
                        $hotel_reserverequirement[] = $value['hotel:HotelProperty']['@attributes']['ReserveRequirement'];
                        $partipationlevel[] = $value['hotel:HotelProperty']['@attributes']['ParticipationLevel'];
                    }
                }
            }

            $insertion_data = array();

            $insertion_data[] = array('provider_code' => $provider_code,
                'VendorCode' => $VendorCode,
                'VendorLocationID' => $VendorLocationID,
                'Key' => $Key,
                'hotel_address' => $hotel_address,
                'direction_units' => $direction_units,
                'direction_value' => $direction_value,
                'direction' => $direction,
                'hotel_chain' => $hotel_chain,
                'hotel_code' => $hotel_code,
                'hotel_location' => $hotel_location,
                'hotel_name' => $hotel_name,
                'hotel_vendorlocation_key' => $hotel_vendorlocation_key,
                'hotel_transportation' => $hotel_transportation,
                'hotel_reserverequirement' => $hotel_reserverequirement,
                'partipationlevel' => $partipationlevel
            );

            $_SESSION['travel_port'][] = $insertion_data;
            
            $Html = $this->load->view('hotel/search_result_ajax', '', TRUE);

            echo json_encode(array('result' => $Html));
        }
    }

    public function voucher($pnr_no){
        $pnr_no = base64_decode(base64_decode($pnr_no));
        $count = $this->booking_model->getBookingPnr($pnr_no)->num_rows();
        
       
        if($count == 1){
            $b_data = $this->booking_model->getBookingPnr($pnr_no)->row();
			
            if($b_data->module == 'HOTEL'){
                $data['Booking'] = $booking = $this->booking_model->getBookingbyPnr($b_data->pnr_no,$b_data->module)->row();
				   $data['book_data'] =  $book_data =  json_decode(base64_decode($data['Booking']->request));
                $data['global'] =  $this->booking_model->getBookingPnr($pnr_no)->row();
                $checkin_date = strtotime($booking->check_in);
                $checkout_date = strtotime($booking->check_out);
                
                $absDateDiff = abs($checkout_date - $checkin_date);
                $data['number_of_nights'] = floor($absDateDiff/(60*60*24));
                
                //$data['Map'] = $this->getStaticMap($data['Booking']->PROP_LATITUDE, $data['Booking']->PROP_LONGITUDE);
                $data['host_profile_link'] = WEB_URL.'/users/show/'.$data['Booking']->user_id;
                //$data['apt_link'] = WEB_URL.'/apartment/rooms/'.$data['Booking']->PROP_ID;
			     # echo "<pre>";
			      # print_r($data);
			     #  exit;
                $this->load->view('hotel/voucher_view', $data);
            }
        }else{
            echo 'Invalid Data';
        }
    }
 public function invoice($pnr_no){
	 
	 
	 if($this->session->userdata('b2b_id')){
		 
		 
        $pnr_no = base64_decode(base64_decode($pnr_no));
        $count = $this->booking_model->getBookingPnr($pnr_no)->num_rows();
        if($count == 1){
            $b_data = $this->booking_model->getBookingPnr($pnr_no)->row();
            if($b_data->module == 'HOTEL'){
                $data['Booking'] = $booking = $this->booking_model->getBookingbyPnr($b_data->pnr_no,$b_data->module)->row();
                $data['global'] = $booking1 = $this->booking_model->getBookingPnr($pnr_no)->row();
				   $datarequest = json_decode(base64_decode($booking->request));
                $checkin_date = strtotime($datarequest->check_in);
                $checkout_date = strtotime($datarequest->check_out);
                
                $absDateDiff = abs($checkout_date - $checkin_date);
                $data['number_of_nights'] = floor($absDateDiff/(60*60*24));
                
                //$data['Map'] = $this->getStaticMap($data['Booking']->PROP_LATITUDE, $data['Booking']->PROP_LONGITUDE);
                $data['host_profile_link'] = WEB_URL.'/users/show/'.$data['Booking']->user_id;
                //$data['apt_link'] = WEB_URL.'/apartment/rooms/'.$data['Booking']->PROP_ID;
                $this->load->view('hotel/invoice_view', $data);
            }
        }else{
              $this->load->view('errors/404');
        }
	 }else{
          $this->load->view('errors/404');
     }
     
     
     
    }
    public function mail_voucher($pnr_no){
        $count = $this->booking_model->getBookingPnr($pnr_no)->num_rows();
        if($count == 1){
            $b_data = $this->booking_model->getBookingPnr($pnr_no)->row();
            if($b_data->module == 'HOTEL'){
                $data['Booking'] = $booking = $this->booking_model->getBookingbyPnr($b_data->pnr_no,$b_data->module)->row();
                 $datarequest = json_decode(base64_decode($booking->request));
                $checkin_day_month = date('D, M', strtotime($datarequest->check_in));
                $checkin_date = $cin = date('d', strtotime($datarequest->check_in));
                $checkout_day_month = date('D, M', strtotime($datarequest->check_out));
                $checkout_date = $cout = date('d', strtotime($datarequest->check_out));

                $checkin_date = strtotime($datarequest->check_in);
                $checkout_date = strtotime($datarequest->check_out);
                    
                $absDateDiff = abs($checkout_date - $checkin_date);
                $number_of_nights = floor($absDateDiff/(60*60*24));


                $getHotelTemplateRow = $this->email_model->get_email_template('HOTEL_BOOKING_VOUCHER')->row();
                $getHotelTemplate = $getHotelTemplateRow->message;
                $getHotelTemplate = str_replace("{%%FIRSTNAME%%}", $booking->GUEST_FIRSTNAME, $getHotelTemplate);
                $getHotelTemplate = str_replace("{%%BOOKING_STATUS%%}", $booking->booking_status, $getHotelTemplate);
                $getHotelTemplate = str_replace("{%%CONFIRMATION_NO%%}", $booking->pnr_no, $getHotelTemplate);
				$getHotelTemplate = str_replace("{%%WEB_URL%%}", WEB_URL, $getHotelTemplate);
                $getHotelTemplate = str_replace("{%%HOTEL_NAME%%}", $booking->hotel_name, $getHotelTemplate);
                $getHotelTemplate = str_replace("{%%ROOM_TYPE%%}", $booking->room_type, $getHotelTemplate);
                $getHotelTemplate = str_replace("{%%NO_OF_NIGHTS%%}", $number_of_nights, $getHotelTemplate);
                $getHotelTemplate = str_replace("{%%GUEST_COUNT%%}", $booking->adult, $getHotelTemplate);
                $getHotelTemplate = str_replace("{%%GUEST_NAME%%}", $booking->leadpax, $getHotelTemplate);
                $getHotelTemplate = str_replace("{%%CHECKIN_DAY_MONTH%%}", $checkin_day_month, $getHotelTemplate);
                $getHotelTemplate = str_replace("{%%CHECKIN_DATE%%}", $cin, $getHotelTemplate);
                $getHotelTemplate = str_replace("{%%CHECKOUT_DAY_MONTH%%}", $checkout_day_month, $getHotelTemplate);
                $getHotelTemplate = str_replace("{%%CHECKOUT_DATE%%}", $cout, $getHotelTemplate);
             
                $data['message'] = $getHotelTemplate;

                $data['to'] = $booking->BILLING_EMAIL;
                $data['email_access'] = $this->email_model->get_email_acess()->row();
                $email_type = 'HOTEL_BOOKING_VOUCHER';
                $data['email_template'] = $this->email_model->get_email_template($email_type)->row();
                $data['social_url'] = array(
                    'facebook_social_url' => 'https://www.facebook.com',
                    'twitter_social_url' => 'https://twitter.com',
                    'google_social_url' => 'https://plus.google.com',
                );
				 $data['booking_status'] =$booking->booking_status;
                $Response = $this->email_model->sendmail_hotelVoucher($data);
                $response = array('status' => 1);
                echo json_encode($response);
            }
        }else{
            $response = array('status' => 0);
            echo json_encode($response);
        }
    }

    public function cancel($pnr_no,$b_status){
        $pnr_no = base64_decode(base64_decode($pnr_no));
        $count = $this->booking_model->getBookingPnr($pnr_no)->num_rows();
        if($count == 1) {
            $b_data = $this->booking_model->getBookingPnr($pnr_no)->row();
		 $Booking = $this->booking_model->getBookingbyPnr($b_data->pnr_no,$b_data->module)->row();
			$book_result = json_decode($Booking->booking_res);
           
            if($b_data->booking_status == 'CONFIRMED'){
                $CancelRes = PurchaseCancelRQ($b_data->booking_no);
			
               
			  $dom = new DOMDocument();
        $dom->loadXML($CancelRes['PurchaseCancelRS']);
		$Error_info = $dom->getElementsByTagName("Error");
		$DetailedMessageval='';
		foreach($Error_info as $Errorinfo)
		{
			$DetailedMessage = $dom->getElementsByTagName("DetailedMessage");
			$DetailedMessageval=$DetailedMessage->item(0)->nodeValue;
		
			
		}
		if($DetailedMessageval=='')
		{
       			$status_j = $dom->getElementsByTagName("Status");
					$statusvalsds=$status_j->item(0)->nodeValue;
				
					if($statusvalsds == 'CANCELLED' )
					{
						$status_j = $dom->getElementsByTagName("Status");
						$statusval=$status_j->item(1)->nodeValue;
						
						$Currency = $dom->getElementsByTagName("Currency");
		
		$c=0;
		foreach($Currency as $Currencyx)
		{
			$currencyCode = $Currency->item($c)->getAttribute("code");
			$c++;
			
		}
		
		$Amount = $dom->getElementsByTagName("Amount");
	
		$a=0;
		foreach($Amount as $Amountv)
		{
			
			$amountval = $Amount->item($a)->nodeValue;
			$a++;
			
		}
	
                    $amountv = $this->flight_model->currency_convertor($amountval,$currencyCode,CURR);
                  
				    $mar = explode("|",$book_result->cancel_markup);
                    $amountv = $this->account_model->PercentageToAmount($amountv,$mar[1]);
					 $amountv = $this->account_model->PercentageToAmount($amountv,$mar[0]);
					
                  		if($statusval == 'CANCELLED')
						{
							
                        //echo '<pre>';print_r($CancelResAttr);die;
                        $update_booking = array(
                            'booking_status' => 'CANCELLED',
							'cancellation_amount' => $amountv
                        );
                        $this->booking_model->Update_Booking_Global($pnr_no, $update_booking, 'HOTEL');
						
		
		
			$amount = ($b_data->amount - $amountv);
			$userid = $b_data->user_id;
			$usertype = $b_data->user_type;
			$this->booking_model->refund_amount($userid,$usertype, $amount,$pnr_no);
		
		
		
                        //$this->cancel_mail_voucher($pnr_no);

                      //  $data['Booking'] = $booking = $this->booking_model->getBookingbyPnr($b_data->pnr_no,$b_data->module)->row();
                  $data['Booking'] = $booking = $this->booking_model->getBookingbyPnr($b_data->pnr_no,$b_data->module)->row();
				  $datarequest = json_decode(base64_decode($booking->request));
                        $checkin_day_month = date('D, M', strtotime($datarequest->check_in));
                        $checkin_date = $cin = date('d', strtotime($datarequest->check_in));
                        $checkout_day_month = date('D, M', strtotime($datarequest->check_out));
                        $checkout_date = $cout = date('d', strtotime($datarequest->check_out));

                        $checkin_date = strtotime($datarequest->check_in);
                        $checkout_date = strtotime($datarequest->check_out);
                            
                        $absDateDiff = abs($checkout_date - $checkin_date);
                        $number_of_nights = floor($absDateDiff/(60*60*24));


                        $getHotelTemplateRow = $this->email_model->get_email_template('HOTEL_BOOKING_VOUCHER')->row();
                        $getHotelTemplate = $getHotelTemplateRow->message;
                        $getHotelTemplate = str_replace("{%%FIRSTNAME%%}", $booking->GUEST_FIRSTNAME, $getHotelTemplate);
                        $getHotelTemplate = str_replace("{%%BOOKING_STATUS%%}", $booking->booking_status, $getHotelTemplate);
                        $getHotelTemplate = str_replace("{%%CONFIRMATION_NO%%}", $booking->pnr_no, $getHotelTemplate);
                        $getHotelTemplate = str_replace("{%%HOTEL_NAME%%}", $booking->hotel_name, $getHotelTemplate);
                        $getHotelTemplate = str_replace("{%%ROOM_TYPE%%}", $booking->room_type, $getHotelTemplate);
                        $getHotelTemplate = str_replace("{%%NO_OF_NIGHTS%%}", $number_of_nights, $getHotelTemplate);
                        $getHotelTemplate = str_replace("{%%GUEST_COUNT%%}", $booking->adult, $getHotelTemplate);
						 $getHotelTemplate = str_replace("{%%WEB_URL%%}", WEB_URL, $getHotelTemplate);
                        $getHotelTemplate = str_replace("{%%GUEST_NAME%%}", $booking->leadpax, $getHotelTemplate);
                        $getHotelTemplate = str_replace("{%%CHECKIN_DAY_MONTH%%}", $checkin_day_month, $getHotelTemplate);
                        $getHotelTemplate = str_replace("{%%CHECKIN_DATE%%}", $cin, $getHotelTemplate);
                        $getHotelTemplate = str_replace("{%%CHECKOUT_DAY_MONTH%%}", $checkout_day_month, $getHotelTemplate);
                        $getHotelTemplate = str_replace("{%%CHECKOUT_DATE%%}", $cout, $getHotelTemplate);
                     
                        $data['message'] = $getHotelTemplate;

                        $data['to'] = $booking->BILLING_EMAIL;
                        $data['email_access'] = $this->email_model->get_email_acess()->row();
                        $email_type = 'HOTEL_BOOKING_VOUCHER';
                        $data['email_template'] = $this->email_model->get_email_template($email_type)->row();
                        $data['social_url'] = array(
                            'facebook_social_url' => 'https://www.facebook.com',
                            'twitter_social_url' => 'https://twitter.com',
                            'google_social_url' => 'https://plus.google.com',
                        );
						 $data['booking_status'] =$booking->booking_status;
                        $Response = $this->email_model->sendmail_hotelVoucher($data);

                		 $response = array('status' => 1,'b_status' => $statusval);
                        echo json_encode($response);
                    
						}
						else{
							$response = array('status' => 0,'b_status' => $b_status);
							echo json_encode($response);
        					}
					}
					else
					{
							$response = array('status' => 0,'b_status' => $b_status);
							echo json_encode($response);
        			}
		}
         else{
							$response = array('status' => 0,'b_status' => $b_status);
							echo json_encode($response);
        					}     
			   
			   
                   
                
				
				
            }
			else{
							$response = array('status' => 0,'b_status' => $b_status);
							echo json_encode($response);
        					}
        }else{
            $response = array('status' => 0,'b_status' => $b_status);
            echo json_encode($response);
        }
    }

    public function XML_Log($request,$response){
        $xml_log = array(
            'Api' => 'Hotelbeds',
            'XML_Type' => 'Hotel',
            'XML_Request' => $request,
            'XML_Response' => $response,
            'Ip_address' => $this->input->ip_address(),
            'XML_Time' => date('Y-m-d H:i:s')
        );
        $this->xml_model->insert_xml_log($xml_log);
    }

}
