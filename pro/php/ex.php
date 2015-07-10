<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Booking extends CI_Controller {

    public function __construct() {
        parent::__construct();
        // $current_url = $_SERVER['QUERY_STRING'] ? '?'.$_SERVER['QUERY_STRING'] : '';
        // $current_url = $this->config->site_url().$this->uri->uri_string(). $current_url;
        // $url =  array(
        //     'continue' => $current_url,
        // );
        $this->load->model('Auth_Model');
        $this->load->model('account_model');
        $this->load->model('Verification_Model');
        $this->load->model('email_model');
        $this->load->model('booking_model');
        $this->load->model('bus_model');
        $this->load->model('cart_model');
        $this->load->helper('flight_helper');
        $this->load->helper('hotel_helper');
        $this->load->helper('transfer_helper');
        $this->load->library('xml_to_array');
    }

    // public function index($session_id = '', $global_id = '', $vald = '',$log = '') {
    public function index($session_id = '', $global_id = '', $vald = '') {
        parent::pre_url();
        // echo $session_id;exit;
        $data['gid'] = $global_id;


        if ($vald == '') {
            $this->islogged_in($session_id, $global_id);
        }
//echo $global_id;
        //echo $continue = $this->session->userdata('continue');die;
        if (!empty($session_id) && !empty($global_id)) {

            $global_ids = explode('ROUND', $global_id);

            if (count($global_ids) > 1) {

                $global_id = $global_ids[0];
                $global_id_R = $global_ids[1];
            } else {

                $global_id = $global_ids[0];
            }


            //$id = $cart_global_id.'(:.:)';
            $global_id = json_decode(base64_decode(base64_decode($global_id)));
            if (isset($global_id_R)) {
                $global_id_R = json_decode(base64_decode(base64_decode($global_id_R)));
            }
            list($global_id) = explode('(:.:)', $global_id);

            $count = $this->cart_model->getBookingTemp($session_id, $global_id)->num_rows();



            if ($count > 0) {
                $data['countries'] = $this->booking_model->getAllCountries()->result();
                $data['book_temp_data'] = $book_temp_data = $this->cart_model->getBookingTemp($session_id, $global_id)->result();
                //$data['guests'] = $book_temp_data->RES_N_ADULTS+$book_temp_data->RES_N_CHILDREN+$book_temp_data->RES_N_BABIES;
                if (isset($global_id_R)) {

                    $data['RentData'] = $book_temp_data_R = $this->cart_model->getBookingTemp($session_id, $global_id_R)->result();
                }
                // echo '<pre>';print_r($book_temp_data_R);die;
                foreach ($book_temp_data as $key => $value) {
                    $cart_global_id[] = $value->module . ',' . $value->cart_id;
                }
                if (isset($global_id_R)) {
                    foreach ($book_temp_data_R as $key => $value) {
                        $cart_global_id[] = $value->module . ',' . $value->cart_id;
                    }
                }
                // echo '<pre>';print_r($cart_global_id);die;
                $data['cart_global'] = $cart_global_id;
                $data['cart_global_id'] = base64_encode(json_encode($cart_global_id));
                $b2c_id = $this->session->userdata('b2c_id');
                if ($this->session->userdata('b2c_id')) {
                    $data['user_type'] = $user_type = 3;
                    $data['user_id'] = $user_id = $this->session->userdata('b2c_id');
                    $data['userInfo'] = $this->account_model->GetUserData($user_type, $user_id)->row();
                    $data['email'] = $data['userInfo']->email;
                    $data['contact_no'] = $data['userInfo']->contact_no;
                    
                    $data['ewallet_bal']=$this->account_model->gettting_ewallet_bal($b2c_id);
               
                } else if ($this->session->userdata('b2b_id')) {
                    $data['user_type'] = $user_type = 2;
                    $data['user_id'] = $user_id = $this->session->userdata('b2b_id');
                    $data['userInfo'] = $this->account_model->GetUserData($user_type, $user_id)->row();
                    $data['email'] = $data['userInfo']->email_id;
                    $data['contact_no'] = $data['userInfo']->mobile;
                }


                if ($this->session->userdata('gust_email')) {
                    $data['user_type'] = $user_type = 4;
                    $data['user_id'] = $user_id = 0;
                    $data['userInfo'] = '';
                    $data['email'] = '';
                    $data['contact_no'] = '';
                }
                $data['ses'] = $session_id;

                $this->load->view('common/booking_flight', $data);
            } else {
                $this->load->view('errors/expiry');
            }
        } else {
            $this->load->view('errors/expiry');
        }
    }

    public function checkBillingDetailsB2c($user_id) {
        $billing_details = $this->booking_model->getBillingDetailsB2c($user_id)->row();
        if ($billing_details->billing_firstname == "" &&
                $billing_details->billing_lastname == "" &&
                $billing_details->billing_addressA == "" &&
                $billing_details->billing_addressB == "" &&
                $billing_details->billing_email == "" &&
                $billing_details->billing_contact == "" &&
                $billing_details->billing_country == "" &&
                $billing_details->billing_city == "" &&
                $billing_details->billing_state == "" &&
                $billing_details->billing_postal == "") {
            return true;
        } else {
            return false;
        }
    }

    public function checkBillingDetailsB2b($user_id) {
        $billing_details = $this->booking_model->getBillingDetailsB2b($user_id)->row();
        if ($billing_details->billing_firstname == "" &&
                $billing_details->billing_lastname == "" &&
                $billing_details->billing_addressA == "" &&
                $billing_details->billing_addressB == "" &&
                $billing_details->billing_email == "" &&
                $billing_details->billing_contact == "" &&
                $billing_details->billing_country == "" &&
                $billing_details->billing_city == "" &&
                $billing_details->billing_state == "" &&
                $billing_details->billing_postal == "") {
            return true;
        } else {
            return false;
        }
    }

    public function doPaymentGate($Total, $Email, $OrderId, $checkoutDate) {
        $data['TotalFare'] = json_decode(base64_decode($Total));
        $data['Email'] = json_decode(base64_decode($Email));
        $data['OrderId'] = json_decode(base64_decode($OrderId));
        $AllData = explode(" ", json_decode(base64_decode($checkoutDate)));


       

        $data['FirstName'] = $AllData[0];
        $data['LasttName'] = $AllData[1];
        $data['Address'] = $AllData[2];
        $data['Mobile'] = $AllData[3];
        $data['Country'] = $AllData[4];
        $data['City'] = $AllData[5];
        $data['State'] = $AllData[6];
        $data['ZipCode'] = $AllData[7];
        //echo "<pre>";echo $data['OrderId'];print_r($data);exit;
        $this->load->view('common/doPayment', $data);
    }

    public function checkout() {
        //parent::pre_url();
        $checkout_form = $this->input->get();
        //echo '<pre>';print_r( $checkout_form);exit;
        //billing array updates the billing details in respective user tables
        $billing['billing_firstname'] = $checkout_form['ADT_GivenName'][0];
        $billing['billing_lastname'] = $checkout_form['ADT_Surname'][0];
        $billing['billing_addressA'] = $checkout_form['street_address'];
        $billing['billing_addressB'] = '';
        $billing['billing_email'] = $checkout_form['email'];
        $billing['billing_contact'] = $checkout_form['mobile'];
        $billing['billing_country'] = $checkout_form['country'];
        $billing['billing_city'] = $checkout_form['city'];
        $billing['billing_state'] = $checkout_form['state'];
        $billing['billing_postal'] = $checkout_form['zip'];


        $total_payable = base64_decode($checkout_form['total']);
        // $total_discount = base64_decode($checkout_form['discount']);
        $cids = base64_decode($checkout_form['cid']);
        $cids = json_decode($cids);

        if ($this->session->userdata('b2c_id')) {
            $user_type = 3;
            $user_id = $this->session->userdata('b2c_id');
            $checkBillingDetailsB2c = $this->checkBillingDetailsB2c($user_id);
                /* getting mode of payment*/
             $option_payment_types_b2c=$checkout_form['payment_type']; 


            if ($checkBillingDetailsB2c) {
                $this->booking_model->updateBillingDetailsB2c($user_id, $billing);
            }
        } else if ($this->session->userdata('b2b_id')) {
            $user_type = 2;
            $user_id = $this->session->userdata('b2b_id');

            $checkBillingDetailsB2b = $this->checkBillingDetailsB2b($user_id);

            if ($checkBillingDetailsB2b) {
                $this->booking_model->updateBillingDetailsB2b($user_id, $billing);
            }


            $status = $this->b2b_check_payment($total_payable, $user_id);
            if ($status == false) {
                $data['status'] = -2;
                echo json_encode($data);
                die;
            }
        } else {
            //$data['status'] = -1;
            //   $data['signup_login'] = WEB_URL.'/booking/signup_login';
            //  echo json_encode($data);die;
            $user_type = 4;
            $user_id = 0;
        }
        // echo '<pre>';print_r($cids);exit;
       // echo 'user_type :'.$user_type;exit;
        $parent_pnr = $this->generate_parent_pnr();

        foreach ($cids as $key => $cid) {
            list($module, $cid) = explode(',', $cid);
            if ($module == 'FLIGHT') {

                $cart_flight_data = $this->flight_model->getBookingTemp($cid)->row();

                $TOTAL = $cart_flight_data->TotalPrice;
                $DISCOUNT = 0;
                // echo '<pre>';print_r($cart_flight_data);exit;


                $AirBookRQ_RS = AirBookRQ($cart_flight_data, $checkout_form, $cid);
                //echo '<pre>';print_r($AirBookRQ_RS);exit;
                // $IsLcc=1;
                //  $StatusCode=3;
                //  $Description="Successful";

                if (isset($AirBookRQ_RS['IsLcc'])) {
                    $IsLcc = $AirBookRQ_RS['IsLcc'];
                } else {
                    $IsLcc = 2;
                }
                if ($IsLcc == 1) {

                    $StatusCode = $AirBookRQ_RS['AirLowFareSearchPlusRS']->GetFareQuoteResult->Status->StatusCode;
                    if (isset($StatusCode)) {
                        $Description = $AirBookRQ_RS['AirLowFareSearchPlusRS']->GetFareQuoteResult->Status->Description;

                        if ($StatusCode != 03) {

                            $data['Description'] = $AirBookRQ_RS['AirLowFareSearchPlusRS']->GetFareQuoteResult->Status->Description;


                            $GateURL = WEB_URL . '/flight/PriceRequest/?Des=' . $data['Description'];

                            $data['status'] = 555;
                            $data['GateURL'] = $GateURL;
                            echo json_encode($data);
                            die;
                        }
                    }
                } else {

                    $BookResult = $AirBookRQ_RS['AirLowFareSearchPlusRS'];
                    if (isset($BookResult->BookResult->Status->StatusCode)) {
                        $StatusCode = $BookResult->BookResult->Status->StatusCode;
                    } else {
                        $StatusCode = '';
                    }

                    //  echo $StatusCode;
                    $data['Description'] = $BookResult->BookResult->Status->Description;
                    if (($StatusCode != 03) && ($StatusCode != 06)) {


                        $GateURL = WEB_URL . '/flight/PriceRequest/?Des=' . $data['Description'];
                        $data['status'] = 555;
                        $data['GateURL'] = $GateURL;
                        echo json_encode($data);
                        die;
                    }
                }




                $cart_flight_data_v1 = base64_encode(json_encode($cart_flight_data));
                $TravelerDetails = json_encode($checkout_form);

                $FareRequest = base64_encode(json_encode($AirBookRQ_RS['AirLowFareSearchPlusRQ']));
                $FareResponse = base64_encode(json_encode($AirBookRQ_RS['AirLowFareSearchPlusRS']));
                if (!empty($AirBookRQ_RS['PassengerInfo'])) {
                    $PassengerInfo = base64_encode(json_encode($AirBookRQ_RS['PassengerInfo']));
                } else {
                    $PassengerInfo = '';
                }
                $booking_flight = array(
                    'cart_flight_data' => $cart_flight_data_v1,
                    'request' => $cart_flight_data->request,
                    'response' => $cart_flight_data->response,
                    'FareRequest' => $FareRequest,
                    'FareResponse' => $FareResponse,
                    'PassengerInfo' => $PassengerInfo,
                    'Origin' => $cart_flight_data->Origin,
                    'Destination' => $cart_flight_data->Destination,
                    'fromCityName' => $cart_flight_data->fromCityName,
                    'toCityName' => $cart_flight_data->toCityName,
                    'DepartureTime' => $cart_flight_data->DepartureTime,
                    'ArrivalTime' => $cart_flight_data->ArrivalTime,
                    'duration' => $cart_flight_data->duration,
                    'AirImage' => $cart_flight_data->AirImage,
                    'TravelerDetails' => $TravelerDetails, //remove here
                    'BILLING_FIRSTNAME' => $checkout_form['ADT_GivenName'][0],
                    'BILLING_LASTNAME' => $checkout_form['ADT_Surname'][0],
                    'BILLING_EMAIL' => $checkout_form['email'],
                    'BILLING_PHONE' => $checkout_form['mobile'],
                    'BILLING_COUNTRY' => $checkout_form['country'],
                    'BILLING_ADDRESS' => $checkout_form['street_address'],
                    'BILLING_CITY' => $checkout_form['city'],
                    'BILLING_STATE' => $checkout_form['state'],
                    'BILLING_ZIP' => $checkout_form['zip'],
                    'TotalPrice' => $TOTAL, //remove here
                    'TravelDate' => date('Y-m-d', $cart_flight_data->DepartureTime),
                    'SITE_CURR' => $cart_flight_data->SITE_CURR,
                    'API_CURR' => $cart_flight_data->API_CURR,
                    'MyMarkup' => $cart_flight_data->MyMarkup, //remove here
                    'AdminMarkup' => $cart_flight_data->AdminMarkup, //remove here
                    'BasePrice' => $cart_flight_data->BasePrice,
                    'TaxPrice' => $cart_flight_data->TaxPrice,
                    'USER_TYPE' => $user_type,
                    'USER_ID' => $user_id,
                    //  'booking_res' => $booking_res,  //remove here
                    'TIMESTAMP' => date('Y-m-d H:i:s')
                );
                $booking_flight_id = $this->flight_model->insert_booking_flight($booking_flight);
                $booking = array(
                    'module' => 'FLIGHT',
                    'ref_id' => $booking_flight_id,
                    'parent_pnr' => $parent_pnr,
                    'user_type' => $user_type,
                    'amount' => $TOTAL,
                    'leadpax' => $checkout_form['ADT_GivenName'][0] . ' ' . $checkout_form['ADT_Surname'][0],
                    'user_id' => $user_id,
                    'ip' => $this->input->ip_address(),
                    'travel_date' => date('Y-m-d', $cart_flight_data->DepartureTime)
                );
                $bid = $this->flight_model->Booking_Global($booking);
                $pnr_d = date("ymd");
                $pnr_d1 = date("His");
                $pnr_no = 'SWTF' . $pnr_d . $pnr_d1 . $bid;
                $update_booking = array(
                    'pnr_no' => $pnr_no
                );
                $this->flight_model->Update_Booking_Global($bid, $update_booking, 'FLIGHT');

                $this->flight_model->clearCart($cid);

                // echo "xsxsxsx";exit;
            } else if ($module == 'HOTEL') {
                $sess_id = $this->session->userdata('session_id');
                #exit;

                $cart_hotel_data = $this->hotel_model->getBookingTempD($sess_id)->row();



                $session_id = $this->session->userdata('session_id');
                $hotel_blocking_id = $this->session->userdata('hotel_blocking_id');




                #$datatest ={"ResultIndex":"11","HotelCode":"JYO|DEL","HotelName":"JYOTI MAHAL","GuestNationality":"IN","NoOfRooms":"1","ClientReferenceNo":1421998920,"IsVoucherBooking":true,"HotelRoomsDetails":[{"RoomIndex":1,"RatePlanCode":"001:JYO:18909:S18618:26579:103054|2","RatePlanName":null,"RoomTypeCode":"TB|0|1|1","RoomTypeName":"Twin Room Deluxe Triple with 1 ExtraBed(s)","BedTypeCode":"","SmokingPreference":0,"Supplements":"","Price":{"CurrencyCode":"INR","RoomPrice":2970,"Tax":0,"ExtraGuestCharge":0,"ChildCharge":0,"OtherCharges":0,"Discount":0,"PublishedPrice":2970,"PublishedPriceRoundedOff":2970,"OfferedPrice":2970,"OfferedPriceRoundedOff":2970,"AgentCommission":0,"AgentMarkUp":0,"ServiceTax":0,"TDS":0},"HotelPassenger":[{"Title":"Mr","FirstName":"raju","MiddleName":"","LastName":"a","Phoneno":"8880004219","Email":"raju.provab@gmail.com","PaxType":"1","LeadPassenger":true,"Age":"24"},{"Title":"Mr","FirstName":"raju","MiddleName":"","LastName":"a","Phoneno":"8880004219","Email":"raju.provab@gmail.com","PaxType":"2","LeadPassenger":"0","Age":"9"},{"Title":"Mr","FirstName":"raju","MiddleName":"","LastName":"a","Phoneno":"8880004219","Email":"raju.provab@gmail.com","PaxType":"2","LeadPassenger":"0","Age":"10"}]}],"EndUserIp":"192.168.10.130","TokenId":"d6b250a0-f840-439e-af82-42de0843e756","TraceId":"cb66720a-fdb7-4ea0-988b-9fe97aa3b175"};
                #$datat = json_decode($datatest ,true);
                #echo "<pre>";
                #print_r($datat);

                $datare = $cart_hotel_data->blockdata;



                $book_array_details = json_decode($datare, true);

                # echo "<pre>";
                # print_r($book_array_details);
                # exit;
                $TOTAL = 0;

                for ($i = 0; $i < count($book_array_details['HotelRoomsDetails']); $i++) {

                    $TOTAL = $TOTAL + $book_array_details['HotelRoomsDetails'][$i]['Price']['PublishedPrice'];
                    $DISCOUNT = 0;

                    $hotel_room_details = $this->hotel_model->getHotelRoomDetails($sess_id, $book_array_details['HotelCode'], $book_array_details['HotelRoomsDetails'][$i]['RoomTypeCode'])->row();
                    #echo "<pre>";
                    #print_r($hotel_room_details );
                    #exit;





                    $TravelerDetails = json_encode($checkout_form);



                    $booking_hotel = array(
                        'parent_cart_id' => $hotel_room_details->api_temp_hotel_id,
                        'request' => $cart_hotel_data->blockdata,
                        'api_temp_hotel_id_key' => $hotel_room_details->api_temp_hotel_id,
                        'hotel_name' => $hotel_room_details->hotelname,
                        'session_id' => $hotel_room_details->session_id,
                        'hotel_code' => $hotel_room_details->hotel_code,
                        'api' => $hotel_room_details->api,
                        'room_code' => $hotel_room_details->room_code,
                        'room_type' => $hotel_room_details->room_type,
                        'inclusion' => $hotel_room_details->inclusion,
                        'total_cost' => $hotel_room_details->total_cost,
                        'MyMarkup' => $hotel_room_details->t_markup,
                        'AdminMarkup' => "",
                        'status' => 'CONFIRMED',
                        'shurival' => $hotel_room_details->shurival,
                        'charval' => $hotel_room_details->charval,
                        'adult' => $hotel_room_details->adult,
                        'child' => $hotel_room_details->child,
                        'board_type' => $hotel_room_details->board_type,
                        'token' => $hotel_room_details->TokenId,
                        'inoffcode' => $hotel_room_details->inoffcode,
                        'contractnameVal' => $hotel_room_details->contractnameVal,
                        'destCodeVal' => $hotel_room_details->destCodeVal,
                        'shortname' => $hotel_room_details->shortname,
                        'child_age' => $hotel_room_details->child_age,
                        'room_count' => $hotel_room_details->noofrooms,
                        'city' => $hotel_room_details->city,
                        'Promotionsaa' => $hotel_room_details->Promotionsaa,
                        'ShortNameaa' => $hotel_room_details->ShortNameaa,
                        'Classification_val' => $hotel_room_details->Classification_val,
                        'des_offer_value' => $hotel_room_details->Discount,
                        'star' => $hotel_room_details->star,
                        'image' => $hotel_room_details->image,
                        'description' => $hotel_room_details->description,
                        'rate_typeval' => $hotel_room_details->rate_typeval,
                        'longitude' => $hotel_room_details->longitude,
                        'latitude' => $hotel_room_details->latitude,
                        'SITE_CURR' => CURR,
                        'API_CURR' => "INR",
                        'GUEST_FIRSTNAME' => $checkout_form['ADT_GivenName'][0],
                        'GUEST_LASTNAME' => $checkout_form['ADT_Surname'][0],
                        'GUEST_EMAIL' => $checkout_form['email'],
                        'GUEST_PHONE' => $checkout_form['mobile'],
                        'BILLING_FIRSTNAME' => $checkout_form['ADT_GivenName'][0],
                        'BILLING_LASTNAME' => $checkout_form['ADT_Surname'][0],
                        'BILLING_EMAIL' => $checkout_form['email'],
                        'BILLING_PHONE' => $checkout_form['mobile'],
                        'BILLING_COUNTRY' => $checkout_form['country'],
                        'BILLING_ADDRESS' => $checkout_form['street_address'],
                        'BILLING_CITY' => $checkout_form['city'],
                        'BILLING_STATE' => $checkout_form['state'],
                        'BILLING_ZIP' => $checkout_form['zip'],
                        'USER_TYPE' => $user_type,
                        'USER_ID' => $user_id,
                        'TravelerDetails' => $TravelerDetails,
                        'TIMESTAMP' => date('Y-m-d H:i:s'),
                        'check_in' => $hotel_room_details->check_in,
                        'check_out' => $hotel_room_details->check_out,
                        'booking_details' => $checkout_form['passengerdetailsenc']
                    );


                    #echo"<pre>";
                    #print_r($booking_hotel);


                    $booking_hotel_id = $this->hotel_model->insert_booking_hotel($booking_hotel);
                    $this->hotel_model->clearCart($cid);
                    $booking = array(
                        'module' => 'HOTEL',
                        'ref_id' => $booking_hotel_id,
                        'parent_pnr' => $parent_pnr,
                        'user_type' => $user_type,
                        'amount' => $hotel_room_details->total_cost,
                        'leadpax' => $checkout_form['ADT_GivenName'][0] . ' ' . $checkout_form['ADT_Surname'][0],
                        'user_id' => $user_id,
                        'ip' => $this->input->ip_address(),
                        'check_in' =>  $hotel_room_details->check_in,
                        'check_out' => $hotel_room_details->check_out
                    );


                    $bid[] = $this->hotel_model->Booking_Global($booking);
                }
                $pnr_d = date("ymd");
                $pnr_d1 = date("His");
                $pnr_no = 'SWTH' . $pnr_d . $pnr_d1 . $bid[0];
                $update_booking = array(
                    'pnr_no' => $pnr_no
                );

                for ($i = 0; $i < count($bid); $i++) {

                    $this->hotel_model->Update_Booking_Global($bid[$i], $update_booking, 'HOTEL');
                }


                # $pnr_no[] = $this->booking($booking_hotel_id, $parent_pnr, $module='HOTEL');
            } else if ($module == 'TRANSFER') {
                $cart_car_data = $this->transfer_model->getBookingTemp($cid)->row();
                $aMarkup = $cart_car_data->aMarkup; //get markup


                $MyMarkup = $this->account_model->get_my_markup(); //get agent markup
                $myMarkup = $MyMarkup['markup'];
                $cancel_markup = $myMarkup . '|' . $aMarkup;

                $car = json_decode(base64_decode($cart_car_data->response));
                $TOTAL = $cart_car_data->TotalPrice;
                $DISCOUNT = 0;
                //	echo '<pre/>';
                //	print_r($cart_car_data);exit;
                //		echo '<pre>';print_r($cart_car_data);die;
                $parent_pnr_ = $this->generate_parent_pnr('2');
                $parent_pnr_f = date('mdHi') . $parent_pnr_;
                $VehicleCreateReservationReq_Res = $res = TransferPurchaseConfirmRQ($cart_car_data, $checkout_form, $cid, $parent_pnr_f);
                //	echo '<pre/>';
                //	print_r($VehicleCreateReservationReq_Res);exit;
                $ServiceAddRS_f = explode("||||", $cart_car_data->ServiceAddRS);
                $booking_res_ = array();
                $bookingItemCodeval_final_f = array();
                $BookingStatus_f = array();
                $book_noval_f = array();
                $statusval_f = array();
                $cancellation_policy_F1 = array();
                $comp_pol_f = array();
                $VehicleCreateReservationReq_Res_filter = array_filter($VehicleCreateReservationReq_Res['PurchaseConfirmRS']);
                if (count($VehicleCreateReservationReq_Res_filter) == count($VehicleCreateReservationReq_Res['PurchaseConfirmRS'])) {
                    for ($pc = 0; $pc < count($VehicleCreateReservationReq_Res['PurchaseConfirmRS']); $pc++) {


                        $PurchaseConfirmRS = $VehicleCreateReservationReq_Res['PurchaseConfirmRS'][$pc];

                        //	echo $PurchaseConfirmRS;exit;
                        $dom = new DOMDocument();
                        $dom->loadXML($PurchaseConfirmRS);

                        $Holder = $dom->getElementsByTagName("Holder");



                        foreach ($Holder as $Holderval) {
                            $clientNameval = $Holderval->getElementsByTagName("Name");
                            $clientFName = $clientNameval->item(0)->nodeValue;
                            $clientLNameval = $Holderval->getElementsByTagName("LastName");
                            $clientLName = $clientLNameval->item(0)->nodeValue;
                            $clientName = ucfirst($clientFName . ' ' . $clientLName);
                        }
                        $AgencyReference = $dom->getElementsByTagName("AgencyReference");
                        $your_reference = $AgencyReference->item(0)->nodeValue;

                        $arr = array('your_reference' => $your_reference, 'client' => $clientName);
                        $this->session->set_userdata($arr);

                        $contract = $dom->getElementsByTagName("Contract");
                        foreach ($contract as $contractval) {
                            $contractname = $contractval->getElementsByTagName("Name");
                            $contractnameVal = $contractname->item(0)->nodeValue;
                        }
                        $agent = $dom->getElementsByTagName("Agency");
                        foreach ($agent as $agentval) {
                            $agentname = $agentval->getElementsByTagName("Code");
                            $agentnameVal = $agentname->item(0)->nodeValue;
                        }
                        $Holder = $dom->getElementsByTagName("Holder");
                        foreach ($Holder as $Holderval) {
                            $Holder = $Holderval->getElementsByTagName("Name");
                            $Holdername = $Holder->item(0)->nodeValue;
                            $Holderlname = $Holderval->getElementsByTagName("LastName");
                            $Holderlnameval = $Holderlname->item(0)->nodeValue;
                        }
                        $IncomingOfficecode = '';
                        $IncomingOffice = $dom->getElementsByTagName("IncomingOffice");
                        foreach ($IncomingOffice as $sdasa) {
                            $IncomingOfficecode = $IncomingOffice->item(0)->getAttribute("code");
                        }
                        $bookingItemCode = $dom->getElementsByTagName("FileNumber");
                        $bookingItemCodeval = '';
                        foreach ($bookingItemCode as $aaaaaaaa) {
                            $bookingItemCodeval = $bookingItemCode->item(0)->nodeValue;
                        }
                        $currency_obj = $dom->getElementsByTagName("Currency");
                        $currency_code = $currency_obj->item(0)->getAttribute("code");

                        $dateFromValc_amount = '';
                        $dateFromc = '';
                        $dateFromValc = '';
                        $cancellation_policy = $dom->getElementsByTagName("CancellationPolicies");
                        foreach ($cancellation_policy as $cancellation_policy_obj) {

                            $CancellationPolicy_obj = $cancellation_policy_obj->getElementsByTagName("CancellationPolicy");
                            $dateFromValc_amount = $CancellationPolicy_obj->item(0)->getAttribute("amount");
                            $dateFromc = $CancellationPolicy_obj->item(0)->getAttribute("time");
                            $dateFromValc = $CancellationPolicy_obj->item(0)->getAttribute("dateFrom");
                        }

                        $CancellationPolicy_amount = $this->flight_model->currency_convertor($dateFromValc_amount, $currency_code, CURR);
                        $CancellationPolicy_amount = $this->account_model->PercentageToAmount($CancellationPolicy_amount, $aMarkup);
                        $CancellationPolicy_amount = $this->account_model->PercentageToAmount($CancellationPolicy_amount, $myMarkup);

                        $year = substr($dateFromValc, 0, 4);
                        $mon = substr($dateFromValc, 4, 2);
                        $date = substr($dateFromValc, 6, 2);
                        $hour = substr($dateFromc, 0, 2);
                        $min = substr($dateFromc, 2, 2);

                        $can_date = $year . '-' . $mon . '-' . $date;

                        if ($can_date == '--' && $CancellationPolicy_amount == '0.00') {
                            $cancellation_policy_F = 'No Cancellation.';
                        } else {
                            $cancellation_policy_F = 'If cancellation done on or after ' . $can_date . ', ' . $CancellationPolicy_amount . CURR_ICON . ' amount will be charged';
                        }
                        $CommentListval = '';
                        $Supplier = $dom->getElementsByTagName("Supplier");
                        $Suppliername = $Supplier->item(0)->getAttribute("name");
                        $vat = $Supplier->item(0)->getAttribute("vatNumber");
                        $ref = $Supplier->item(0)->getAttribute("ref");
                        if ($ref == '') {
                            $comp_pol = 'Payable through' . ' ' . $Suppliername . ' ' . ', acting as agent for the service operating company, details of which can be provided upon request. VAT:' . $vat;
                        } else {
                            $comp_pol = 'Payable through' . ' ' . $Suppliername . ' ' . ', acting as agent for the service operating company, details of which can be provided upon request. VAT:' . $vat . ' ' . ' Reference:' . $ref;
                        }
                        $IncomingOfficecode = '';
                        $IncomingOffice = $dom->getElementsByTagName("IncomingOffice");
                        foreach ($IncomingOffice as $sdasa) {
                            $IncomingOfficecode = $IncomingOffice->item(0)->getAttribute("code");
                        }
                        $bookingItemCode = $dom->getElementsByTagName("FileNumber");
                        $bookingItemCodeval = '';
                        foreach ($bookingItemCode as $aaaaaaaa) {
                            $bookingItemCodeval = $bookingItemCode->item(0)->nodeValue;
                        }
                        $bookingItemCodeval_final = $IncomingOfficecode . '-' . $bookingItemCodeval;
                        $booking_status = 'Success';
                        $statusval = '';
                        $status = $dom->getElementsByTagName("Service");
                        foreach ($status as $ddd) {
                            $statuss = $ddd->getElementsByTagName("Status");
                            $statusval = $statuss->item(0)->nodeValue;
                        }
                        $book_noval = '';
                        $AgencyReference = $dom->getElementsByTagName("AgencyReference");
                        foreach ($AgencyReference as $sdsadsa) {
                            $book_noval = $AgencyReference->item(0)->nodeValue;
                        }
                        //	echo 				$statusval;		echo $BookingStatus;echo $Remarks;echo $Status;exit;


                        $PickupLocation_val_address = '';
                        $DestinationLocation_val_address = '';
                        $service_list = $dom->getElementsByTagName("ServiceList");
                        foreach ($service_list as $service_list_obj) {
                            $service = $service_list_obj->getElementsByTagName("Service");
                            foreach ($service as $service_obj) {
                                $shopping_cart_service_type = $service_obj->getAttribute("transferType");




                                $PickupLocation = $service_obj->getElementsByTagName("PickupLocation");
                                foreach ($PickupLocation as $PickupLocation_obj) {
                                    $Name = $PickupLocation_obj->getElementsByTagName("Name");
                                    $PickupLocation_val_address = $Name->item(0)->nodeValue;
                                }




                                $DestinationLocation = $service_obj->getElementsByTagName("DestinationLocation");
                                foreach ($DestinationLocation as $DestinationLocation_obj) {
                                    $Name = $DestinationLocation_obj->getElementsByTagName("Name");
                                    $DestinationLocation_val_address = $Name->item(0)->nodeValue;
                                }
                            }
                        }

                        $BookingStatus = 'CONFIRMED';
                        $Remarks = 'No remarks';
                        $Status = 'SUCCESS';

                        if ($statusval == '' || $bookingItemCodeval_final == '') {
                            $booking_status = 'Failed';
                            $bookingItemCodeval = 'XXXXXXXXXXX';
                            $statusval = 'Failed';
                            $book_noval = 'XXXXXXXXXXX';

                            $BookingStatus = 'FAILED';
                            $PurchaseConfirmCode = '';
                            $Status = 'Fail';
                        }

                        $bookingItemCodeval_final_f[] = $bookingItemCodeval_final . ' ( ' . $PickupLocation_val_address . ' To ' . $DestinationLocation_val_address . ' ) ';
                        $BookingStatus_f[] = $BookingStatus;
                        $book_noval_f[] = $book_noval;
                        $statusval_f[] = $bookingItemCodeval_final . ' ( ' . $statusval . ' ) ';
                        $cancellation_policy_F1[] = '<b>' . $PickupLocation_val_address . ' To ' . $DestinationLocation_val_address . '</b><br />' . $cancellation_policy_F;
                        $comp_pol_f[] = $PickupLocation_val_address . ' To ' . $DestinationLocation_val_address . '<br />' . $comp_pol;
                    }
                }
                $cancellation_policy_F2 = implode("<br /><br />", $cancellation_policy_F1);

                if (count(array_unique($BookingStatus_f)) == 1) {
                    $BookingStatus_f1 = $BookingStatus_f[0];
                } else {
                    $BookingStatus_f1 = 'PENDING';
                }
                $booking_res = array(
                    'PurchaseConfirmCode' => implode("<br />", $bookingItemCodeval_final_f),
                    'BookingStatus' => $BookingStatus_f1,
                    'CancellationPolicy' => $cancellation_policy_F2,
                    'cancel_markup' => $cancel_markup,
                    'total_price_con' => $cart_car_data->TotalPrice,
                    'parent_pnr_no' => $parent_pnr_f,
                    'parent_pnr_no1' => '',
                    'book_no' => implode("|", $book_noval_f),
                    'booking_info' => implode("<br /><br />", $comp_pol_f),
                    'Status' => implode("<br />", $statusval_f)
                );
                $booking_res_f = json_encode($booking_res);
                $TravelerDetails = json_encode($checkout_form);


                $car_request = json_decode(base64_decode($cart_car_data->request));
                $booking_transfer = array(
                    'parent_cart_id' => $cart_car_data->parent_cart_id,
                    'request' => $cart_car_data->request,
                    'response' => $cart_car_data->response,
                    'VehicleCreateReservationRes' => $cart_car_data->ServiceAddRS,
                    'Pickup' => $cart_car_data->Pickup,
                    'Dropoff' => $cart_car_data->Dropoff,
                    'pickupCityName' => $cart_car_data->pickupCityName,
                    'dropoffCityName' => $cart_car_data->dropoffCityName,
                    'DepartureTime' => $cart_car_data->DepartureTime,
                    'ReturnTime' => $cart_car_data->ReturnTime,
                    'Image' => $cart_car_data->Image,
                    'session_id' => $cart_car_data->session_id,
                    'TotalPrice' => $cart_car_data->TotalPrice,
                    'MyMarkup' => $cart_car_data->MyMarkup,
                    'AdminMarkup' => $cart_car_data->AdminMarkup,
                    'status' => '',
                    //'shurival' => $car_response->SPUI,
                    'adult' => $car_request->adult,
                    'child' => $car_request->child,
                    //'token' => $car_response->purchase_token,
                    'API_CURR' => $cart_car_data->API_CURR,
                    'TravelDate' => date("Y-m-d", strtotime($car_request->depart_date)),
                    'GUEST_FIRSTNAME' => $checkout_form['first_name'],
                    'GUEST_LASTNAME' => $checkout_form['last_name'],
                    'GUEST_EMAIL' => $checkout_form['email'],
                    'GUEST_PHONE' => $checkout_form['mobile'],
                    'BILLING_FIRSTNAME' => $checkout_form['first_name'],
                    'BILLING_LASTNAME' => $checkout_form['last_name'],
                    'BILLING_EMAIL' => $checkout_form['email'],
                    'BILLING_PHONE' => $checkout_form['mobile'],
                    'BILLING_COUNTRY' => $checkout_form['country'],
                    'BILLING_ADDRESS' => $checkout_form['street_address'] . ', ' . $checkout_form['address2'],
                    'BILLING_CITY' => $checkout_form['city'],
                    'BILLING_STATE' => $checkout_form['state'],
                    'BILLING_ZIP' => $checkout_form['zip'],
                    'USER_TYPE' => $user_type,
                    'USER_ID' => $user_id,
                    'TravelerDetails' => $TravelerDetails,
                    'booking_res' => $booking_res_f,
                    'TIMESTAMP' => date('Y-m-d H:i:s')
                );

                $booking_transfer_id = $this->transfer_model->insert_booking_transfer($booking_transfer);

                $pnr_no[] = $this->booking($booking_transfer_id, $parent_pnr, $module = 'TRANSFER');

                $this->transfer_model->clearCart($cid);
//echo '<pre/>';
//print_r($car_request);print_r($car_response);exit;
            }else if ($module == 'BUS') {
			                      
				$gloablid = $checkout_form['globalcid'];
			
				$formdata = base64_encode(json_encode($checkout_form));
				
				$sess_id = $this->session->userdata('session_id');
				
				$URL = WEB_URL . '/bus/block/' . $parent_pnr . '/' . $gloablid.'/'.$formdata;
                redirect($URL);
			}
        }

            if ($this->session->userdata('b2b_id')) {

            $user_type = 2;
            $user_id = $this->session->userdata('b2b_id');
            $this->booking($pnr_no);

            $data['status'] = 1;
            $data['voucher_url'] = WEB_URL . '/booking/confirm/' . base64_encode($parent_pnr);
            echo json_encode($data);
        } else {
            

            $Email = $checkout_form['email'];
            $OrderId = $pnr_no;

            $TOTAL = base64_encode(json_encode($TOTAL));
            $Email = base64_encode(json_encode($Email));
            $OrderId = base64_encode(json_encode($OrderId));
            $checkout_form1[] = $checkout_form['ADT_GivenName'][0];
            $checkout_form1[] = $checkout_form['ADT_Surname'][0];
            $checkout_form1[] = $checkout_form['street_address'];
            $checkout_form1[] = $checkout_form['mobile'];
            $checkout_form1[] = $checkout_form['country'];
            $checkout_form1[] = $checkout_form['city'];
            $checkout_form1[] = $checkout_form['state'];
            $checkout_form1[] = $checkout_form['zip'];
           // echo "<pre>";print_r($checkout_form1);exit;
            $checkoutDate = base64_encode(json_encode(implode(" ", $checkout_form1)));
			
            $GateURL = WEB_URL . '/booking/doPaymentGate/' . $TOTAL . '/' . $Email . '/' . $OrderId . '/' . $checkoutDate;
			
             if($option_payment_types_b2c=='ewallet')
            {     

              $this->booking_b2c_ewallet($pnr_no);
            $data['status'] = 1;
            $data['voucher_url'] = WEB_URL . '/booking/confirm/' . base64_encode($parent_pnr);
            echo json_encode($data);   

                 

             
             }
             else{ 
               
            $data['status'] = 555;
            $data['GateURL'] = $GateURL;
            
            echo json_encode($data);
            die;
            } 
            //redirect(WEB_URL.'/booking/doPaymentGate/'.$TOTAL.'/'.$Email.'/'.$OrderId);
            //$this->load->view('common/doPayment',$data);
        }


        # echo '<pre>';print_r($GateURL);die;
    }

    public function booking($booking_globalid) {



        $pnr_d = date("ymd");
        $pnr_d1 = date("His");
        $booking_id_mdule = substr($booking_globalid, 0, 4);
        $module = '';
        if ($booking_id_mdule == 'SWTF') {
            $module = 'FLIGHT';
        }
        
         if ($booking_id_mdule == 'SWTH') {
            $module = 'HOTEL';
        }
      if($booking_id_mdule == 'SWTB')
        {
			$module = 'BUS';
			
       }
        
        
        $parent_pnr = '';
        if ($module == 'FLIGHT') {
            $book_globaldata = $this->flight_model->getBookingFlightGLOBAL($booking_globalid)->row();
            $parent_pnr = $book_globaldata->parent_pnr;
            $RoundtripGlobalid = $this->flight_model->getBookingFlightGLOBALRound($parent_pnr)->num_rows();
            if ($RoundtripGlobalid == 2) {
                $bookRoundGlobaldata = $this->flight_model->getBookingFlightGLOBALRound($parent_pnr)->result();
                foreach ($bookRoundGlobaldata as $value) {


                    $count_g = $this->flight_model->getBookingFlightGLOBAL($booking_globalid)->num_rows();
                    if ($count_g == 1) {
                        $book_globaldata = $this->flight_model->getBookingFlightGLOBAL($booking_globalid)->row();
                        $booking_id = $book_globaldata->ref_id;
                        $parent_pnr = $book_globaldata->parent_pnr;
                        $bid = $book_globaldata->id;
                        $count = $this->flight_model->getBookingFlightTemp($booking_id)->num_rows();
                        if ($count == 1) {
                            $book_temp_data = $this->flight_model->getBookingFlightTemp($booking_id)->row();
                            $cart_flight_data = json_decode(base64_decode($book_temp_data->cart_flight_data));
                            $checkout_form = json_decode($book_temp_data->TravelerDetails);

                            $FareRequest = json_decode(base64_decode($book_temp_data->FareRequest));
                            $FareResponse = json_decode(base64_decode($book_temp_data->FareResponse));
                            $PassengerInfo = json_decode(base64_decode($book_temp_data->PassengerInfo));

                            $AirBookRQ_RS = AirFinalBookingQ($cart_flight_data, $checkout_form, $FareRequest, $FareResponse, $PassengerInfo);
                            $AirBookRS = $AirBookRQ_RS['AirBookRS'];

                            // $AirBookRS = new SimpleXMLElement($AirBookRS);
                            //  $AirBookRS = $AirBookRS->children('http://schemas.xmlsoap.org/soap/envelope/')->Body->children()->wmTravelBuildResponse->OTA_TravelItineraryRS;
                            $BookingStatus = 'HOLD';
                            $LocatorCode = '';
                            $Status = '';
                            $ProviderLocatorCode = '';
                            $SupplierLocatorCode = '';
                            $AirReservationLocatorCode = '';
                            $ProviderReservationInfoRef = '';
                            $BookingTravelerRef = '';
                            if (isset($AirBookRS->TicketResult->PNR)) {
                              //  $response = $AirBookRS->TravelItinerary;

                                $PNR = (string) $AirBookRS->TicketResult->PNR;
                                $BookingID = $AirBookRS->TicketResult->BookingId;
                                if (isset($response->ItineraryInfo->ReservationItems->ItemPricing)) {
                                    $TotalFare = $OriginalTotalFare = (string) $response->ItineraryInfo->ReservationItems->ItemPricing->AirFareInfo->ItinTotalFare->TotalFare['Amount'];
                                    $CurrencyCode = $OriginalTotalFareCurrencyCode = (string) $response->ItineraryInfo->ReservationItems->ItemPricing->AirFareInfo->ItinTotalFare->TotalFare['CurrencyCode'];
                                    $DecimalPlaces = $response->ItineraryInfo->ReservationItems->ItemPricing->AirFareInfo->ItinTotalFare->TotalFare['DecimalPlaces'];
                                    $b = substr($TotalFare, -$DecimalPlaces);
                                    $a = substr($TotalFare, 0, -$DecimalPlaces);
                                    $TotalFare = $a . '.' . $b;


                                    $aMarkup = $cart_flight_data->aMarkup;

                                    $MyMarkup = $this->account_model->get_my_markup(); //get agent markup
                                    $myMarkup = $MyMarkup['markup'];

                                    $TotalFare = $this->flight_model->currency_convertor($TotalFare, $CurrencyCode, CURR);
                                    $TMarkup = $this->account_model->PercentageAmount($TotalFare, $aMarkup);

                                    $TotalFare = $this->account_model->PercentageToAmount($TotalFare, $aMarkup);
                                    $Markup = $this->account_model->PercentageAmount($TotalFare, $myMarkup);
                                    $TOTAL = $this->account_model->PercentageToAmount($TotalFare, $myMarkup);
                                }

                                $LocatorCode = $PNR;
                                $Status = 'SUCCESS';
                                $BookingStatus = 'CONFIRMED';
                                $Remarks = 'No remarks';
                            } else {
                                $err = $AirBookRS->Errors->Error;
                                $ErrorInfo = $err;
                                $Remarks = (string) $ErrorInfo->Description;
                                $xml_log = array(
                                    'Api' => 'TripXML',
                                    'XML_Type' => 'Flight',
                                    'XML_Request' => $AirBookRQ_RS['AirBookRQ'],
                                    'XML_Response' => $AirBookRQ_RS['AirBookRS'],
                                    'Ip_address' => $this->input->ip_address(),
                                    'XML_Time' => date('Y-m-d H:i:s')
                                );
                                $this->xml_model->insert_xml_log($xml_log);
                                $Markup = 0;
                                $TMarkup = 0;
                            }

                            $booking_res = array(
                                'LocatorCode' => $LocatorCode,
                                'ProviderLocatorCode' => $ProviderLocatorCode,
                                'AirReservationLocatorCode' => $AirReservationLocatorCode,
                                'SupplierLocatorCode' => $SupplierLocatorCode,
                                'ProviderReservationInfoRef' => $ProviderReservationInfoRef,
                                'BookingTravelerRef' => $BookingTravelerRef,
                                'BookingStatus' => $BookingStatus,
                                'Status' => $Status,
                                'Remarks' => $Remarks
                            );
                            $booking_res = json_encode($booking_res);

                            $booking_flight = array(
                                'AirBookRQ' => base64_encode(json_encode($AirBookRQ_RS['AirBookRQ'])),
                                'AirBookRS' => base64_encode(json_encode($AirBookRQ_RS['AirBookRS'])),
                                //'TotalPrice' => $TOTAL,  //remove here
                                //  'MyMarkup' => $Markup,  //remove here
                                //  'AdminMarkup' => $TMarkup,  //remove here
                                'booking_res' => $booking_res  //remove here
                            );
                            $this->flight_model->Update_Booking_flight_data($booking_id, $booking_flight);

                            //$count = $this->flight_model->CheckDuplicateBooking($booking_id)->num_rows();
                            //if($count == 0){

                            $booking_res = json_decode($booking_res);
                            //echo '<pre>';print_r($booking_res);die;
                            if ($booking_res->BookingStatus == 'CONFIRMED') {
                                $bStatus = 'CONFIRMED';
                                $aStatus = 'CONFIRMED';
                                if ($this->session->userdata('b2b_id')) {
                                    $user_type = 2;
                                    $user_id = $this->session->userdata('b2b_id');
                                    $this->b2b_do_payment($booking_id, $module, $book_temp_data->TotalPrice, $book_temp_data->MyMarkup, $user_id);
                                }
                            } else {
                                $bStatus = 'FAILED';
                                $aStatus = 'FAILED';
                            }

                            $update_booking = array(
                                'booking_no' => $booking_res->LocatorCode,
                                'BookingID' => $BookingID,
                                'api_status' => $aStatus,
                                'booking_status' => $bStatus
                            );
                            $this->flight_model->Update_Booking_Global($bid, $update_booking, 'FLIGHT');
                            if ($booking_res->BookingStatus == 'CONFIRMED') {
                                if ($this->session->userdata('b2b_id')) {
                                    $user_type = 2;
                                    $user_id = $this->session->userdata('b2b_id');
                                    $this->do_payment_account($booking_globalid, $module, $book_temp_data->TotalPrice, $book_temp_data->MyMarkup, $user_id, $user_type);
                                } elseif ($this->session->userdata('b2c_id')) {
                                    $user_type = 3;
                                    $user_id = $this->session->userdata('b2c_id');
                                    $this->do_payment_account($booking_globalid, $module, $book_temp_data->TotalPrice, '0.00', $user_id, $user_type);
                                } else {
                                    $user_type = 4;
                                    $user_id = 0;
                                    $this->do_payment_account($booking_globalid, $module, $book_temp_data->TotalPrice, '0.00', $user_id, $user_type);
                                }
                            }
                            //Here We have to send mail
                            $this->flight_mail_voucher($booking_globalid);
                             $this->flight_sms_voucher($booking_globalid);

                            //}else{
                            //echo 'Invalid inf';
                            //}
                        } else {
                            echo 'Invalid Data';
                        }
                    } else {
                        echo 'Invalid Data';
                    }
                }
            } else {
                 $count_g = $this->flight_model->getBookingFlightGLOBAL($booking_globalid)->num_rows();
                if ($count_g == 1) {
                    $book_globaldata = $this->flight_model->getBookingFlightGLOBAL($booking_globalid)->row();
                    $booking_id = $book_globaldata->ref_id;
                    $parent_pnr = $book_globaldata->parent_pnr;
                    $bid = $book_globaldata->id;
                    $count = $this->flight_model->getBookingFlightTemp($booking_id)->num_rows();
                    if ($count == 1) {
                        $book_temp_data = $this->flight_model->getBookingFlightTemp($booking_id)->row();
                        $cart_flight_data = json_decode(base64_decode($book_temp_data->cart_flight_data));
                        $checkout_form = json_decode($book_temp_data->TravelerDetails);

                        $FareRequest = json_decode(base64_decode($book_temp_data->FareRequest));
                        $FareResponse = json_decode(base64_decode($book_temp_data->FareResponse));
                        $PassengerInfo = json_decode(base64_decode($book_temp_data->PassengerInfo));

                        $AirBookRQ_RS = AirFinalBookingQ($cart_flight_data, $checkout_form, $FareRequest, $FareResponse, $PassengerInfo);
                        $AirBookRS = $AirBookRQ_RS['AirBookRS'];

                        // $AirBookRS = new SimpleXMLElement($AirBookRS);
                        //  $AirBookRS = $AirBookRS->children('http://schemas.xmlsoap.org/soap/envelope/')->Body->children()->wmTravelBuildResponse->OTA_TravelItineraryRS;
                        $BookingStatus = 'HOLD';
                        $LocatorCode = '';
                        $Status = '';
                        $ProviderLocatorCode = '';
                        $SupplierLocatorCode = '';
                        $AirReservationLocatorCode = '';
                        $ProviderReservationInfoRef = '';
                        $BookingTravelerRef = '';
                        if (isset($AirBookRS->TicketResult->PNR)) {
                           // $response = $AirBookRS->TravelItinerary;

                            $PNR = (string) $AirBookRS->TicketResult->PNR;
                            $BookingID = $AirBookRS->TicketResult->BookingId;
                            if (isset($response->ItineraryInfo->ReservationItems->ItemPricing)) {
                                $TotalFare = $OriginalTotalFare = (string) $response->ItineraryInfo->ReservationItems->ItemPricing->AirFareInfo->ItinTotalFare->TotalFare['Amount'];
                                $CurrencyCode = $OriginalTotalFareCurrencyCode = (string) $response->ItineraryInfo->ReservationItems->ItemPricing->AirFareInfo->ItinTotalFare->TotalFare['CurrencyCode'];
                                $DecimalPlaces = $response->ItineraryInfo->ReservationItems->ItemPricing->AirFareInfo->ItinTotalFare->TotalFare['DecimalPlaces'];
                                $b = substr($TotalFare, -$DecimalPlaces);
                                $a = substr($TotalFare, 0, -$DecimalPlaces);
                                $TotalFare = $a . '.' . $b;


                                $aMarkup = $cart_flight_data->aMarkup;

                                $MyMarkup = $this->account_model->get_my_markup(); //get agent markup
                                $myMarkup = $MyMarkup['markup'];

                                $TotalFare = $this->flight_model->currency_convertor($TotalFare, $CurrencyCode, CURR);
                                $TMarkup = $this->account_model->PercentageAmount($TotalFare, $aMarkup);

                                $TotalFare = $this->account_model->PercentageToAmount($TotalFare, $aMarkup);
                                $Markup = $this->account_model->PercentageAmount($TotalFare, $myMarkup);
                                $TOTAL = $this->account_model->PercentageToAmount($TotalFare, $myMarkup);
                            }

                            $LocatorCode = $PNR;
                            $Status = 'SUCCESS';
                            $BookingStatus = 'CONFIRMED';
                            $Remarks = 'No remarks';
                        } else {
                            $err = $AirBookRS->Errors->Error;
                            $ErrorInfo = $err;
                            $Remarks = (string) $ErrorInfo->Description;
                            $xml_log = array(
                                'Api' => 'TripXML',
                                'XML_Type' => 'Flight',
                                'XML_Request' => $AirBookRQ_RS['AirBookRQ'],
                                'XML_Response' => $AirBookRQ_RS['AirBookRS'],
                                'Ip_address' => $this->input->ip_address(),
                                'XML_Time' => date('Y-m-d H:i:s')
                            );
                            $this->xml_model->insert_xml_log($xml_log);
                            $Markup = 0;
                            $TMarkup = 0;
                        }

                        $booking_res = array(
                            'LocatorCode' => $LocatorCode,
                            'ProviderLocatorCode' => $ProviderLocatorCode,
                            'AirReservationLocatorCode' => $AirReservationLocatorCode,
                            'SupplierLocatorCode' => $SupplierLocatorCode,
                            'ProviderReservationInfoRef' => $ProviderReservationInfoRef,
                            'BookingTravelerRef' => $BookingTravelerRef,
                            'BookingStatus' => $BookingStatus,
                            'Status' => $Status,
                            'Remarks' => $Remarks
                        );
                        $booking_res = json_encode($booking_res);

                        $booking_flight = array(
                            'AirBookRQ' => base64_encode(json_encode($AirBookRQ_RS['AirBookRQ'])),
                            'AirBookRS' => base64_encode(json_encode($AirBookRQ_RS['AirBookRS'])),
                            //'TotalPrice' => $TOTAL,  //remove here
                            //  'MyMarkup' => $Markup,  //remove here
                            //  'AdminMarkup' => $TMarkup,  //remove here
                            'booking_res' => $booking_res  //remove here
                        );
                        $this->flight_model->Update_Booking_flight_data($booking_id, $booking_flight);

                        //$count = $this->flight_model->CheckDuplicateBooking($booking_id)->num_rows();
                        //if($count == 0){

                        $booking_res = json_decode($booking_res);
                        //echo '<pre>';print_r($booking_res);die;
                        if ($booking_res->BookingStatus == 'CONFIRMED') {
                            $bStatus = 'CONFIRMED';
                            $aStatus = 'CONFIRMED';
                            if ($this->session->userdata('b2b_id')) {
                                $user_type = 2;
                                $user_id = $this->session->userdata('b2b_id');
                                $this->b2b_do_payment($booking_id, $module, $book_temp_data->TotalPrice, $book_temp_data->MyMarkup, $user_id);
                            }
                        } else {
                            $bStatus = 'FAILED';
                            $aStatus = 'FAILED';
                        }

                        $update_booking = array(
                            'booking_no' => $booking_res->LocatorCode,
                            'BookingID' => $BookingID,
                            'api_status' => $aStatus,
                            'booking_status' => $bStatus
                        );
                        $this->flight_model->Update_Booking_Global($bid, $update_booking, 'FLIGHT');
                        if ($booking_res->BookingStatus == 'CONFIRMED') {
                            if ($this->session->userdata('b2b_id')) {
                                $user_type = 2;
                                $user_id = $this->session->userdata('b2b_id');
                                $this->do_payment_account($booking_globalid, $module, $book_temp_data->TotalPrice, $book_temp_data->MyMarkup, $user_id, $user_type);
                            } elseif ($this->session->userdata('b2c_id')) {
                                $user_type = 3;
                                $user_id = $this->session->userdata('b2c_id');
                                $this->do_payment_account($booking_globalid, $module, $book_temp_data->TotalPrice, '0.00', $user_id, $user_type);
                            } else {
                                $user_type = 4;
                                $user_id = 0;
                                $this->do_payment_account($booking_globalid, $module, $book_temp_data->TotalPrice, '0.00', $user_id, $user_type);
                            }
                        }
                        //Here We have to send mail
                        $this->flight_mail_voucher($booking_globalid);
                         $this->hotel_sms_voucher($pnr_no);
                        

                        //}else{
                        //echo 'Invalid inf';
                        //}
                    } else {
                        echo 'Invalid Data';
                    }
                } else {
                    echo 'Invalid Data';
                }
            }
        }
        if ($module == 'HOTEL') {
        	$count = $this->hotel_model->getBookingHotelTemp($booking_id)->num_rows();
            if ($count == 1) {
                //$count = $this->flight_model->CheckDuplicateBooking($booking_id)->num_rows();
                //if($count == 0){
                $book_temp_data = $this->hotel_model->getBookingHotelTemp($booking_id)->row();
                $booking_res = json_decode($book_temp_data->booking_res);
                //echo '<pre>';print_r($booking_res);die;

                if ($booking_res->BookingStatus == 'CONFIRMED') {
                    //echo '<pre>';print_r($booking_res);die;

                    $bStatus = 'CONFIRMED';
                    $aStatus = 'CONFIRMED';
                    if ($this->session->userdata('b2b_id')) {
                        $user_type = 2;
                        $user_id = $this->session->userdata('b2b_id');
                        $this->b2b_do_payment($booking_id, $module, $book_temp_data->total_cost, $book_temp_data->MyMarkup, $user_id);
                    }
                } else {
                    $bStatus = 'FAILED';
                    $aStatus = 'FAILED';
                }
                $booking = array(
                    'module' => 'HOTEL',
                    'ref_id' => $booking_id,
                    'parent_pnr' => $parent_pnr,
                    'user_type' => $book_temp_data->USER_TYPE,
                    'amount' => $book_temp_data->total_cost,
                    'leadpax' => $book_temp_data->BILLING_FIRSTNAME . ' ' . $book_temp_data->BILLING_LASTNAME,
                    'user_id' => $book_temp_data->USER_ID,
                    'ip' => $this->input->ip_address(),
                    'api_status' => $aStatus,
                    'travel_date' => $book_temp_data->TravelDate,
                    'booking_status' => $bStatus
                );
                $bid = $this->hotel_model->Booking_Global($booking);
                $pnr_no = 'SWTH' . $pnr_d . $pnr_d1 . $bid;
                $update_booking = array(
                    'pnr_no' => $pnr_no,
                    'booking_no' => $booking_res->PurchaseConfirmCode
                );
                $this->hotel_model->Update_Booking_Global($bid, $update_booking, 'HOTEL');
                if ($booking_res->BookingStatus == 'CONFIRMED') {
                    if ($this->session->userdata('b2b_id')) {
                        $user_type = 2;
                        $user_id = $this->session->userdata('b2b_id');
                    } elseif ($this->session->userdata('b2c_id')) {
                        $user_type = 3;
                        $user_id = $this->session->userdata('b2c_id');
                    } else {
                        $user_type = 4;
                        $user_id = 0;
                    }
                    $this->do_payment_account($pnr_no, $module, $book_temp_data->total_cost, '0.00', $user_id, $user_type);
                }
                //Here We have to send mail
                $this->hotel_mail_voucher($pnr_no);
                return $pnr_no;
                //}else{
                //echo 'Invalid inf';
                //}
            } else {
                echo 'Invalid Data';
            }
        }

        if ($module == 'TRANSFER') {
            $count = $this->transfer_model->getBookingCarTemp($booking_id)->num_rows();
            if ($count == 1) {
                //$count = $this->flight_model->CheckDuplicateBooking($booking_id)->num_rows();
                //if($count == 0){
                $book_temp_data = $this->transfer_model->getBookingCarTemp($booking_id)->row();
                $booking_res = json_decode($book_temp_data->booking_res);
                //echo '<pre>';print_r($booking_res);die;
                if ($booking_res->BookingStatus == 'CONFIRMED') {
                    $bStatus = 'CONFIRMED';
                    $aStatus = 'CONFIRMED';
                    if ($this->session->userdata('b2b_id')) {
                        $user_type = 2;
                        $user_id = $this->session->userdata('b2b_id');
                        $this->b2b_do_payment($booking_id, $module, $book_temp_data->TotalPrice, $book_temp_data->MyMarkup, $user_id);
                    }
                } else {
                    $bStatus = 'FAILED';
                    $aStatus = 'FAILED';
                }
                $booking = array(
                    'module' => 'TRANSFER',
                    'ref_id' => $booking_id,
                    'parent_pnr' => $parent_pnr,
                    'user_type' => $book_temp_data->USER_TYPE,
                    'amount' => $book_temp_data->TotalPrice,
                    'leadpax' => $book_temp_data->BILLING_FIRSTNAME . ' ' . $book_temp_data->BILLING_LASTNAME,
                    'user_id' => $book_temp_data->USER_ID,
                    'ip' => $this->input->ip_address(),
                    'api_status' => $aStatus,
                    'travel_date' => '',
                    'booking_status' => $bStatus
                );
                $bid = $this->transfer_model->Booking_Global($booking);
                $pnr_no = 'SWTT' . $pnr_d . $pnr_d1 . $bid;
                $update_booking = array(
                    'pnr_no' => $pnr_no,
                    'booking_no' => $booking_res->book_no
                );
                $this->transfer_model->Update_Booking_Global($bid, $update_booking, 'TRANSFER');
                if ($booking_res->BookingStatus == 'CONFIRMED') {
                    if ($this->session->userdata('b2b_id')) {
                        $user_type = 2;
                        $user_id = $this->session->userdata('b2b_id');
                    } elseif ($this->session->userdata('b2c_id')) {
                        $user_type = 3;
                        $user_id = $this->session->userdata('b2c_id');
                    } else {
                        $user_type = 4;
                        $user_id = 0;
                    }
                    $this->do_payment_account($pnr_no, $module, $book_temp_data->TotalPrice, '0.00', $user_id, $user_type);
                }
                //Here We have to send mail
                // $this->car_mail_voucher($pnr_no);
                return $pnr_no;
                //}else{
                //echo 'Invalid inf';
                //}
            } else {
                echo 'Invalid Data';
            }
        }
        
        
        if ($module == 'BUS') {
			
       	/* Booking process ------------------------*/
		


		$sess_id = $this->session->userdata('session_id');
				#exit;
				
				$count = $this->bus_model->getBookingDetails($booking_globalid)->num_rows();
				if ($count == 1) {
				$cart_bus_data = $this->bus_model->getBookingDetails($booking_globalid)->row();
				
				#echo "<pre>";
				#print_r($cart_bus_data);
				#exit;
				
				$bus_book_data = base64_encode(json_encode($cart_bus_data));
			  	 $session_id = $this->session->userdata('session_id');
			  	 
			  	 	
				
				 $URL = WEB_URL . '/bus/book_bus/' . $bus_book_data . '/' . $session_id;
                redirect($URL);
				
			}
			else
			{
				echo 'Invalid Data';
			}
			   
        }
        
        
        

        if ($this->session->userdata('b2b_id')) {
            return $booking_globalid;
        } else {
            if ($parent_pnr != '') {
                redirect(WEB_URL . 'booking/confirm/' . base64_encode($parent_pnr), 'refresh');
            } else {

                echo 'INvalid';
            }
        }
    }

    public function book($booking_globalid) {

        //echo $booking_globalid;exit;

        $booking_id_mdule = substr($booking_globalid, 0, 4);
        $module = '';
        if ($booking_id_mdule == 'SWTF') {
            $module = 'FLIGHT';
        }
           if ($booking_id_mdule == 'SWTH') {
            $module = 'HOTEL';
        }
       if($booking_id_mdule == 'SWTB')
        {
			$module = 'BUS';
			
        }
        $parent_pnr = '';
        if ($module == 'FLIGHT') {
        	
            $book_globaldata = $this->flight_model->getBookingFlightGLOBAL($booking_globalid)->row();
            $parent_pnr = $book_globaldata->parent_pnr;
            $RoundtripGlobalid = $this->flight_model->getBookingFlightGLOBALRound($parent_pnr)->num_rows();


            if ($RoundtripGlobalid == 2) {
            	

                $bookRoundGlobaldata = $this->flight_model->getBookingFlightGLOBALRound($parent_pnr)->result();
                // echo '<pre>';print_r($bookRoundGlobaldata);exit;

                foreach ($bookRoundGlobaldata as $value) {


                    $count_g = $this->flight_model->getBookingFlightGLOBAL($booking_globalid)->num_rows();




                    if ($count_g == 1) {
                        $book_globaldata = $this->flight_model->getBookingFlightGLOBAL($value->pnr_no)->row();

                        $booking_id = $book_globaldata->ref_id;
                        $parent_pnr = $book_globaldata->parent_pnr;
                        $bid = $book_globaldata->id;
                        $count = $this->flight_model->getBookingFlightTemp($value->ref_id)->num_rows();

                        if ($count == 1) {
                            $book_temp_data = $this->flight_model->getBookingFlightTemp($value->ref_id)->row();

                            $cart_flight_data = json_decode(base64_decode($book_temp_data->cart_flight_data));
                            $checkout_form = json_decode($book_temp_data->TravelerDetails);

                            $FareRequest = json_decode(base64_decode($book_temp_data->FareRequest));
                            $FareResponse = json_decode(base64_decode($book_temp_data->FareResponse));
                            $PassengerInfo = json_decode(base64_decode($book_temp_data->PassengerInfo));

                            $AirBookRQ_RS = AirFinalBookingQ($cart_flight_data, $checkout_form, $FareRequest, $FareResponse, $PassengerInfo);


                            $BookingStatus = 'HOLD';
                            $LocatorCode = '';
                            $Status = '';
                            $ProviderLocatorCode = '';
                            $SupplierLocatorCode = '';
                            $AirReservationLocatorCode = '';
                            $ProviderReservationInfoRef = '';
                            $BookingTravelerRef = '';
                            $AirBookRS = $AirBookRQ_RS['AirBookRS'];


                            // echo '<pre>';print_r($AirBookRS);exit;
                            if (isset($AirBookRS->TicketResult->PNR)) {
                                // $response = $AirBookRS->TravelItinerary;
                                //echo '<pre>';print_r($response);die;
                                $PNR = (string) $AirBookRS->TicketResult->PNR;
                                if (isset($response->ItineraryInfo->ReservationItems->ItemPricing)) {
                                    $TotalFare = $OriginalTotalFare = (string) $response->ItineraryInfo->ReservationItems->ItemPricing->AirFareInfo->ItinTotalFare->TotalFare['Amount'];
                                    $CurrencyCode = $OriginalTotalFareCurrencyCode = (string) $response->ItineraryInfo->ReservationItems->ItemPricing->AirFareInfo->ItinTotalFare->TotalFare['CurrencyCode'];
                                    $DecimalPlaces = $response->ItineraryInfo->ReservationItems->ItemPricing->AirFareInfo->ItinTotalFare->TotalFare['DecimalPlaces'];
                                    $b = substr($TotalFare, -$DecimalPlaces);
                                    $a = substr($TotalFare, 0, -$DecimalPlaces);
                                    $TotalFare = $a . '.' . $b;


                                    $aMarkup = $cart_flight_data->aMarkup;

                                    $MyMarkup = $this->account_model->get_my_markup(); //get agent markup
                                    $myMarkup = $MyMarkup['markup'];

                                    $TotalFare = $this->flight_model->currency_convertor($TotalFare, $CurrencyCode, CURR);
                                    $TMarkup = $this->account_model->PercentageAmount($TotalFare, $aMarkup);

                                    $TotalFare = $this->account_model->PercentageToAmount($TotalFare, $aMarkup);
                                    $Markup = $this->account_model->PercentageAmount($TotalFare, $myMarkup);
                                    $TOTAL = $this->account_model->PercentageToAmount($TotalFare, $myMarkup);
                                }
                                $LocatorCode = $PNR;
                                $Status = 'SUCCESS';
                                $BookingStatus = 'CONFIRMED';
                                $Remarks = 'No remarks';
                            } else {
                                $err = $AirBookRS->Errors->Error;
                                $ErrorInfo = $err;
                                $Remarks = (string) $ErrorInfo->Description;
                                $xml_log = array(
                                    'Api' => 'TBO-F',
                                    'XML_Type' => 'Flight',
                                    'XML_Request' => base64_encode(json_encode($AirBookRQ_RS['AirBookRQ'])),
                                    'XML_Response' => base64_encode(json_encode($AirBookRQ_RS['AirBookRS'])),
                                    'Ip_address' => $this->input->ip_address(),
                                    'XML_Time' => date('Y-m-d H:i:s')
                                );
                               
                                $this->xml_model->insert_xml_log($xml_log);
                                $Markup = 0;
                                $TMarkup = 0;
                            }

                            $booking_res = array(
                                'LocatorCode' => $LocatorCode,
                                'ProviderLocatorCode' => $ProviderLocatorCode,
                                'AirReservationLocatorCode' => $AirReservationLocatorCode,
                                'SupplierLocatorCode' => $SupplierLocatorCode,
                                'ProviderReservationInfoRef' => $ProviderReservationInfoRef,
                                'BookingTravelerRef' => $BookingTravelerRef,
                                'BookingStatus' => $BookingStatus,
                                'Status' => $Status,
                                'Remarks' => $Remarks
                            );
                            $booking_res = base64_encode(json_encode($booking_res));

                            $booking_flight = array(
                                'AirBookRQ' => base64_encode(json_encode($AirBookRQ_RS['AirBookRQ'])),
                                'AirBookRS' => base64_encode(json_encode($AirBookRQ_RS['AirBookRS'])),
                                //'TotalPrice' => $TOTAL,  //remove here
                                //  'MyMarkup' => $Markup,  //remove here
                                //  'AdminMarkup' => $TMarkup,  //remove here
                                'booking_res' => $booking_res  //remove here
                            );

                            $this->flight_model->Update_Booking_flight_data($booking_id, $booking_flight);



                            $booking_res = json_decode(base64_decode($booking_res));

                            if ($booking_res->BookingStatus == 'CONFIRMED') {
                                $bStatus = 'CONFIRMED';
                                $aStatus = 'CONFIRMED';
                                if ($this->session->userdata('b2b_id')) {
                                    $user_type = 2;
                                    $user_id = $this->session->userdata('b2b_id');
                                    $this->b2b_do_payment($booking_id, $module, $book_temp_data->TotalPrice, $book_temp_data->MyMarkup, $user_id);
                                }
                            } else {
                                $bStatus = 'FAILED';
                                $aStatus = 'FAILED';
                            }

                            $update_booking = array(
                                'booking_no' => $booking_res->LocatorCode,
                                'api_status' => $aStatus,
                                'booking_status' => $bStatus
                            );
                            $this->flight_model->Update_Booking_Global($bid, $update_booking, 'FLIGHT');
                        } else {
                            echo 'Invalid Data';
                        }
                    } else {
                        echo 'Invalid Data';
                    }
                }
            } else {
            	//echo $booking_globalid;exit;
                $count_g = $this->flight_model->getBookingFlightGLOBAL($booking_globalid)->num_rows();
                if ($count_g == 1) {
                	$book_globaldata = $this->flight_model->getBookingFlightGLOBAL($booking_globalid)->row();
                    $booking_id = $book_globaldata->ref_id;
                    $parent_pnr = $book_globaldata->parent_pnr;
                    $bid = $book_globaldata->id;
                    $count = $this->flight_model->getBookingFlightTemp($booking_id)->num_rows();
                    if ($count == 1) {
						$book_temp_data = $this->flight_model->getBookingFlightTemp($booking_id)->row();
                        $cart_flight_data = json_decode(base64_decode($book_temp_data->cart_flight_data));
                        $checkout_form = json_decode($book_temp_data->TravelerDetails);

                        $FareRequest = json_decode(base64_decode($book_temp_data->FareRequest));
                        $FareResponse = json_decode(base64_decode($book_temp_data->FareResponse));
                        $PassengerInfo = json_decode(base64_decode($book_temp_data->PassengerInfo));

                        $AirBookRQ_RS = AirFinalBookingQ($cart_flight_data, $checkout_form, $FareRequest, $FareResponse, $PassengerInfo);
                        //  $AirBookRS = $AirBookRQ_RS->AirNonLaccTicket->AirNonLaccTicketRS;
                        //   $AirBookRS = new SimpleXMLElement($AirBookRS);
                        //   $AirBookRS = $AirBookRS->children('http://schemas.xmlsoap.org/soap/envelope/')->Body->children()->wmTravelBuildResponse->OTA_TravelItineraryRS;

                        $BookingStatus = 'HOLD';
                        $LocatorCode = '';
                        $Status = '';
                        $ProviderLocatorCode = '';
                        $SupplierLocatorCode = '';
                        $AirReservationLocatorCode = '';
                        $ProviderReservationInfoRef = '';
                        $BookingTravelerRef = '';
                        $AirBookRS = $AirBookRQ_RS['AirBookRS'];


                        // echo '<pre>';print_r($AirBookRS);exit;
                        if (isset($AirBookRS->TicketResult->PNR)) {
                        	// $response = $AirBookRS->TravelItinerary;
                            //echo '<pre>';print_r($response);die;
                            $PNR = (string) $AirBookRS->TicketResult->PNR;
                            $BookingID = $AirBookRS->TicketResult->BookingId;
                            if (isset($response->ItineraryInfo->ReservationItems->ItemPricing)) {
                                $TotalFare = $OriginalTotalFare = (string) $response->ItineraryInfo->ReservationItems->ItemPricing->AirFareInfo->ItinTotalFare->TotalFare['Amount'];
                                $CurrencyCode = $OriginalTotalFareCurrencyCode = (string) $response->ItineraryInfo->ReservationItems->ItemPricing->AirFareInfo->ItinTotalFare->TotalFare['CurrencyCode'];
                                $DecimalPlaces = $response->ItineraryInfo->ReservationItems->ItemPricing->AirFareInfo->ItinTotalFare->TotalFare['DecimalPlaces'];
                                $b = substr($TotalFare, -$DecimalPlaces);
                                $a = substr($TotalFare, 0, -$DecimalPlaces);
                                $TotalFare = $a . '.' . $b;


                                $aMarkup = $cart_flight_data->aMarkup;

                                $MyMarkup = $this->account_model->get_my_markup(); //get agent markup
                                $myMarkup = $MyMarkup['markup'];

                                $TotalFare = $this->flight_model->currency_convertor($TotalFare, $CurrencyCode, CURR);
                                $TMarkup = $this->account_model->PercentageAmount($TotalFare, $aMarkup);

                                $TotalFare = $this->account_model->PercentageToAmount($TotalFare, $aMarkup);
                                $Markup = $this->account_model->PercentageAmount($TotalFare, $myMarkup);
                                $TOTAL = $this->account_model->PercentageToAmount($TotalFare, $myMarkup);
                            }
                            $LocatorCode = $PNR;
                            $Status = 'SUCCESS';
                            $BookingStatus = 'CONFIRMED';
                            $Remarks = 'No remarks';
                        } else {
                        	$err = $AirBookRS->Errors->Error;
                            $ErrorInfo = $err;
                            $Remarks = (string) $ErrorInfo->Description;
                            $xml_log = array(
                                'Api' => 'TripXML',
                                'XML_Type' => 'Flight',
                                'XML_Request' => base64_encode(json_encode($AirBookRQ_RS['AirBookRQ'])),
                                'XML_Response' => base64_encode(json_encode($AirBookRQ_RS['AirBookRS'])),
                                'Ip_address' => $this->input->ip_address(),
                                'XML_Time' => date('Y-m-d H:i:s')
                            );
                            
                            $this->xml_model->insert_xml_log($xml_log);
                            $Markup = 0;
                            $TMarkup = 0;
                        }

                        $booking_res = array(
                            'LocatorCode' => $LocatorCode,
                            'ProviderLocatorCode' => $ProviderLocatorCode,
                            'AirReservationLocatorCode' => $AirReservationLocatorCode,
                            'SupplierLocatorCode' => $SupplierLocatorCode,
                            'ProviderReservationInfoRef' => $ProviderReservationInfoRef,
                            'BookingTravelerRef' => $BookingTravelerRef,
                            'BookingStatus' => $BookingStatus,
                            'Status' => $Status,
                            'Remarks' => $Remarks
                        );
                        $booking_res = base64_encode(json_encode($booking_res));

                        $booking_flight = array(
                            'AirBookRQ' => base64_encode(json_encode($AirBookRQ_RS['AirBookRQ'])),
                            'AirBookRS' => base64_encode(json_encode($AirBookRQ_RS['AirBookRS'])),
                            //'TotalPrice' => $TOTAL,  //remove here
                            //  'MyMarkup' => $Markup,  //remove here
                            //  'AdminMarkup' => $TMarkup,  //remove here
                            'booking_res' => $booking_res  //remove here
                        );
                        $this->flight_model->Update_Booking_flight_data($booking_id, $booking_flight);



                        $booking_res = json_decode(base64_decode($booking_res));

                        if ($booking_res->BookingStatus == 'CONFIRMED') {
                            $bStatus = 'CONFIRMED';
                            $aStatus = 'CONFIRMED';
                            if ($this->session->userdata('b2b_id')) {
                                $user_type = 2;
                                $user_id = $this->session->userdata('b2b_id');
                                $this->b2b_do_payment($booking_id, $module, $book_temp_data->TotalPrice, $book_temp_data->MyMarkup, $user_id);
                            }
                        } else {
                            $bStatus = 'FAILED';
                            $aStatus = 'FAILED';
                        }

                        $update_booking = array(
                            'booking_no' => $booking_res->LocatorCode,
                            'BookingID' => $BookingID,
                            'api_status' => $aStatus,
                            'booking_status' => $bStatus
                        );
                        $this->flight_model->Update_Booking_Global($bid, $update_booking, 'FLIGHT');


                        if ($booking_res->BookingStatus == 'CONFIRMED') {
                            if ($this->session->userdata('b2b_id')) {
                                $user_type = 2;
                                $user_id = $this->session->userdata('b2b_id');
                            } elseif ($this->session->userdata('b2c_id')) {
                                $user_type = 3;
                                $user_id = $this->session->userdata('b2c_id');
                            } else {
                                $user_type = 4;
                                $user_id = 0;
                            }
                            $this->do_payment_account($booking_globalid, $module, $book_temp_data->TotalPrice, '0.00', $user_id, $user_type);
                        }
                        //Here We have to send mail
                        $this->flight_mail_voucher($booking_globalid);
                        $this->flight_sms_voucher($booking_globalid);

                        //}else{
                        //echo 'Invalid inf';
                        //}
                    } else {
                        echo 'Invalid Data';
                    }
                } else {
                    echo 'Invalid Data';
                }
            }
        }
        if ($module == 'HOTEL') {

            /* Booking process ------------------------ */

            //echo $module;exit;
			$pnr_no = $booking_globalid;
            $sess_id = $this->session->userdata('session_id');
            $this->db->where('pnr_no',$pnr_no);
            $CRS_DATA=$this->db->get('booking_global')->result_array();
           // echo "<pre>";print_r($CRS_DATA);exit;
            $booking_id = $CRS_DATA[0]['id'];
            $parent_pnr=$CRS_DATA[0]['parent_pnr'];;
  			if(count($CRS_DATA) == 1) {
	  	//echo "<pre>";print_r($_SESSION);echo "<br>";print_r($this->session->all_userdata());exit;
	  	
         $HOTEL_STATUS = 'SUCCESS';
		 $inramount = $CRS_DATA[0]['amount'];
         $ref = 'SMJHLHP'.rand(000000,999999);
         $fialtrans_bookinfo = array( 
		'booking_number'=> 'CRS-'.$CRS_DATA[0]['parent_pnr'],
		'prn_no' => $CRS_DATA[0]['pnr_no'],
		'status'=> $HOTEL_STATUS,
		'ref_supplier'=> $ref,
		'cancellation_till_date' => '',
		'supplier_name' => '',
		'supplier_vatNumber' => '',
		'contract_list_comment' => '',
		'payment_status' => ''   
	    );
		$this->db->where('transaction_details_id', $CRS_DATA[0]['parent_pnr']);
		$this->db->update('booking_transaction_details', $fialtrans_bookinfo); 
		 
		$fialtrans_globe_bookinfo = array( 
							'booking_no'=> 'CRS-'.$CRS_DATA[0]['parent_pnr'],
							'pnr_no' => $pnr_no,
							'booking_status'=> $HOTEL_STATUS,
							'payment_status' => 1,
							'payment_method'=>$_SESSION['PGTYPE']							   
						);
		 $this->db->where('id', $trans_globe_book_id);
		 $this->db->update('booking_global', $fialtrans_globe_bookinfo); 
		
		 
		} else {
			$HOTEL_STATUS = 'FAILED';
		}
            
            $count = $this->hotel_model->getBookingHotelTemp($booking_id)->num_rows();

            if ($count >= 1) {
                //$count = $this->flight_model->CheckDuplicateBooking($booking_id)->num_rows();
                //if($count == 0){
                $book_temp_data = $this->hotel_model->getBookingHotelTemp($booking_id)->row();


                $booking_res = json_decode($booking_resenc);
                # echo '<pre>';print_r($booking_res);die;

                if ($booking_res->HotelBookingStatus == 'Vouchered') {
                    #echo '<pre>';print_r($booking_res);die;

                    $bStatus = 'CONFIRMED';
                    $aStatus = 'CONFIRMED';
                    if ($this->session->userdata('b2b_id')) {
                        $user_type = 2;
                        $user_id = $this->session->userdata('b2b_id');
                        $this->b2b_do_payment($booking_id, $module, $book_temp_data->total_cost, $book_temp_data->MyMarkup, $user_id);
                    }
                } else {
                    $bStatus = 'FAILED';
                    $aStatus = 'FAILED';
                }

				/*
                if ($booking_res->HotelBookingStatus == 'Vouchered') {
                    if ($this->session->userdata('b2b_id')) {
                        $user_type = 2;
                        $user_id = $this->session->userdata('b2b_id');
                    } elseif ($this->session->userdata('b2c_id')) {
                        $user_type = 3;
                        $user_id = $this->session->userdata('b2c_id');
                    } else {
                        $user_type = 4;
                        $user_id = 0;
                    }
                    $this->do_payment_account($pnr_no, $module, $book_temp_data->total_cost, '0.00', $user_id, $user_type);
                }
                */
                //Here We have to send mail
                $this->hotel_mail_voucher($pnr_no);
                $this->hotel_sms_voucher($pnr_no);
               
                
            } else {
                echo 'Invalid Data';
            }
        }


		/*  bus booking */
        
      
  if ($module == 'BUS') {
			
       	/* Booking process ------------------------*/
		


		$sess_id = $this->session->userdata('session_id');
				#exit;
				
				$count = $this->bus_model->getBookingDetails($booking_globalid)->num_rows();
				if ($count == 1) {
				$cart_bus_data = $this->bus_model->getBookingDetails($booking_globalid)->row();
				
				#echo "<pre>";
				#print_r($cart_bus_data);
				#exit;
				
				$bus_book_data = base64_encode(json_encode($cart_bus_data));
			  	 $session_id = $this->session->userdata('session_id');
			  	 
			  	 	
				
				 $URL = WEB_URL . '/bus/book_bus/' . $bus_book_data . '/' . $session_id;
                redirect($URL);
				
			}
			else
			{
				echo 'Invalid Data';
			}
			   
        }
        
               
        
        /* bus booking ends */


        if ($module == 'TRANSFER') {
            $count = $this->transfer_model->getBookingCarTemp($booking_id)->num_rows();
            if ($count == 1) {
                //$count = $this->flight_model->CheckDuplicateBooking($booking_id)->num_rows();
                //if($count == 0){
                $book_temp_data = $this->transfer_model->getBookingCarTemp($booking_id)->row();
                $booking_res = json_decode($book_temp_data->booking_res);
                //echo '<pre>';print_r($booking_res);die;
                if ($booking_res->BookingStatus == 'CONFIRMED') {
                    $bStatus = 'CONFIRMED';
                    $aStatus = 'CONFIRMED';
                    if ($this->session->userdata('b2b_id')) {
                        $user_type = 2;
                        $user_id = $this->session->userdata('b2b_id');
                        $this->b2b_do_payment($booking_id, $module, $book_temp_data->TotalPrice, $book_temp_data->MyMarkup, $user_id);
                    }
                } else {
                    $bStatus = 'FAILED';
                    $aStatus = 'FAILED';
                }
                $booking = array(
                    'module' => 'TRANSFER',
                    'ref_id' => $booking_id,
                    'parent_pnr' => $parent_pnr,
                    'user_type' => $book_temp_data->USER_TYPE,
                    'amount' => $book_temp_data->TotalPrice,
                    'leadpax' => $book_temp_data->BILLING_FIRSTNAME . ' ' . $book_temp_data->BILLING_LASTNAME,
                    'user_id' => $book_temp_data->USER_ID,
                    'ip' => $this->input->ip_address(),
                    'api_status' => $aStatus,
                    'travel_date' => '',
                    'booking_status' => $bStatus
                );
                $bid = $this->transfer_model->Booking_Global($booking);
                $pnr_no = 'SWTT' . $pnr_d . $pnr_d1 . $bid;
                $update_booking = array(
                    'pnr_no' => $pnr_no,
                    'booking_no' => $booking_res->book_no
                );
                $this->transfer_model->Update_Booking_Global($bid, $update_booking, 'TRANSFER');
                if ($booking_res->BookingStatus == 'CONFIRMED') {
                    if ($this->session->userdata('b2b_id')) {
                        $user_type = 2;
                        $user_id = $this->session->userdata('b2b_id');
                    } elseif ($this->session->userdata('b2c_id')) {
                        $user_type = 3;
                        $user_id = $this->session->userdata('b2c_id');
                    } else {
                        $user_type = 4;
                        $user_id = 0;
                    }
                    $this->do_payment_account($pnr_no, $module, $book_temp_data->TotalPrice, '0.00', $user_id, $user_type);
                }
                //Here We have to send mail
                // $this->car_mail_voucher($pnr_no);
                return $pnr_no;
                //}else{
                //echo 'Invalid inf';
                //}
            } else {
                echo 'Invalid Data';
            }
        }

        if ($this->session->userdata('b2b_id')) {
            return $booking_globalid;
        } else {
            if ($parent_pnr != '') {
                redirect(WEB_URL . '/booking/confirm/' . base64_encode($parent_pnr), 'refresh');
            } else {
                echo 'INvalid';
            }
        }
    }

    public function confirm($parent_pnr = '') {
        if (!empty($parent_pnr)) {
            $parent_pnr = base64_decode($parent_pnr);
            $count = $this->booking_model->getBookingByParentPnr($parent_pnr)->num_rows();
            if ($count > 0) {
                $data['pnr_nos'] = $this->booking_model->getBookingByParentPnr($parent_pnr)->result();
				//echo '<pre>';print_r($data);die;
                $this->load->view('common/voucher', $data);
            } else {
                echo 'Invalid Data';
            }
        } else {
            echo 'Invalid Data';
        }
    }

    public function hotel_mail_voucher($pnr_no) {
        $count = $this->booking_model->getBookingPnr($pnr_no)->num_rows();
        if ($count == 1) {
            $b_data = $this->booking_model->getBookingPnr($pnr_no)->row();
            if ($b_data->module == 'HOTEL') {
                $data['Booking'] = $booking = $this->booking_model->getBookingbyPnr($b_data->pnr_no, $b_data->module)->row();

                $request = json_decode(base64_decode($booking->request));
                $checkin_day_month = date('D, M', strtotime($request->check_in));
                $checkin_date = $cin = date('d', strtotime($request->check_in));
                $checkout_day_month = date('D, M', strtotime($request->check_out));
                $checkout_date = $cout = date('d', strtotime($request->check_out));

                $checkin_date = strtotime($request->check_in);
                $checkout_date = strtotime($request->check_out);

                $absDateDiff = abs($checkout_date - $checkin_date);
                $number_of_nights = floor($absDateDiff / (60 * 60 * 24));


                $getHotelTemplateRow = $this->email_model->get_email_template('HOTEL_BOOKING_VOUCHER')->row();
                $getHotelTemplate = $getHotelTemplateRow->message;
                $getHotelTemplate = str_replace("{%%FIRSTNAME%%}", $booking->GUEST_FIRSTNAME, $getHotelTemplate);
                $getHotelTemplate = str_replace("{%%WEB_URL%%}", WEB_URL, $getHotelTemplate);
                $getHotelTemplate = str_replace("{%%BOOKING_STATUS%%}", $booking->booking_status, $getHotelTemplate);
                $getHotelTemplate = str_replace("{%%CONFIRMATION_NO%%}", $booking->pnr_no, $getHotelTemplate);
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
                    'facebook_social_url' => 'https://www.facebook.com/skywalker',
                    'twitter_social_url' => 'https://twitter.com/skywalker/',
                    'google_social_url' => 'https://plus.google.com/',
                );
                $data['booking_status'] = $booking->booking_status;
                $Response = $this->email_model->sendmail_hotelVoucher($data);
                $response = array('status' => 1);
                //echo json_encode($response);
            }
        } else {
            $response = array('status' => 0);
            echo json_encode($response);
        }
    }

    public function hotel_sms_voucher($pnr_no) {
        $count = $this->booking_model->getBookingPnr($pnr_no)->num_rows();
        if ($count == 1) {
            $b_data = $this->booking_model->getBookingPnr($pnr_no)->row();
            if ($b_data->module == 'HOTEL') {
                $data['Booking'] = $booking = $this->booking_model->getBookingbyPnr($b_data->pnr_no, $b_data->module)->row();

                $request = json_decode(base64_decode($booking->request));
                $checkin_day_month = date('D, M', strtotime($request->check_in));
                $checkin_date = $cin = date('d', strtotime($request->check_in));
                $checkout_day_month = date('D, M', strtotime($request->check_out));
                $checkout_date = $cout = date('d', strtotime($request->check_out));

                $checkin_date = strtotime($request->check_in);
                $checkout_date = strtotime($request->check_out);

                $absDateDiff = abs($checkout_date - $checkin_date);
                $number_of_nights = floor($absDateDiff / (60 * 60 * 24));

                $customer_phone = $booking->BILLING_PHONE;


                $sms.= 'Dear' . $booking->GUEST_FIRSTNAME;
                $sms.='Your Booking at' . $booking->hotel_name . 'Has Been' . $booking->booking_status;
                $sms.='Your Booking Id is' . $booking->pnr_no;
                $sms.='Room type is ' . $booking->room_type . 'and Number of persons:' . $booking->adult;
                $sms.='check in date ' . $cin . 'check out date' . $cout;
                $sms.='Have a Pleasant Stay';

                $sendSMS = "setmyjourney.com-Ticket:" . $sms . "Customer Care No. 08288888555";

                $sms_customer = "http://api.smscountry.com/SMSCwebservice_bulk.aspx?User=mantraholidays&passwd=neetumantra696&mobilenumber=$customer_phone&message=$sendSMS&sid=ZCPIND&mtype=N&DR=Y";

                $Response = $this->curl_get_file_contents($sms_customer);
                $response = array('status' => 1);
                //echo json_encode($response);
            }
        } else {
            $response = array('status' => 0);
            echo json_encode($response);
        }
    }

    public function flight_mail_voucher($pnr_no) {
        $count = $this->booking_model->getBookingPnr($pnr_no)->num_rows();
        if ($count == 1) {
            $b_data = $this->booking_model->getBookingPnr($pnr_no)->row();
            if ($b_data->module == 'FLIGHT') {
                $data['Booking'] = $booking = $this->booking_model->getBookingbyPnr($b_data->pnr_no, $b_data->module)->row();
                $data['global'] = $this->booking_model->getBookingPnr($pnr_no)->row();
                $data['message'] = $this->load->view('flight/mail_voucher', $data, TRUE);
                $data['to'] = $booking->BILLING_EMAIL;
                $data['booking_status'] = $booking->booking_status;
                $data['email_access'] = $this->email_model->get_email_acess()->row();
                $email_type = 'HOTEL_BOOKING_VOUCHER';
                $data['email_template'] = $this->email_model->get_email_template($email_type)->row();
                $data['social_url'] = array(
                    'facebook_social_url' => 'https://www.facebook.com/pages/Skywalker-Travels-TOURS/1458214957735422',
                    'twitter_social_url' => 'https://twitter.com/swtravelsntours',
                    'google_social_url' => 'https://plus.google.com/u/0/113071008947537095277/posts',
                );
                $Response = $this->email_model->sendmail_flightVoucher($data);
                $response = array('status' => 1);
                //echo json_encode($response);
            }
        } else {
            $response = array('status' => 0);
            echo json_encode($response);
        }
    }

    public function flight_sms_voucher($pnr_no) {
        $count = $this->booking_model->getBookingPnr($pnr_no)->num_rows();
        if ($count == 1) {
            $b_data = $this->booking_model->getBookingPnr($pnr_no)->row();
            if ($b_data->module == 'FLIGHT') {
                $data['Booking'] = $booking = $this->booking_model->getBookingbyPnr($b_data->pnr_no, $b_data->module)->row();
                $data['global'] = $this->booking_model->getBookingPnr($pnr_no)->row();
                $data['message'] = $this->load->view('flight/mail_voucher', $data, TRUE);
                $data['to'] = $booking->BILLING_EMAIL;
                $customer_phone = $booking->BILLING_PHONE;



                $sms.= 'Dear' . $booking->GUEST_FIRSTNAME;
                $sms.='Your Flight Booking is' . $booking->booking_status;
                $sms.='Your Booking Id is' . $booking->pnr_no;
                $sms.='Travel From' . $booking->fromCityName . 'To' . $booking->toCityName;
                $sms.='Date' . $TravelDate;
                $sms.='Have a Pleasant Journey';

                $sendSMS = "setmyjourney.com-Ticket:" . $sms . "Customer Care No. 08288888555";

                $sms_customer = "http://api.smscountry.com/SMSCwebservice_bulk.aspx?User=mantraholidays&passwd=neetumantra696&mobilenumber=$customer_phone&message=$sendSMS&sid=ZCPIND&mtype=N&DR=Y";

                $Response = $this->curl_get_file_contents($sms_customer);
                $response = array('status' => 1);
                //echo json_encode($response);
            }
        } else {
            $response = array('status' => 0);
            echo json_encode($response);
        }
    }

    public function curl_get_file_contents($url) {
        $url = str_replace(' ', '%20', $url);
        $c = curl_init();
        curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($c, CURLOPT_URL, $url);
        $contents = curl_exec($c);
        curl_close($c);
        if ($contents)
            return $contents;
        else
            return FALSE;
    }

    public function car_mail_voucher($pnr_no) {
        $count = $this->booking_model->getBookingPnr($pnr_no)->num_rows();
        if ($count == 1) {
            $b_data = $this->booking_model->getBookingPnr($pnr_no)->row();
            if ($b_data->module == 'CAR') {
                $data['Booking'] = $booking = $this->booking_model->getBookingbyPnr($b_data->pnr_no, $b_data->module)->row();
                $data['global'] = $this->booking_model->getBookingPnr($pnr_no)->row();
                $data['message'] = $this->load->view('car/mail_voucher', $data, TRUE);
                $data['to'] = $booking->BILLING_EMAIL;
                $data['email_access'] = $this->email_model->get_email_acess()->row();
                $email_type = 'ApartmentVoucher';
                $data['email_template'] = $this->email_model->get_email_template($email_type)->row();
                $data['booking_status'] = strtolower($booking->booking_status);
                $data['social_url'] = array(
                    'facebook_social_url' => 'https://www.facebook.com/skywalker',
                    'twitter_social_url' => 'https://twitter.com/skywalker/',
                    'google_social_url' => 'https://plus.google.com/',
                );
                $Response = $this->email_model->sendmail_carVoucher($data);
                $response = array('status' => 1);
                //echo json_encode($response);
            }
        } else {
            $response = array('status' => 0);
            echo json_encode($response);
        }
    }

    public function vacation_mail_voucher($pnr_no) {
        $count = $this->booking_model->getBookingPnr($pnr_no)->num_rows();
        if ($count == 1) {
            $b_data = $this->booking_model->getBookingPnr($pnr_no)->row();
            if ($b_data->module == 'VACATION') {
                $data['Booking'] = $booking = $this->booking_model->getBookingbyPnr($b_data->pnr_no, $b_data->module)->row();
                $data['global'] = $this->booking_model->getBookingPnr($pnr_no)->row();
                $data['message'] = $this->load->view('vacation/mail_voucher', $data, TRUE);
                $data['to'] = $booking->BILLING_EMAIL;
                $data['email_access'] = $this->email_model->get_email_acess()->row();
                $email_type = 'ApartmentVoucher';
                $data['email_template'] = $this->email_model->get_email_template($email_type)->row();
                $data['booking_status'] = strtolower($booking->booking_status);
                $data['social_url'] = array(
                    'facebook_social_url' => 'https://www.facebook.com/skywalker',
                    'twitter_social_url' => 'https://twitter.com/skywalker/',
                    'google_social_url' => 'https://plus.google.com/',
                );
                $Response = $this->email_model->sendmail_carVoucher($data);
                $response = array('status' => 1);
                //echo json_encode($response);
            }
        } else {
            $response = array('status' => 0);
            echo json_encode($response);
        }
    }

    public function get_mail_content_apartmentVoucher($pnr_no) {
        $data['Booking'] = $Booking = $this->booking_model->getBooking($pnr_no)->row();
        $data['email_access'] = $this->email_model->get_email_acess()->row();
        $email_type = 'ApartmentVoucher';
        $data['email_template'] = $this->email_model->get_email_template($email_type)->row();
        //echo '<pre>';print_r($Booking);die;
        $data['email'] = $Booking->RES_GUEST_EMAIL;
        $data['host_data'] = $this->account_model->GetUserData($Booking->PROP_USER_TYPE, $Booking->PROP_USER_ID)->row();
        $data['Map'] = $this->getStaticMap($data['Booking']->PROP_LATITUDE, $data['Booking']->PROP_LONGITUDE);
        $data['social_url'] = array(
            'facebook_social_url' => 'https://www.facebook.com/skywalker',
            'twitter_social_url' => 'https://twitter.com/skywalker/',
            'google_social_url' => 'https://plus.google.com/',
        );
        $Response = $this->email_model->sendmail_apartmentVoucher($data);
        $this->get_mail_content_apartmentInvoice($pnr_no);
        //return $Response;
    }

    public function get_mail_content_apartmentInvoice($pnr_no) {
        $data['Booking'] = $Booking = $this->booking_model->getBooking($pnr_no)->row();
        $data['email_access'] = $this->email_model->get_email_acess()->row();
        $email_type = 'ApartmentInvoice';
        $data['email_template'] = $this->email_model->get_email_template($email_type)->row();

        $data['host_data'] = $this->account_model->GetUserData($Booking->PROP_USER_TYPE, $Booking->PROP_USER_ID)->row();
        $data['social_url'] = array(
            'facebook_social_url' => 'https://www.facebook.com/skywalker',
            'twitter_social_url' => 'https://twitter.com/skywalker/',
            'google_social_url' => 'https://plus.google.com/',
        );
        $Response = $this->email_model->sendmail_apartmentInvoice($data);
        return $Response;
    }

    public function get_mail_content_apartmentNonInstantVoucher($pnr_no) {
        $data['Booking'] = $Booking = $this->booking_model->getBooking($pnr_no)->row();
        $data['email_access'] = $this->email_model->get_email_acess()->row();
        $email_type = 'ApartmentNonInstantVoucher';
        $data['email_template'] = $this->email_model->get_email_template($email_type)->row();

        $data['email'] = $Booking->RES_GUEST_EMAIL;
        $data['host_data'] = $this->account_model->GetUserData($Booking->PROP_USER_TYPE, $Booking->PROP_USER_ID)->row();
        $data['Map'] = $this->getStaticMap($data['Booking']->PROP_LATITUDE, $data['Booking']->PROP_LONGITUDE);
        $data['social_url'] = array(
            'facebook_social_url' => 'https://www.facebook.com/skywalker',
            'twitter_social_url' => 'https://twitter.com/skywalker/',
            'google_social_url' => 'https://plus.google.com/',
        );
        $Response = $this->email_model->sendmail_apartmentNonInstantVoucher($data);
        //return $Response;
    }

    public function generate_random_key($length = 50) {
        $alphabets = range('A', 'Z');
        $numbers = range('0', '9');
        $final_array = array_merge($alphabets, $numbers);
        $id = '';
        while ($length--) {
            $key = array_rand($final_array);
            $id .= $final_array[$key];
        }
        return $id;
    }

    public function generate_parent_pnr($length = 12) {
        $alphabets = range('a', 'z');
        $numbers = range('0', '9');
        $final_array = array_merge($alphabets, $numbers);
        $id = '';
        while ($length--) {
            $key = array_rand($final_array);
            $id .= $final_array[$key];
        }
        return $id;
    }

    public function promo() {
        $promo_code = $this->input->get('code');
        $total = base64_decode($this->input->get('total'));
        $count = $this->booking_model->check_promocode($promo_code)->num_rows();
        if ($count == 1) {
            $promo_data = $this->booking_model->check_promocode($promo_code)->row();
            $user_time = time();
            $exp_time = strtotime($promo_data->exp_date);
            if ($user_time <= $exp_time) {
                $promo_type = $promo_data->promo_type;
                $discount = $mdic = $promo_data->discount;
                $cids = base64_decode($this->input->get('cid'));
                $cids = json_decode($cids);
                foreach ($cids as $key => $cid) {
                    list($module, $cid) = explode(',', $cid);
                    if ($module == 'APARTMENT') {
                        $cart = $this->apartment_model->getBookingTemp($cid)->row();
                        $total = $cart->TOTAL;
                        if ($promo_type == 1) {
                            $discount = ($total / 100) * $mdic;
                            $discount = number_format(($discount), 2, '.', '');
                            $total_payable[] = $total - $discount;
                            $total_discount[] = $discount;
                        } else if ($promo_type == 2) {
                            $discount = $discount;
                            if ($total >= $promo_data->promo_amount) {
                                $discount = number_format(($discount), 2, '.', '');
                                $total_payable[] = $total - $discount;
                                $total_discount[] = $discount;
                            } else {
                                $total_payable[] = $total;
                            }
                        }
                    }
                    if ($module == 'FLIGHT') {
                        $cart = $this->flight_model->getBookingTemp($cid)->row();
                        $total = $cart->TotalPrice;
                        if ($promo_type == 1) {
                            $discount = ($total / 100) * $mdic;
                            $discount = number_format(($discount), 2, '.', '');
                            $total_payable[] = $total - $discount;
                            $total_discount[] = $discount;
                        } else if ($promo_type == 2) {
                            $discount = $discount;
                            if ($total >= $promo_data->promo_amount) {
                                $discount = number_format(($discount), 2, '.', '');
                                $total_payable[] = $total - $discount;
                                $total_discount[] = $discount;
                            } else {
                                $total_payable[] = $total;
                            }
                        }
                    }
                    if ($module == 'HOTEL') {
                        $cart = $this->hotel_model->getBookingTemp($cid)->row();
                        $total = $cart->total_cost;
                        if ($promo_type == 1) {
                            $discount = ($total / 100) * $mdic;
                            $discount = number_format(($discount), 2, '.', '');
                            $total_payable[] = $total - $discount;
                            $total_discount[] = $discount;
                        } else if ($promo_type == 2) {
                            $discount = $discount;
                            if ($total >= $promo_data->promo_amount) {
                                $discount = number_format(($discount), 2, '.', '');
                                $total_payable[] = $total - $discount;
                                $total - $discount;
                                $total_discount[] = $discount;
                            } else {
                                $total_payable[] = $total;
                            }
                        }
                    }
                    if ($module == 'CAR') {
                        $cart = $this->car_model->getBookingTemp($cid)->row();
                        $total = $cart->TotalPrice;
                        if ($promo_type == 1) {
                            $discount = ($total / 100) * $mdic;
                            $discount = number_format(($discount), 2, '.', '');
                            $total_payable[] = $total - $discount;
                            $total_discount[] = $discount;
                        } else if ($promo_type == 2) {
                            $discount = $discount;
                            if ($total >= $promo_data->promo_amount) {
                                $discount = number_format(($discount), 2, '.', '');
                                $total_payable[] = $total - $discount;
                                $total_discount[] = $discount;
                            } else {
                                $total_payable[] = $total;
                            }
                        }
                    }
                    if ($module == 'VACATION') {
                        $cart = $this->vacation_model->getBookingTemp($cid)->row();
                        $total = $cart->TotalPrice;
                        if ($promo_type == 1) {
                            $discount = ($total / 100) * $mdic;
                            $discount = number_format(($discount), 2, '.', '');
                            $total_payable[] = $total - $discount;
                            $total_discount[] = $discount;
                        } else if ($promo_type == 2) {
                            $discount = $discount;
                            if ($total >= $promo_data->promo_amount) {
                                $discount = number_format(($discount), 2, '.', '');
                                $total_payable[] = $total - $discount;
                                $total_discount[] = $discount;
                            } else {
                                $total_payable[] = $total;
                            }
                        }
                    }
                }
                //echo '<pre>';print_r($total_discount);die;
                $count = count($total_discount);
                $total_payable = array_sum($total_payable);
                $total_discount = array_sum($total_discount);

                if ($promo_type == 1) {
                    $response['discMsg'] = 'Coupon succesfully applied, You save <strong>' . $mdic . '%</strong> of total amount';
                } else if ($promo_type == 2) {
                    $response['discMsg'] = 'Coupon succesfully applied for ' . $count . ' products, You save <strong>' . CURR_ICON . $total_discount . '</strong> of total amount';
                }
                $response['discount'] = $total_discount;
                $response['finalAmt'] = $total_payable;
                $response['code'] = base64_encode($promo_code);
                $response['status'] = 1;
            } else {
                $response['status'] = 0;
                $response['discMsg'] = 'Sorry, promo code has expired';
            }
        } else {
            $response['status'] = 0;
            $response['discMsg'] = 'Not a valid coupon';
        }
        echo json_encode($response);
    }

    public function b2b_check_payment($payable_amount, $user_id) {
        $deposit_amount_det = $this->account_model->get_deposit_amount($user_id)->row();
        $credit_amount = $deposit_amount_det->balance_credit;
        //if($credit_amount >= $payable_amount){
        if ($credit_amount >= $payable_amount) {
            return true;
        } else {
            return false;
        }
    }

    public function do_payment_account($booking_id, $module, $payable_amount, $myMarkup, $user_id, $user_type) {
        if ($user_type == '2') {
            $deposit_amount_det = $this->account_model->get_deposit_amount($user_id)->row();
            $credit_amount = $deposit_amount_det->balance_credit;
            //echo $myMarkup;
            $payable_amount = $this->account_model->PercentageMinusAmount($payable_amount, $myMarkup);

            $balance_credit = $credit_amount;

            $description = 'Booking - ' . $module . ' : <a href="' . WEB_URL . '/' . strtolower($module) . '/invoice/' . base64_encode(base64_encode($booking_id)) . '" target="_blank">' . $booking_id . '</a>';

            $account_transaction = array(
                'statment_type' => 'WITHDRAW',
                'booking_number' => $booking_id,
                'user_type' => '2',
                'user_id' => $user_id,
                'amount' => $payable_amount,
                'balance_amount' => $balance_credit,
                'description' => $description
            );
            $this->account_model->update_account_transaction($account_transaction);
        } elseif ($user_type == '3') {

            $balance_credit = 0;

            $description = 'Booking - ' . $module . ' : <a href="' . WEB_URL . '/' . strtolower($module) . '/invoice/' . base64_encode(base64_encode($booking_id)) . '" target="_blank">' . $booking_id . '</a>';

            $account_transaction = array(
                'statment_type' => 'WITHDRAW',
                'booking_number' => $booking_id,
                'user_type' => '3',
                'user_id' => $user_id,
                'amount' => $payable_amount,
                'balance_amount' => $balance_credit,
                'description' => $description
            );
            $this->account_model->update_account_transaction($account_transaction);
        }
    }

    public function b2b_do_payment($booking_id, $module, $payable_amount, $myMarkup, $user_id) {
        $deposit_amount_det = $this->account_model->get_deposit_amount($user_id)->row();
        $credit_amount = $deposit_amount_det->balance_credit;
        //echo $myMarkup;
        $payable_amount = $this->account_model->PercentageMinusAmount($payable_amount, $myMarkup);
        //die;
        if ($credit_amount >= $payable_amount) {
            $balance_credit = $credit_amount - $payable_amount;
            $update_credit_amount = array(
                'balance_credit' => $balance_credit,
                'last_debit' => $payable_amount
            );

            $this->account_model->update_credit_amount($update_credit_amount, $user_id);
            $payment_transaction = array(
                'module' => $module,
                'reference_id' => $booking_id,
                'user_type' => '2',
                'user_id' => $user_id,
                'amount_deducted' => $payable_amount
            );
            $this->account_model->update_payment_transaction($payment_transaction);
        }
    }

    public function islogged_in($session_id, $global_id_) {
        // if (!$this->session->userdata('b2c_id')) {
        //    redirect(WEB_URL.'/booking/signup_login');
        // }

        if ($this->session->userdata('b2c_id')) {
            
        } else if ($this->session->userdata('b2b_id')) {
            
        } else {
            redirect(WEB_URL . '/booking/signup_login/' . $session_id . '/' . $global_id_);
        }
        // }else if($this->session->userdata('b2c_id')){
        //     if($this->session->userdata('b2c_id')){
        //         $user_type = 3;
        //         $user_id = $this->session->userdata('b2c_id');
        //     }
        //     $b2c_data = $this->apartment_model->is_signup($user_id)->num_rows();
        //     if($b2c_data->password == ''){
        //         //redirect(WEB_URL.'/apt/signup_login');
        //     }
        // }
    }

    public function getStaticMap($lat, $long) {
        $locstring = '';
        $firstloc = 0;
        $long = "77.5667";
        $lat = "12.9667";

        if ($firstloc == 0) {
            $locstring = $locstring . $lat . ',' . $long . '&markers=icon:http://skywalkertravels.com/assets/images/marker_out.png%7C' . $lat . ',' . $long;
            $firstloc = 1;
        } else {
            $locstring = $locstring . '&markers=icon:http://skywalkertravels.com/assets/images/marker_out.png%7C' . $lat . ',' . $long;
        }
        $url = "http://maps.googleapis.com/maps/api/staticmap?zoom=13&size=627x327&maptype=ROADMAP&" . urlencode("center") . "=" . $locstring . "&sensor=false";
        return $url;
    }

    public function signup_login($session_id, $global_id_) {
        if ($this->session->userdata('b2c_id') || $this->session->userdata('b2b_id')) {
            $continue = $this->session->userdata('continue');
            redirect($continue);
            $data['msg'] = 'Please Sign up / Login to book';
            //$this->load->view('login', $data);
            $this->guest_login($session_id, $global_id_);
        } else {
            $data['ses'] = $session_id;
            $data['gid'] = $global_id_;
            $data['msg'] = 'Please Sign up / Login to book';
            //$this->load->view('login', $data);
            $this->guest_login($session_id, $global_id_);
        }
    }

    public function guest_login($session_id, $global_id_) {
        $gust_email = $this->input->post('gust_email');
        $gust_mobile = $this->input->post('gust_mobile');
        $this->session->set_userdata('gust_email', $gust_email);   //set
        $this->session->set_userdata('gust_mobile', $gust_mobile);   //set
        //echo 'dcdcdcdc<pre>';print_r($this->input->post('gust_email'));exit;
        redirect(WEB_URL . '/booking/' . $session_id . '/' . $global_id_ . '/1');
    }





    public function b2c_ewallet_do_payment($booking_id, $module, $payable_amount,$user_id)
    {       

         $available_amount = $this->account_model->gettting_ewallet_bal($user_id);

          print_r($available_amount);exit;

        $credit_amount = $available_amount->ewallet_bal; 
        //echo $myMarkup;
               //die;
        if ($credit_amount >= $payable_amount) {
            $balance_credit = $credit_amount - $payable_amount;
            
            $update_credit_amount = array(
                'ewallet_bal' => $balance_credit
                
            );   
             
          
             $this->account_model->update_ewallet($update_credit_amount,$user_id); 
         
             $payment_transaction = array(
                'module' => $module,
                'reference_id' => $booking_id,
                'user_type' => '3',
                'user_id' => $user_id,
                'amount_deducted' => $payable_amount
            );
            $this->account_model->update_payment_transaction($payment_transaction);
            
       }  

    } 

    public function b2c_ewallet_do_payment_account($booking_id, $module, $payable_amount, $myMarkup, $user_id, $user_type)
    {   
        if ($user_type == '3') {
            $deposit_amount_det = $this->account_model->gettting_ewallet_bal($user_id);
            $credit_amount = $deposit_amount_det->ewallet_bal;
            //echo $myMarkup;
          
            $balance_credit = $credit_amount;

            $description = 'Booking - ' . $module . ' : <a href="' . WEB_URL . '/' . strtolower($module) . '/invoice/' . base64_encode(base64_encode($booking_id)) . '" target="_blank">' . $booking_id . '</a>';

            $account_transaction = array(
                'statment_type' => 'WITHDRAW',
                'booking_number' => $booking_id,
                'user_type' => '3',
                'user_id' => $user_id,
                'amount' => $payable_amount,
                'balance_amount' => $balance_credit,
                'description' => $description
            );
            $this->account_model->update_account_transaction($account_transaction);
       
    }  

  }
  public function booking_b2c_ewallet($booking_globalid)
     { 
         
        $pnr_d = date("ymd");
        $pnr_d1 = date("His");
        $booking_id_mdule = substr($booking_globalid, 0, 4);
        $module = '';
        if ($booking_id_mdule == 'SWTF') {
            $module = 'FLIGHT';
        }
        
         if ($booking_id_mdule == 'SWTH') {
            $module = 'HOTEL';
        }
      if($booking_id_mdule == 'SWTB')
        {
            $module = 'BUS';
            
       }
        
        
        $parent_pnr = '';
        if ($module == 'FLIGHT') {   
            $book_globaldata = $this->flight_model->getBookingFlightGLOBAL($booking_globalid)->row();
            $parent_pnr = $book_globaldata->parent_pnr;
            $RoundtripGlobalid = $this->flight_model->getBookingFlightGLOBALRound($parent_pnr)->num_rows();
           
            
            if ($RoundtripGlobalid == 2) {   
                $bookRoundGlobaldata = $this->flight_model->getBookingFlightGLOBALRound($parent_pnr)->result();
                foreach ($bookRoundGlobaldata as $value) {


                    $count_g = $this->flight_model->getBookingFlightGLOBAL($booking_globalid)->num_rows();
                    if ($count_g == 1) {
                        $book_globaldata = $this->flight_model->getBookingFlightGLOBAL($booking_globalid)->row();
                        $booking_id = $book_globaldata->ref_id;
                        $parent_pnr = $book_globaldata->parent_pnr;
                        $bid = $book_globaldata->id;
                        $count = $this->flight_model->getBookingFlightTemp($booking_id)->num_rows();
                        if ($count == 1) {
                            $book_temp_data = $this->flight_model->getBookingFlightTemp($booking_id)->row();
                            $cart_flight_data = json_decode(base64_decode($book_temp_data->cart_flight_data));
                            $checkout_form = json_decode($book_temp_data->TravelerDetails);

                            $FareRequest = json_decode(base64_decode($book_temp_data->FareRequest));
                            $FareResponse = json_decode(base64_decode($book_temp_data->FareResponse));
                            $PassengerInfo = json_decode(base64_decode($book_temp_data->PassengerInfo));

                            $AirBookRQ_RS = AirFinalBookingQ($cart_flight_data, $checkout_form, $FareRequest, $FareResponse, $PassengerInfo);
                            $AirBookRS = $AirBookRQ_RS['AirBookRS'];

                            // $AirBookRS = new SimpleXMLElement($AirBookRS);
                            //  $AirBookRS = $AirBookRS->children('http://schemas.xmlsoap.org/soap/envelope/')->Body->children()->wmTravelBuildResponse->OTA_TravelItineraryRS;
                            $BookingStatus = 'HOLD';
                            $LocatorCode = '';
                            $Status = '';
                            $ProviderLocatorCode = '';
                            $SupplierLocatorCode = '';
                            $AirReservationLocatorCode = '';
                            $ProviderReservationInfoRef = '';
                            $BookingTravelerRef = '';   
                            if (isset($AirBookRS->TicketResult->PNR)) {
                              //  $response = $AirBookRS->TravelItinerary;

                                $PNR = (string) $AirBookRS->TicketResult->PNR;
                                $BookingID = $AirBookRS->TicketResult->BookingId;
                                if (isset($response->ItineraryInfo->ReservationItems->ItemPricing)) {
                                    $TotalFare = $OriginalTotalFare = (string) $response->ItineraryInfo->ReservationItems->ItemPricing->AirFareInfo->ItinTotalFare->TotalFare['Amount'];
                                    $CurrencyCode = $OriginalTotalFareCurrencyCode = (string) $response->ItineraryInfo->ReservationItems->ItemPricing->AirFareInfo->ItinTotalFare->TotalFare['CurrencyCode'];
                                    $DecimalPlaces = $response->ItineraryInfo->ReservationItems->ItemPricing->AirFareInfo->ItinTotalFare->TotalFare['DecimalPlaces'];
                                    $b = substr($TotalFare, -$DecimalPlaces);
                                    $a = substr($TotalFare, 0, -$DecimalPlaces);
                                    $TotalFare = $a . '.' . $b;


                                    $aMarkup = $cart_flight_data->aMarkup;

                                    $MyMarkup = $this->account_model->get_my_markup(); //get agent markup
                                    $myMarkup = $MyMarkup['markup'];

                                    $TotalFare = $this->flight_model->currency_convertor($TotalFare, $CurrencyCode, CURR);
                                    $TMarkup = $this->account_model->PercentageAmount($TotalFare, $aMarkup);

                                    $TotalFare = $this->account_model->PercentageToAmount($TotalFare, $aMarkup);
                                    $Markup = $this->account_model->PercentageAmount($TotalFare, $myMarkup);
                                    $TOTAL = $this->account_model->PercentageToAmount($TotalFare, $myMarkup);
                                }
                                  
                                $LocatorCode = $PNR;
                                $Status = 'SUCCESS';
                                $BookingStatus = 'CONFIRMED';
                                $Remarks = 'No remarks';
                            } else {
                                $err = $AirBookRS->Errors->Error;
                                $ErrorInfo = $err;
                                $Remarks = (string) $ErrorInfo->Description;
                                $xml_log = array(
                                    'Api' => 'TripXML',
                                    'XML_Type' => 'Flight',
                                    'XML_Request' => base64_encode(json_encode($AirBookRQ_RS['AirBookRQ'])),
                                    'XML_Response' => base64_encode(json_encode($AirBookRQ_RS['AirBookRS'])),
                                    'Ip_address' => $this->input->ip_address(),
                                    'XML_Time' => date('Y-m-d H:i:s')
                                );
                                $this->xml_model->insert_xml_log($xml_log);
                                $Markup = 0;
                                $TMarkup = 0;
                            }

                            $booking_res = array(
                                'LocatorCode' => $LocatorCode,
                                'ProviderLocatorCode' => $ProviderLocatorCode,
                                'AirReservationLocatorCode' => $AirReservationLocatorCode,
                                'SupplierLocatorCode' => $SupplierLocatorCode,
                                'ProviderReservationInfoRef' => $ProviderReservationInfoRef,
                                'BookingTravelerRef' => $BookingTravelerRef,
                                'BookingStatus' => $BookingStatus,
                                'Status' => $Status,
                                'Remarks' => $Remarks
                            );
                            $booking_res = json_encode($booking_res);

                            $booking_flight = array(
                                'AirBookRQ' => base64_encode(json_encode($AirBookRQ_RS['AirBookRQ'])),
                                'AirBookRS' => base64_encode(json_encode($AirBookRQ_RS['AirBookRS'])),
                                //'TotalPrice' => $TOTAL,  //remove here
                                //  'MyMarkup' => $Markup,  //remove here
                                //  'AdminMarkup' => $TMarkup,  //remove here
                                'booking_res' => $booking_res  //remove here
                            );
                            $this->flight_model->Update_Booking_flight_data($booking_id, $booking_flight);

                            //$count = $this->flight_model->CheckDuplicateBooking($booking_id)->num_rows();
                            //if($count == 0){

                            $booking_res = json_decode($booking_res);
                           // echo '<pre>';print_r($booking_res);die;
                            if ($booking_res->BookingStatus == 'CONFIRMED') {
                                $bStatus = 'CONFIRMED';
                                $aStatus = 'CONFIRMED';
                                if ($this->session->userdata('b2c_id')) {
                                    $user_type = 3;
                                    $user_id = $this->session->userdata('b2c_id');
                                    $this->b2c_ewallet_do_payment($booking_id, $module, $book_temp_data->TotalPrice,$user_id);
                                     
                                    
                                }
                            } else {
                                $bStatus = 'FAILED';
                                $aStatus = 'FAILED';
                            }

                            $update_booking = array(
                                'booking_no' => $booking_res->LocatorCode,
                                'BookingID' => $BookingID,
                                'api_status' => $aStatus,
                                'booking_status' => $bStatus
                            );
                            $this->flight_model->Update_Booking_Global($bid, $update_booking, 'FLIGHT');
                            if ($booking_res->BookingStatus == 'CONFIRMED') {
                               if ($this->session->userdata('b2c_id')) {
                                    $user_type = 3;
                                    $user_id = $this->session->userdata('b2c_id');
                                    $this->b2c_ewallet_do_payment_account($booking_globalid, $module, $book_temp_data->TotalPrice, '0.00', $user_id, $user_type);
                                } else {
                                    $user_type = 4;
                                    $user_id = 0;
                                    $this->b2c_ewallet_do_payment_account($booking_globalid, $module, $book_temp_data->TotalPrice, '0.00', $user_id, $user_type);
                                }
                            }
                            //Here We have to send mail
                            $this->flight_mail_voucher($booking_globalid);
                             $this->flight_sms_voucher($booking_globalid);

                            //}else{
                            //echo 'Invalid inf';
                            //}
                        } else {
                            echo 'Invalid Data';
                        }
                    } else {
                        echo 'Invalid Data';
                    }
                }
            } else {
                 $count_g = $this->flight_model->getBookingFlightGLOBAL($booking_globalid)->num_rows();
                if ($count_g == 1) {
                    $book_globaldata = $this->flight_model->getBookingFlightGLOBAL($booking_globalid)->row();
                    $booking_id = $book_globaldata->ref_id;
                    $parent_pnr = $book_globaldata->parent_pnr;
                    $bid = $book_globaldata->id;
                    $count = $this->flight_model->getBookingFlightTemp($booking_id)->num_rows();
                    if ($count == 1) {
                        $book_temp_data = $this->flight_model->getBookingFlightTemp($booking_id)->row();
                        $cart_flight_data = json_decode(base64_decode($book_temp_data->cart_flight_data));
                        $checkout_form = json_decode($book_temp_data->TravelerDetails);

                        $FareRequest = json_decode(base64_decode($book_temp_data->FareRequest));
                        $FareResponse = json_decode(base64_decode($book_temp_data->FareResponse));
                        $PassengerInfo = json_decode(base64_decode($book_temp_data->PassengerInfo));

                        $AirBookRQ_RS = AirFinalBookingQ($cart_flight_data, $checkout_form, $FareRequest, $FareResponse, $PassengerInfo);
                        $AirBookRS = $AirBookRQ_RS['AirBookRS'];

                        // $AirBookRS = new SimpleXMLElement($AirBookRS);
                        //  $AirBookRS = $AirBookRS->children('http://schemas.xmlsoap.org/soap/envelope/')->Body->children()->wmTravelBuildResponse->OTA_TravelItineraryRS;
                        $BookingStatus = 'HOLD';
                        $LocatorCode = '';
                        $Status = '';
                        $ProviderLocatorCode = '';
                        $SupplierLocatorCode = '';
                        $AirReservationLocatorCode = '';
                        $ProviderReservationInfoRef = '';
                        $BookingTravelerRef = '';
                        if (isset($AirBookRS->TicketResult->PNR)) {
                           // $response = $AirBookRS->TravelItinerary;

                            $PNR = (string) $AirBookRS->TicketResult->PNR;
                            $BookingID = $AirBookRS->TicketResult->BookingId;
                            if (isset($response->ItineraryInfo->ReservationItems->ItemPricing)) {
                                $TotalFare = $OriginalTotalFare = (string) $response->ItineraryInfo->ReservationItems->ItemPricing->AirFareInfo->ItinTotalFare->TotalFare['Amount'];
                                $CurrencyCode = $OriginalTotalFareCurrencyCode = (string) $response->ItineraryInfo->ReservationItems->ItemPricing->AirFareInfo->ItinTotalFare->TotalFare['CurrencyCode'];
                                $DecimalPlaces = $response->ItineraryInfo->ReservationItems->ItemPricing->AirFareInfo->ItinTotalFare->TotalFare['DecimalPlaces'];
                                $b = substr($TotalFare, -$DecimalPlaces);
                                $a = substr($TotalFare, 0, -$DecimalPlaces);
                                $TotalFare = $a . '.' . $b;


                                $aMarkup = $cart_flight_data->aMarkup;

                                $MyMarkup = $this->account_model->get_my_markup(); //get agent markup
                                $myMarkup = $MyMarkup['markup'];

                                $TotalFare = $this->flight_model->currency_convertor($TotalFare, $CurrencyCode, CURR);
                                $TMarkup = $this->account_model->PercentageAmount($TotalFare, $aMarkup);

                                $TotalFare = $this->account_model->PercentageToAmount($TotalFare, $aMarkup);
                                $Markup = $this->account_model->PercentageAmount($TotalFare, $myMarkup);
                                $TOTAL = $this->account_model->PercentageToAmount($TotalFare, $myMarkup);
                            }

                            $LocatorCode = $PNR;
                            $Status = 'SUCCESS';
                            $BookingStatus = 'CONFIRMED';
                            $Remarks = 'No remarks';
                        } else {
                            //$err = $AirBookRS->Errors->Error;

                            //$ErrorInfo = $err;
                           // $Remarks = (string) $ErrorInfo->Description;

                               $Remarks = 'No remarks';

                            //print_r($AirBookRQ_RS['AirBookRQ']);
                            $xml_log = array(
                                'Api' => 'TripXML',
                                'XML_Type' => 'Flight',
                                'XML_Request' => base64_encode(json_encode($AirBookRQ_RS['AirBookRQ'])),
                                'XML_Response' => base64_encode(json_encode($AirBookRQ_RS['AirBookRS'])),
                                'Ip_address' => $this->input->ip_address(),
                                'XML_Time' => date('Y-m-d H:i:s')
                            );
                            $this->xml_model->insert_xml_log($xml_log);
                            $Markup = 0;
                            $TMarkup = 0;
                        }

                        $booking_res = array(
                            'LocatorCode' => $LocatorCode,
                            'ProviderLocatorCode' => $ProviderLocatorCode,
                            'AirReservationLocatorCode' => $AirReservationLocatorCode,
                            'SupplierLocatorCode' => $SupplierLocatorCode,
                            'ProviderReservationInfoRef' => $ProviderReservationInfoRef,
                            'BookingTravelerRef' => $BookingTravelerRef,
                            'BookingStatus' => $BookingStatus,
                            'Status' => $Status,
                            'Remarks' => $Remarks
                        );
                        $booking_res = json_encode($booking_res);

                        $booking_flight = array(
                            'AirBookRQ' => base64_encode(json_encode($AirBookRQ_RS['AirBookRQ'])),
                            'AirBookRS' => base64_encode(json_encode($AirBookRQ_RS['AirBookRS'])),
                            //'TotalPrice' => $TOTAL,  //remove here
                            //  'MyMarkup' => $Markup,  //remove here
                            //  'AdminMarkup' => $TMarkup,  //remove here
                            'booking_res' => $booking_res  //remove here
                        );
                        $this->flight_model->Update_Booking_flight_data($booking_id, $booking_flight);

                        //$count = $this->flight_model->CheckDuplicateBooking($booking_id)->num_rows();
                        //if($count == 0){

                        $booking_res = json_decode($booking_res);
                        //echo '<pre>';print_r($booking_res);die;
                        if ($booking_res->BookingStatus == 'CONFIRMED') {
                            $bStatus = 'CONFIRMED';
                            $aStatus = 'CONFIRMED';
                            if ($this->session->userdata('b2c_id')) {
                                $user_type = 3;
                                $user_id = $this->session->userdata('b2b_id');
                                $this->b2c_ewallet_do_payment($booking_id, $module, $book_temp_data->TotalPrice,$user_id);
                            }
                        } else {
                            $bStatus = 'FAILED';
                            $aStatus = 'FAILED';
                        }

                        $update_booking = array(
                            'booking_no' => $booking_res->LocatorCode,
                            //'BookingID' => $BookingID,
                            'api_status' => $aStatus,
                            'booking_status' => $bStatus
                        );
                        $this->flight_model->Update_Booking_Global($bid, $update_booking, 'FLIGHT');
                        if ($booking_res->BookingStatus == 'CONFIRMED') {
                            if ($this->session->userdata('b2c_id')) {
                                $user_type = 3;
                                $user_id = $this->session->userdata('b2c_id');
                                $this->do_payment_account($booking_globalid, $module, $book_temp_data->TotalPrice, '0.00', $user_id, $user_type);
                            } else {
                                $user_type = 4;
                                $user_id = 0;
                                $this->b2c_ewallet_do_payment_account($booking_globalid, $module, $book_temp_data->TotalPrice, '0.00', $user_id, $user_type);
                            }
                        }
                        //Here We have to send mail
                        $this->flight_mail_voucher($booking_globalid);
                       //  $this->hotel_sms_voucher($pnr_no);
                        

                        //}else{
                        //echo 'Invalid inf';
                        //}
                    } else {
                        echo 'Invalid Data';
                    }
                } else {
                    echo 'Invalid Data';
                }
            }
        }
        if ($module == 'HOTEL') {
            $count = $this->hotel_model->getBookingHotelTemp($booking_id)->num_rows();
            if ($count == 1) {
                //$count = $this->flight_model->CheckDuplicateBooking($booking_id)->num_rows();
                //if($count == 0){
                $book_temp_data = $this->hotel_model->getBookingHotelTemp($booking_id)->row();
                $booking_res = json_decode($book_temp_data->booking_res);
                //echo '<pre>';print_r($booking_res);die;

                if ($booking_res->BookingStatus == 'CONFIRMED') {
                    //echo '<pre>';print_r($booking_res);die;

                    $bStatus = 'CONFIRMED';
                    $aStatus = 'CONFIRMED';
                    if ($this->session->userdata('b2c_id')) {
                        $user_type = 3;
                        $user_id = $this->session->userdata('b2c_id');
                        $this->b2c_ewallet_do_payment($booking_id, $module, $book_temp_data->total_cost,$user_id);
                    }
                } else {
                    $bStatus = 'FAILED';
                    $aStatus = 'FAILED';
                }
                $booking = array(
                    'module' => 'HOTEL',
                    'ref_id' => $booking_id,
                    'parent_pnr' => $parent_pnr,
                    'user_type' => $book_temp_data->USER_TYPE,
                    'amount' => $book_temp_data->total_cost,
                    'leadpax' => $book_temp_data->BILLING_FIRSTNAME . ' ' . $book_temp_data->BILLING_LASTNAME,
                    'user_id' => $book_temp_data->USER_ID,
                    'ip' => $this->input->ip_address(),
                    'api_status' => $aStatus,
                    'travel_date' => $book_temp_data->TravelDate,
                    'booking_status' => $bStatus
                );
                $bid = $this->hotel_model->Booking_Global($booking);
                $pnr_no = 'SWTH' . $pnr_d . $pnr_d1 . $bid;
                $update_booking = array(
                    'pnr_no' => $pnr_no,
                    'booking_no' => $booking_res->PurchaseConfirmCode
                );
                $this->hotel_model->Update_Booking_Global($bid, $update_booking, 'HOTEL');
                if ($booking_res->BookingStatus == 'CONFIRMED') {
                    if ($this->session->userdata('b2c_id')) {
                        $user_type = 3;
                        $user_id = $this->session->userdata('b2c_id');
                    } else {
                        $user_type = 4;
                        $user_id = 0;
                    }
                    $this->b2c_ewallet_do_payment_account($pnr_no, $module, $book_temp_data->total_cost, '0.00', $user_id, $user_type);
                }
                //Here We have to send mail
                $this->hotel_mail_voucher($pnr_no);
                return $pnr_no;
                //}else{
                //echo 'Invalid inf';
                //}
            } else {
                echo 'Invalid Data';
            }
        }

      /* 
        if ($this->session->userdata('b2c_id')) {
            return $booking_globalid;
        } else {
            if ($parent_pnr != '') {
                redirect(WEB_URL . 'booking/confirm/' . base64_encode($parent_pnr), 'refresh');
            } else {

                echo 'INvalid';
            }
        } */



     }   



}

/* End of file apartment.php */
/* Location: ./application/controllers/apartment.php */
