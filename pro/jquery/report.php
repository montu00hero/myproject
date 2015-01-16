<?php session_start(); if ( ! defined('BASEPATH')) exit('No direct script access allowed');
ob_start();
class Report extends CI_Controller {
	function __construct()
	{
	   parent::__construct();
	   $this->load->model("Report_Model");
	   $this->load->model("Holiday_Model");
	   $this->load->library('session');
	   $this->load->library("provab_mailer");
	   $this->load->library("pagination"); 
	   $this->load->library('calendar', $this->_setting());
	 
	     
        //error_reporting(0); 
	}
	
 
	public function my_booking()
	{
		if (!$this->session->userdata('agent_id') || $this->session->userdata('agent_id') == '') {
			redirect('hotel/login', 'refresh');
		 }
		//GET BOOKING STATUS ***HG
		$data['booking_status'] = $this->Report_Model->model_get_booking_status();
		
		// SEARCHING IN THE LIST START ***HG
		if($this->uri->segment(3) == '')
		{
			$search = array(
			'bookingStatus'=>$this->session->userdata('bookingStatus'),
			'fromDate'=>$this->session->userdata('fromDate'),
			'toDate'=>$this->session->userdata('toDate'),
			'bookingNumber'=>$this->session->userdata('bookingNumber'),
			'hotelNumber'=>$this->session->userdata('hotelNumber')
			);
			$this->session->unset_userdata($search); 
		}
		elseif($this->input->post('submit') != '')
		{
			 $search = array(
			'bookingStatus'=>$this->input->post('bookingStatus'),
			'fromDate'=>$this->input->post('fromDate'),
			'toDate'=>$this->input->post('toDate'),
			'bookingNumber'=>$this->input->post('bookingNumber'),
			'hotelNumber'=>$this->input->post('hotelNumber')
			);
			$this->session->set_userdata($search); 
		}
		else
		{
			$search = array(
			'bookingStatus'=>$this->session->userdata('bookingStatus'),
			'fromDate'=>$this->session->userdata('fromDate'),
			'toDate'=>$this->session->userdata('toDate'),
			'bookingNumber'=>$this->session->userdata('bookingNumber'),
			'hotelNumber'=>$this->session->userdata('hotelNumber')
			);
		}
		// SEARCHING IN THE LIST START END ***HG
		
        //pagination Start ***HG
        $config = array();
        $config["base_url"] = site_url() . "/report/my_booking/pgntn";
        $config["total_rows"] = $this->Report_Model->mybook_count($search);
        $config["per_page"] = 10;
        $config["uri_segment"] = 4;
        $this->pagination->initialize($config);
        $page = ($this->uri->segment(4)) ? $this->uri->segment(4) : 0;
        $data['book_details'] = $this->Report_Model->mybook_sarch($search,$config["per_page"], $page);
        $data["links"] = $this->pagination->create_links();	    
		//pagination End ***HG
		
		$this->load->view('report/book',$data);
	}
	
	
	public function flight_booking(){
		
	//pagination
	
	if(!$this->session->userdata('agent_id'))
		{
			redirect('hotel/login');
		}
		$this->load->model('Flightreport');
		$data['flight_booking_data'] = $this->Flightreport->flight_report_data();
		$data['flight_booking_status'] = $this->Flightreport->flight_report_status();

		if($this->input->post('submit') != '')
		{
			 $search = array(
			'bookingStatus'=>$this->input->post('bookingStatus'),
			'fromDate'=>$this->input->post('fromDate'),
			'toDate'=>$this->input->post('toDate'),
			'bookingNumber'=>$this->input->post('bookingNumber'),
			'pnr'=>$this->input->post('pnr')
			);
			$this->session->set_userdata($search); 
		}
		else
		{
			$search = array(
			'bookingStatus'=>$this->session->userdata('bookingStatus'),
			'fromDate'=>$this->session->userdata('fromDate'),
			'toDate'=>$this->session->userdata('toDate'),
			'bookingNumber'=>$this->session->userdata('bookingNumber'),
			'pnr'=>$this->session->userdata('pnr')
			);
			if($this->uri->segment(3) == '')
			{
				$this->session->unset_userdata($search);
				$search = array(
					'bookingStatus'=>'',
					'fromDate'=>'',
					'toDate'=>'',
					'bookingNumber'=>'',
					'pnr'=>''
					); 
			}
		}
		
		$count_data = $this->Flightreport->mybook_count($search);
		 //pagination Start ***HG
        $config = array();
        $config["base_url"] = site_url() . "/report/flight_booking/pgntn/";
        $config["total_rows"] = $count_data;
        $config["per_page"] = 10;
        $config["uri_segment"] = 4;
        $this->pagination->initialize($config);
        $page = ($this->uri->segment(4)) ? $this->uri->segment(4) : 0;
        $data['book_details'] = $this->Flightreport->mybook_search($config["per_page"], $page ,$search);
        $data["links"] = $this->pagination->create_links();	    
		//pagination End ***HG
		$this->load->view('report/flights_booking',$data);	
		
		
		
	}
	
	
	
	
	
	public function holiday_booking()
	{
		if (!$this->session->userdata('agent_id') || $this->session->userdata('agent_id') == '') {
			redirect('hotel/login', 'refresh');
		}
		
		$config = array();
		$config["base_url"] = base_url()."/report/holiday_booking/";
		$is_search=$this->input->post('search');
		$search = array(
		'holibookingStatus'=>$this->input->post('holibookingStatus'),
		'holifromDate'=>$this->input->post('holifromDate'),
		'holitoDate'=>$this->input->post('holitoDate'),
		'holibookingNumber'=>$this->input->post('holibookingNumber'),
		'holihotelNumber'=>$this->input->post('holihotelNumber')
		);

		if (!array_filter($search) && $is_search===false) { 

		$search=array(
		'holibookingStatus'=>$this->session->userdata('holibookingStatus'),
		'holifromDate'=>$this->session->userdata('holifromDate'),
		'holitoDate'=>$this->session->userdata('holitoDate'),
		'holibookingNumber'=>$this->session->userdata('holibookingNumber'),
		'holihotelNumber'=>$this->session->userdata('holihotelNumber')

		);

		}else{

		$this->session->set_userdata($search);

		}
		$config["total_rows"] = $this->Report_Model->holiday_booking_count($search);
		$config["per_page"] = 25;
		$config["uri_segment"] = 3;
		$this->pagination->initialize($config);
		$page = ($this->uri->segment(3)) ? $this->uri->segment(3) : 0;
		$data["book_details"]=$book_details = $this->Report_Model->holiday_booking_pack($search,$config["per_page"], $page);
		$data["links"] = $this->pagination->create_links();
		
		
		
		$this->load->view('report/holi_booking',$data);
		
		
		
		
	
	
	
	}
	
	
	
