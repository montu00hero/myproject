<?php

class check extends CI_Controller {
      
    function __construct() {
        parent::__construct();
        $this->load->helper(array("form","url"));
	  $this->load->library('form_validation');
          $this->load->library('session');
        
    }


    function chk()
	{//echo "chk";
        
         $this->load->model('check_model');
         $data['sqls']=$this->check_model->agent();
         $this->load->view('check_view',$data);        
        
		//$this->load->view('login_view');
	}
        
   function agent_data()
   {    
          if(isset($_REQUEST['name']))
              {
               $this->form_validation->set_rules("name","Name","trim|required|min_length[5]|max_length[15]");
              }
               $this->form_validation->set_rules("email","Email Id","trim|required|valid_email");
          if(isset($_REQUEST['age']))
          {
          
          $this->form_validation->set_rules("age","Age","trim|required|numeric|min_length[1]|max_length[2]");
          }
          if(isset($_REQUEST['address']))
          {  
          $this->form_validation->set_rules("address","Address","trim|required");
          }
          if(isset($_REQUEST['city']))
          {
          $this->form_validation->set_rules("city","City","trim|required");
          }
          if(isset($_REQUEST['state']))
          {
           $this->form_validation->set_rules("state","State","trim|required");
          }            
          if(isset($_REQUEST['country']))
          {    
          $this->form_validation->set_rules("country","Country","trim|required");
          }
            //$this->form_validation->set_rules("title","Title","required");
         
      if($this->form_validation->run()==FALSE)
          {
             echo validation_errors();
              //$this->load->view('registration_view');
           }
       else
         {
       $user_email=$_REQUEST['email'];
       
       
        $this->load->model('check_model');
       $data['info']=$this->check_model->emailVaildation($user_email);
       
        if(empty($data['info']))
       {
       
       
       $this->load->model('check_model');
      $resp=$this->check_model->agent_reg_data();
     if($resp=='1')
     {$p='';
      $from="sadanand.provab1@gmail.com"; 
           $pass="$p";
            $to=$user_email;  
            $subject="Welcome to punjab travelers ";
            $message="Your request is in process.For more info visit our site";


           $config = Array(
         'protocol' => 'smtp',
         'smtp_host' => 'ssl://smtp.googlemail.com',
         'smtp_port' => 465,
         'smtp_user' => $from, // change it to yours
         'smtp_pass' => $pass, // change it to yours
         'mailtype' => 'html',
         'charset' => 'iso-8859-1',
         'wordwrap' => TRUE
       );

      $this->load->library('email', $config);
      $this->email->set_newline("\r\n");
      $this->email->from($from); // change it to yours
      $this->email->to($to);// change it to yours
      $this->email->subject($subject);
      $this->email->message($message);

    if($this->email->send())
        {
         echo 'You are registered successfully';
        }
     else
        {echo 'You are registered successfully';
       //  show_error($this->email->print_debugger());
        }
        
     }
       
     } 
    if(!empty($data['info']))
     {
       echo"Email id already exists ";
     }
     
    }
  }      
        
}     


?>