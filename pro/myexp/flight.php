<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Flight extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('Auth_Model');
        $current_url = $_SERVER['QUERY_STRING'] ? '?' . $_SERVER['QUERY_STRING'] : '';
        $current_url = $this->config->site_url() . $this->uri->uri_string() . $current_url;
        $this->url = array(
            'continue' => $current_url,
        );

        $this->helpMenuLink = "";
        $this->load->model('Help_Model');
        $this->helpMenuLink = $this->Help_Model->fetchHelpLinks();
        $this->load->helper('flight_helper');
        $this->load->model('flight_model');

        $this->load->model('booking_model');
        $this->load->model('email_model');
        $this->load->library('xml_to_array');
    }

    public function flight_index() {
        $current_url = $this->uri->segment('4');

        $data['banners'] = $this->Help_Model->getHomeSettings();
        $data['portfolio'] = $this->Help_Model->getAllPortfolio();

//Transfer Data Starts
        $data['time'] = $this->transfer_model->get_transfer_time();
        $data['country'] = $this->transfer_model->get_country();
        $this->load->view('flight/flight_index_v1', $data);
    }

    public function home() {
        $language = $this->uri->segment('3');
        $current_url = $this->uri->segment('4');
        if ($language) {
            $this->lang->load('home', $language);
            $language = array('language' => $language);
            $this->session->set_userdata($language);
            $current = base64_decode($current_url);
            redirect($current);
        } else {
            $this->lang->load('home', 'english');
        }
        $data['banners'] = $this->Help_Model->getHomeSettings();
        $data['portfolio'] = $this->Help_Model->getAllPortfolio();
        $this->load->view('flight/flight_index', $data);
    }

    public function index() {
        $this->session->set_userdata($this->url);
        $request = $this->input->get();

        $data['request_array'] = $request;
        $request = json_encode($request);
        $data['req'] = json_decode($request);
        $data['request'] = $request = base64_encode($request);
        $this->load->view('flight/results', $data);
    }

    public function search() {
        $request = $this->input->get();
//echo "<pre>";print_r($request); die();
        if ($request['trip_type'] != 'multicity') {
            $origin = '&origin=' . substr(chop(substr($request['from'], -5), ')'), -3);
            $destination = '&destination=' . substr(chop(substr($request['to'], -5), ')'), -3);
            $depart_date = '&depart_date=' . $request['depature'];
        }
        $adult = '&ADT=' . $request['adult'];
        $child = '&CHD=' . $request['child'];
        $infant = '&INF=' . $request['infant'];
        $class = '&class=' . $request['v_class'];
        if ($request['trip_type'] == 'oneway') {
            $type = 'type=O';
            $query = $type . $origin . $destination . $depart_date . $adult . $child . $infant . $class;
        } else if ($request['trip_type'] == 'circle') {
            $type = 'type=R';
            $return_date = '&return_date=' . $request['return'];
            $query = $type . $origin . $destination . $depart_date . $return_date . $adult . $child . $infant . $class;
        } else if ($request['trip_type'] == 'multicity') {
            $type = 'type=M';
//$return_date = '&return_date='.$request['return'];
            $multi = json_decode(json_encode($this->input->get()));
//echo '<pre>';print_r($multi);

            foreach ($multi->mfrom as $key => $value) {
                $origin[] = substr(chop(substr($value, -5), ')'), -3);
                $destination[] = substr(chop(substr($multi->mto[$key], -5), ')'), -3);
                $depature[] = $multi->mdepature[$key];
            }
//echo http_build_query($multi);
            $multicity = array(
                'type' => 'M',
                'origin' => $origin,
                'destination' => $destination,
                'depart_date' => $depature,
                'ADT' => $request['adult'],
                'CHD' => $request['child'],
                'INF' => $request['infant'],
                'class' => $request['class'],
            );
//echo '<pre>';print_r($multicity);
//echo http_build_query($multicity);die;
            $query = http_build_query($multicity);
        }

        $url = WEB_URL . '/flight/?' . $query;
        redirect($url);
    }

    public function GetResults($request = '') {
        $request = base64_decode($request);

        $data['request'] = $request = json_decode($request);

        $AirLowFareSearchPlusRQ_RS = AirLowFareSearchPlusRQ($request);

       //echo '<pre>';print_r($AirLowFareSearchPlusRQ_RS);exit;
        if ($request->type == 'R') {
            $IsDomestic = $AirLowFareSearchPlusRQ_RS['AirLowFareSearchPlusRS']->SearchResult->IsDomestic;
        }


        if ($request->type == 'M') {
            $OriginRequest = $request->origin[0];
        } else {
            $OriginRequest = $request->origin;
        }

        $airport_country = $this->account_model->get_airport_country($OriginRequest);

        $aMarkup = $this->account_model->get_markup('TBO-F', $airport_country); //get markup
        $aMarkup = $aMarkup['markup'];
        
        if($this->session->userdata('b2b_id')!=''){
        
        $MyMarkup = $this->account_model->get_my_markup_flight(); //get agent markup
       // $myMarkup = $MyMarkup['markup'];
         
         $myMarkup= $MyMarkup;  
        }
      else {
          $myMarkup=0;
      }
        
         //echo ($aMarkup);
         // echo'<pre>';
        // print_r($myMarkup);
       //die;
       
        #Agent Commission Start Here Balu
       
       
        
        
        if ($request->type == 'M') {
            $results = $this->formatMultiResponse($AirLowFareSearchPlusRQ_RS, $aMarkup, $myMarkup);
        } else if ($request->type == 'O') {
			
			
            $results = $this->formatResponse($AirLowFareSearchPlusRQ_RS, $aMarkup, $myMarkup);
            
            
        } else if ($request->type == 'R' && $IsDomestic == 1) {
            $results = $this->formatDomesticRoundResponse($AirLowFareSearchPlusRQ_RS, $aMarkup, $myMarkup);
        } else if ($request->type == 'R' && $IsDomestic == "") {

            $results = $this->formatRoundResponse($AirLowFareSearchPlusRQ_RS, $aMarkup, $myMarkup);
            $data['IsInt'] = 1;
        }

        if ($results) {
            $data['flights'] = $results;
          // echo '<pre>'; print_r($data);exit;
            $first_seg = reset($results);
            if ($request->type == 'O') {
                $this->load->view('flight/ajax_results', $data);
            } else if ($request->type == 'R' && $IsDomestic == 1) {
                $this->load->view('flight/ajax_results_round', $data);
            } else if ($request->type == 'R' && $IsDomestic == "") {
                $this->load->view('flight/ajax_results_INTround', $data);
            }
            if ($request->type == 'M') {
                $this->load->view('flight/ajax_results_multi', $data);
            }
// if(isset($first_seg['trip_type'])){
// 	$this->load->view('flight/ajax_results_round', $data);
// }else{
// 	$this->load->view('flight/ajax_results', $data);
// }
//echo '<pre>';print_r($results);
        } else {
            $this->load->view('flight/no_result');
        }
    }

    public function GetAirRules() {
        $request = $this->input->get();

        $data['flight'] = $flight = json_decode(base64_decode($request['temp_d']));
        $data['request'] = $Frequest = json_decode(base64_decode($request['temp_r']));

  if(isset($flight->FlightDetails->TripIndicator)) {
 $TripType=$flight->FlightDetails->TripIndicator;
    }
        if (isset($flight->session_id)) {
            $session_ids = explode(",", $flight->session_id);
        }


        if ($TripType == 1) {
            $session_id = $session_ids[0];
        } else {
            $session_id = $session_ids[1];
        }
        



        $AirLowFareSearchPlusRQ_RS = GetFareRule($flight->FlightDetails,$session_id);

        $data['AirFareRulesRS'] = $AirLowFareSearchPlusRQ_RS['AirFareRulesRS'];



        $response = array(
            'detail' => $this->load->view('flight/ajax_rules', $data, TRUE),
            'status' => 1
        );
        echo json_encode($response);
    }

    public function voucher($pnr_no) {
        $pnr_no = base64_decode(base64_decode($pnr_no));
        $count = $this->booking_model->getBookingPnr($pnr_no)->num_rows();
        if ($count == 1) {
            $b_data = $this->booking_model->getBookingPnr($pnr_no)->row();
            if ($b_data->module == 'FLIGHT') {
                $data['Booking'] = $booking = $this->booking_model->getBookingbyPnr($b_data->pnr_no, $b_data->module)->row();
                $data['global'] = $booking = $this->booking_model->getBookingPnr($pnr_no)->row();
                $this->load->view('flight/voucher_view', $data);
            }
        } else {
            echo 'Invalid Data';
        }
    }

    public function mail_voucher($pnr_no) {
        $count = $this->booking_model->getBookingPnr($pnr_no)->num_rows();
        if ($count == 1) {
            $b_data = $this->booking_model->getBookingPnr($pnr_no)->row();

            if ($b_data->module == 'FLIGHT') {
                $data['Booking'] = $booking = $this->booking_model->getBookingbyPnr($b_data->pnr_no, $b_data->module)->row();
                $data['leadpax'] = $booking->leadpax;
                $data['message'] = $this->load->view('flight/mail_voucher', $data, TRUE);
                $data['to'] = $booking->BILLING_EMAIL;
                $data['email_access'] = $this->email_model->get_email_acess()->row();
                $email_type = 'HOTEL_BOOKING_VOUCHER';
                $data['email_template'] = $this->email_model->get_email_template($email_type)->row();
                $data['booking_status'] = strtolower($booking->booking_status);
                $data['social_url'] = array(
                    'facebook_social_url' => 'https://www.facebook.com',
                    'twitter_social_url' => 'https://twitter.com',
                    'google_social_url' => 'https://plus.google.com',
                );
//	echo '<pre/>';
//	echo $data['message'];exit;
                $Response = $this->email_model->sendmail_flightVoucher($data);
                $response = array('status' => 1);
                echo json_encode($response);
            }
        } else {
            $response = array('status' => 0);
            echo json_encode($response);
        }
    }

    function round_comb_result() {

        $data1['request'] = $_POST['requestval'];
        $data1['flight'] = $_POST['resut'];
        $this->load->view('flight/ajax_results_round_ajax', $data1);
    }

    public function AddToCart() {


        if ($this->input->post('temp_d')) {

            $flight1 = $this->input->post('temp_d');
            $flight = json_decode(base64_decode($flight1));

            $request1 = $this->input->post('temp_r');
            $request = json_decode(base64_decode($request1));
        } else {

            $flight1 = $this->input->get('temp_d');
            $flight = json_decode(base64_decode($flight1));

            $request1 = $this->input->get('temp_r');
            $request = json_decode(base64_decode($request1));
        }


//header("Content-type: text/xml");
//print_r($AirPriceRes);die;

        $MyMarkup = $flight->MyMarkup;
        $AdminMarkup = $flight->AdminMarkup;
        $aMarkup = $flight->aMarkup;
        $TotalPrice = $flight->TotalFare;


        if ($request->type == 'O' || $request->type == 'M') {
//echo '<pre>';print_r($flight);
            $first_seg = reset($flight->Segments);
            $last_seg = end($flight->Segments);
        } else if ($request->type == 'R') {
            $first_seg = reset($flight->onward->Segments);
            $last_seg = end($flight->onward->Segments);
        }
//echo '<pre>';print_r($first_seg);echo $first_seg->Origin;
        $fromCityName = $this->flight_model->get_airport_cityname($first_seg->DepartureAirportCode);
        $toCityName = $this->flight_model->get_airport_cityname($last_seg->ArrivalAirportCode);
//die;
        $AirImage = "https://www.amadeus.net/static/img/static/airlines/medium/" . $first_seg->MarketingAirlineCode . ".png";
//Exploding T from arrival time  
        list($date, $time) = explode('T', $last_seg->ArrivalDateTime);
        $ArrivalDateTime = $date . " " . $time; //Exploding T and adding space
        $ArrivalDateTime = $art = strtotime($ArrivalDateTime);
//Exploding T from depature time  
        list($date, $time) = explode('T', $first_seg->DepartureDateTime);
        $DepartureDateTime = $date . " " . $time; //Exploding T and adding space
        $DepartureDateTime = $dpt = strtotime($DepartureDateTime);

        $seconds = $ArrivalDateTime - $DepartureDateTime;
        $days = floor($seconds / 86400);
        $hours = floor(($seconds - ($days * 86400)) / 3600);
        $minutes = floor(($seconds - ($days * 86400) - ($hours * 3600)) / 60);
// $seconds = floor(($seconds - ($days * 86400) - ($hours * 3600) - ($minutes*60)));
        if ($days == 0) {
            $dur = $hours . "h " . $minutes . "m";
        } else {
            $dur = $days . "d " . $hours . "h " . $minutes . "m";
        }

        $cart_flight = array(
            'request' => $request1,
            'response' => $flight1,
            'Origin' => $first_seg->DepartureAirportCode,
            'Destination' => $last_seg->ArrivalAirportCode,
            'fromCityName' => $fromCityName,
            'toCityName' => $toCityName,
            'DepartureTime' => $DepartureDateTime,
            'ArrivalTime' => $ArrivalDateTime,
            'duration' => $dur,
            'AirImage' => $AirImage,
            'TotalPrice' => $TotalPrice,
            'SITE_CURR' => CURR,
            'MyMarkup' => $MyMarkup,
            'AdminMarkup' => $AdminMarkup,
            'aMarkup' => $aMarkup,
            'API_CURR' => $flight->APICurrencyType,
            'BasePrice' => $flight->BaseFare,
            'TaxPrice' => $flight->TotalTax,
            'TIMESTAMP' => date('Y-m-d H:i:s')
        );

//echo '<pre/>xml';print_r($update_data);exit;		
        $booking_cart_id = $this->flight_model->insert_cart_flight($cart_flight);
        $session_id = $this->session->userdata('session_id');
        if ($this->session->userdata('b2c_id')) {
            $user_type = 3;
            $user_id = $this->session->userdata('b2c_id');
        } else if ($this->session->userdata('b2b_id')) {
            $user_type = 2;
            $user_id = $this->session->userdata('b2b_id');
        } else {
            $user_type = 4;
            $user_id = 0;
        }
        $cart_global = array(
            'parent_cart_id' => 0,
            'ref_id' => $booking_cart_id,
            'module' => 'FLIGHT',
            'user_type' => $user_type,
            'user_id' => $user_id,
            'session_id' => $session_id,
            'site_curr' => CURR,
            'total' => $TotalPrice,
            'ip' => $this->input->ip_address(),
            'timestamp' => date('Y-m-d H:i:s')
        );
        $cart_global_id = $this->cart_model->insert_cart_global($cart_global);
//echo '<pre>';print_r($cart_flight);die;
        $id = $cart_global_id . '(:.:)';
        $id = base64_encode(base64_encode(json_encode($id)));
        $URL = WEB_URL . '/booking/' . $session_id . '/' . $id;
        redirect($URL);
    }

    public function AddToCartRound() {


        if ($this->input->post('Otemp_d')) {

            $flight1 = $this->input->post('Otemp_d');
            $flight_Onward = json_decode(base64_decode($flight1));

            $request1 = $this->input->post('Otemp_r');
            $request_Onward = json_decode(base64_decode($request1));
        }

        if ($this->input->post('Rtemp_d')) {

            $flight1_R = $this->input->post('Rtemp_d');
            $flight_Round = json_decode(base64_decode($flight1_R));

            $request1_R = $this->input->post('Rtemp_r');
            $request_Round = json_decode(base64_decode($request1_R));
        }




//header("Content-type: text/xml");
//print_r($AirPriceRes);die;













        if ($flight_Onward) {


            $MyMarkup = $flight_Onward->MyMarkup;
            $AdminMarkup = $flight_Onward->AdminMarkup;
            $aMarkup = $flight_Onward->aMarkup;
            $TotalPrice = $flight_Onward->TotalFare;


            $first_seg = reset($flight_Onward->onward->Segments);
            $last_seg = end($flight_Onward->onward->Segments);


            $fromCityName = $this->flight_model->get_airport_cityname($first_seg->DepartureAirportCode);
            $toCityName = $this->flight_model->get_airport_cityname($last_seg->ArrivalAirportCode);
//die;
            $AirImage = "https://www.amadeus.net/static/img/static/airlines/medium/" . $first_seg->MarketingAirlineCode . ".png";
//Exploding T from arrival time  
            list($date, $time) = explode('T', $last_seg->ArrivalDateTime);
            $ArrivalDateTime = $date . " " . $time; //Exploding T and adding space
            $ArrivalDateTime = $art = strtotime($ArrivalDateTime);
//Exploding T from depature time  
            list($date, $time) = explode('T', $first_seg->DepartureDateTime);
            $DepartureDateTime = $date . " " . $time; //Exploding T and adding space
            $DepartureDateTime = $dpt = strtotime($DepartureDateTime);

            $seconds = $ArrivalDateTime - $DepartureDateTime;
            $days = floor($seconds / 86400);
            $hours = floor(($seconds - ($days * 86400)) / 3600);
            $minutes = floor(($seconds - ($days * 86400) - ($hours * 3600)) / 60);
// $seconds = floor(($seconds - ($days * 86400) - ($hours * 3600) - ($minutes*60)));
            if ($days == 0) {
                $dur = $hours . "h " . $minutes . "m";
            } else {
                $dur = $days . "d " . $hours . "h " . $minutes . "m";
            }


            $cart_flight = array(
                'request' => $request1,
                'response' => $flight1,
                'Origin' => $first_seg->DepartureAirportCode,
                'Destination' => $last_seg->ArrivalAirportCode,
                'fromCityName' => $fromCityName,
                'toCityName' => $toCityName,
                'DepartureTime' => $DepartureDateTime,
                'ArrivalTime' => $ArrivalDateTime,
                'duration' => $dur,
                'AirImage' => $AirImage,
                'TotalPrice' => $TotalPrice,
                'SITE_CURR' => CURR,
                'MyMarkup' => $MyMarkup,
                'AdminMarkup' => $AdminMarkup,
                'aMarkup' => $aMarkup,
                'API_CURR' => $flight_Onward->APICurrencyType,
                'BasePrice' => $flight_Onward->BaseFare,
                'TaxPrice' => $flight_Onward->TotalTax,
                'TIMESTAMP' => date('Y-m-d H:i:s')
            );


            $booking_cart_id = $this->flight_model->insert_cart_flight($cart_flight);

            $session_id = $this->session->userdata('session_id');
            if ($this->session->userdata('b2c_id')) {
                $user_type = 3;
                $user_id = $this->session->userdata('b2c_id');
            } else if ($this->session->userdata('b2b_id')) {
                $user_type = 2;
                $user_id = $this->session->userdata('b2b_id');
            } else {
                $user_type = 4;
                $user_id = 0;
            }
            $cart_global = array(
                'parent_cart_id' => 0,
                'ref_id' => $booking_cart_id,
                'module' => 'FLIGHT',
                'user_type' => $user_type,
                'user_id' => $user_id,
                'session_id' => $session_id,
                'site_curr' => CURR,
                'total' => $TotalPrice,
                'ip' => $this->input->ip_address(),
                'timestamp' => date('Y-m-d H:i:s')
            );
            $cart_global_id = $this->cart_model->insert_cart_global($cart_global);
        }
        if ($flight_Round) {



            $MyMarkup_R = $flight_Round->MyMarkup;
            $AdminMarkup_R = $flight_Round->AdminMarkup;
            $aMarkup_R = $flight_Round->aMarkup;
            $TotalPrice_R = $flight_Round->TotalFare;

            $first_seg = reset($flight_Round->return->Segments);
            $last_seg = end($flight_Round->return->Segments);

            $fromCityName = $this->flight_model->get_airport_cityname($first_seg->DepartureAirportCode);
            $toCityName = $this->flight_model->get_airport_cityname($last_seg->ArrivalAirportCode);
//die;
            $AirImage = "https://www.amadeus.net/static/img/static/airlines/medium/" . $first_seg->MarketingAirlineCode . ".png";
//Exploding T from arrival time  
            list($date, $time) = explode('T', $last_seg->ArrivalDateTime);
            $ArrivalDateTime = $date . " " . $time; //Exploding T and adding space
            $ArrivalDateTime = $art = strtotime($ArrivalDateTime);
//Exploding T from depature time  
            list($date, $time) = explode('T', $first_seg->DepartureDateTime);
            $DepartureDateTime = $date . " " . $time; //Exploding T and adding space
            $DepartureDateTime = $dpt = strtotime($DepartureDateTime);

            $seconds = $ArrivalDateTime - $DepartureDateTime;
            $days = floor($seconds / 86400);
            $hours = floor(($seconds - ($days * 86400)) / 3600);
            $minutes = floor(($seconds - ($days * 86400) - ($hours * 3600)) / 60);
// $seconds = floor(($seconds - ($days * 86400) - ($hours * 3600) - ($minutes*60)));
            if ($days == 0) {
                $dur = $hours . "h " . $minutes . "m";
            } else {
                $dur = $days . "d " . $hours . "h " . $minutes . "m";
            }

            $cart_flight = array(
                'request' => $request1_R,
                'response' => $flight1_R,
                'Origin' => $first_seg->DepartureAirportCode,
                'Destination' => $last_seg->ArrivalAirportCode,
                'fromCityName' => $fromCityName,
                'toCityName' => $toCityName,
                'DepartureTime' => $DepartureDateTime,
                'ArrivalTime' => $ArrivalDateTime,
                'duration' => $dur,
                'AirImage' => $AirImage,
                'TotalPrice' => $TotalPrice_R,
                'SITE_CURR' => CURR,
                'MyMarkup' => $MyMarkup_R,
                'AdminMarkup' => $AdminMarkup_R,
                'aMarkup' => $aMarkup_R,
                'API_CURR' => $flight_Round->APICurrencyType,
                'BasePrice' => $flight_Round->BaseFare,
                'TaxPrice' => $flight_Round->TotalTax,
                'TIMESTAMP' => date('Y-m-d H:i:s')
            );


            $booking_cart_id = $this->flight_model->insert_cart_flight($cart_flight);

            $session_id = $this->session->userdata('session_id');
            if ($this->session->userdata('b2c_id')) {
                $user_type = 3;
                $user_id = $this->session->userdata('b2c_id');
            } else if ($this->session->userdata('b2b_id')) {
                $user_type = 2;
                $user_id = $this->session->userdata('b2b_id');
            } else {
                $user_type = 4;
                $user_id = 0;
            }
            $cart_global = array(
                'parent_cart_id' => 0,
                'ref_id' => $booking_cart_id,
                'module' => 'FLIGHT',
                'user_type' => $user_type,
                'user_id' => $user_id,
                'session_id' => $session_id,
                'site_curr' => CURR,
                'total' => $TotalPrice,
                'ip' => $this->input->ip_address(),
                'timestamp' => date('Y-m-d H:i:s')
            );
            $cart_global_id_R = $this->cart_model->insert_cart_global($cart_global);
        }



        $id = $cart_global_id . '(:.:)';
        $id_R = $cart_global_id_R . '(:.:)';
        $id = base64_encode(base64_encode(json_encode($id)));
        $id_R = base64_encode(base64_encode(json_encode($id_R)));
        $URL = WEB_URL . '/booking/' . $session_id . '/' . $id . 'ROUND' . $id_R;
        redirect($URL);
    }
    
    function PassengerMarkup()
    {
  
       
    $dataPass=(json_decode(base64_decode($_POST['respone'])));
  
    
  
   foreach($dataPass as $value)
   {
      $FlightSessionID=$value->session_id; 
     if(isset($value->IsDomestic))
     {
         $IsDomestic=$value->IsDomestic;
     }
      break; 
   }
  
       $data1['flights']=$dataPass;
       $data1['PassengerMarkup']=$_POST['PassengerMarkup'];
       $data1['request']=$_POST['Request'];
      
         $Markup_details = array(
            'FlightSessionId' => $FlightSessionID,
            'Amount' => $_POST['PassengerMarkup'],
            'Ip_address' => $this->input->ip_address(),
            'Time' => date('Y-m-d H:i:s')
        );
        
        $count = $this->flight_model->getTempMarkupPass($FlightSessionID)->num_rows();  
          if ($count == 0) {
            
             $this->flight_model->insert_TempMarkupPass($Markup_details);
          }
          else 
          {
              $this->flight_model->UpdateTempMarkupPass($Markup_details,$FlightSessionID); 
          }
    
            if ($dataPass) {
            $data['flights'] = $results;
         
            $first_seg = reset($results);
            if ($_POST['Request']['type'] == 'O') {
                $this->load->view('flight/ajax_results', $data1);
            } else if ($_POST['Request']['type'] == 'R' && $IsDomestic == 1) {
                $this->load->view('flight/ajax_results_round', $data1);
            } else if ($_POST['Request']['type'] == 'R' && $IsDomestic == "") {
                $this->load->view('flight/ajax_results_INTround', $data1);
            }
            if ($_POST['Request']['type'] == 'M') {
                $this->load->view('flight/ajax_results_multi', $data1);
            }

            } else {
                $this->load->view('flight/no_result');
            }    
       // $this->load->view('flight/ajax_results', $data1);
        
    }

    function formatResponse($AirLowFareSearchPlusRQ_RS, $aMarkup, $myMarkup) {

        $response = $AirLowFareSearchPlusRQ_RS['AirLowFareSearchPlusRS'];
        if (!empty($AirLowFareSearchPlusRS)) {
//$this->XML_Log($AirLowFareSearchPlusRQ_RS['AirLowFareSearchPlusRQ'],$AirLowFareSearchPlusRQ_RS['AirLowFareSearchPlusRS']);
//return false;
      }

//   $response = $response->children('http://schemas.xmlsoap.org/soap/envelope/')->Body->children()->wmLowFarePlusResponse->OTA_AirLowFareSearchPlusRS;
        if ($response->SearchResult->Status->Description == 'Successfull') {
//	$this->XML_Log($AirLowFareSearchPlusRQ_RS['AirLowFareSearchPlusRQ'],$AirLowFareSearchPlusRQ_RS['AirLowFareSearchPlusRS']);
//	return false;
        }
      
        $SearchResult = $response->SearchResult;
        $session_id = $response->SearchResult->SessionId;
       
       // echo '<pre>';print_r($SearchResult);exit;
   // echo $SearchResult->Result->WSResult[0]->Segment->WSSegment->Airline->AirlineName;   exit;
        $i = 1;

        if (isset($SearchResult->Result->WSResult)) {
            
            foreach ($SearchResult->Result->WSResult as $Result) {
                $flight_id = $Result->SegmentKey;
              
    
               
//$TicketTimeLimit = (string) $PricedItinerary->TicketingInfo['TicketTimeLimit'];
                
      

                $fs[$i]['Flight_Id'] = $flight_id;
                $fs[$i]['session_id'] = $session_id;

//echo $i.'-';
//echo $f.'-';
                $fs[$i]['ArrivalAirportCode'] = '';
                $fs[$i]['ArrivalDateTime'] = '';
                $fs[$i]['MarketingAirline'] = '';
                $fs[$i]['MarketingAirlineCode'] = '';

                $BaseFare = (string) $Result->Fare->BaseFare;
                $BaseFareCurrencyCode = (string) $Result->Fare->Currency;
                
                
        if($this->session->userdata('b2b_id')!='')
        {   
           // echo '<pre>';
           // print_r($Result);
            
           /* if (is_array($Result->Segment->WSSegment)) {
                    $Segment = $Result->Segment->WSSegment;
                } else {
                    $Segment[] = $Result->Segment->WSSegment;
                }
              
               $AirlineName=$Segment[0]->Airline->AirlineName;
             */
            
            if(is_array($Result->Segment->WSSegment)){
                  $AirlineName=$Result->Segment->WSSegment[0]->Airline->AirlineName;
               }
              else {
                   $AirlineName=$Result->Segment->WSSegment->Airline->AirlineName;
                 }
            
            
            
            
               $b2b_id = $this->session->userdata('b2b_id');
               $MyCommission=$this->account_model->Agent_commission_SpecFlight($b2b_id,$AirlineName);
           
           if(isset($MyCommission['result'])) {
             $CommissionType=$MyCommission['result'][0]->Type;
             $CommissionPlan=$MyCommission['CommissionPlan'];
             $CommissionValue=$MyCommission['result'][0]->$CommissionPlan;
             
              
           if($CommissionType=="Rs")
           {
               $AgentCommissionAmount=$CommissionValue;
               $TdsOnCommission=(($AgentCommissionAmount*10)/100);
           }
           else 
           {
               $CommissionValueQ=explode("|",$CommissionValue);
               $AgentCommissionAmount=(($BaseFare*$CommissionValueQ[0])/100);
                $TdsOnCommission=(($AgentCommissionAmount*10)/100);
               
           }
            }
                
               if(is_array($Result->Segment->WSSegment)){
                  $AirlinesNames=$Result->Segment->WSSegment[0]->Airline->AirlineName;
               }
              else {
                   $AirlinesNames=$Result->Segment->WSSegment->Airline->AirlineName;
                 }
            
 
            
               $flight_names=$AirlinesNames;
               
             /* Adding the agent flight wise markup */
                if (array_key_exists($flight_names,$myMarkup)){
                
                    /* 0 for  percentage  */

                    if($myMarkup[$flight_names]['type']=='0')
                     { 
                      //  echo'<pre>';
                       // echo $flight_names;
                      //  print_r($myMarkup[$flight_names]);
                        
                        $myMarkup_per=$myMarkup[$flight_names]['markup'];
                        
                        $BaseFare = $this->flight_model->currency_convertor($BaseFare, $BaseFareCurrencyCode, CURR);
                        $BaseFare = $this->account_model->PercentageToAmount($BaseFare, $aMarkup);
                        $BaseFare = $this->account_model->PercentageToAmount($BaseFare, $myMarkup_per);

                        $TotalTax = (string) $Result->Fare->Tax;
                        $TotalTaxCurrencyCode = (string) $Result->Fare->Currency;

                        $TotalTax = $this->flight_model->currency_convertor($TotalTax, $TotalTaxCurrencyCode, CURR);
                        $TotalTax = $this->account_model->PercentageToAmount($TotalTax, $aMarkup);
                        $TotalTax = $this->account_model->PercentageToAmount($TotalTax, $myMarkup_per);


                        $TotalFare = (string) $Result->Fare->PublishedPrice;
                        $AgentNetFare=(string) $Result->Fare->OfferedFare;
                        $CurrencyCode = (string) $Result->Fare->Currency;
                        $TotalFare_org = $TotalFare;

                        $TotalFare = $this->flight_model->currency_convertor($TotalFare, $CurrencyCode, CURR);
                        $AgentNetFare=$this->flight_model->currency_convertor($AgentNetFare, $CurrencyCode, CURR);
                        $TMarkup = $this->account_model->PercentageAmount($TotalFare, $aMarkup);
                        $TotalFare = $this->account_model->PercentageToAmount($TotalFare, $aMarkup);
                        $Markup = $this->account_model->PercentageAmount($TotalFare, $myMarkup_per);
                        $TotalFare = $this->account_model->PercentageToAmount($TotalFare, $myMarkup_per);
                     }
                     /* 1 for addition */
                    if($myMarkup[$flight_names]['type']=='1')
                    {
                     
                            $myMarkup_add=$myMarkup[$flight_names]['markup'];
                        
                            $BaseFare = $this->flight_model->currency_convertor($BaseFare, $BaseFareCurrencyCode, CURR);
                            $BaseFare = $this->account_model->PercentageToAmount($BaseFare, $aMarkup);
                           // $BaseFare = $this->account_model->PercentageToAmount($BaseFare, $myMarkup);
                             $BaseFare =$BaseFare+$myMarkup_add;    
                            $TotalTax = (string) $Result->Fare->Tax;
                            $TotalTaxCurrencyCode = (string) $Result->Fare->Currency;

                            $TotalTax = $this->flight_model->currency_convertor($TotalTax, $TotalTaxCurrencyCode, CURR);
                            $TotalTax = $this->account_model->PercentageToAmount($TotalTax, $aMarkup);
                           //  $TotalTax = $this->account_model->PercentageToAmount($TotalTax, $myMarkup);
                            $TotalTax=$TotalTax+$myMarkup_add;   // ?

                            $TotalFare = (string) $Result->Fare->PublishedPrice;
                            $AgentNetFare=(string) $Result->Fare->OfferedFare;
                            $CurrencyCode = (string) $Result->Fare->Currency;
                            $TotalFare_org = $TotalFare;

                            $TotalFare = $this->flight_model->currency_convertor($TotalFare, $CurrencyCode, CURR);
                            $AgentNetFare=$this->flight_model->currency_convertor($AgentNetFare, $CurrencyCode, CURR);
                            $TMarkup = $this->account_model->PercentageAmount($TotalFare, $aMarkup);
                            $TotalFare = $this->account_model->PercentageToAmount($TotalFare, $aMarkup);
                             
                            $Markup=$myMarkup_add;
                            $TotalFare=$TotalFare+$myMarkup_add;
                           // $Markup = $this->account_model->PercentageAmount($TotalFare, $myMarkup);
                          //  $TotalFare = $this->account_model->PercentageToAmount($TotalFare, $myMarkup);

                       
                    }
                    
                    
                    }
             else{ 
                   /* if flight is not there in agent flightlist  for individul flight markup adding markup 0 */
                 //   echo '<br>ffff'.'-'.$flight_names;
                 $myMarkup_not_in_agent_flightlist=0;
                 
                $BaseFare = $this->flight_model->currency_convertor($BaseFare, $BaseFareCurrencyCode, CURR);
                $BaseFare = $this->account_model->PercentageToAmount($BaseFare, $aMarkup);
                $BaseFare = $this->account_model->PercentageToAmount($BaseFare, $myMarkup_not_in_agent_flightlist);

                $TotalTax = (string) $Result->Fare->Tax;
                $TotalTaxCurrencyCode = (string) $Result->Fare->Currency;

                $TotalTax = $this->flight_model->currency_convertor($TotalTax, $TotalTaxCurrencyCode, CURR);
                $TotalTax = $this->account_model->PercentageToAmount($TotalTax, $aMarkup);
                $TotalTax = $this->account_model->PercentageToAmount($TotalTax, $myMarkup_not_in_agent_flightlist);


                $TotalFare = (string) $Result->Fare->PublishedPrice;
                $AgentNetFare=(string) $Result->Fare->OfferedFare;
                $CurrencyCode = (string) $Result->Fare->Currency;
                $TotalFare_org = $TotalFare;

                $TotalFare = $this->flight_model->currency_convertor($TotalFare, $CurrencyCode, CURR);
                $AgentNetFare=$this->flight_model->currency_convertor($AgentNetFare, $CurrencyCode, CURR);
                $TMarkup = $this->account_model->PercentageAmount($TotalFare, $aMarkup);
                $TotalFare = $this->account_model->PercentageToAmount($TotalFare, $aMarkup);
                $Markup = $this->account_model->PercentageAmount($TotalFare, $myMarkup_not_in_agent_flightlist);
                $TotalFare = $this->account_model->PercentageToAmount($TotalFare, $myMarkup_not_in_agent_flightlist);
             }
           
        }
        
        else{


                $BaseFare = $this->flight_model->currency_convertor($BaseFare, $BaseFareCurrencyCode, CURR);
                $BaseFare = $this->account_model->PercentageToAmount($BaseFare, $aMarkup);
                $BaseFare = $this->account_model->PercentageToAmount($BaseFare, $myMarkup);




                $TotalTax = (string) $Result->Fare->Tax;
                $TotalTaxCurrencyCode = (string) $Result->Fare->Currency;

                $TotalTax = $this->flight_model->currency_convertor($TotalTax, $TotalTaxCurrencyCode, CURR);
                $TotalTax = $this->account_model->PercentageToAmount($TotalTax, $aMarkup);
                $TotalTax = $this->account_model->PercentageToAmount($TotalTax, $myMarkup);


                $TotalFare = (string) $Result->Fare->PublishedPrice;
                $AgentNetFare=(string) $Result->Fare->OfferedFare;
                $CurrencyCode = (string) $Result->Fare->Currency;
                $TotalFare_org = $TotalFare;

                $TotalFare = $this->flight_model->currency_convertor($TotalFare, $CurrencyCode, CURR);
                $AgentNetFare=$this->flight_model->currency_convertor($AgentNetFare, $CurrencyCode, CURR);
                $TMarkup = $this->account_model->PercentageAmount($TotalFare, $aMarkup);
                $TotalFare = $this->account_model->PercentageToAmount($TotalFare, $aMarkup);
                $Markup = $this->account_model->PercentageAmount($TotalFare, $myMarkup);
                $TotalFare = $this->account_model->PercentageToAmount($TotalFare, $myMarkup);

        }


                $fs[$i]['FlightDetails'] = $Result;

                if ($Result->IsLcc != "") {
                    $fs[$i]['IsLcc'] = $Result->IsLcc;
                }
                $fs[$i]['BaseFare'] = $BaseFare;
                $fs[$i]['TotalTax'] = $TotalTax;
                $fs[$i]['TotalFare'] = $TotalFare;
                $fs[$i]['AgentNetFare'] = $AgentNetFare;
                if(isset($AgentCommissionAmount)) {
                $fs[$i]['AgentCommissionByAdmin'] = $AgentCommissionAmount;
                 $fs[$i]['TdsOnCommission'] = $TdsOnCommission;
                }
                $fs[$i]['TotalPrice_API'] = $TotalFare_org;
                $fs[$i]['APICurrencyType'] = $CurrencyCode;
                $fs[$i]['SITECurrencyType'] = CURR;
                $fs[$i]['MyMarkup'] = $Markup;
                $fs[$i]['aMarkup'] = $aMarkup;
                $fs[$i]['AdminMarkup'] = $TMarkup;

                $fs[$i]['PricingSource'] = '';
                $fs[$i]['PassengerTypeQuantityCode'] = (string) $Result->FareBreakdown->WSPTCFare->PassengerType;
                $fs[$i]['PassengerTypeQuantity'] = (string) $Result->FareBreakdown->WSPTCFare->PassengerCount;
                $Segment = '';
                if (is_array($Result->Segment->WSSegment)) {
                    $Segment = $Result->Segment->WSSegment;
                } else {
                    $Segment[] = $Result->Segment->WSSegment;
                } $f = 0;
                foreach ($Segment as $Segmentval) {



                    $fs[$i]['Segments'][$f]['ResBookDesigCode'] = (string) $Segmentval->SegmentIndicator;
                    $fs[$i]['Segments'][$f]['NumberInParty'] = '';
                    $fs[$i]['Segments'][$f]['RPH'] = '';
                    $fs[$i]['Segments'][$f]['FlightNumber'] = (string) $Segmentval->FlightNumber;
                    $fs[$i]['Segments'][$f]['DepartureDateTime'] = (string) $Segmentval->DepTIme;
                    $fs[$i]['Segments'][$f]['ArrivalDateTime'] = (string) $Segmentval->ArrTime;
                    $fs[$i]['Segments'][$f]['StopQuantity'] = (string) $Segmentval->Stop;
                    $fs[$i]['Segments'][$f]['DepartureAirport'] = (string) $Segmentval->Origin->AirportName;
                    $fs[$i]['Segments'][$f]['DepartureAirportCode'] = (string) $Segmentval->Origin->AirportCode;
                    $fs[$i]['Segments'][$f]['ArrivalAirport'] = (string) $Segmentval->Destination->AirportName;
                    $fs[$i]['Segments'][$f]['ArrivalAirportCode'] = (string) $Segmentval->Destination->AirportCode;
                    $fs[$i]['Segments'][$f]['OperatingAirline'] = (string) $Segmentval->Airline->AirlineName;
                    $fs[$i]['Segments'][$f]['OperatingAirlineCode'] = (string) $Segmentval->Airline->AirlineCode;
                    $fs[$i]['Segments'][$f]['Equipment'] = '';
                    $fs[$i]['Segments'][$f]['EquipmentAirEquipType'] = '';
                    $fs[$i]['Segments'][$f]['MarketingAirline'] = (string) $Segmentval->OperatingCarrier;
                    $fs[$i]['Segments'][$f]['MarketingAirlineCode'] = (string) $Segmentval->OperatingCarrier;
                    $fs[$i]['Segments'][$f]['CabinType'] = '';
                    $fs[$i]['Segments'][$f]['JourneyTotalDuration'] = (string) $Segmentval->Duration;
                    if ($f > 0) {
//$fs[$f] = $FlightSegment['DepartureDateTime'];
                    }
//print_r($FlightSegment['DepartureDateTime']);
//$segm[$p][$q] = $FlightSegment;

                    $f++;
                }
                $i++;
            }
//		echo '<pre>';print_r($fs);die;

            return $fs;
        }
    }

    function formatRoundResponse($AirLowFareSearchPlusRQ_RS, $aMarkup, $myMarkup) {


        $response = $AirLowFareSearchPlusRQ_RS['AirLowFareSearchPlusRS'];
        if (empty($AirLowFareSearchPlusRS)) {
//  $this->XML_Log($AirLowFareSearchPlusRQ_RS['AirLowFareSearchPlusRQ'],$AirLowFareSearchPlusRQ_RS['AirLowFareSearchPlusRS']);
//  return false;
        }


        if ($response->SearchResult->Status->Description == 'Successfull') {
//$this->XML_Log($AirLowFareSearchPlusRQ_RS['AirLowFareSearchPlusRQ'],$AirLowFareSearchPlusRQ_RS['AirLowFareSearchPlusRS']);
//return false;
        }
        $SearchResult = $response->SearchResult;
        $session_id = $response->SearchResult->SessionId;

//$count  = count($results['PricedItinerary']);
//$flight_details;
        $i = 0;
        if (isset($SearchResult->Result->WSResult)) {
            foreach ($SearchResult->Result->WSResult as $Result) {

                $flight_id = $Result->SegmentKey;
                $flight_id = $flight_id;
//$TicketTimeLimit = (string) $PricedItinerary->TicketingInfo['TicketTimeLimit'];


                $fs[$i]['Flight_Id'] = $flight_id;
                $fs[$i]['session_id'] = $session_id;
              
                $fs[$i]['ArrivalAirportCode'] = '';
                $fs[$i]['ArrivalDateTime'] = '';
                $fs[$i]['MarketingAirline'] = '';
                $fs[$i]['MarketingAirlineCode'] = '';



                $BaseFare = (string) $Result->Fare->BaseFare;
                $BaseFareCurrencyCode = (string) $Result->Fare->Currency;
                
                
                   if($this->session->userdata('b2b_id')!='')
        {
            if (is_array($Result->Segment->WSSegment)) {
                    $Segment = $Result->Segment->WSSegment;
                } else {
                    $Segment[] = $Result->Segment->WSSegment;
                }
             $AirlineName=$Segment[0]->Airline->AirlineName;
               $b2b_id = $this->session->userdata('b2b_id');
               $MyCommission=$this->account_model->Agent_commission_SpecFlight($b2b_id,$AirlineName);
              if(isset($MyCommission['result'])) {
             $CommissionType=$MyCommission['result'][0]->Type;
             $CommissionPlan=$MyCommission['CommissionPlan'];
             $CommissionValue=$MyCommission['result'][0]->$CommissionPlan;
             
              
           if($CommissionType=="Rs")
           {
               $AgentCommissionAmount=$CommissionValue;
               $TdsOnCommission=(($AgentCommissionAmount*10)/100);
           }
           else 
           {
               $CommissionValueQ=explode("|",$CommissionValue);
               $AgentCommissionAmount=(($BaseFare*$CommissionValueQ[0])/100);
                $TdsOnCommission=(($AgentCommissionAmount*10)/100);
               
           }
            }
              

           
        }
                
                
                if($this->session->userdata('b2b_id')!='')
        {
            if (is_array($Result->Segment->WSSegment)) {
                    $Segment = $Result->Segment->WSSegment;
                } else {
                    $Segment[] = $Result->Segment->WSSegment;
                }
             $AirlineName=$Segment[0]->Airline->AirlineName;
               $b2b_id = $this->session->userdata('b2b_id');
               $MyCommission=$this->account_model->Agent_commission_SpecFlight($b2b_id,$AirlineName);
              if(isset($MyCommission['result'])) {
             $CommissionType=$MyCommission['result'][0]->Type;
             $CommissionPlan=$MyCommission['CommissionPlan'];
             $CommissionValue=$MyCommission['result'][0]->$CommissionPlan;
             
              
           if($CommissionType=="Rs")
           {
               $AgentCommissionAmount=$CommissionValue;
               $TdsOnCommission=(($AgentCommissionAmount*10)/100);
           }
           else 
           {
               $CommissionValueQ=explode("|",$CommissionValue);
               $AgentCommissionAmount=(($BaseFare*$CommissionValueQ[0])/100);
                $TdsOnCommission=(($AgentCommissionAmount*10)/100);
               
           }
            }
              

           
        }



                $BaseFare = $this->flight_model->currency_convertor($BaseFare, $BaseFareCurrencyCode, CURR);
                $BaseFare = $this->account_model->PercentageToAmount($BaseFare, $aMarkup);
                $BaseFare = $this->account_model->PercentageToAmount($BaseFare, $myMarkup);
                $TotalTax = (string) $Result->Fare->Tax;
                $TotalTaxCurrencyCode = (string) $Result->Fare->Currency;

                $TotalTax = $this->flight_model->currency_convertor($TotalTax, $TotalTaxCurrencyCode, CURR);
                $TotalTax = $this->account_model->PercentageToAmount($TotalTax, $aMarkup);
                $TotalTax = $this->account_model->PercentageToAmount($TotalTax, $myMarkup);

                $TotalFare = (string) $Result->Fare->PublishedPrice;
                  $AgentNetFare=(string) $Result->Fare->OfferedFare;
                $CurrencyCode = (string) $Result->Fare->Currency;
                $TotalFare_org = $TotalFare;

                $TotalFare = $this->flight_model->currency_convertor($TotalFare, $CurrencyCode, CURR);
                 $AgentNetFare=$this->flight_model->currency_convertor($AgentNetFare, $CurrencyCode, CURR);
                $TMarkup = $this->account_model->PercentageAmount($TotalFare, $aMarkup);
                $TotalFare = $this->account_model->PercentageToAmount($TotalFare, $aMarkup);
                $Markup = $this->account_model->PercentageAmount($TotalFare, $myMarkup);
                $TotalFare = $this->account_model->PercentageToAmount($TotalFare, $myMarkup);

                $fs[$i]['FlightDetails'] = $Result;

                if ($Result->IsLcc != "") {
                    $fs[$i]['IsLcc'] = $Result->IsLcc;
                }
                $fs[$i]['BaseFare'] = $BaseFare;
                $fs[$i]['TotalTax'] = $TotalTax;
                $fs[$i]['TotalFare'] = $TotalFare;
                $fs[$i]['AgentNetFare'] = $AgentNetFare;
                if(isset($AgentCommissionAmount)) {
                $fs[$i]['AgentCommissionByAdmin'] = $AgentCommissionAmount;
                 $fs[$i]['TdsOnCommission'] = $TdsOnCommission;
                }
                $fs[$i]['TotalPrice_API'] = $TotalFare_org;
                $fs[$i]['APICurrencyType'] = $CurrencyCode;
                $fs[$i]['SITECurrencyType'] = CURR;
                $fs[$i]['MyMarkup'] = $Markup;
                $fs[$i]['aMarkup'] = $aMarkup;
                $fs[$i]['AdminMarkup'] = $TMarkup;

                $fs[$i]['PricingSource'] = '';
                $fs[$i]['PassengerTypeQuantityCode'] = (string) $Result->FareBreakdown->WSPTCFare->PassengerType;
                $fs[$i]['PassengerTypeQuantity'] = (string) $Result->FareBreakdown->WSPTCFare->PassengerCount;


                $Segment = '';
                if (is_array($Result->Segment->WSSegment)) {
                    $Segment = $Result->Segment->WSSegment;
                } else {
                    $Segment[] = $Result->Segment->WSSegment;
                }

//echo '<pre>';print_r($Segment);exit;
//foreach ($Result->AirItinerary as $AirItinerary) {
//	$trip_type = (string) $AirItinerary['DirectionInd'];
//echo '<pre>';print_r($AirItinerary);
                $round = 0;
                $f = 0;
                foreach ($Segment as $FlightSegment) {

                    if ($FlightSegment->SegmentIndicator == 1) {
                        $mode = 'onward';
                    } else {
                        $mode = 'return';
                    }
//$segments_count  = count($Segmentval);
//echo $i.'-';
//echo $f.'-';
                    $fs[$i][$mode]['Segments'][$f]['ResBookDesigCode'] = '';
                    $fs[$i][$mode]['Segments'][$f]['NumberInParty'] = '';
                    $fs[$i][$mode]['Segments'][$f]['RPH'] = '';
                    $fs[$i][$mode]['Segments'][$f]['FlightNumber'] = (string) $FlightSegment->FlightNumber;
                    $fs[$i][$mode]['Segments'][$f]['DepartureDateTime'] = (string) $FlightSegment->DepTIme;
                    $fs[$i][$mode]['Segments'][$f]['ArrivalDateTime'] = (string) $FlightSegment->ArrTime;
                    $fs[$i][$mode]['Segments'][$f]['StopQuantity'] = (string) $FlightSegment->Stop;
                    $fs[$i][$mode]['Segments'][$f]['DepartureAirport'] = (string) $FlightSegment->Origin->CityName;
                    $fs[$i][$mode]['Segments'][$f]['DepartureAirportCode'] = (string) $FlightSegment->Origin->CityCode;
                    $fs[$i][$mode]['Segments'][$f]['ArrivalAirport'] = (string) $FlightSegment->Destination->CityName;
                    $fs[$i][$mode]['Segments'][$f]['ArrivalAirportCode'] = (string) $FlightSegment->Destination->CityCode;
                    $fs[$i][$mode]['Segments'][$f]['OperatingAirline'] = (string) $FlightSegment->Airline->AirlineName;
                    $fs[$i][$mode]['Segments'][$f]['OperatingAirlineCode'] = (string) $FlightSegment->Airline->AirlineCode;
                    $fs[$i][$mode]['Segments'][$f]['Equipment'] = '';
                    $fs[$i][$mode]['Segments'][$f]['EquipmentAirEquipType'] = '';
                    $fs[$i][$mode]['Segments'][$f]['MarketingAirline'] = (string) $FlightSegment->Airline->AirlineName;
                    $fs[$i][$mode]['Segments'][$f]['MarketingAirlineCode'] = (string) $FlightSegment->Airline->AirlineCode;
                    $fs[$i][$mode]['Segments'][$f]['CabinType'] = '';
                    $fs[$i][$mode]['Segments'][$f]['JourneyTotalDuration'] = '';
                    if ($f > 0) {
//$fs[$f] = $FlightSegment['DepartureDateTime'];
                    }
//print_r($FlightSegment['DepartureDateTime']);
//$segm[$p][$q] = $FlightSegment;

                    $f++;

                    $round++;
                }
//}
//echo '<pre>';print_r($fs);die;
                $i++;
            }
//print_r($Flights);
//print_r($flight_details);
//echo '<pre>';print_r($fs);die;
            return $fs;
        }
    }

    function formatDomesticRoundResponse($AirLowFareSearchPlusRQ_RS, $aMarkup, $myMarkup) {


        $response = $AirLowFareSearchPlusRQ_RS['AirLowFareSearchPlusRS'];
        if (empty($AirLowFareSearchPlusRS)) {
//  $this->XML_Log($AirLowFareSearchPlusRQ_RS['AirLowFareSearchPlusRQ'],$AirLowFareSearchPlusRQ_RS['AirLowFareSearchPlusRS']);
//  return false;
        }


        if ($response->SearchResult->Status->Description == 'Successfull') {
//$this->XML_Log($AirLowFareSearchPlusRQ_RS['AirLowFareSearchPlusRQ'],$AirLowFareSearchPlusRQ_RS['AirLowFareSearchPlusRS']);
//return false;
        }
        $SearchResult = $response->SearchResult;
        $session_id = $response->SearchResult->SessionId;

//$count  = count($results['PricedItinerary']);
//$flight_details;
        $i = 0;
        if (isset($SearchResult->Result->WSResult)) {
            foreach ($SearchResult->Result->WSResult as $Result) {

                $flight_id = $Result->SegmentKey;
                $flight_id = $flight_id;
//$TicketTimeLimit = (string) $PricedItinerary->TicketingInfo['TicketTimeLimit'];


                $fs[$i]['Flight_Id'] = $flight_id;
                $fs[$i]['session_id'] = $session_id;
                $fs[$i]['IsDomestic'] = $SearchResult->IsDomestic;
                $fs[$i]['ArrivalAirportCode'] = '';
                $fs[$i]['ArrivalDateTime'] = '';
                $fs[$i]['MarketingAirline'] = '';
                $fs[$i]['MarketingAirlineCode'] = '';



                $BaseFare = (string) $Result->Fare->BaseFare;
                $BaseFareCurrencyCode = (string) $Result->Fare->Currency;
                
                   if($this->session->userdata('b2b_id')!='')
        {
            if (is_array($Result->Segment->WSSegment)) {
                    $Segment = $Result->Segment->WSSegment;
                } else {
                    $Segment[] = $Result->Segment->WSSegment;
                }
             $AirlineName=$Segment[0]->Airline->AirlineName;
               $b2b_id = $this->session->userdata('b2b_id');
               $MyCommission=$this->account_model->Agent_commission_SpecFlight($b2b_id,$AirlineName);
              if(isset($MyCommission['result'])) {
             $CommissionType=$MyCommission['result'][0]->Type;
             $CommissionPlan=$MyCommission['CommissionPlan'];
             $CommissionValue=$MyCommission['result'][0]->$CommissionPlan;
             
              
           if($CommissionType=="Rs")
           {
               $AgentCommissionAmount=$CommissionValue;
               $TdsOnCommission=(($AgentCommissionAmount*10)/100);
           }
           else 
           {
               $CommissionValueQ=explode("|",$CommissionValue);
               $AgentCommissionAmount=(($BaseFare*$CommissionValueQ[0])/100);
                $TdsOnCommission=(($AgentCommissionAmount*10)/100);
               
           }
            }
              

           
        }


                $BaseFare = $this->flight_model->currency_convertor($BaseFare, $BaseFareCurrencyCode, CURR);
                $BaseFare = $this->account_model->PercentageToAmount($BaseFare, $aMarkup);
                $BaseFare = $this->account_model->PercentageToAmount($BaseFare, $myMarkup);
                $TotalTax = (string) $Result->Fare->Tax;
                $TotalTaxCurrencyCode = (string) $Result->Fare->Currency;

                $TotalTax = $this->flight_model->currency_convertor($TotalTax, $TotalTaxCurrencyCode, CURR);
                $TotalTax = $this->account_model->PercentageToAmount($TotalTax, $aMarkup);
                $TotalTax = $this->account_model->PercentageToAmount($TotalTax, $myMarkup);

                $TotalFare = (string) $Result->Fare->PublishedPrice;
                  $AgentNetFare=(string) $Result->Fare->OfferedFare;
                $CurrencyCode = (string) $Result->Fare->Currency;
                $TotalFare_org = $TotalFare;

                $TotalFare = $this->flight_model->currency_convertor($TotalFare, $CurrencyCode, CURR);
                 $AgentNetFare=$this->flight_model->currency_convertor($AgentNetFare, $CurrencyCode, CURR);
                $TMarkup = $this->account_model->PercentageAmount($TotalFare, $aMarkup);
                $TotalFare = $this->account_model->PercentageToAmount($TotalFare, $aMarkup);
                $Markup = $this->account_model->PercentageAmount($TotalFare, $myMarkup);
                $TotalFare = $this->account_model->PercentageToAmount($TotalFare, $myMarkup);


                $fs[$i]['FlightDetails'] = $Result;

                if ($Result->IsLcc != "") {
                    $fs[$i]['IsLcc'] = $Result->IsLcc;
                }

                $fs[$i]['BaseFare'] = $BaseFare;
                $fs[$i]['TotalTax'] = $TotalTax;
                $fs[$i]['AgentNetFare'] = $AgentNetFare;
                if(isset($AgentCommissionAmount)) {
                $fs[$i]['AgentCommissionByAdmin'] = $AgentCommissionAmount;
                 $fs[$i]['TdsOnCommission'] = $TdsOnCommission;
                }
                $fs[$i]['TotalFare'] = $TotalFare;
                $fs[$i]['TotalPrice_API'] = $TotalFare_org;
                $fs[$i]['APICurrencyType'] = $CurrencyCode;
                $fs[$i]['SITECurrencyType'] = CURR;
                $fs[$i]['MyMarkup'] = $Markup;
                $fs[$i]['aMarkup'] = $aMarkup;
                $fs[$i]['AdminMarkup'] = $TMarkup;

                $fs[$i]['PricingSource'] = '';
                $fs[$i]['PassengerTypeQuantityCode'] = (string) $Result->FareBreakdown->WSPTCFare->PassengerType;
                $fs[$i]['PassengerTypeQuantity'] = (string) $Result->FareBreakdown->WSPTCFare->PassengerCount;


                $Segment = '';
                if (is_array($Result->Segment->WSSegment)) {
                    $Segment = $Result->Segment->WSSegment;
                } else {
                    $Segment[] = $Result->Segment->WSSegment;
                }
                if ($Result->TripIndicator == 1) {
                    $mode = 'onward';
                } else {
                    $mode = 'return';
                }
//echo '<pre>';print_r($Segment);exit;
//foreach ($Result->AirItinerary as $AirItinerary) {
//	$trip_type = (string) $AirItinerary['DirectionInd'];
//echo '<pre>';print_r($AirItinerary);
                $round = 0;
                $f = 0;
                foreach ($Segment as $FlightSegment) {


//$segments_count  = count($Segmentval);
//echo $i.'-';
//echo $f.'-';
                    $fs[$i][$mode]['Segments'][$f]['ResBookDesigCode'] = '';
                    $fs[$i][$mode]['Segments'][$f]['NumberInParty'] = '';
                    $fs[$i][$mode]['Segments'][$f]['RPH'] = '';
                    $fs[$i][$mode]['Segments'][$f]['FlightNumber'] = (string) $FlightSegment->FlightNumber;
                    $fs[$i][$mode]['Segments'][$f]['DepartureDateTime'] = (string) $FlightSegment->DepTIme;
                    $fs[$i][$mode]['Segments'][$f]['ArrivalDateTime'] = (string) $FlightSegment->ArrTime;
                    $fs[$i][$mode]['Segments'][$f]['StopQuantity'] = (string) $FlightSegment->Stop;
                    $fs[$i][$mode]['Segments'][$f]['DepartureAirport'] = (string) $FlightSegment->Origin->CityName;
                    $fs[$i][$mode]['Segments'][$f]['DepartureAirportCode'] = (string) $FlightSegment->Origin->CityCode;
                    $fs[$i][$mode]['Segments'][$f]['ArrivalAirport'] = (string) $FlightSegment->Destination->CityName;
                    $fs[$i][$mode]['Segments'][$f]['ArrivalAirportCode'] = (string) $FlightSegment->Destination->CityCode;
                    $fs[$i][$mode]['Segments'][$f]['OperatingAirline'] = (string) $FlightSegment->Airline->AirlineName;
                    $fs[$i][$mode]['Segments'][$f]['OperatingAirlineCode'] = (string) $FlightSegment->Airline->AirlineCode;
                    $fs[$i][$mode]['Segments'][$f]['Equipment'] = '';
                    $fs[$i][$mode]['Segments'][$f]['EquipmentAirEquipType'] = '';
                    $fs[$i][$mode]['Segments'][$f]['MarketingAirline'] = (string) $FlightSegment->Airline->AirlineName;
                    $fs[$i][$mode]['Segments'][$f]['MarketingAirlineCode'] = (string) $FlightSegment->Airline->AirlineCode;
                    $fs[$i][$mode]['Segments'][$f]['CabinType'] = '';
                    $fs[$i][$mode]['Segments'][$f]['JourneyTotalDuration'] = '';
                    if ($f > 0) {
//$fs[$f] = $FlightSegment['DepartureDateTime'];
                    }
//print_r($FlightSegment['DepartureDateTime']);
//$segm[$p][$q] = $FlightSegment;

                    $f++;

                    $round++;
                }
//}
//echo '<pre>';print_r($fs);die;
                $i++;
            }
//print_r($Flights);
//print_r($flight_details);
//echo '<pre>';print_r($fs);die;
            return $fs;
        }
    }

    function formatMultiResponse($AirLowFareSearchPlusRQ_RS, $aMarkup, $myMarkup) {
        // echo "balu";exit;
        $response = $AirLowFareSearchPlusRQ_RS['AirLowFareSearchPlusRS'];
        if (empty($AirLowFareSearchPlusRS)) {
//  $this->XML_Log($AirLowFareSearchPlusRQ_RS['AirLowFareSearchPlusRQ'],$AirLowFareSearchPlusRQ_RS['AirLowFareSearchPlusRS']);
//  return false;
        }

//echo '<pre/>';
//print_r($response);exit;
        if ($response->SearchResult->Status->Description == 'Successfull') {
//$this->XML_Log($AirLowFareSearchPlusRQ_RS['AirLowFareSearchPlusRQ'],$AirLowFareSearchPlusRQ_RS['AirLowFareSearchPlusRS']);
//return false;
        }
        $SearchResult = $response->SearchResult;


        $session_id = $response->SearchResult->SessionId;
        $i = 0;
        if (isset($SearchResult->Result->WSResult)) {
            foreach ($SearchResult->Result->WSResult as $Result) {

                $flight_id = $Result->SegmentKey;
                $flight_id = $flight_id;
//$TicketTimeLimit = (string) $PricedItinerary->TicketingInfo['TicketTimeLimit'];

                $fs[$i]['Flight_Id'] = $flight_id;
                $fs[$i]['session_id'] = $session_id;
                $fs[$i]['ArrivalAirportCode'] = '';
                $fs[$i]['ArrivalDateTime'] = '';
                $fs[$i]['MarketingAirline'] = '';
                $fs[$i]['MarketingAirlineCode'] = '';



                $BaseFare = (string) $Result->Fare->BaseFare;
                $BaseFareCurrencyCode = (string) $Result->Fare->Currency;
                
                   if($this->session->userdata('b2b_id')!='')
        {
            if (is_array($Result->Segment->WSSegment)) {
                    $Segment = $Result->Segment->WSSegment;
                } else {
                    $Segment[] = $Result->Segment->WSSegment;
                }
             $AirlineName=$Segment[0]->Airline->AirlineName;
               $b2b_id = $this->session->userdata('b2b_id');
               $MyCommission=$this->account_model->Agent_commission_SpecFlight($b2b_id,$AirlineName);
              if(isset($MyCommission['result'])) {
             $CommissionType=$MyCommission['result'][0]->Type;
             $CommissionPlan=$MyCommission['CommissionPlan'];
             $CommissionValue=$MyCommission['result'][0]->$CommissionPlan;
             
              
           if($CommissionType=="Rs")
           {
               $AgentCommissionAmount=$CommissionValue;
               $TdsOnCommission=(($AgentCommissionAmount*10)/100);
           }
           else 
           {
               $CommissionValueQ=explode("|",$CommissionValue);
               $AgentCommissionAmount=(($BaseFare*$CommissionValueQ[0])/100);
                $TdsOnCommission=(($AgentCommissionAmount*10)/100);
               
           }
            }
              

           
        }


                $BaseFare = $this->flight_model->currency_convertor($BaseFare, $BaseFareCurrencyCode, CURR);
                $BaseFare = $this->account_model->PercentageToAmount($BaseFare, $aMarkup);
                $BaseFare = $this->account_model->PercentageToAmount($BaseFare, $myMarkup);
                $TotalTax = (string) $Result->Fare->Tax;
                $TotalTaxCurrencyCode = (string) $Result->Fare->Currency;

                $TotalTax = $this->flight_model->currency_convertor($TotalTax, $TotalTaxCurrencyCode, CURR);
                $TotalTax = $this->account_model->PercentageToAmount($TotalTax, $aMarkup);
                $TotalTax = $this->account_model->PercentageToAmount($TotalTax, $myMarkup);

                $TotalFare = (string) $Result->Fare->PublishedPrice;
                  $AgentNetFare=(string) $Result->Fare->OfferedFare;
                $CurrencyCode = (string) $Result->Fare->Currency;
                $TotalFare_org = $TotalFare;

                $TotalFare = $this->flight_model->currency_convertor($TotalFare, $CurrencyCode, CURR);
                 $AgentNetFare=$this->flight_model->currency_convertor($AgentNetFare, $CurrencyCode, CURR);
                $TMarkup = $this->account_model->PercentageAmount($TotalFare, $aMarkup);
                $TotalFare = $this->account_model->PercentageToAmount($TotalFare, $aMarkup);
                $Markup = $this->account_model->PercentageAmount($TotalFare, $myMarkup);
                $TotalFare = $this->account_model->PercentageToAmount($TotalFare, $myMarkup);


                $fs[$i]['FlightDetails'] = $Result;

                if ($Result->IsLcc != "") {
                    $fs[$i]['IsLcc'] = $Result->IsLcc;
                }
                $fs[$i]['BaseFare'] = $BaseFare;
                $fs[$i]['TotalTax'] = $TotalTax;
                $fs[$i]['TotalFare'] = $TotalFare;
                $fs[$i]['AgentNetFare'] = $AgentNetFare;
                if(isset($AgentCommissionAmount)) {
                $fs[$i]['AgentCommissionByAdmin'] = $AgentCommissionAmount;
                 $fs[$i]['TdsOnCommission'] = $TdsOnCommission;
                }
                $fs[$i]['TotalPrice_API'] = $TotalFare_org;
                $fs[$i]['APICurrencyType'] = $CurrencyCode;
                $fs[$i]['SITECurrencyType'] = CURR;
                $fs[$i]['MyMarkup'] = $Markup;
                $fs[$i]['aMarkup'] = $aMarkup;
                $fs[$i]['AdminMarkup'] = $TMarkup;

                $fs[$i]['PricingSource'] = '';
                $fs[$i]['PassengerTypeQuantityCode'] = (string) $Result->FareBreakdown->WSPTCFare->PassengerType;
                $fs[$i]['PassengerTypeQuantity'] = (string) $Result->FareBreakdown->WSPTCFare->PassengerCount;


                $Segment = '';
                if (is_array($Result->Segment->WSSegment)) {
                    $Segment = $Result->Segment->WSSegment;
                } else {
                    $Segment[] = $Result->Segment->WSSegment;
                }

//echo '<pre>';print_r($Segment);exit;
//foreach ($Result->AirItinerary as $AirItinerary) {
//	$trip_type = (string) $AirItinerary['DirectionInd'];
//echo '<pre>';print_r($AirItinerary);
                $round = 0;
                $f = 0;

                foreach ($Segment as $FlightSegment) {

                    if ($FlightSegment->SegmentIndicator == 1) {
                        $mode = 'onward';
                    } else {
                        $mode = 'return';
                    }
//$segments_count  = count($Segmentval);
//echo $i.'-';
//echo $f.'-';
                    $fs[$i]['Segments'][$f]['ResBookDesigCode'] = '';
                    $fs[$i]['Segments'][$f]['NumberInParty'] = '';
                    $fs[$i]['Segments'][$f]['RPH'] = '';
                    $fs[$i]['Segments'][$f]['FlightNumber'] = (string) $FlightSegment->FlightNumber;
                    $fs[$i]['Segments'][$f]['DepartureDateTime'] = (string) $FlightSegment->DepTIme;
                    $fs[$i]['Segments'][$f]['ArrivalDateTime'] = (string) $FlightSegment->ArrTime;
                    $fs[$i]['Segments'][$f]['StopQuantity'] = (string) $FlightSegment->Stop;
                    $fs[$i]['Segments'][$f]['DepartureAirport'] = (string) $FlightSegment->Origin->CityName;
                    $fs[$i]['Segments'][$f]['DepartureAirportCode'] = (string) $FlightSegment->Origin->CityCode;
                    $fs[$i]['Segments'][$f]['ArrivalAirport'] = (string) $FlightSegment->Destination->CityName;
                    $fs[$i]['Segments'][$f]['ArrivalAirportCode'] = (string) $FlightSegment->Destination->CityCode;
                    $fs[$i]['Segments'][$f]['OperatingAirline'] = (string) $FlightSegment->Airline->AirlineName;
                    $fs[$i]['Segments'][$f]['OperatingAirlineCode'] = (string) $FlightSegment->Airline->AirlineCode;
                    $fs[$i]['Segments'][$f]['Equipment'] = '';
                    $fs[$i]['Segments'][$f]['EquipmentAirEquipType'] = '';
                    $fs[$i]['Segments'][$f]['MarketingAirline'] = (string) $FlightSegment->Airline->AirlineName;
                    $fs[$i]['Segments'][$f]['MarketingAirlineCode'] = (string) $FlightSegment->Airline->AirlineCode;
                    $fs[$i]['Segments'][$f]['CabinType'] = '';
                    $fs[$i]['Segments'][$f]['JourneyTotalDuration'] = '';
                    if ($f > 0) {
//$fs[$f] = $FlightSegment['DepartureDateTime'];
                    }
//print_r($FlightSegment['DepartureDateTime']);
//$segm[$p][$q] = $FlightSegment;

                    $f++;

                    $round++;
                }
//}
//echo '<pre>';print_r($fs);die;
                $i++;
            }
//print_r($Flights);
//print_r($flight_details);
//echo '<pre>';print_r($fs);die;
            return $fs;
        }
    }

    public function cancel($pnr_no) {
        $pnr_no = base64_decode(base64_decode($pnr_no));
        $count = $this->booking_model->getBookingPnr($pnr_no)->num_rows();
        if ($count == 1) {
            $b_data = $this->booking_model->getBookingPnr($pnr_no)->row();
//echo '<pre>';print_r($b_data);die;
            if ($b_data->booking_status == 'CONFIRMED') {
                $CancelReq_Res = CancelReq($b_data->booking_no);
                $CancelRes = $CancelReq_Res['CancelRes'];
                $CancelRes = $this->xml_to_array->XmlToArray($CancelRes);
//echo '<pre>';print_r($CancelRes);die;
                if (isset($CancelRes['SOAP:Body']['universal:UniversalRecordCancelRsp'])) {
                    $CancelRes = $CancelRes['SOAP:Body']['universal:UniversalRecordCancelRsp']['universal:ProviderReservationStatus'];
                    $CancelResAttr = $CancelRes['@attributes'];
                    if ($CancelResAttr['Cancelled']) {
//echo '<pre>';print_r($CancelResAttr);die;
                        $update_booking = array(
                            'booking_status' => 'CANCELED'
                        );
                        $this->booking_model->Update_Booking_Global($pnr_no, $update_booking, 'FLIGHT');
                        $this->cancel_mail_voucher($pnr_no);
                        $response = array('status' => 1);
                        echo json_encode($response);
                    }
                } else {
                    $xml_log = array(
                        'Api' => 'UAPI',
                        'XML_Type' => 'Flight',
                        'XML_Request' => $CancelReq_Res['CancelReq'],
                        'XML_Response' => $CancelReq_Res['CancelRes'],
                        'Ip_address' => $this->input->ip_address(),
                        'XML_Time' => date('Y-m-d H:i:s')
                    );
                    $this->xml_model->insert_xml_log($xml_log);
                }
            }
        } else {
            $response = array('status' => 0);
            echo json_encode($response);
        }
    }

    public function cancel_mail_voucher($pnr_no) {
        $count = $this->booking_model->getBookingPnr($pnr_no)->num_rows();
        if ($count == 1) {
            $b_data = $this->booking_model->getBookingPnr($pnr_no)->row();
            if ($b_data->module == 'FLIGHT') {
                $data['Booking'] = $booking = $this->booking_model->getBookingbyPnr($b_data->pnr_no, $b_data->module)->row();
                $data['message'] = $this->load->view('flight/mail_voucher', $data, TRUE);
                $data['to'] = $booking->BILLING_EMAIL;
                $data['email_access'] = $this->email_model->get_email_acess()->row();
                $email_type = 'ApartmentVoucher';
                $data['email_template'] = $this->email_model->get_email_template($email_type)->row();
                $data['booking_status'] = strtolower($booking->booking_status);
                $data['social_url'] = array(
                    'facebook_social_url' => 'https://www.facebook.com',
                    'twitter_social_url' => 'https://twitter.com',
                    'google_social_url' => 'https://plus.google.com',
                );
                $Response = $this->email_model->sendmail_flightVoucher($data);
                $response = array('status' => 1);
            }
        }
    }

    function PriceRequest($data) {

        $this->load->view('flight/PriceRequest', $data);
    }

    public function XML_Log($request, $response) {
        $xml_log = array(
            'Api' => 'Tripxml',
            'XML_Type' => 'Flight',
            'XML_Request' => $request,
            'XML_Response' => $response,
            'Ip_address' => $this->input->ip_address(),
            'XML_Time' => date('Y-m-d H:i:s')
        );
        $this->xml_model->insert_xml_log($xml_log);
    }

}

/* End of file flight.php */
/* Location: ./application/controllers/flight.php */