	function holiday_pdf_booking($from,$to,$status,$bookingno,$pnr){
		
		
		
		
		$data["book_details"]=$book_details = $this->Report_Model->holiday_booking_pack1($from,$to,$status,$bookingno);
		
		$filename = 'HolidayBooking';
		
		
		$holidayhtml='<div id="resultSetContainer" style="width:100%;">
        <table width="975" cellspacing="0" cellpadding="0" border="0" style="margin:15px 0 0 0; border:1px solid #ccc;" id="agentresult">
            <tr>
            <th class="my_profile_name_ex_tab">Booking Number</th>
            <th class="my_profile_name_ex_tab">PRN Number</th>
            <th class="my_profile_name_ex_tab">Guest Name</th>
            <th class="my_profile_name_ex_tab">Booking Date</th>
            <th class="my_profile_name_ex_tab">Form Date</th>
            <th class="my_profile_name_ex_tab">To Date</th>
            <th class="my_profile_name_ex_tab">Status</th>
            <th class="my_profile_name_ex_tab">Amount</th>
            <th class="my_profile_name_ex_tab">Net Amount</th>
            <th class="my_profile_name_ex_tab">Margin</th>
          </tr>';
          
            if (isset($book_details)) {
			if(count($book_details) > 0 ){
			foreach($book_details as $val)
			{	
				$holidayhtml.='<tr>
				  
				  <td>'.$val->holi_bookno.'</td>
				  <td>'.$val->holi_pnrno.'</td>
				  <td>'.$val->holi_custname.'</td>
				  <td>'.$val->create_date.'</td>
				  <td>'.$val->form_date.'</td>
				  <td>'.$val->to_date.'</td>
				   <td>';
				   if($val->holi_payment_status ==1){
					 $holidayhtml.= "Compeleted";
				   }else { 
					 $holidayhtml.= "Pending";					   
				   }
				   
				    
				  $holidayhtml.='</td>
				  <td>'.$val->holi_packprice.'</td>
				  <td>'.intval($val->holi_total_amount).'</td>
				  <td>'.$val->agent_markup.'</td></tr>';
				 								
								}
								
							}else{
							 $holidayhtml.='<tr>
            <td align="center" valign="top" colspan="11" class="my_profile_name_ex_tab_whit_ex">No Result Found... </td>
          </tr>';
             
							}
								
								
							}
						else
						{
					
              $holidayhtml.='<tr>
            <td align="center" valign="top" colspan="11" class="my_profile_name_ex_tab_whit_ex">No Result Found... </td>
          </tr>';
              
						}
				
            $holidayhtml.='</table>';
          
          
		
		$this->load->helper(array('my_pdf_helper')); 
	 create_pdf($holidayhtml,$filename); 
	
		
		
		
		
		
	}
	
	
	public function generateflightPDF($id){
			$flighthtml = '';
			$data['ticketinfo'] = $this->Report_Model->getvoucherdata($id); 
			/*echo "<pre/>";
			print_r($data);
			exit;*/
			if($data['ticketinfo'][0]['dom_int'] == 'domestic'){
				//$this->load->view('report/ticket_dom',$data);
				$flighthtml .= '<div style="width:960px;height:auto;margin:0 auto;">
            <div  style="width:960px;height:80px;float:left;">
                <div style="width:257px;height:80px;float:left;"><img src="'.base_url().'images/logo.jpg"/></div>
            </div>
			<div style="width:960px; height:auto;float:left;">
                <div style="width:960px;height:20px;float:left;background-color:#CCC;">
                    <h1 style="	font-family:Verdana, Geneva, sans-serif;font-size:12px;color:#000;font-weight:bold;margin-top:5px;margin-left:10px">eTicket Itinerary / Receipt</h1>
                </div>
                <div style="width:960px;height:auto;float:left;margin-top:10px;">
					<td style="align:left"><div style="width:389px;height:auto;float:left;margin-left:10px;">
						<div style="width:400px;height:25px;float:left;">
                            <div style="width:100px;height:25px;float:left;font-family:Verdana, Geneva, sans-serif;font-size:12px;color:#000;">Issuing Airline</div>
                            <div style="width:10px;	height:25px;float:left;">:</div>
                            <div style="width:190px;height:25px;float:left;	font-family:Verdana, Geneva, sans-serif;font-size:12px;color:#0">'.$data['ticketinfo'][0]['tbo_source'].'</div>
                        </div>
                        <div style="width:400px;height:25px;float:left;"><!-- box1 start -->
                            <div style="width:100px;height:25px;float:left;font-family:Verdana, Geneva, sans-serif;font-size:12px;color:#000;">
                                Place of issue</div>
                            <div style="width:10px;	height:25px;float:left;">:</div>
                            <div style="width:190px;height:25px;float:left;	font-family:Verdana, Geneva, sans-serif;font-size:12px;	color:#000;">globalwings.asia</div>
                        </div><!-- box1 end -->
                        <div style="width:400px;height:25px;float:left;"><!-- box1 start -->
                            <div style="width:100px;height:25px;float:left;font-family:Verdana, Geneva, sans-serif;font-size:12px;color:#000;">Date of issue</div>
                            <div style="width:10px;	height:25px;float:left;">:</div>
                            <div style="width:190px;height:25px;float:left;font-family:Verdana, Geneva, sans-serif;font-size:12px;color:#000;">'.date("l j F Y", strtotime($data['ticketinfo'][0]['booked_date'])).'</div>
                        </div><!-- box1 end -->
                        <div style="	width:960px;height:auto;float:left;">
                            <h1 style="font-family:Verdana, Geneva, sans-serif;font-size:12px;color:#000;font-weight:bold;margin-top:5px;padding-bottom:7px;">Booking Reference (PNR)</h1>
                            <h2 style="	font-family:Verdana, Geneva, sans-serif;font-size:30px;color:#000;font-weight:bold;margin-top:5px;">'.$data['ticketinfo'][0]['tbo_pnr'].'</h2>
                        </div>
                    </div></td>
					<td style="align:left;valign:top"><div style="width:560px;height:auto;float:left;">
                        <div style="width:263px;height:auto;float:right;valign:top;"><img src="'.base_url().'images/code.jpg" width="263" height="43" /></div>
                    </div>
                </div>
            </div>
            <div style="width:960px;height:auto;float:left;">
                <div style="	width:960px;height:25px;float:left;background-color:#CCC;"><!-- PassengerItinerary_Details_table heading start -->
                    <h1 style="font-family:Verdana, Geneva, sans-serif;font-size:12px;color:#000;font-weight:bold;margin-top:5px;margin-left:10px;">Passenger / Itinerary Details</h1>
                </div><!-- PassengerItinerary_Details_table heading end -->
                <div style="	width:950px;height:auto;float:left;margin-top:10px;margin-left:10px;"><!-- box 1 start -->
                    <div style="width:500px;height:15px;float:left;font-weight:bold; font-family:Verdana, Geneva, sans-serif;font-size:12px;">Passenger Name</div>
                    <div style="width:150px;height:15px;float:left;font-weight:bold;font-family:Verdana, Geneva, sans-serif;font-size:12px;">Frequent Flyer #</div>
                    <div style="	width:250px;height:15px;float:left;text-align:right;font-weight:bold;font-family:Verdana, Geneva, sans-serif;font-size:12px;">eTicket #</div>
                </div><!-- box 1 end -->
				<div style="	width:950px;height:auto;float:left;margin-top:10px;margin-left:10px;"><!-- box 1 start -->
					<div style="	width:500px;height:15px;float:left;font-weight:normal;font-family:Verdana, Geneva, sans-serif;font-size:12px;">'.$data['ticketinfo'][0]['lead_pax_name'].'</div>
					<div style="	width:150px;height:15px;float:left;font-weight:normal;font-size:12px;">Frequent Flyer #</div>
					<div style="	width:250px;height:15px;float:left;text-align:right;font-weight:normal;font-family:Verdana, Geneva, sans-serif;font-size:12px;"></div>
				</div><!-- box 1 end -->
				<div style="	width:950px;height:auto;float:left;margin-top:10px;margin-left:10px;"><!-- box 1 start -->
					<div style="	width:500px;height:25px;float:left;font-weight:normal;font-family:Verdana, Geneva, sans-serif;font-size:12px;"></div>
					<div style="	width:150px;height:25px;float:left;font-weight:normal;font-size:12px;">Frequent Flyer #</div>
					<div style="	width:250px;height:25px;float:left;text-align:right;font-weight:normal;font-family:Verdana, Geneva, sans-serif;font-size:12px;"></div>
				</div><!-- box 1 end -->
				<div style="width:950px;height:auto;float:left;margin-left:10px; margin-top: 15px; border-top:1px solid #ccc;border-bottom:1px solid #ccc;"><!-- box 2 start -->
				<div style="width:130px;height:25px;float:left;border-right:1px solid #ccc;">
					<h1 style="font-size:12px;margin-top:4px;font-family:Verdana, Geneva, sans-serif;text-align:center; font-weight:normal;">Date</h1></div>
				<div  style="	width:130px;height:25px;float:left;border-right:1px solid #ccc;text-align:center;font-size:12px;">
					<h1 style="font-size:12px;font-family:Verdana, Geneva, sans-serif;margin-top:4px; font-weight:normal;text-align:center;">Dep Time</h1></div>
				<div style="width:190px;height:25px;float:left;border-right:1px solid #ccc;text-align:center;">
					<h1 style="font-size:12px;margin-top:4px;text-align:center;font-family:Verdana, Geneva, sans-serif; font-weight:normal;">From</h1></div>
				<div style="width:190px;height:25px;float:left;border-right:1px solid #ccc;text-align:center;">
					<h1 style="font-size:12px;margin-top:4px;font-family:Verdana, Geneva, sans-serif; font-weight:normal;text-align:center;">To</h1></div>
				<div style="width:100px;height:25px;float:left;	border-right:1px solid #ccc;text-align:center;">
					<h1 style="font-size:12px;margin-top:4px;font-family:Verdana, Geneva, sans-serif; font-weight:normal;text-align:center;">Flight No.</h1></div>    
				<div style="	width:130px;height:25px;float:left;text-align:center;">
					<h1 style="font-size:12px;font-family:Verdana, Geneva, sans-serif;margin-top:4px;font-family:Verdana, Geneva, sans-serif; font-weight:normal;text-align:center;">Airline</h1>
				</div>
			</div><!-- box 2 end -->
			<div style="width:950px;height:auto;float:left;margin-left:10px;border-bottom:1px solid #ccc;"><!-- box 2 start -->
			<div style="width:130px;height:25px;float:left;border-right:1px solid #ccc;">
				<h1 style="font-size:12px;font-family:Verdana, Geneva, sans-serif; font-weight:normal;margin-top:4px;text-align:center;">'.date("j M Y", strtotime($data['ticketinfo'][0]['fromDate'])).'</h1>
			</div>
			<div  style="width:130px;height:25px;float:left;border-right:1px solid #ccc;font-family:Verdana, Geneva, sans-serif; font-weight:normal;text-align:center;font-size:12px;">
				<h1 style="font-size:12px;margin-top:4px;text-align:center;font-family:Verdana, Geneva, sans-serif; font-weight:normal;">15:20:00 hrs</h1>
			</div>
			<div style="width:190px;height:25px;float:left;border-right:1px solid #ccc;text-align:center;">';
			$this->db->select('city');
			$this->db->from('city_int');
			$this->db->where('city_code', $data['ticketinfo'][0]['origin']);
			$query = $this->db->get();
			$fromcity = $query->row('city');
			
			$this->db->select('city');
			$this->db->from('city_int');
			$this->db->where('city_code', $data['ticketinfo'][0]['destination']);
			$query = $this->db->get();
			$tocity = $query->row('city');
			$flighthtml .= '<h1 style="font-size:12px;margin-top:4px;text-align:center;font-family:Verdana, Geneva, sans-serif; font-weight:normal;">'.$fromcity.'</h1>
			</div>
			<div style="width:190px;height:25px;float:left;border-right:1px solid #ccc;text-align:center;">
				<h1 style="font-size:12px;margin-top:4px;font-family:Verdana, Geneva, sans-serif; font-weight:normal;text-align:center;">'.$tocity.'</h1>
			</div>
			<div style="width:100px;height:25px;float:left;border-right:1px solid #ccc;	text-align:center;">
				<h1 style="font-size:12px;margin-top:4px;font-family:Verdana, Geneva, sans-serif; font-weight:normal;text-align:center;">TBO</h1>
			</div>    
			<div style="width:130px;height:25px;float:left;text-align:center;">
				<h1 style="font-size:12px;margin-top:4px;font-family:Verdana, Geneva, sans-serif; font-weight:normal;text-align:center;">TBO Flight</h1>
			</div>
		</div><!-- box 2 end -->
<table><tr><td>&nbsp;</td></tr></table>
</div><!-- PassengerItinerary_Details_table end -->

<div style="width:960px;height:auto;float:left;"> <!-- Detailed Itinerary start -->
    <div  style="width:960px;height:auto;float:left;"><!-- heading start -->
        <div style="width:960px;height:25px;float:left;background-color:#CCC;">
            <h1 style="	font-family:Verdana, Geneva, sans-serif;font-size:12px;color:#000;font-weight:bold;margin-top:5px;margin-left:10px;">Detailed Itinerary</h1>
        </div>
    </div><!-- heading end -->
	<div style="width:950px;height:auto;float:left;margin-top:10px;	margin-left:10px;border-top:1px solid #ccc;border-bottom:1px solid #ccc;"><!--- new box1 start -->
        <div style="	width:80px;height:35px;float:left;border-right:1px solid #ccc;">
            <h1 style="font-size:12px;margin-top:4px;text-align:center;font-family:Verdana, Geneva, sans-serif; font-weight:normal;">Flight</h1>
        </div>
        <div style="width:180px;height:35px;float:left;border-right:1px solid #ccc;">
            <h1 style="font-size:12px;margin-top:4px;text-align:center;font-family:Verdana, Geneva, sans-serif; font-weight:normal;">Depart</h1>
        </div>
        <div  style="width:180px;height:35px;float:left;border-right:1px solid #ccc;"><h1 style="font-size:12px;margin-top:4px;text-align:center;font-family:Verdana, Geneva, sans-serif; font-weight:normal;">Arrive</h1></div>                                                                                                
        <div style="width:80px;height:35px;	float:left;border-right:1px solid #ccc;"><h1 style="font-size:12px;margin-top:4px;text-align:center;font-family:Verdana, Geneva, sans-serif; font-weight:normal;">Fare Basis</h1></div>
        <div style="width:80px;height:35px;float:left;border-right:1px solid #ccc;"><h1 style="font-size:12px;margin-top:4px;text-align:center;font-family:Verdana, Geneva, sans-serif; font-weight:normal;">NVB</h1></div>
        <div style="width:80px;	height:35px;float:left;border-right:1px solid #ccc;"><h1 style="font-size:12px;margin-top:4px;text-align:center;font-family:Verdana, Geneva, sans-serif; font-weight:normal;">NVA</h1></div>
        <div style="width:80px;height:35px;	float:left;border-right:1px solid #ccc;"><h1 style="font-size:12px;margin-top:4px;text-align:center;font-family:Verdana, Geneva, sans-serif; font-weight:normal;">Status</h1></div>
        <div style="width:80px;height:35px;float:left;border-right:1px solid #ccc;"><h1 style="font-size:12px;margin-top:4px;text-align:center;font-family:Verdana, Geneva, sans-serif; font-weight:normal;">Duration Stops</h1></div>
        <div style="width:80px;height:35px;float:left;"><h1 style="font-size:12px;margin-top:4px;text-align:center;font-family:Verdana, Geneva, sans-serif; font-weight:normal;">Baggage</h1></div>
   </div><!--- new box1 end -->
   <div style="width:950px;height:auto;float:left;	margin-left:10px;border-bottom:1px solid #ccc;"><!--- new box1 start -->
        <div style="width:80px;	height:50px;float:left;border-right:1px solid #ccc;"><h1 style="font-size:12px;margin-top:4px;text-align:center;font-family:Verdana, Geneva, sans-serif; font-weight:normal;"><img src="'.base_url().'images/flight.png" width="40" height="30" /><br>TBO</h1></div>
        <div style="width:180px;height:50px;float:left;border-right:1px solid #ccc;"><h1 style="font-size:12px;margin-top:4px;text-align:center;font-family:Verdana, Geneva, sans-serif; font-weight:normal;">'.$fromcity.' - '.date("j M Y", strtotime($data['ticketinfo'][0]['fromDate'])).' 15:20:00</h1></div>
        <div style="width:180px;height:50px;float:left;border-right:1px solid #ccc;"><h1 style="font-size:12px;margin-top:4px;text-align:center;font-family:Verdana, Geneva, sans-serif; font-weight:normal;">'.$tocity.' - '.date("j M Y", strtotime($data['ticketinfo'][0]['toDate'])).' 19:20:00</h1></div>
        <div style="width:80px;height:50px;	float:left;	border-right:1px solid #ccc;"><h1 style="font-size:12px;margin-top:4px;text-align:center;font-family:Verdana, Geneva, sans-serif; font-weight:normal;">INR '.$data['ticketinfo'][0]['BF'].'</h1></div>
        <div style="width:80px;height:50px;float:left;border-right:1px solid #ccc;"><h1 style="font-size:12px;margin-top:4px;text-align:center;font-family:Verdana, Geneva, sans-serif; font-weight:normal;">NVB</h1></div>
        <div style="width:80px;	height:50px;float:left;border-right:1px solid #ccc;"><h1 style="font-size:12px;margin-top:4px;text-align:center;font-family:Verdana, Geneva, sans-serif; font-weight:normal;">NVB</h1></div>
        <div style="width:80px;height:50px;float:left;border-right:1px solid #ccc;"><h1 style="font-size:12px;margin-top:4px;text-align:center;font-family:Verdana, Geneva, sans-serif; font-weight:normal;">'.$data['ticketinfo'][0]['status'].'</h1></div>
        <div style="width:80px;height:50px;	float:left;	border-right:1px solid #ccc;"><h1 style="font-size:12px;margin-top:4px;text-align:center;font-family:Verdana, Geneva, sans-serif; font-weight:normal;">1h 30m/ 0 stops</h1></div>
        <div style="width:80px;height:50px;	float:left;"><h1 style="font-size:12px;margin-top:4px;text-align:center;font-family:Verdana, Geneva, sans-serif; font-weight:normal;">5</h1></div>
	</div><!--- new box1 end -->
</div><!--Detailed Itinerary End -->
	<div style="width:960px; margin-top: 20px;height:auto;float:left;"><!-- RedemptionDetails_Page Start -->
    <div style="width:960px;height:auto;float:left;"><!-- heading start -->
		<div  style="width:960px;height:25px;float:left;background-color:#CCC;">
            <h1 style="font-family:Verdana, Geneva, sans-serif;	font-size:12px;	color:#000;font-weight:bold;margin-top:5px;margin-left:10px;">Redemption Details</h1></div></div><!-- heading  End -->
		<div style="width:950px;height:auto;float:left;	margin-top:10px;margin-left:10px;"><!-- Redemption Details  Start -->
		<div style="width:480px;height:auto;float:left;"><!-- Left start  000000000001-->
			<div  style="width:480px;height:auto;float:left;"><!-- 1 start -->
                <h1 style="	font-family:Verdana, Geneva, sans-serif;font-size:12px;	color:#000;	font-weight:bold;">Fare</h1>
            </div><!-- 1 End -->
        </div><!-- Left end -->

        <div style="width:380px;height:auto;float:left;"><!-- Right start -->
            <h1></h1>
        </div><!-- Rigtht end   0000000000001-->

        <div style="width:480px;height:auto;float:left;"><!-- Left start  000000000001-->

            <div style="width:480px;height:auto;float:left;"><!-- 1 start -->
                <h1 style="	font-family:Verdana, Geneva, sans-serif;font-size:12px;color:#000;font-weight:normal;	">
                    [Fare(s) include Base Fare + Airline Fuel Charge</h1>
            </div><!-- 1 End -->
        </div><!-- Left end -->

        <div style="width:380px;height:auto;float:left;"><!-- Right start -->
            <h1 style="	font-family:Verdana, Geneva, sans-serif;font-size:12px;	color:#000;	font-weight:bold;">INR '.number_format($data['ticketinfo'][0]['BF'], '2').'</h1>
        </div><!-- Rigtht end   0000000000001-->
		<div style="width:480px;height:auto;float:left;"><!-- Left start  000000000001-->
			<div  style="width:480px;height:auto;float:left;"><!-- 1 start -->
                <h1 style="	font-family:Verdana, Geneva, sans-serif;font-size:12px;	color:#000;	font-weight:bold;">Tax</h1>
            </div><!-- 1 End -->
        </div><!-- Left end -->
        <div style="width:380px;height:auto;float:left;"><!-- Right start -->
            <h1 style="	font-family:Verdana, Geneva, sans-serif;font-size:12px;	color:#000;	font-weight:bold;"></h1>
        </div><!-- Rigtht end   0000000000001-->
		<div style="width:480px;height:auto;float:left;"><!-- Left start  000000000001-->
			<div style="width:480px;height:auto;float:left;"><!-- 1 start -->
                <h1 style="	font-family:Verdana, Geneva, sans-serif;font-size:12px;color:#000;font-weight:normal;	">Service Tax (JN)</h1>
            </div><!-- 1 End -->
        </div><!-- Left end -->

        <div  style="width:380px;height:auto;float:left;"><!-- Right start -->
            <h1 style="	font-family:Verdana, Geneva, sans-serif;font-size:12px;	color:#000;	font-weight:normal;">INR '.number_format($data['ticketinfo'][0]['ST'], '2').'</h1>
        </div><!-- Rigtht end   0000000000001-->
			<div style="width:480px;height:auto;float:left;"><!-- Left start  000000000001-->

            <div  style="width:480px;height:auto;float:left;"><!-- 1 start -->
                <h1 style="	font-family:Verdana, Geneva, sans-serif;font-size:12px;	color:#000;	font-weight:bold;">Fees</h1>
            </div><!-- 1 End -->
        </div><!-- Left end -->
        <div  style="width:380px;height:auto;float:left;"><!-- Right start -->
            <h1 style="	font-family:Verdana, Geneva, sans-serif;font-size:12px;	color:#000;	font-weight:bold;"></h1>
        </div><!-- Rigtht end   0000000000001-->

        <div style="width:480px;height:auto;float:left;"><!-- Left start  000000000001-->

            <div style="width:480px;height:auto;float:left;"><!-- 1 start -->
                <h1 style="	font-family:Verdana, Geneva, sans-serif;font-size:12px;color:#000;font-weight:normal;">Passenger Service Fee (WO)</h1>
            </div><!-- 1 End -->
        </div><!-- Left end -->

        <div  style="width:380px;height:auto;float:left;"><!-- Right start -->
            <h1 style="	font-family:Verdana, Geneva, sans-serif;font-size:12px;	color:#000;	font-weight:normal;">INR '.number_format($data['ticketinfo'][0]['AT'], '2').'</h1>
        </div><!-- Rigtht end   0000000000001-->
        
        <div style="width:480px;height:auto;float:left;"><!-- Left start  000000000001-->

            <div  style="width:480px;height:auto;float:left;"><!-- 1 start -->
                <h1 style="	font-family:Verdana, Geneva, sans-serif;font-size:12px;	color:#000;	font-weight:bold;">TOTAL TRIP COST<br />
                    <span style=" font-weight:normal;">(including Fare, Tax & Fees</span>)</h1>
            </div><!-- 1 End -->
        </div><!-- Left end -->
        <div  style="width:380px;height:auto;float:left;"><!-- Right start -->
            <h1 style="	font-family:Verdana, Geneva, sans-serif;font-size:12px;	color:#000;	font-weight:bold;">INR '.number_format($data['ticketinfo'][0]['user_fare'], '2').'</h1>
        </div><!-- Rigtht end   0000000000001-->
		<div style="width:960px;height:auto;float:left;"><!-- heading start -->
			<div  style="width:960px;height:25px;float:left;">
                <h1 style="font-family:Verdana, Geneva, sans-serif;	font-size:12px;	color:#000;
                    font-weight:normal;margin-top:5px;margin-left:10px;">* Convenience fee is an additional non-refundable charge that is applicable per guest.</h1></div></div><!-- heading  End -->
		<div style="width:960px;height:auto;float:left;"><!-- heading start -->
			<div  style="width:960px;height:25px;float:left;">
                <h1 style="font-family:Verdana, Geneva, sans-serif;	font-size:12px;	color:#000;font-weight:normal;margin-top:5px;margin-left:10px;">Total Amount is inclusive of service tax, wherever applicable.</h1></div></div><!-- heading  End -->
			</div>
		</div>
	<div style="width:960px;height:auto;float:left; ;"><!-- Important Notes Page start -->
	<div  style="width:960px;height:auto;float:left;"><!-- heading start -->
		<div style="width:960px;height:25px;float:left;background-color:#CCC;">
            <h1 style="	width:590px;font-family:Verdana, Geneva, sans-serif;font-size:12px;color:#000;font-weight:bold;margin-top:5px;margin-left:10px; float:left;">Important Notes</h1>
		</div>
    </div><!-- heading end -->
    <div style="width:930px;height:auto;margin-top:2px; margin-left:10px; padding-bottom:15px; padding-top:10px;float:left;font-family:Verdana, Geneva, sans-serif; line-height:20px;	font-size:12px;	color:#000;"><!-- page start -->
		This is an eTicket itinerary. To enter the airport and for check-in, you must present the itinerary receipt along with valid photo
        identification, viz: Official Government issued photo identification, driving license, election photo id, passport (for international
        passengers) and photo credit card. It is mandatory to carry your photo identification during your entire journey.<br />
	</div><!-- page end -->
        <div  style="width:960px;height:auto;float:left;"><!-- heading start -->
            <div style="width:960px;height:25px;float:left;">
                <h1 style="	width:590px;font-family:Verdana, Geneva, sans-serif;font-size:12px;color:#000;font-weight:bold;margin-top:5px;margin-left:10px; float:left;">Reprint of eTicket(s)</h1>
            </div>
        </div><!-- heading end -->
        <div style="width:930px;height:auto;margin-top:2px; margin-left:10px; padding-bottom:15px; padding-top:10px;float:left;font-family:Verdana, Geneva, sans-serif; line-height:20px;	font-size:12px;	color:#000;"><!-- page start -->
            If you require a reprint of your eTicket, we recommend you use our Manage Booking feature on jetairways.com or request our
            ticketing staff to email the same at no cost. Please note guest(s) will be charged INR 50 for every reprint requested at our
            City or Airport ticketing offices located in India.
        </div><!-- page end -->
        <div style="width:960px;height:auto;float:left;"><!-- Important Notes Page start -->
            <div  style="width:960px;height:auto;float:left;"><!-- heading start -->
                <div style="width:960px;height:25px;float:left;">
                    <h1 style="	width:590px;font-family:Verdana, Geneva, sans-serif;font-size:12px;color:#000;font-weight:bold;margin-top:5px;margin-left:10px; float:left;">Check-in</h1>
                </div>
            </div><!-- heading end -->
            <div style="width:930px;height:auto;margin-top:2px; margin-left:10px; padding-bottom:15px; padding-top:10px;float:left;font-family:Verdana, Geneva, sans-serif; line-height:20px;	font-size:12px;	color:#000;"><!-- page start -->
                Check-in counters for flights within India* will now close 45 minutes prior to flight departure and the boarding gate(s)
                closes 25 minutes prior to departure. For our guests who wish to Tele Check-in, please ensure that you collect your
                boarding pass for your assigned seat no later than 50 minutes prior to departure. Check-in counters for International flights
                continue to close 60 minutes prior to flight departure.<br />

                *Applicable only for flights from Mumbai, Hyderabad, Chennai, Bengaluru, Kolkata and Delhi
            </div><div style="width:960px;height:auto;float:left;"><!-- Important Notes Page start -->
                <div  style="width:960px;height:auto;float:left;"><!-- heading start -->
                    <div style="width:960px;height:25px;float:left;">
                        <h1 style="	width:590px;font-family:Verdana, Geneva, sans-serif;font-size:12px;color:#000;font-weight:bold;margin-top:5px;margin-left:10px; float:left;">Meals, Baggage & Lounge</h1>
                    </div>
                </div><!-- heading end -->
                <div style="width:930px;height:auto;margin-top:2px; margin-left:10px; padding-bottom:15px; padding-top:10px;float:left;font-family:Verdana, Geneva, sans-serif; line-height:20px;	font-size:12px;	color:#000;"><!-- page start -->

                    Meals are applicable for guests traveling on Jet Airways, and in Première on JetKonnect flights.
                    In case of piece concept, free baggage allowance per piece is 23kgs.
                    Cabin baggage should not exceed 7 kgs in weight and 115 linear cms. Guests traveling on flights originating from Jammu,
                    Srinagar and Leh will not be allowed to carry any cabin baggage.
                    Jet Airways levies a fee for carriage of Television & Computer LCD/LED Screens.
                    Please refer to jetairways.com for information on lounge access.<br />

                    Please contact the respective airline(s), or alternatively visit their website(s), to know more about your entitlement on Baggage
                    Allowance, Meal(s) and Special Request(s) on flights operated by our Codeshare / Interline partners.

                </div><!-- page end -->


                <div  style="width:960px;height:auto;float:left;"><!-- heading start -->
					<div style="width:930px;height:auto; float:left;"><img src="'.base_url().'images/cabin.jpg"/></div>
                </div>
            </div><!-- heading end -->
            <div style="width:960px;height:auto;float:left;"><!-- Important Notes Page start -->
                <div  style="width:960px;height:auto;float:left;"><!-- heading start -->
                    <div style="width:960px;height:25px;float:left;">
                        <h1 style="	width:590px;font-family:Verdana, Geneva, sans-serif;font-size:12px;color:#000;font-weight:bold;margin-top:5px;margin-left:10px; float:left;">Security Requirement</h1>
                    </div>
                </div><!-- heading end -->
                <div style="width:930px;height:auto;margin-top:2px; margin-left:10px; padding-bottom:15px; padding-top:10px;float:left;font-family:Verdana, Geneva, sans-serif; line-height:20px;	font-size:12px;	color:#000;"><!-- page start -->

                    . The card used to purchase the tickets will have to be produced at the time of Check-in.<br />

                    . If the holder of the card is not the passenger, then the passenger should possess:<br />

                    &nbsp;&nbsp;a. A photocopy of both sides of the card, which will have to be self attested by the card holder authorising the use
                    of the card for the purchase of the ticket. For security reasons, please strike out the Card Verification Value (CVV) code on
                    the copy of your card.<br />

                    &nbsp;&nbsp;b. This photocopy should also contain the name of the passenger, the date of journey and the sector on which the journey is
                    made.<br /><br />


                    The above document MUST be produced at the time of check-in. If the passenger fails to comply with these conditions, Jet Airways
                    reserves the right to deny the passenger(s) from boarding.
                    The details mentioned above do not apply for Net Banking / Cash Card and Cash on Delivery.<br /><br />


                    For International travel, please ensure that the validity of the passport is as per the requirements of the destination country.<br />

                    Due to security reasons, liquids, aerosols and gels (LAGs) in carry-on baggage are restricted to containers of 100ml each. At
                    some airports, duty- free LAGs may be purchased after screening checkpoints. At most airports, including Indian airports,
                    transit guests are not allowed to carry duty-free LAGs purchased on a previous sector in cabin baggage, these will be
                    confiscated at the Security Checkpoint.<br /><br />


                    For carriage of arms, ammunition, prohibited, restricted articles, please refer to our terms and conditions.
                    Please keep your valuables in your hand baggage for precaution measures.

                </div><!-- page end -->

                        <!-- Payment Details start -->

                        <div style="width:960px;height:auto;float:left;"><!-- Important Notes Page start -->

                            <div  style="width:960px;height:auto;float:left;"><!-- heading start -->

                                <div style="width:960px;height:auto;float:left;">
                                    <h1 style="	width:960px;font-family:Verdana, Geneva, sans-serif;font-size:12px;color:#000;font-weight:normal;
                                        margin-top:5px;margin-left:10px; float:left; text-align:center;">For any queries please write to us at globalwings.asia</h1>
                                    <h1 style="	width:960px;font-family:Verdana, Geneva, sans-serif;font-size:12px;color:#000;font-weight:bold;
                                        margin-top:5px;margin-left:10px;text-align:center; float:left;">Thank you for choosing Globalwings.asia. We wish you a pleasant journey</h1>    
                                </div>
                            </div><!-- heading end -->
                            <div style="width:930px;height:auto;margin-top:15px margin-left:15px; padding-bottom:15px; padding-top:10px;float:left;font-family:Verdana, Geneva, sans-serif; line-height:20px;	font-size:12px;	color:#000;"><!-- page start -->                                
                            </div><!-- page end -->
                        </div><!-- Important Notes Page start -->
                        <!-- pae 2 end -->
                    </div><!-- wrapper end -->';
                   
				$filename = 'PDFFLIGHTTICKET'.$id;
				$this->load->helper(array('my_pdf_helper'));   //  Load helper
				//$data = file_put_contents($html); // Pass the url of html report    base_url().'application/view/report/voucher_print.php'
				create_pdf($flighthtml,$filename); //Create pdf
            }else if($data['ticketinfo'][0]['dom_int'] == 'international'){
				$flighthtml .= '<div style="width:960px;height:auto;margin:0 auto;"><!-- wrapper start -->
            <div style="width:960px;height:99px;float:left;"><!-- Header start -->
                <div style="width:257px;height:99px;float:left;"><img src="'.base_url().'images/logo.jpg"  /></div>
            </div><!-- Header enb -->
			<div style="width:960px; height:auto;float:left;"><!-- eTicketItinerary_Receipt start -->
                <div style="width:960px;height:25px;float:left;background-color:#CCC;">
                    <h1 style="font-family:Verdana, Geneva, sans-serif;font-size:12px;color:#000;font-weight:bold;margin-top:5px;margin-left:10px">eTicket Itinerary / Receipt</h1>
                </div>
                <div style="width:960px;height:auto;float:left;margin-top:10px;"><!-- Table start -->
                    <div style="width:389px;height:auto;float:left;margin-left:10px;"><!-- Left start -->
                        <div style="width:400px;height:25px;float:left;"><!-- box1 start -->
                            <div style="width:100px;height:25px;float:left;font-family:Verdana, Geneva, sans-serif;font-size:12px;color:#000;">Issuing Airline</div>
                            <div style="width:10px;	height:25px;float:left;">:</div>
                            <div style="width:190px;height:25px;float:left;	font-family:Verdana, Geneva, sans-serif;font-size:12px;color:#0">'.$data['ticketinfo'][0]['tbo_source'].'</div>
                        </div><!-- box1 end -->
                        <div style="width:400px;height:25px;float:left;"><!-- box1 start -->
                            <div style="width:100px;height:25px;float:left;font-family:Verdana, Geneva, sans-serif;font-size:12px;color:#000;">
                                Place of issue</div>
                            <div style="width:10px;	height:25px;float:left;">:</div>
                            <div style="width:190px;height:25px;float:left;	font-family:Verdana, Geneva, sans-serif;font-size:12px;	color:#000;">globalwings.asia</div>
                        </div><!-- box1 end -->
                        <div style="width:400px;height:25px;float:left;"><!-- box1 start -->
                            <div style="width:100px;height:25px;float:left;font-family:Verdana, Geneva, sans-serif;font-size:12px;color:#000;">Date of issue</div>
                            <div style="width:10px;	height:25px;float:left;">:</div>
                            <div style="width:190px;height:25px;float:left;font-family:Verdana, Geneva, sans-serif;font-size:12px;color:#000;">'.date("l j F Y", strtotime($data['ticketinfo'][0]['booked_date'])).'</div>
                        </div><!-- box1 end -->
                        <div style="	width:960px;height:auto;float:left;">
                            <h1 style="font-family:Verdana, Geneva, sans-serif;font-size:12px;color:#000;font-weight:bold;margin-top:5px;padding-bottom:7px;">Booking Reference (PNR)</h1>
                            <h2 style="font-family:Verdana, Geneva, sans-serif;font-size:30px;color:#000;font-weight:bold;margin-top:5px;">'.$data['ticketinfo'][0]['tbo_pnr'].'</h2>
                        </div>
                    </div><!-- Left end -->
                    <div style="width:560px;height:auto;float:left;"><!-- Right start -->
                        <div style="width:263px;height:auto;float:right;"><img src="'.base_url().'images/code.jpg" width="263" height="43" /></div>
                    </div><!-- Right end -->
                </div><!-- Table end -->
            </div><!-- eTicketItinerary_Receipt send -->
            <div style="width:960px;height:auto;float:left;"><!-- PassengerItinerary_Details_table start -->
                <div style="width:960px;height:25px;float:left;background-color:#CCC;"><!-- PassengerItinerary_Details_table heading start -->
                    <h1 style="font-family:Verdana, Geneva, sans-serif;font-size:12px;color:#000;font-weight:bold;margin-top:5px;margin-left:10px;">Passenger / Itinerary Details</h1>
                </div><!-- PassengerItinerary_Details_table heading end -->
                <div style="width:950px;height:auto;float:left;margin-top:10px;margin-left:10px;"><!-- box 1 start -->
                    <div style="width:500px;height:15px;float:left;font-weight:bold; font-family:Verdana, Geneva, sans-serif;font-size:12px;">Passenger Name</div>
                    <div style="width:150px;height:15px;float:left;font-weight:bold;font-family:Verdana, Geneva, sans-serif;font-size:12px;">Frequent Flyer #</div>
                    <div style="	width:250px;height:15px;float:left;text-align:right;font-weight:bold;font-family:Verdana, Geneva, sans-serif;font-size:12px;">eTicket #</div>
                </div><!-- box 1 end -->
						<!-- box 1 end -->
						<div style="	width:950px;height:auto;float:left;margin-top:10px;margin-left:10px;"><!-- box 1 start -->
                            <div style="	width:500px;height:25px;float:left;font-weight:normal;font-family:Verdana, Geneva, sans-serif;font-size:12px;">'.$data['ticketinfo'][0]['lead_pax_name'].'</div>
                            <div style="	width:150px;height:25px;float:left;font-weight:normal;font-size:12px;">Frequent Flyer #</div>
                            <div style="	width:250px;height:25px;float:left;text-align:right;font-weight:normal;font-family:Verdana, Geneva, sans-serif;font-size:12px;"></div>
                        </div><!-- box 1 end -->
    <div style="width:950px;height:auto;float:left;margin-left:10px; margin-top: 15px; border-top:1px solid #ccc;border-bottom:1px solid #ccc;"><!-- box 2 start -->
    <div style="width:130px;height:25px;float:left;border-right:1px solid #ccc;">
        <h1 style="font-size:12px;margin-top:4px;font-family:Verdana, Geneva, sans-serif;text-align:center; font-weight:normal;">Date</h1></div>
    <div style="width:130px;height:25px;float:left;border-right:1px solid #ccc;text-align:center;font-size:12px;">
        <h1 style="font-size:12px;font-family:Verdana, Geneva, sans-serif;margin-top:4px; font-weight:normal;text-align:center;">Dep Time</h1></div>
    <div style="width:190px;height:25px;float:left;border-right:1px solid #ccc;text-align:center;">
        <h1 style="font-size:12px;margin-top:4px;text-align:center;font-family:Verdana, Geneva, sans-serif; font-weight:normal;">From</h1></div>
    <div style="width:190px;height:25px;float:left;border-right:1px solid #ccc;text-align:center;">
        <h1 style="font-size:12px;margin-top:4px;font-family:Verdana, Geneva, sans-serif; font-weight:normal;text-align:center;">To</h1></div>
    <div style="width:100px;height:25px;float:left;	border-right:1px solid #ccc;text-align:center;">
        <h1 style="font-size:12px;margin-top:4px;font-family:Verdana, Geneva, sans-serif; font-weight:normal;text-align:center;">Flight No.</h1></div>    
    <div style="width:130px;height:25px;float:left;text-align:center;">
        <h1 style="font-size:12px;font-family:Verdana, Geneva, sans-serif;margin-top:4px;font-family:Verdana, Geneva, sans-serif; font-weight:normal;text-align:center;">Airline</h1>
    </div>
</div><!-- box 2 end -->
	<div style="width:950px;height:auto;float:left;margin-left:10px;border-bottom:1px solid #ccc;"><!-- box 2 start -->
    <div style="width:130px;height:25px;float:left;border-right:1px solid #ccc;">
        <h1 style="font-size:12px;font-family:Verdana, Geneva, sans-serif; font-weight:normal;margin-top:4px;text-align:center;">'.date("j M Y", strtotime($data['ticketinfo'][0]['fromdate'])).'</h1>
    </div>
    <div style="width:130px;height:25px;float:left;border-right:1px solid #ccc;font-family:Verdana, Geneva, sans-serif; font-weight:normal;text-align:center;font-size:12px;">
        <h1 style="font-size:12px;margin-top:4px;text-align:center;font-family:Verdana, Geneva, sans-serif; font-weight:normal;">15:20:00 hrs</h1>
    </div>
    <div style="width:190px;height:25px;float:left;border-right:1px solid #ccc;text-align:center;">';
			$this->db->select('city');
			$this->db->from('city_int');
			$this->db->where('city_code', $data['ticketinfo'][0]['origin']);
			$query = $this->db->get();
			$fromcity = $query->row('city');
			
			$this->db->select('city');
			$this->db->from('city_int');
			$this->db->where('city_code', $data['ticketinfo'][0]['destination']);
			$query = $this->db->get();
			$tocity = $query->row('city');
	$flighthtml .='<h1 style="font-size:12px;margin-top:4px;text-align:center;font-family:Verdana, Geneva, sans-serif; font-weight:normal;">'.$fromcity.'</h1>
    </div>
    <div style="width:190px;height:25px;float:left;border-right:1px solid #ccc;text-align:center;">
        <h1 style="font-size:12px;margin-top:4px;font-family:Verdana, Geneva, sans-serif; font-weight:normal;text-align:center;">'.$tocity.'</h1>
    </div>
    <div style="width:100px;height:25px;float:left;border-right:1px solid #ccc;	text-align:center;">
        <h1 style="font-size:12px;margin-top:4px;font-family:Verdana, Geneva, sans-serif; font-weight:normal;text-align:center;">TBO</h1>
    </div>    
    <div style="width:130px;height:25px;float:left;text-align:center;">
        <h1 style="font-size:12px;margin-top:4px;font-family:Verdana, Geneva, sans-serif; font-weight:normal;	text-align:center;">TBO Flight</h1>
    </div>
</div><!-- box 2 end -->
<table><tr><td>&nbsp;</td></tr></table>
</div><!-- PassengerItinerary_Details_table end -->
<div style="width:960px;height:auto;float:left;"> <!-- Detailed Itinerary start -->
    <div style="width:960px;height:auto;float:left;"><!-- heading start -->
        <div style="width:960px;height:25px;float:left;background-color:#CCC;">
            <h1 style="font-family:Verdana, Geneva, sans-serif;font-size:12px;color:#000;font-weight:bold;margin-top:5px;margin-left:10px;">Detailed Itinerary</h1>
        </div>
    </div><!-- heading end -->
    <div style="width:950px;height:auto;float:left;margin-top:10px;	margin-left:10px;border-top:1px solid #ccc;border-bottom:1px solid #ccc;"><!--- new box1 start -->
        <div style="width:80px;height:35px;float:left;border-right:1px solid #ccc;">
            <h1 style="font-size:12px;margin-top:4px;text-align:center;font-family:Verdana, Geneva, sans-serif; font-weight:normal;">Flight</h1>
        </div>
        <div style="width:180px;height:35px;float:left;border-right:1px solid #ccc;">
            <h1 style="font-size:12px;margin-top:4px;text-align:center;font-family:Verdana, Geneva, sans-serif; font-weight:normal;">Depart</h1>
        </div>
        <div  style="width:180px;height:35px;float:left;border-right:1px solid #ccc;"><h1 style="font-size:12px;margin-top:4px;text-align:center;font-family:Verdana, Geneva, sans-serif; font-weight:normal;">Arrive</h1></div>                                                                                                
        <div style="width:80px;height:35px;	float:left;border-right:1px solid #ccc;"><h1 style="font-size:12px;margin-top:4px;text-align:center;font-family:Verdana, Geneva, sans-serif; font-weight:normal;">Fare Basis</h1></div>
        <div style="width:80px;height:35px;float:left;border-right:1px solid #ccc;"><h1 style="font-size:12px;margin-top:4px;text-align:center;font-family:Verdana, Geneva, sans-serif; font-weight:normal;">NVB</h1></div>
        <div style="width:80px;	height:35px;float:left;border-right:1px solid #ccc;"><h1 style="font-size:12px;margin-top:4px;text-align:center;font-family:Verdana, Geneva, sans-serif; font-weight:normal;">NVA</h1></div>
        <div style="width:80px;height:35px;	float:left;border-right:1px solid #ccc;"><h1 style="font-size:12px;margin-top:4px;text-align:center;font-family:Verdana, Geneva, sans-serif; font-weight:normal;">Status</h1></div>
        <div style="width:80px;height:35px;float:left;border-right:1px solid #ccc;"><h1 style="font-size:12px;margin-top:4px;text-align:center;font-family:Verdana, Geneva, sans-serif; font-weight:normal;">Duration Stops</h1></div>
        <div style="width:80px;height:35px;float:left;"><h1 style="font-size:12px;margin-top:4px;text-align:center;font-family:Verdana, Geneva, sans-serif; font-weight:normal;">Baggage</h1></div>
	</div><!--- new box1 end -->

    <div style="width:950px;height:auto;float:left;	margin-left:10px;border-bottom:1px solid #ccc;"><!--- new box1 start -->
        <div style="width:80px;	height:50px;float:left;border-right:1px solid #ccc;"><h1 style="font-size:12px;margin-top:4px;text-align:center;font-family:Verdana, Geneva, sans-serif; font-weight:normal;"><img src="'.base_url().'images/flight.png" width="40" height="30" /><br>TBO</h1></div>
        <div style="width:180px;height:50px;float:left;border-right:1px solid #ccc;"><h1 style="font-size:12px;margin-top:4px;text-align:center;font-family:Verdana, Geneva, sans-serif; font-weight:normal;">'.$fromcity." - ".date("j M Y", strtotime($data['ticketinfo'][0]['fromDate'])).' 15:20:00</h1></div>
        <div style="width:180px;height:50px;float:left;border-right:1px solid #ccc;"><h1 style="font-size:12px;margin-top:4px;text-align:center;font-family:Verdana, Geneva, sans-serif; font-weight:normal;">'.$tocity." - ".date("j M Y", strtotime($data['ticketinfo'][0]['toDate'])).' 19:20:00</h1></div>
        <div style="width:80px;height:50px;	float:left;	border-right:1px solid #ccc;"><h1 style="font-size:12px;margin-top:4px;text-align:center;font-family:Verdana, Geneva, sans-serif; font-weight:normal;">INR '.$data['ticketinfo'][0]['BF'].'</h1></div>
        <div style="width:80px;height:50px;float:left;border-right:1px solid #ccc;"><h1 style="font-size:12px;margin-top:4px;text-align:center;font-family:Verdana, Geneva, sans-serif; font-weight:normal;">NVA</h1></div>
        <div style="width:80px;	height:50px;float:left;border-right:1px solid #ccc;"><h1 style="font-size:12px;margin-top:4px;text-align:center;font-family:Verdana, Geneva, sans-serif; font-weight:normal;">NVB</h1></div>
        <div style="width:80px;height:50px;float:left;border-right:1px solid #ccc;"><h1 style="font-size:12px;margin-top:4px;text-align:center;font-family:Verdana, Geneva, sans-serif; font-weight:normal;">'.$data['ticketinfo'][0]['status'].'</h1></div>
        <div style="width:80px;height:50px;	float:left;	border-right:1px solid #ccc;"><h1 style="font-size:12px;margin-top:4px;text-align:center;font-family:Verdana, Geneva, sans-serif; font-weight:normal;">1h 30m / 0 stops</h1></div>
        <div style="width:80px;height:50px;	float:left;"><h1 style="font-size:12px;margin-top:4px;text-align:center;font-family:Verdana, Geneva, sans-serif; font-weight:normal;">5</h1></div>
	</div><!--- new box1 end -->
</div>
<div style="width:960px; margin-top: 20px;height:auto;float:left;">
    <div style="width:960px;height:auto;float:left;">
        <div style="width:960px;height:25px;float:left;background-color:#CCC;">
            <h1 style="font-family:Verdana,Geneva,sans-serif;font-size:12px;color:#000;font-weight:bold;margin-top:5px;margin-left:10px;">Redemption Details</h1>
        </div>
    </div>
    <div style="width:950px;height:auto;float:left;	margin-top:10px;margin-left:10px;">
        <div style="width:480px;height:auto;float:left;">
            <div style="width:480px;height:auto;float:left;">
                <h1 style="font-family:Verdana, Geneva, sans-serif;font-size:12px;color:#000;font-weight:bold;">Fare</h1>
            </div>
        </div>
        <div style="width:480px;height:auto;float:left;">
            <div style="width:480px;height:auto;float:left;">
                <h1 style="font-family:Verdana,Geneva,sans-serif;font-size:12px;color:#000;font-weight:normal;">[Fare(s) include Base Fare + Airline Fuel Charge</h1>
            </div>
        </div>
		<div style="width:380px;height:auto;float:left;">
            <h1 style="font-family:Verdana,Geneva,sans-serif;font-size:12px;color:#000;font-weight:bold;">INR '.number_format($data['ticketinfo'][0]['BF'], '2').'</h1>
        </div>
        <div style="width:480px;height:auto;float:left;">
            <div style="width:480px;height:auto;float:left;">
                <h1 style="font-family:Verdana,Geneva,sans-serif;font-size:12px;color:#000;font-weight:bold;">Tax</h1>
            </div>
        </div>
        <div style="width:380px;height:auto;float:left;">
            <h1 style="font-family:Verdana,Geneva,sans-serif;font-size:12px;color:#000;font-weight:bold;"></h1>
        </div>
        <div style="width:480px;height:auto;float:left;"><!-- Left start  000000000001-->
            <div style="width:480px;height:auto;float:left;">
                <h1 style="	font-family:Verdana, Geneva, sans-serif;font-size:12px;color:#000;font-weight:normal;">Service Tax (JN)</h1>
            </div>
        </div>
        <div style="width:380px;height:auto;float:left;">
            <h1 style="	font-family:Verdana, Geneva, sans-serif;font-size:12px;	color:#000;	font-weight:normal;">INR '.number_format($data['ticketinfo'][0]['ST'], '2').'</h1>
        </div>
        <div style="width:480px;height:auto;float:left;">
            <div  style="width:480px;height:auto;float:left;">
                <h1 style="	font-family:Verdana,Geneva,sans-serif;font-size:12px;color:#000;font-weight:bold;">Fees</h1>
            </div>
        </div>
        <div style="width:380px;height:auto;float:left;">
            <h1 style="	font-family:Verdana, Geneva, sans-serif;font-size:12px;	color:#000;	font-weight:bold;"></h1>
        </div>
        <div style="width:480px;height:auto;float:left;">
            <div style="width:480px;height:auto;float:left;">
                <h1 style="	font-family:Verdana, Geneva, sans-serif;font-size:12px;color:#000;font-weight:normal;">Passenger Service Fee (WO)</h1>
            </div>
        </div>
       <div style="width:380px;height:auto;float:left;">
            <h1 style="	font-family:Verdana, Geneva, sans-serif;font-size:12px;color:#000;font-weight:normal;">INR '.number_format($data['ticketinfo'][0]['AT'], '2').'</h1>
        </div>
        <div style="width:480px;height:auto;float:left;">
            <div style="width:480px;height:auto;float:left;">
                <h1 style="font-family:Verdana, Geneva, sans-serif;font-size:12px;color:#000;font-weight:bold;">TOTAL TRIP COST<br /><span style=" font-weight:normal;">(including Fare, Tax & Fees</span>)</h1>
            </div>
        </div>
        <div style="width:380px;height:auto;float:left;">
            <h1 style="	font-family:Verdana, Geneva, sans-serif;font-size:12px;	color:#000;	font-weight:bold;">INR '.number_format($data['ticketinfo'][0]['user_fare'], '2').'</h1>
        </div>
        <div style="width:960px;height:auto;float:left;">
            <div  style="width:960px;height:25px;float:left;">
                <h1 style="font-family:Verdana, Geneva, sans-serif;	font-size:12px;	color:#000;font-weight:normal;margin-top:5px;margin-left:10px;">* Convenience fee is an additional non-refundable charge that is applicable per guest.</h1>
            </div>
        </div>
        <div style="width:960px;height:auto;float:left;">
            <div style="width:960px;height:25px;float:left;">
                <h1 style="font-family:Verdana, Geneva, sans-serif;	font-size:12px;	color:#000;font-weight:normal;margin-top:5px;margin-left:10px;">Total Amount is inclusive of service tax, wherever applicable.</h1>
            </div>
        </div>
	</div>
</div>
<div style="width:960px;height:auto;float:left; ;">
    <div style="width:960px;height:auto;float:left;"><!-- heading start -->
        <div style="width:960px;height:25px;float:left;background-color:#CCC;">
            <h1 style="	width:590px;font-family:Verdana, Geneva, sans-serif;font-size:12px;color:#000;font-weight:bold;margin-top:5px;margin-left:10px; float:left;">Important Notes</h1>
        </div>
    </div><!-- heading end -->
    <div style="width:930px;height:auto;margin-top:2px; margin-left:10px; padding-bottom:15px; padding-top:10px;float:left;font-family:Verdana,Geneva,sans-serif;line-height:20px;font-size:12px;	color:#000;"><!-- page start -->

        This is an eTicket itinerary. To enter the airport and for check-in, you must present the itinerary receipt along with valid photo
        identification, viz: Official Government issued photo identification, driving license, election photo id, passport (for international
        passengers) and photo credit card. It is mandatory to carry your photo identification during your entire journey.<br />

    </div><!-- page end -->
    <div style="width:960px;height:auto;float:left;"><!-- Important Notes Page start -->
        <div  style="width:960px;height:auto;float:left;"><!-- heading start -->
            <div style="width:960px;height:25px;float:left;">
                <h1 style="	width:590px;font-family:Verdana, Geneva, sans-serif;font-size:12px;color:#000;font-weight:bold;margin-top:5px;margin-left:10px; float:left;">Reprint of eTicket(s)</h1>
            </div>
        </div><!-- heading end -->
        <div style="width:930px;height:auto;margin-top:2px; margin-left:10px; padding-bottom:15px; padding-top:10px;float:left;font-family:Verdana, Geneva, sans-serif; line-height:20px;font-size:12px;	color:#000;"><!-- page start -->

            If you require a reprint of your eTicket, we recommend you use our Manage Booking feature on jetairways.com or request our
            ticketing staff to email the same at no cost. Please note guest(s) will be charged INR 50 for every reprint requested at our
            City or Airport ticketing offices located in India.

        </div><!-- page end -->
        <div style="width:960px;height:auto;float:left; ;"><!-- Important Notes Page start -->
	<div  style="width:960px;height:auto;float:left;"><!-- heading start -->
        <div style="width:960px;height:25px;
             float:left;background-color:#CCC;">
            <h1 style="	width:590px;font-family:Verdana, Geneva, sans-serif;font-size:12px;color:#000;font-weight:bold;margin-top:5px;margin-left:10px; float:left;">Important Notes</h1>
        </div>
    </div><!-- heading end -->
    <div style="width:930px;height:auto;margin-top:2px; margin-left:10px; padding-bottom:15px; padding-top:10px;float:left;font-family:Verdana, Geneva, sans-serif; line-height:20px;	font-size:12px;	color:#000;"><!-- page start -->

        This is an eTicket itinerary. To enter the airport and for check-in, you must present the itinerary receipt along with valid photo
        identification, viz: Official Government issued photo identification, driving license, election photo id, passport (for international
        passengers) and photo credit card. It is mandatory to carry your photo identification during your entire journey.<br />

    </div><!-- page end -->
    <div style="width:960px;height:auto;float:left;"><!-- Important Notes Page start -->
        <div  style="width:960px;height:auto;float:left;"><!-- heading start -->
            <div style="width:960px;height:25px;float:left;">
                <h1 style="	width:590px;font-family:Verdana, Geneva, sans-serif;font-size:12px;color:#000;font-weight:bold;margin-top:5px;margin-left:10px; float:left;">Reprint of eTicket(s)</h1>
            </div>
        </div><!-- heading end -->
        <div style="width:930px;height:auto;margin-top:2px; margin-left:10px; padding-bottom:15px; padding-top:10px;float:left;font-family:Verdana, Geneva, sans-serif; line-height:20px;	font-size:12px;	color:#000;"><!-- page start -->

            If you require a reprint of your eTicket, we recommend you use our Manage Booking feature on jetairways.com or request our
            ticketing staff to email the same at no cost. Please note guest(s) will be charged INR 50 for every reprint requested at our
            City or Airport ticketing offices located in India.

        </div><!-- page end -->
     <div style="width:960px;height:auto;float:left;"><!-- Important Notes Page start -->
            <div  style="width:960px;height:auto;float:left;"><!-- heading start -->
                <div style="width:960px;height:25px;float:left;">
                    <h1 style="	width:590px;font-family:Verdana, Geneva, sans-serif;font-size:12px;color:#000;font-weight:bold;margin-top:5px;margin-left:10px; float:left;">Check-in</h1>
                </div>
            </div><!-- heading end -->
            <div style="width:930px;height:auto;margin-top:2px; margin-left:10px; padding-bottom:15px; padding-top:10px;float:left;font-family:Verdana, Geneva, sans-serif; line-height:20px;	font-size:12px;	color:#000;"><!-- page start -->

                Check-in counters for flights within India* will now close 45 minutes prior to flight departure and the boarding gate(s)
                closes 25 minutes prior to departure. For our guests who wish to Tele Check-in, please ensure that you collect your
                boarding pass for your assigned seat no later than 50 minutes prior to departure. Check-in counters for International flights
                continue to close 60 minutes prior to flight departure.<br />

                *Applicable only for flights from Mumbai, Hyderabad, Chennai, Bengaluru, Kolkata and Delhi

            </div><!-- page end -->
            <div style="width:960px;height:auto;float:left;"><!-- Important Notes Page start -->
                <div  style="width:960px;height:auto;float:left;"><!-- heading start -->
                    <div style="width:960px;height:25px;float:left;">
                        <h1 style="	width:590px;font-family:Verdana, Geneva, sans-serif;font-size:12px;color:#000;font-weight:bold;margin-top:5px;margin-left:10px; float:left;">Meals, Baggage & Lounge</h1>
                    </div>
                </div><!-- heading end -->
                <div style="width:930px;height:auto;margin-top:2px; margin-left:10px; padding-bottom:15px; padding-top:10px;float:left;font-family:Verdana, Geneva, sans-serif; line-height:20px;	font-size:12px;	color:#000;"><!-- page start -->

                    Meals are applicable for guests traveling on Jet Airways, and in Première on JetKonnect flights.
                    In case of piece concept, free baggage allowance per piece is 23kgs.
                    Cabin baggage should not exceed 7 kgs in weight and 115 linear cms. Guests traveling on flights originating from Jammu,
                    Srinagar and Leh will not be allowed to carry any cabin baggage.
                    Jet Airways levies a fee for carriage of Television & Computer LCD/LED Screens.
                    Please refer to jetairways.com for information on lounge access.<br />

                    Please contact the respective airline(s), or alternatively visit their website(s), to know more about your entitlement on Baggage
                    Allowance, Meal(s) and Special Request(s) on flights operated by our Codeshare / Interline partners.

                </div><!-- page end -->
                <div  style="width:960px;height:auto;float:left;"><!-- heading start -->
                    <div style="width:930px;height:auto; float:left;"><img src="'.base_url().'images/cabin.jpg"  /></div>
                </div>
            </div><!-- heading end -->
            <div style="width:960px;height:auto;float:left;"><!-- Important Notes Page start -->
                <div  style="width:960px;height:auto;float:left;"><!-- heading start -->
                    <div style="width:960px;height:25px;float:left;">
                        <h1 style="	width:590px;font-family:Verdana, Geneva, sans-serif;font-size:12px;color:#000;font-weight:bold;margin-top:5px;margin-left:10px; float:left;">Security Requirement</h1>
                    </div>
                </div><!-- heading end -->
                <div style="width:930px;height:auto;margin-top:2px; margin-left:10px; padding-bottom:15px; padding-top:10px;float:left;font-family:Verdana, Geneva, sans-serif; line-height:20px;	font-size:12px;	color:#000;"><!-- page start -->

                    . The card used to purchase the tickets will have to be produced at the time of Check-in.<br />

                    . If the holder of the card is not the passenger, then the passenger should possess:<br />

                    &nbsp;&nbsp;a. A photocopy of both sides of the card, which will have to be self attested by the card holder authorising the use
                    of the card for the purchase of the ticket. For security reasons, please strike out the Card Verification Value (CVV) code on
                    the copy of your card.<br />

                    &nbsp;&nbsp;b. This photocopy should also contain the name of the passenger, the date of journey and the sector on which the journey is
                    made.<br /><br />


                    The above document MUST be produced at the time of check-in. If the passenger fails to comply with these conditions, Jet Airways
                    reserves the right to deny the passenger(s) from boarding.
                    The details mentioned above do not apply for Net Banking / Cash Card and Cash on Delivery.<br />

                    For International travel, please ensure that the validity of the passport is as per the requirements of the destination country.<br />

                    Due to security reasons, liquids, aerosols and gels (LAGs) in carry-on baggage are restricted to containers of 100ml each. At
                    some airports, duty- free LAGs may be purchased after screening checkpoints. At most airports, including Indian airports,
                    transit guests are not allowed to carry duty-free LAGs purchased on a previous sector in cabin baggage, these will be
                    confiscated at the Security Checkpoint.<br />

                    For carriage of arms, ammunition, prohibited, restricted articles, please refer to our terms and conditions.
                    Please keep your valuables in your hand baggage for precaution measures.

                </div><!-- page end -->
				<div style="width:960px;height:auto;float:left;"><!-- Important Notes Page start -->
				<div  style="width:960px;height:auto;float:left;"><!-- heading start -->
				<div style="width:960px;height:auto;float:left;">
				<h1 style="width:960px;font-family:Verdana, Geneva, sans-serif;font-size:12px;color:#000;font-weight:normal;margin-top:5px;margin-left:10px; float:left; text-align:center;">For any queries please write to us at globalwings.asia</h1>
				<h1 style="width:960px;font-family:Verdana, Geneva, sans-serif;font-size:12px;color:#000;font-weight:bold;margin-top:5px;margin-left:10px;text-align:center; float:left;">Thank you for choosing Globalwings.asia. We wish you a pleasant journey</h1>    
				</div>
				</div><!-- heading end -->
				</div></div>';
				$filename = 'PDFFLIGHTTICKET'.$id;
				$this->load->helper(array('my_pdf_helper'));   //  Load helper
				//$data = file_put_contents($html); // Pass the url of html report    base_url().'application/view/report/voucher_print.php'
				create_pdf($flighthtml,$filename); //Create pdf
			}
		}
	
	
	
	
	
    /**
    *get staff under branch
    *
    *@param int $branch_id unique id which identifies branch
    *
    *$return json containing status of the operation
    */
    public function get_staff($branch_id)
    {
        $status = false;
        $data = '';
        if (intval($branch_id) > 0) {
            $data['staff_data'] = $this->Report_Model->model_get_staff($branch_id);
        } else if ($branch_id == 'default') {
            //to get staff under agent
            $data['staff_data'] = $this->Report_Model->model_get_staff_under_agent($this->agent_agent_id);
        }
        $data = $this->load->view('report/ajax_get_staff', $data, true);
		header('content-type:application/json');
		echo $data;
		exit;
    }
	
	function my_booking_search_panel()
	{
	extract($this->input->post());
		if (strcmp($viewType, 'SUP_DETAILS') == 0) {
			$where = '';
			//check for search criteria
			if (isset($bookingStatus) == true and empty($bookingStatus) == false) {
				$where .= ' and BTD.status =\''.$bookingStatus.'\'';
			}
            //specific branch to get staff
            if (isset($branch_id) == true and empty($branch_id) == false and $branch_id != 'default') {
                $where .= ' and BTD.branch_id='.$branch_id.' ';
            }
            //under agent staff
            if (isset($branch_id) == true and empty($branch_id) == false and $branch_id == 'default') {
                $where .= ' and BTD.branch_id=0 ';
            }
        
            if (isset($staff_id) == true and empty($staff_id) == false and intval($staff_id) > 0) {
                $where .= ' and BTD.agent_id='.$staff_id.' ';
            }
       
            if (isset($fromDate) == true and empty($fromDate) == false) {
                //convert date to database format
                $where .= ' and BTD.created_date >= '.$fromDate.' ';
            }
       
            if (isset($toDate) == true and empty($toDate) == false) {
                //convert date to database format
                $where .= ' and BTD.created_date <= '.$toDate.' ';
            }
       
            if (isset($bookingNumber) == true and empty($bookingNumber) == false) {
                $where .= ' and BTD.ref_supplier like \'%'.$bookingNumber.'%\'';
            }
       
            if (isset($hotelNumber) == true and empty($hotelNumber) == false) {
                $where .= ' and BTD.prn_no like \'%'.$hotelNumber.'%\' ';
            }

			$data['book_details'] = $this->Report_Model->model_my_booking_search_panel($where);
			$search_result_data = $this->load->view('report/ajax_my_booking_search', $data, true);
		}
		header('content-type:application/json');
		
		// echo json_encode($search_result_data); 
		echo $search_result_data;
		//exit;
	
	}
	
	public function voucher_print($id)
	{
		$data['bookinginfo'] = $this->Hotel_Model->getvoucher($id); 
		$api = $data['bookinginfo']->api;
		$hotel_id = $data['bookinginfo']->hotel_code;
		$passangerid = $data['bookinginfo']->customer_contact_details_id; 
		$data['hotel_info'] = $this->Hotel_Model->hotel_info($api,$hotel_id);
		$data['pass_info']  = $this->Hotel_Model->passagerdetail($passangerid);
		$this->load->view('report/voucher_print', $data);
	}
	public function invoice_print($id)
	{
		$data['bookinginfo'] = $this->Hotel_Model->getvoucher($id); 
		$api = $data['bookinginfo']->api;
		$hotel_id = $data['bookinginfo']->hotel_code;
		$passangerid = $data['bookinginfo']->customer_contact_details_id; 
		$data['hotel_info'] = $this->Hotel_Model->hotel_info($api,$hotel_id);		
		$data['pass_info']  = $this->Hotel_Model->passagerdetail($passangerid);
		$this->load->view('report/invoice_print', $data);
	}
	public function send_voucher_email($id, $msg = '')
	{
		$data['id'] = $id;
		$data['msg'] = $msg;
		
		$this->form_validation->set_rules('from_name', 'From Name', 'required');
		$this->form_validation->set_rules('from_mail_id', 'From Mail Id', 'required|valid_email');
		$this->form_validation->set_rules('to_name', 'To Name', 'required');
		$this->form_validation->set_rules('to_mail_id', 'To Mail Id', 'required|valid_email');
		if ($this->form_validation->run() == FALSE)
		{
			$data['from_name'] = $this->session->userdata('name'); 
			$this->load->view('Hotel/send_voucher_email', $data);
		}
		 else
		{
			$id = $this->input->post('id');
			$from_name = $this->input->post('from_name');
			$from_mail_id = $this->input->post('from_mail_id');
			$to_name = $this->input->post('to_name');
			$to_mail_id = $this->input->post('to_mail_id');
			
			$bookinginfo = $this->Hotel_Model->getvoucher($id);
			$mssage='';
			$mssage.='<table style="width: 800px;" border="0" cellspacing="0" cellpadding="1" class="r-hoteldeta">
					<tr>
						<td>
							<table class="booking_details" cellspacing="0" cellpadding="5">
								<tr>
									<td class="fbold" width="200">Booking Reference Number </td>
									<td>'.$bookinginfo->reference.'</td>
								</tr>				
								<tr>
									<td class="fbold">Booking Status</td>
									<td>'. $bookinginfo->status.'</td>
								</tr>
							</table>
						</td>
						<td align="right"><img src="http://192.168.0.135/travelstudio/images/logo.png"></td>
					</tr>
					<tr><td>&nbsp;</td><td>&nbsp;</td></tr>
					<tr>
						<td width="50%" valign="top">
							<div>
							<table width="99%" border="0" cellspacing="0" cellpadding="3" class="traveller_details">
								<tr><td colspan="2" class="vocher_headline"> Traveller Details</td></tr>
								<tr>
									<td width="37%" class="fbold">Guest Name</td>
									<td>:'.$bookinginfo->gender." ". $bookinginfo->first_name." ".$bookinginfo->last_name.'</td>
								</tr>
								<tr>
									<td class="fbold">Voucher Date</td>
									<td>:'. $bookinginfo->voucher_date.'</td>
								</tr>
								<tr>
									<td class="fbold">Reference number</td>
									<td>: '. $bookinginfo->reference.'</td>
								</tr>
								<tr>
									<td class="fbold"></td>
									<td class="fbold"></td>
								</tr>
								<tr>
									<td class="fbold"></td>
									<td class="fbold"></td>
								</tr>
							</table>
							</div>
						</td>
						<td width="50%">
							<table width="99%" border="0" cellspacing="0" cellpadding="3" class="reservation_details">
								<tr>
									<td colspan="2" class="vocher_headline">Your Reservation</td>
								</tr>
								<tr>
									<td width="37%" class="fbold">Check - in</td>
									<td>: '. $bookinginfo->check_in.'</td>
								</tr>
								<tr>
									<td class="fbold">Check - out</td>
									<td>: '. $bookinginfo->check_out.'</td>
								</tr>
								<tr>
									<td class="fbold">Rooms</td>
									<td>:</td>
								</tr>
								<tr>
									<td class="fbold">Nights</td>
									<td>: </td>
								</tr>
								<tr>
									<td class="fbold">Ref. Supplier</td>
									<td>: </td>
								</tr>
							</table>
						</td>
					</tr>
					<tr>
						<td colspan="2">
							<table width="100%" class="traveller_details">
								<tr> <td class="vocher_headline">Hotel Details</td> </tr>
							</table>
						</td>
					</tr>
					<tr>
						<td colspan="2">
							<table class="hotel_details">
								<tr>
									<td class="fbold">Hotel - '. $bookinginfo->hotel_name.'</td>
								</tr>
								<tr>
									<td></td>
								</tr>
							</table>
							<table class="hotel_details hotel_address" cellspacing="0" cellpadding="5">
								<tr>
									<td class="fbold" width="8%">Address </td>
									<td align="justify">: </td>
									<td class="fbold" width="8%">City </td>
									<td>: </td>
								</tr>
								<tr>
									<td class="fbold" width="5%">Phone </td>
									<td>: </td>
									<td class="fbold" width="5%">Fax </td>
									<td>: </td>
								</tr>
							</table>
						</td>
					</tr>
					<tr>
						<td colspan="2">
							<table width="100%" class="traveller_details">
								<tr><td class="vocher_headline">Room Details</td></tr>
							</table>
						</td>
					</tr>
					<tr>
						<td width="350" colspan="2" class="room_details">
						'. $bookinginfo->room_type.'</td>
					</tr>
					<tr>
						<td colspan="2">
							<table width="100%" class="traveller_details">
								<tr><td class="vocher_headline">Cancellation Policy</td></tr>
							</table>
						</td>
					</tr>
					<tr>
						<td colspan="2">
							<table class="cancellation_details" cellspacing="0" cellpadding="7">
								<tr>
									<td class="fbold">CheckIn Date</td>
									<td class="fbold">Cancellation Till Date</td>
									<td class="fbold">Cancellation Charges</td>
									<td class="fbold">Refund Charges</td>
								</tr>
								<tr>
									<td>'. $bookinginfo->check_in.'</td>
									<td>'. $bookinginfo->cancellation_till_date.'</td>
									<td></td>
									<td></td>
								</tr>
								<tr>
									<td colspan="6"><span style="color:red;">(*) Date and time is calculated based on local time of destination.</span></td>
								</tr>
							</table>
						</td>
					</tr>
						<tr>
						<td colspan="2">
							<table width="100%" class="traveller_details">
								<tr><td  style=" background-color: #FFFFF; color: #517BA5; font-size: 14px; font-weight: bold; padding-bottom: 2px; padding-left: 10px;padding-top: 2px; border: 1px solid;">Bookable and payable by   </td></tr>
							</table>
						</td>
					</tr>
						<tr>
						<td colspan="2">
							<table class="cancellation_details" cellspacing="0" cellpadding="5">
								<tr>
									<td class="fbold">Payable through Supplier , acting as agent for the service operating company, details of which can be provided upon request". VAT:  Reference: '. $bookinginfo->reference.'</td>					
								</tr>				
							</table>
						</td>
					</tr>
					<tr>
						<td colspan="2">
							<table width="100%" class="traveller_details">
								<tr><td class="vocher_headline">Passenger Details</td></tr>
							</table>
						</td>
					</tr>
					<tr>
						<td colspan="2">
							<table class="cancellation_details" cellspacing="0" cellpadding="5">
								<tr>
									<td class="fbold">'. $bookinginfo->first_name." ".$bookinginfo->last_name.'</td>
								</tr>
							</table>
						</td>
					</tr>
						 <tr>
						<td colspan="2">
							<table class="cancellation_details" cellspacing="0" cellpadding="5">
							</table>
						</td>
					</tr>
					<tr>
						<td align="center" colspan="3">
						</td>
					</tr>
				</table>';
			$sub = "Hotel Ticket from www.Globalwings.in";
			
			//$status = $this->sendmail_booking($to_mail_id,$mssage,$sub);    
			$data['msg'] = $mssage;
			$this->load->view('Hotel/send_voucher_email', $data);	
		}
	}
	function sendmail_booking($email,$message,$subject)
	{
		$config['protocol'] = 'smtp';
		$config['smtp_host'] = 'ssl://smtp.gmail.com';
		$config['smtp_port'] = 465;
		$config['smtp_user'] = 'yogesh.provab@gmail.com';
		$config['smtp_pass'] = 'admin@123';
		$config['wordwrap'] = FALSE;
		$config['mailtype'] = 'html';
		$config['charset'] = 'utf-8';
		$config['crlf'] = "\r\n";
		$config['newline'] = "\r\n";
		$this->load->library('email', $config);
		$this->email->set_newline("\r\n");        
		$msg=$message;
		$from = 'techsuport@globalwings.in';
		$sub = trim($subject);
		$this->email->from($from,'www.globalwings.in');
		$to = strip_tags($email);
		$this->email->to($to);
		$this->email->subject($sub);
		$this->email->message($msg);                     
		if($this->email->send())
		{
			return"with mail Conformation";
		}else{
			return "without mail Conformation";
		}
	}
	
	public function view_maps()
	{
		$cityname = $this->session->userdata('destination');
		$city_name = explode(',' ,$cityname); 
		$mapdata['mapresultdata'] = $this->Report_Model->view_map($city_name[0],$city_name[1]);
        $mapdata['dest'] = $cityname; 
        /*echo "<pre/>";
        print_r($mapdata);
        exit;*/
		$this->load->view('general/view_map', $mapdata);
	}
	
	public function fetch_search_result_map()
    {
		$cityname = $this->session->userdata('destination');
		$city_name = explode(',' ,$cityname); 
		$query = $this->Report_Model->view_map($city_name[0],$city_name[1]);
        $map_data = array();
        $cnt = 0;
        //echo  count($query);exit;
        for ($k = 0; $k < count($query); $k++)
        {
            $map_data[$cnt]['lat'] = $query[$k]['latitude'];
            $map_data[$cnt]['lng'] = $query[$k]['longitude'];
            $map_data[$cnt]['name'] = $query[$k]['hotel_name'];
			$img = $query[$k]['image'];
			
            $star = $query[$k]['star'];
            //$api = $query[$k]['api'];
            if ($star == 1) {
                $st = "<img src='http://192.168.0.136/luxeholidaysnew/images/1 star.jpg' />";
            } elseif ($star == 2) {
                $st = "<img src='http://192.168.0.136/luxeholidaysnew/images/2 star.jpg' />";
            } elseif ($star == 3) {
                $st = "<img src='http://192.168.0.136/luxeholidaysnew/images/3 star.jpg' />";
            } elseif ($star == 4) {
                $st = "<img src='http://192.168.0.136/luxeholidaysnew/images/4 star.jpg' />";
            } elseif ($star == 5) {
                $st = "<img src='http://192.168.0.136/luxeholidaysnew/images/5 star.jpg' />";
            } else {
                $st = "<img src='http://192.168.0.136/luxeholidaysnew/images/0 star copy.jpg' />";
			}
            
            $info ="<div id='mapdetailsbox2'><div id='imgbox2'><img src='".$img."' width='70px' height='70px' /></div><div id='hotelname2'>" . $query[$k]['hotel_name'] . "</div><div id='star2'> " . $st . " </div> <div style='clear:both'></div></div>";
            $map_data[$cnt]['info'] = $info;
            $cnt++;
        }
 
        echo json_encode($map_data);
    }
    
    
    
    function branch_calender(){
		if (!$this->session->userdata('agent_id') || $this->session->userdata('agent_id') == '') {
			redirect('hotel/login', 'refresh');
		}
		 $this->load->view('report/branchcalender');  
		
	}
    
 function branch_calender_search(){
	
	$year=$this->input->post('year');
	$month=$this->input->post('month');
	
	if($this->input->post('module') == 'Flight')
	{
		
		 echo $this->Report_Model->get_flight_calendar($year,$month);
	}
	if($this->input->post('module') == 'Hotels')
	{
		
  echo $this->Report_Model->get_calendar($year,$month);
 }
 
 
if($this->input->post('module') == 'Holidays')
{
	

  echo $this->Report_Model->get_holidaycalendar($year,$month);	
		
		
	}
 
		
	}
	
	function _setting(){
		return array(
			'start_day' 		=> 'sunday',
			
			'template' 			=> ' {table_open}<table style="width:800px;" border="0" class="date" cellpadding="0" cellspacing="0">{/table_open}
	{heading_row_start}<tr style="background-color:#568BCF;color:#FFFFFF;font-size:18px;font-weight:bold">{/heading_row_start}

   {heading_previous_cell}<th><a href="{previous_url}">&lt;&lt;</a></th>{/heading_previous_cell}
   {heading_title_cell}<th colspan="{colspan}">{heading}</th>{/heading_title_cell}
   {heading_next_cell}<th><a href="{next_url}">&gt;&gt;</a></th>{/heading_next_cell}

   {heading_row_end}</tr>{/heading_row_end}

   {week_row_start}<tr>{/week_row_start}
   {week_day_cell}<td style="background-color:#F2F2F2;color:#000000;font-size:16px">{week_day}</td>{/week_day_cell}
   {week_row_end}</tr>{/week_row_end}

   {cal_row_start}<tr>{/cal_row_start}
   {cal_cell_start}<td class="calenderbox">{/cal_cell_start}

   {cal_cell_content}
   
   <a href="" class="act_note" style="color:#000000;font-size:14px;">
								   {day}</a>
								   <div class="notes">{content}
								   </div>
								 
   
   
   {/cal_cell_content}
   {cal_cell_content_today}<div class="highlight"><a href="{content}">{day}</a></div>
   
   
   {/cal_cell_content_today}

   {cal_cell_no_content}{day}{/cal_cell_no_content}
   {cal_cell_no_content_today}<div class="highlight">{day}</div>{/cal_cell_no_content_today}

   {cal_cell_blank}&nbsp;{/cal_cell_blank}

   {cal_cell_end}</td>{/cal_cell_end}
   {cal_row_end}</tr>{/cal_row_end}

   {table_close}</table>{/table_close}');
   
	}
	
	function pnr(){
		if (!$this->session->userdata('agent_id') || $this->session->userdata('agent_id') == '') {
			redirect('hotel/login', 'refresh');
		}
		$this->load->view('report/pnr');
	}
	
	
	function pnr_search(){
	
		$this->Report_Model->search_pnr();
		
		
	}
	
	function bookno(){
		
		$this->load->view('report/booknumber');
	}
	
	function booking_number_search(){
		
		$this->Report_Model->search_bno();
		
	}
	
	
	function booking_deatils(){
		if(!$this->session->userdata('agent_id'))
		{
			redirect('hotel/login');
		}
		
		if($this->input->post('api_opt') == 'Hotels')
		{
			 $config = array();
			 $config["base_url"] = site_url() . "/report/booking_deatils/";
			 $agent_id = $this->session->userdata('agent_id');
				
				 $search = array('stdate' => $this->input->post('stdate'),
				'endate'=>$this->input->post('endate')
				);
			 $is_search=$this->input->post('search');
			 
			  if (!array_filter($search) && $is_search===false) { 
				  
				  
				  $search=array('stdate'=>$this->session->userdata('stdate'),
				  'endate'=>$this->session->userdata('endate')
				  );
				  
			  }else{
				  
				   $this->session->set_userdata($search);
				  
			  }
			$config["total_rows"] = $this->Report_Model->bclan_search_count1($agent_id,$search);
			$config["per_page"] = 50;
			$config["uri_segment"] = 3;
			$this->pagination->initialize($config);
			$page = ($this->uri->segment(3)) ? $this->uri->segment(3) : 0;
			$data['book_details'] = $this->Report_Model->bclan_search_data($agent_id,$search,$config["per_page"], $page);
			$data['links'] = $this->pagination->create_links();	
			$this->load->view('report/bookingdetails',$data);
		}
		
		
		
		if($this->input->post('api_opt') == 'Holidays'  || $this->uri->segment('3') == 'holi')
		{
			 $config = array();
			 $config["base_url"] = site_url() . "/report/booking_deatils/holi";
			 $agent_id = $this->session->userdata('agent_id');
				
				 $search = array('hstdate' => $this->input->post('stdate'),
				'hendate'=>$this->input->post('endate')
				);
			 $is_search=$this->input->post('search');
			 
			  if (!array_filter($search) && $is_search===false) { 
				  
				  
				  $search=array('hstdate'=>$this->session->userdata('hstdate'),
				  'hendate'=>$this->session->userdata('hendate')
				  );
				  
			  }else{
				  
				   $this->session->set_userdata($search);
				  
			  }
			$config["total_rows"] = $this->Report_Model->holidaybok_search_count1($agent_id,$search);
			$config["per_page"] = 10;
			$config["uri_segment"] = 4;
			$this->pagination->initialize($config);
			$page = ($this->uri->segment(4)) ? $this->uri->segment(4) : 0;
			$data['book_details'] = $this->Report_Model->holidaybok_search_data($agent_id,$search,$config["per_page"], $page);
			$data['links'] = $this->pagination->create_links();	
			
			
			$this->load->view('report/holidaybookingdetails',$data);
		}
		
		
		
		
		
		if($this->input->post('api_opt') == 'Flights' || $this->uri->segment('3') == 'pgntn')
		{
			
		$this->load->model('Flightreport');
		$data['flight_booking_data'] = $this->Flightreport->flight_report_data();
		$data['flight_booking_status'] = $this->Flightreport->flight_report_status();
$data['stdate'] = $this->input->post('stdate');
$data['endate'] = $this->input->post('endate');
		if($this->input->post('search') != '')
		{
			 $search = array(
			'fromDate'=>$this->input->post('stdate'),
			'toDate'=>$this->input->post('endate')
			
			);
			$this->session->set_userdata($search); 
		}
		else
		{
			$search = array(
			'fromDate'=>$this->session->userdata('fromDate'),
			'toDate'=>$this->session->userdata('toDate')
			);
			if($this->uri->segment(3) == '')
			{
				$this->session->unset_userdata($search);
				$search = array(
					'fromDate'=>'',
					'toDate'=>'',
					
					); 
			}
		}
		
		$count_data = $this->Flightreport->mybook_count($search);
		 //pagination Start ***HG
        $config = array();
        $config["base_url"] = site_url() . "/report/booking_deatils/pgntn/";
        $config["total_rows"] = $count_data;
        $config["per_page"] = 10;
        $config["uri_segment"] = 4;
        $this->pagination->initialize($config);
        $page = ($this->uri->segment(4)) ? $this->uri->segment(4) : 0;
        $data['book_details'] = $this->Flightreport->mybook_search($config["per_page"], $page ,$search);
        $data["links"] = $this->pagination->create_links();	    
		//pagination End ***HG
		$this->load->view('report/flights_booking_details',$data);	
		
		}
		
		if($this->input->post('api_opt') == '' && $this->uri->segment('3') == '')
		{
			$this->load->view('report/bookingdetails',$data);
		}
		
	}
	
		public function generatePDF($id)
		{	
			$html = '';
			$data['bookinginfo'] = $this->Hotel_Model->getvoucher($id); 
			$passangerid = $data['bookinginfo']->customer_contact_details_id; 
			$data['pass_info']  = $this->Hotel_Model->passagerdetail($passangerid);
			//$this->load->view('report/voucher_print', $data);
			$date1=date("Y-m-d",strtotime($data['bookinginfo']->check_in));
			$date2=date("Y-m-d",strtotime($data['bookinginfo']->check_out));
			$no_of_day =round(abs(strtotime($date1)-strtotime($date2))/86400);
			$paxname = '';
			for($i = 0; $i<count($data['pass_info']);$i++){
			    $paxname .= '<tr>
				<td class="fbold">'.$data['pass_info'][$i]->gender . ' ' .$data['pass_info'][$i]->first_name . ' ' . $data['pass_info'][$i]->last_name.'</td>
				</tr>';
			}
			$html .= '<title>Globalwings.in Voucher Info</title>
						<style type="text/css">
						.r-hoteldeta {
							border:1px solid #D8D9DA;background-color: #FBFBFB;padding: 8px;
						}
						.vocher_headline {
							color: #fff;background-color: #517BA5;font-size: 14px;font-weight:bold;padding-bottom: 2px;padding-left: 10px;padding-top: 2px;
						}

						.traveller_details {
							color: #444444;font-family: arial;font-size: 12px;line-height: 21px;
						}

						.hotel_details {
							color: #444444;font-family: arial;font-size: 12px;line-height: 21px;margin: auto;width: 100%;
						}

						.room_details{
							color: #444444;font-family: arial;font-size: 12px;line-height: 21px;padding: 8px 12px;
						}

						.cancellation_details{
							color: #444444;font-family: arial;font-size: 12px;line-height: 21px;background-color: #EDEFED;border: 1px solid #9D9D9D;width:99%;margin:auto;
						}

						.cancellation_details tr td{
							border-bottom: 1px solid #D8D9DA;border-right: 1px solid #D8D9DA;
						}
						.fbold{
							font-weight:bold;
						}

						.hotel_address{
							border-bottom: 1px solid #D8D9DA;	
						}

						.hotel_address tr td{
							border-top: 1px solid #D8D9DA;
						}

						.print_text{
							vertical-align: super;font-family: arial;font-size: 16px;color: #444444;
						}	

						.booking_details{
							color: #444444;font-family: arial;font-size: 13px;line-height: 21px;width:auto;border: 1px solid gray; padding:10px;
						}

						.booking_details tr td{
							border-bottom: 1px solid #D8D9DA; 
						}

						.reservation_details tr td{
							border-bottom: 1px solid #D8D9DA; 
						}

						.traveller_details tr td {
							border-bottom: 1px solid #D8D9DA; 
						}

						.reservation_details{
							color: #444444;font-family: arial;font-size: 12px;line-height: 21px;
						}
						</style>';
				
			$html .= '<table style="width: 800px;" border="0" cellspacing="0" cellpadding="1" class="r-hoteldeta">
    <tr>
    	<td colspan="2" valign="top" style="text-align:center;font-family:MAIAN; font-size:20px;"><strong>Hotel Voucher</strong></td>
    </tr>
	<tr>
		<td>
			<table class="booking_details" cellspacing="0" cellpadding="5">
				<tr>
					<td class="fbold" width="200">Booking Number </td>
					<td>'.$data['bookinginfo']->booking_number.'</td>
				</tr>				
				<tr>
					<td class="fbold">Booking Status</td>
					<td>'.$data['bookinginfo']->status.'</td>
				</tr>
			</table>
		</td>
		<td align="right"><img src="'.base_url().'images/logo.jpg'.'"></td>
	</tr>
	<tr><td>&nbsp;</td><td>&nbsp;</td></tr>
	<tr>
		<td style="width:335px" valign="top">
			<div>
			<table style="width:335px" border="0" cellspacing="0" cellpadding="3" class="traveller_details">
				<tr><td colspan="2" class="vocher_headline"> Traveller Details</td></tr>
				<tr>
					<td width="37%" class="fbold">Guest Name</td>
					<td>:'.$data['bookinginfo']->first_name.' '.$data['bookinginfo']->last_name.'</td>
				</tr>
				<tr>
					<td class="fbold">Voucher Date</td>
					<td>:'.$data['bookinginfo']->voucher_date.'</td>
				</tr>
                                <tr>
					<td class="fbold">Reference Supplier</td>
					<td>:'.$data['bookinginfo']->reference.'</td>
				</tr>
                                <tr>
					<td class="fbold"></td>
					<td class="fbold"></td>
				</tr>
                                <tr>
					<td class="fbold"></td>
					<td class="fbold"></td>
				</tr>
			</table>
			</div>
		</td>
		<td style="width:320px">
			<table style="width:320px" border="0" cellspacing="0" cellpadding="3" class="reservation_details">
				<tr>
					<td colspan="2" class="vocher_headline">Your Reservation</td>
				</tr>
				<tr>
					<td width="37%" class="fbold">Check - in</td>
					<td>:'.date("d-m-Y",strtotime($data['bookinginfo']->check_in)).'</td>
				</tr>
				<tr>
					<td class="fbold">Check - out</td>
					<td>'.date("d-m-Y",strtotime($data['bookinginfo']->check_out)).'</td>
				</tr>
                                
				<tr>
					<td class="fbold">Rooms</td>
					<td>:</td>

				</tr>
				<tr>
					<td class="fbold">Nights</td>
					<td>: '.$no_of_day.'Days</td>
				</tr>
                                <tr>
					<td class="fbold">Ref. Number</td>
					<td>:'.$data['bookinginfo']->ref_supplier.'</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr><td>&nbsp;</td><td>&nbsp;</td></tr>
	<tr>
		<td colspan="2">
			<table style="width:1500px" class="traveller_details">
				<tr> <td class="vocher_headline">Hotel Details</td> </tr>
			</table>
		</td>
	</tr>

	<tr>
		<td colspan="2">
			<table style="width:1500px" class="hotel_details" >
				<tr>
					<td class="fbold">Hotel - '.$data['bookinginfo']->hotel_name.'</td>
				</tr>
				<tr>
					<td></td>
				</tr>
			</table>
			<table style="width:1500px" class="hotel_details hotel_address" cellspacing="0" cellpadding="5">
				<tr>
					<td class="fbold" width="8%">Address </td>
					<td align="justify">:</td>
					<td class="fbold" width="8%">City </td>
					<td>:'.$data['bookinginfo']->city.'</td>
				</tr>
				<tr>
					<td class="fbold" width="5%">Phone </td>
					<td>:</td>
					<td class="fbold" width="5%">Fax </td>
					<td>:</td>
				</tr>
                <tr>
					<td class="fbold" width="5%">Email </td>
					<td>:</td>
					<td class="fbold" width="5%">Web </td>
					<td>:</td>
				</tr>
                <tr>
                	<td colspan="4">'.$data['bookinginfo']->contract_list_comment.'</td>
                </tr>
			</table>
		</td>
	</tr>
	<tr><td>&nbsp;</td><td>&nbsp;</td></tr>
	<tr>
		<td colspan="2">
			<table style="width:1500px" class="traveller_details">
				<tr><td class="vocher_headline">Room Details</td></tr>
			</table>
		</td>
	</tr>
	<tr>
		<td width="350" colspan="2" class="room_details">
		'.$data['bookinginfo']->room_type.'</td>
	</tr>
    <tr>
        <td colspan="6"><span style="color:red;">(*) Some services shall be paid at the establishment.</span></td>
    </tr>
    <tr><td>&nbsp;</td><td>&nbsp;</td></tr>
	<tr>
		<td colspan="2">
			<table style="width:1500px" class="traveller_details">
				<tr><td class="vocher_headline">Cancellation Policy</td></tr>
			</table>
		</td>
	</tr>
	<tr>
		<td colspan="2">
			<table class="cancellation_details" style="width:1500px" cellspacing="0" cellpadding="7">
				<tr>
					<td class="fbold">CheckIn Date</td>
					<td class="fbold">Cancellation Till Date</td>
					<td class="fbold">Cancellation Charges</td>
					<td class="fbold">Refund Charges</td>
				</tr>
				<tr>
					<td>'. $data['bookinginfo']->check_in.'</td>
					<td>'.$data['bookinginfo']->cancellation_till_date.'</td>
					<td></td>
					<td></td>
				</tr>
				<tr>
					<td colspan="6"><span style="color:red;">(*) Date and time is calculated based on local time of destination.</span></td>
				</tr>
			</table>
		</td>
	</tr>
        <tr>
		<td colspan="2">
			<table style="width:1500px" class="traveller_details">
				<tr><td  style=" background-color: #C0C0C0; color: #800000; font-size: 14px; font-weight: bold; padding-bottom: 2px; padding-left: 10px;padding-top: 2px; border: 1px solid;">Bookable and payable by   </td></tr>
			</table>
		</td>
	</tr>
        <tr>
		<td colspan="2">
			<table style="width:1500px" class="cancellation_details" cellspacing="0" cellpadding="5">
				<tr>
                    <td class="fbold">Payable through Supplier '.$data['bookinginfo']->supplier_name.', acting as agent for the service operating company, details of which can be provided upon request. VAT: '.$data['bookinginfo']->supplier_vatNumber.', Reference: '.$data['bookinginfo']->reference.'</td>					
				</tr>				
			</table>
		</td>
	</tr>
	<tr><td>&nbsp;</td><td>&nbsp;</td></tr>
	<tr>
		<td colspan="2">
			<table style="width:1500px" class="traveller_details">
				<tr><td class="vocher_headline">Passenger Details</td></tr>
			</table>
		</td>
	</tr>
	<tr>
		<td colspan="2">
			<table style="width:1500px" class="cancellation_details" cellspacing="0" cellpadding="5">
                '.$paxname.' 
			</table>
		</td>
	</tr>
</table>'; 
			
			$filename = 'PDFTICKET'.$id;
			$this->load->helper(array('my_pdf_helper'));   //  Load helper
			//$data = file_put_contents($html); // Pass the url of html report    base_url().'application/view/report/voucher_print.php'
			create_pdf($html,$filename); //Create pdf
		
		}
	
	function booking_search_data(){
		
		
		$agent_id = $this->session->userdata('agent_id');
		
		
		
		
	
		

		$this->Report_Model->bsearch_data($agent_id);
		

		
		
	}
	
	function booking_searchby_date(){
		
		
		
			$agent_id = $this->session->userdata('agent_id');
			
			
			
			
			
			
			
			
			
			
			
		if($this->input->post('apiopt') == 'Holidays'  || $this->uri->segment('3') == 'holi')
		{
			 $config = array();
			 $config["base_url"] = site_url() . "/report/booking_deatils/holi";
			 $agent_id = $this->session->userdata('agent_id');
				
				 $search = array('hbokstdate' => $this->input->post('sdate'),
				'hbokendate'=>$this->input->post('edate')
				);
			 $is_search=$this->input->post('search');
			 
			  if (!array_filter($search) && $is_search===false) { 
				  
				  
				  $search=array('hbokstdate'=>$this->session->userdata('hbokstdate'),
				  'hbokendate'=>$this->session->userdata('hbokendate')
				  );
				  
			  }else{
				  
				   $this->session->set_userdata($search);
				  
			  }
			$config["total_rows"] = $this->Report_Model->holidaybok_search_count1($agent_id,$search);
			$config["per_page"] = 10;
			$config["uri_segment"] = 4;
			$this->pagination->initialize($config);
			$page = ($this->uri->segment(4)) ? $this->uri->segment(4) : 0;
			$data['book_details'] = $this->Report_Model->holidaybok_search_data($agent_id,$search,$config["per_page"], $page);
			$data['links'] = $this->pagination->create_links();	
			
			
			echo $this->load->view('report/holiday_searchdate',$data);
		}
			
			if($this->input->post('apiopt') == 'Hotels'){
				
			//pagination start
			
			 $is_search=$this->input->post('search');
	
		    $search = array('sdate' => $this->input->post('sdate'),
		    'edate'=>$this->input->post('edate')
		    );
		 
		  if (!array_filter($search) && $is_search===false) { 
			  
			
			  
			  $search=array('sdate'=>$this->session->userdata('sdate'),
			  'edate'=>$this->session->userdata('edate')
			  );
			  
		  }else{
			  
			   $this->session->set_userdata($search);
			  
		  }
		  
		  
		     
        $config = array();
        $config["base_url"] = site_url() . "/report/booking_deatils/";
        
        $config["total_rows"] = $this->Report_Model->bclan_data_count1($agent_id,$search);
        
        $config["per_page"] = 10;
        $config["uri_segment"] = 3;
        $config['full_tag_open'] = '<div class="pagination">';
        $config['full_tag_close'] = '</div>';
        $this->pagination->initialize($config);
 
        $page = ($this->uri->segment(3)) ? $this->uri->segment(3) : 0;
     
        $data['book_details'] = $this->Report_Model->bclan_data($agent_id,$search,$config["per_page"], $page);
      
  }
  
   
		
	}
	
	
	
	function pending_ticket(){
		
		if (!$this->session->userdata('agent_id') || $this->session->userdata('agent_id') == '') {
			redirect('hotel/login', 'refresh');
		}
		
       $agent_id = $this->session->userdata('agent_id');
		
		$config = array();
        $config["base_url"] = site_url() . "/report/pending_ticket/";
        
        $config["total_rows"] = $this->Report_Model->pending_data_count1($agent_id);
        
        $config["per_page"] = 20;
        $config["uri_segment"] = 3;
        
        $this->pagination->initialize($config);
 
        $page = ($this->uri->segment(3)) ? $this->uri->segment(3) : 0;
        $data['book_details'] = $this->Report_Model->pending_data($agent_id,$config["per_page"], $page);
        
        $data["links"] = $this->pagination->create_links();	
        $this->load->view('report/pendingticket',$data);		
		
	}
	
	
	function account_details(){
		if (!$this->session->userdata('agent_id') || $this->session->userdata('agent_id') == '') {
			redirect('hotel/login', 'refresh');
		}
		
		$agent_id = $this->session->userdata('agent_id');
		$is_search=$this->input->post('search');
	
		$search = array('st' => $this->input->post('st'),
		    'et'=>$this->input->post('et')
		    );
		 
		  if (!array_filter($search) && $is_search===false) { 
			  
			
			  
			  $search=array('st'=>$this->session->userdata('st'),
			  'et'=>$this->session->userdata('et')
			  );
			  
		  }else{
			  
			   $this->session->set_userdata($search);
			  
		  }
        $data["deposit"] = $this->Report_Model->deposit_ledgerdetails($agent_id,$search);
	$data["Hotel_booking"] = $this->Report_Model->hotel_ledgerdetails($agent_id,$search);
		
		
       /* $config = array();
        $config["base_url"] = site_url() . "/report/account_details/";
        
        $config["total_rows"] = $this->Report_Model->account_data_count1($agent_id,$search);
        $config["per_page"] = 100;
        $config["uri_segment"] = 3;
        $this->pagination->initialize($config);
        $page = ($this->uri->segment(3)) ? $this->uri->segment(3) : 0;
        $data['book_details'] = $this->Report_Model->account_data($agent_id,$search,$config["per_page"], $page);
        
        $data["links"] = $this->pagination->create_links();	
        */
                  
         $this->load->view('report/account_details',$data);		
		
		
	}
	
	function sales_report(){	
		if (!$this->session->userdata('agent_id') || $this->session->userdata('agent_id') == '') {
			redirect('hotel/login', 'refresh');
		}
		$this->load->view('report/sales_report');	
	}
	
	 function filght_ticket($id){
		redirect('flight/e-ticket.php?ticketid='.$id);
	}
	
	public function Flight_send_ticket_email($id, $msg = '')
	{
		//print_r($this->input->post());
		$this->load->model('Flightreport');
		$data['id'] = $id;
		$data['agent_info'] = $this->Flightreport->agent_info($id);
		
		$this->form_validation->set_rules('from_name','First Name','required');
		$this->form_validation->set_rules('to_name','First Name','required');
		$this->form_validation->set_rules('from_mail_id','Email','required|valid_email');
		$this->form_validation->set_rules('to_mail_id','First Name','required|valid_email');
		if($this->form_validation->run() == FALSE)
		{
			$data['msg'] = '';
			$data['from_name'] = $this->input->post('from_name');
			$data['to_name'] = $this->input->post('to_name');
			$data['from_mail_id'] = $this->input->post('from_mail_id');
			$data['to_mail_id'] = $this->input->post('to_mail_id');
			$this->load->view('report/flight_send_ticket_email.php',$data);
		}else
		{
			$data['msg'] = 'Ticket Send Successfully...';
			$emailto = $this->input->post('to_mail_id');
			$emailfrom = $this->input->post('from_mail_id');
			$to_name = $this->input->post('to_name');
			$from_name = $this->input->post('from_name');
			//include 'flight/ticket_pdf_in_mail.php';
			redirect('flight/ticket_pdf_in_mail.php?ticketid='.$id.'&emailto='.$emailto.'&emailfrom='.$emailfrom.'&to_name='.$to_name.'&from_name='.$from_name);
			//$this->provabmail($emailto,$emailfrom,$to_name,$from_name,$id);
			
			$this->load->view('report/flight_send_ticket_email',$data);
		}
	}
	
    public function flight_pdf($id)
	{
		//$this->load->model('Flightreport');
		//$data['bookinginfo'] = $this->Flightreport->getvoucher($id);
		//$pdf_string = $this->load->view('report/pdf_voucher',$data,true);
		//$filename = 'GW'.$id;
		//PDF GENRATION
		//$this->load->helper(array('my_pdf_helper')); 
		//create_pdf($pdf_string,$filename); 
		redirect('flight/ticket_pdf?ticketid='.$id);
	}
	
	
	
	public function holiday_voucher_email($id)
	{
		$data['id'] = $id;
		
		
		$this->form_validation->set_rules('from_name', 'From Name', 'required');
		$this->form_validation->set_rules('from_mail_id', 'From Mail Id', 'required|valid_email');
		$this->form_validation->set_rules('to_name', 'To Name', 'required');
		$this->form_validation->set_rules('to_mail_id', 'To Mail Id', 'required|valid_email');
		if ($this->form_validation->run() == FALSE)
		{
			$data['from_name'] = $this->session->userdata('name'); 
			$data['msg'] = '';
			$this->load->view('holidays/holiday_voucher_email', $data);
		}
		 else
		{
			
			$id = $this->input->post('id');
			$from_name = $this->input->post('from_name');
			$from_mail_id = $this->input->post('from_mail_id');
			$to_name = $this->input->post('to_name');
			$to_mail_id = $this->input->post('to_mail_id');
			$bookinginfo = $this->Holiday_Model->getvoucher($id);		
			$msessage='<table style="width: 800px;" border="0" cellspacing="0" cellpadding="1" class="r-hoteldeta">
					<tr>
						<td>
							<table class="booking_details" cellspacing="0" cellpadding="5">
								<tr>
									<td class="fbold" width="200">Booking Reference Number </td>
									<td>'.$bookinginfo[0]->holi_bookno.'</td>
								</tr>				
								<tr>
									<td class="fbold">Booking Status</td>
									<td>'.$bookinginfo[0]->holi_payment_status.'</td>
								</tr>
							</table>
						</td>
						<td align="right"><img src="http://192.168.0.135/travelstudio/images/logo.png"></td>
					</tr>
					<tr><td>&nbsp;</td><td>&nbsp;</td></tr>
					<tr>
						<td width="50%" valign="top">
							<div>
							<table width="99%" border="0" cellspacing="0" cellpadding="3" class="traveller_details">
								<tr><td colspan="2" class="vocher_headline"> Traveller Details</td></tr>
								<tr>
									<td width="37%" class="fbold">Guest Name</td>
									<td>:'.$bookinginfo[0]->name.'</td>
								</tr>
								<tr>
									<td class="fbold">Voucher Date</td>
									<td>:'.$bookinginfo[0]->create_date.'</td>
								</tr>
								<tr>
									<td class="fbold">Reference number</td>
									<td>: '.$bookinginfo[0]->reference_no.'</td>
								</tr>
								<tr>
									<td class="fbold"></td>
									<td class="fbold"></td>
								</tr>
								<tr>
									<td class="fbold"></td>
									<td class="fbold"></td>
								</tr>
							</table>
							</div>
						</td>
						<td width="50%">
							<table width="99%" border="0" cellspacing="0" cellpadding="3" class="reservation_details">
								<tr>
									<td colspan="2" class="vocher_headline">Your Reservation</td>
								</tr>
								<tr>
									<td width="37%" class="fbold">Form- in</td>
									<td>: '.$bookinginfo[0]->form_date.'</td>
								</tr>
								<tr>
									<td class="fbold">Form - out</td>
									<td>: '.$bookinginfo[0]->to_date.'</td>
								</tr>
								
								<tr>
									<td class="fbold">Nights</td>
									<td>:'.$bookinginfo[0]->p_nights.' </td>
								</tr>
								
							</table>
						</td>
					</tr>
				</table>';
				
				$sub="Holidays Booking Packages Details";
			
			$status = $this->sendmail($to_mail_id,$sub,$msessage);
			
			if(!$status){
		
		  $data['msg']='mail Not Sended';
		
		 }else{
			 
			  $data['msg']='mail Sended Sucess Fully';
			 
		 }
			
			$this->load->view('holidays/holiday_voucher_email', $data);
		}

	}
	
	
	
	
		public function holiday_voucher($id){  

		$data['id'] = $id;
		$bookinginfo=$data['bookinginfo']= $this->Holiday_Model->getvoucher($id);
		$this->load->view('report/holiday_voucher_print', $data);    

		}
    
		public function sendmail($email,$subject,$message)
		{
		$ss=$this->provab_mailer->send_mail($email, $subject, $message);
		if(!$ss){
		return FALSE;
		}else{
		return TRUE;
		}
		}
		
		public function payment_successful()
		{
			$this->load->view('payment_successful');
		}
		
		
		function holtel_booking_pdf($form,$to,$status,$bookno,$pnr){
			
			
			
		 $data['book_details']=$book_details= $this->Report_Model->mybook_sarch_pdf($form,$to,$status,$bookno,$pnr);	
			
			$hotelhtml=' <div id="resultSetContainer" style="width:100%;">
        <table width="975" cellspacing="0" cellpadding="0" border="0" style="margin:15px 0 0 0; border:1px solid #ccc;" id="agentresult">
              <tr>
            <th class="my_profile_name_ex_tab">Booking Number</th>
            <th class="my_profile_name_ex_tab">PRN Number</th>
            <th class="my_profile_name_ex_tab">Guest Name</th>
            <th class="my_profile_name_ex_tab">Booking Date</th>
            <th class="my_profile_name_ex_tab">CheckIn</th>
            <th class="my_profile_name_ex_tab">CheckOut</th>
            <th class="my_profile_name_ex_tab">Status</th>
            <th class="my_profile_name_ex_tab">Cancel Till</th>
            <th class="my_profile_name_ex_tab">Amount</th>
            <th class="my_profile_name_ex_tab">Net Amount</th>
            <th class="my_profile_name_ex_tab">Margin</th>
          </tr>';
          
          
          if (isset($book_details)) {
							
							if(count($book_details) > 0 ){
								
								
								foreach($book_details as $val)
								{
          
          $hotelhtml.=' <tr>
            <td>'.$val->ref_supplier.'</td>
            <td>'.$val->prn_no.'</td>
            <td>'.$val->first_name.'</td>
            <td>'.$val->created_date.'</td>
            <td>'.$val->check_in.'</td>
            <td>'.$val->check_out.'</td>
            <td>'.$val->status.'</td>
            <td>'.$val->cancellation_till_date.'</td>
            <td>'.$val->agentselectcurrency.'  '.number_format($val->amount * $val->currencyprice, 2, '.', '').'</td>
            <td>'.$val->agentselectcurrency.'  '.number_format($val->net_amount * $val->currencyprice, 2, '.', '').'</td>
            <td>'.$val->agentselectcurrency.'  '.number_format(($val->amount - $val->net_amount) * $val->currencyprice, 2, '.', '').'</td>';
			
			
			 
			  $hotelhtml.='</tr>';
             
									
								}
								
							}else{
								
								 $hotelhtml.='<tr>
            <td align="center" valign="top" colspan="11" class="my_profile_name_ex_tab_whit_ex">No Result Found... </td>
          </tr>';
             
							}
								
								
							}
						else
						{
				 $hotelhtml.='<tr>
            <td align="center" valign="top" colspan="11" class="my_profile_name_ex_tab_whit_ex">No Result Found... </td>
          </tr>';
             
						}
	
          $hotelhtml.='</table></div>';
          
          
          
          $filename = 'Hotelbooking';
			$this->load->helper(array('my_pdf_helper'));   //  Load helper
			//$data = file_put_contents($html); // Pass the url of html report    base_url().'application/view/report/voucher_print.php'
			create_pdf($hotelhtml,$filename); //Create pdf
			
		}
		
		
		
		
		function filght_booking_pdf($form,$to,$status,$bookno,$pnr){
			
			
		/*	$this->load->model('Flightreport');
			
		$data['book_details']=$book_details = $this->Flightreport->mybook_search1($form,$to,$status,$bookno,$pnr);
			
			$fighthtml='  <div id="resultSetContainer">
              <table cellspacing="0" cellpadding="0" border="0" style="margin:15px 0 0 0; border:1px solid #ccc;" id="agentresult">
            <tr>
                  <th class="my_profile_name_ex_tab bookingno">Globalwings No.</th>
                  <th class="my_profile_name_ex_tab guestname">Guest Name</th>
                  <th class="my_profile_name_ex_tab pnrno">Onward PNR</th>
                  <th class="my_profile_name_ex_tab pnrno">Return PNR</th>
                  <th class="my_profile_name_ex_tab flightname">Onward Flight</th>
                  <th class="my_profile_name_ex_tab flightname">Return Flight</th>
                  <th class="my_profile_name_ex_tab status">Onward Status</th>
                  <th class="my_profile_name_ex_tab status">Return Status</th>
                  <th class="my_profile_name_ex_tab fromdate">Trip Type</th>
                  <th class="my_profile_name_ex_tab fromdate">Depart on</th>
                  <th class="my_profile_name_ex_tab todate">Return on</th>
                  <th class="my_profile_name_ex_tab bookingdate">Booking Date</th>
                  <th class="my_profile_name_ex_tab amount">Net Price</th>
                </tr>';
                
                	if (isset($book_details)) {
							
							if(count($book_details) != ""){
								
								foreach($book_details as $val)
								{
                                    if(($val->flight_mode == 'R') && ($val->dom_int == 'domestic'))
                                    {
										$PNR = explode('|A|',$val->tbo_pnr);
										$SOURCE = explode('|A|',$val->tbo_source);
										$STATUS = explode('|A|',$val->status);
									}
                
                
			$fighthtml=' <tr>
                  <td>'.$val->wts_ref_id.'</td>
                  <td>'.$val->lead_pax_name.'</td>';
			if(($val->flight_mode == 'R') && ($val->dom_int == 'domestic'))
											{
											 $fighthtml.='<td>'.$PNR[0].'</td>';
												 $fighthtml.='<td>'.$PNR[1].'</td>';
												 $fighthtml.='<td>'.$SOURCE[0].'</td>';
												 $fighthtml.='<td>'.$SOURCE[1].'</td>';
												 $fighthtml.='<td>'.$STATUS[0].'</td>';
												 $fighthtml.='<td>'.$STATUS[1].'</td>';
												$booking_id = explode('|A|',$val->booking_id);
											}else
											{
												 $fighthtml.='<td>'.$val->tbo_pnr.'</td>';
												 $fighthtml.='<td>*****</td>';
												 $fighthtml.= '<td>'.$val->tbo_source.'</td>';
												 $fighthtml.='<td>*****</td>';
												 $fighthtml.= '<td>'.$val->status.'</td>';
												 $fighthtml.='<td>*****</td>';
												$booking_id = $val->booking_id;
											}
								
                  $fighthtml=. '<td>';
												if($val->flight_mode == 'O')
												{
													$fighthtml=.'Oneway';
												}else
												{
													$fighthtml=.'Round';
										 $fighthtml.= '</td>
                  <td>'; 
												$fromDate = explode('-',$val->fromDate);
												 $fighthtml.=$fromDate[2].'-'.$fromDate[1].'-'.$fromDate[0];
											 $fighthtml.= '</td>
                  <td>';					if($val->flight_mode == 'R')
												{
													$toDate = explode('-',$val->toDate);
													$fighthtml=.$toDate[2].'-'.$toDate[1].'-'.$toDate[0];
												}else
												{
													 $fighthtml.=  '*****';
												}
			$fighthtml.= '</td>
                  <td>';
												$booking_date_only = explode('-',$val->booking_date_only);
												 $fighthtml.=$booking_date_only[2].'-'.$booking_date_only[1].'-'.$booking_date_only[0].'</td>
                  <td>Rs. '.$val->coupon_markup_price.'</td>';
                  
                  
                  $fighthtml.=' </tr>';
         
								
							}else{
								
							
            $fighthtml.=' <tr>
                  <td align="center" valign="top" colspan="11" class="my_profile_name_ex_tab_whit_ex">'.$result_not_found = 'No Result Found...'.'</td>
                </tr>';
        
							}
								
								
							}
						else
						{
					
           $fighthtml.=' <tr>
                  <td align="center" valign="top" colspan="11" class="my_profile_name_ex_tab_whit_ex">'.$result_not_found = 'No Result Found...</td>
                </tr>';

						}
				
         $fighthtml.='</table>
            </div>';
			
			
			
			
		  $filename = 'filghtBooking';
			$this->load->helper(array('my_pdf_helper'));   //  Load helper
			//$data = file_put_contents($html); // Pass the url of html report    base_url().'application/view/report/voucher_print.php'
			create_pdf($fighthtml,$filename); //Create pdf	
			
			*/
			
			
		}

    function send_email()
    {                     
                           

                         
                               $send_msg=$_REQUEST['edata']; 
                               $udata=explode('??*?',$send_msg);
                               $email_id=$udata[0];
                               $msg=$udata[1];
                               
                                $this->load->library('Provab_Mailer');
				$msg =$msg;
				
				$sub = 'INVOICE - Globalwings.in ';
				$to_address = $email_id;
				$res=$this->provab_mailer->send_mail($to_address, $sub, $msg);
                                if($res['status']==1)
				{  echo "Mail Sent Successfully";
                                }
                                if($res['status']!=1)
                                {echo "Mail Sent Unsuccessful";
                                 }
   
    }	
    
    
    
}
