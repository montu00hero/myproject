<?php

class check_model extends CI_Model
{
    function __construct() {
        parent::__construct();
        $this->load->database();
         $this->load->library('session');
    }
    
    function agent()
    {
     //echo"city";
     $qu='select f_id,f_name from reg_conf where status=1';
     $res=$this->db->query($qu);
     return $res->result(); 
       
    }
    
    
    
        function emailVaildation($details)
       {

         $emailid= $details; 
    
         $updates="select email from agent_reg where email='$emailid'";
         $res=$this->db->query($updates);
         return $res->result();
      }
    
    
    function agent_reg_data()
    {
       
       $data=array();
   
       if(isset($_REQUEST['name']))
       {
           $data['name']=$_REQUEST['name'];
       }
       if(isset($_REQUEST['age']))
       { 
        $data['age']=$_REQUEST['age']; 
        
       }
       if(isset($_REQUEST['address']))
       {
           
       $data['address']=$_REQUEST['address'];
       }
       if(isset($_REQUEST['city']))
       {
           
        $data['city']=$_REQUEST['city'];
       }
       
       if(isset($_REQUEST['state']))
       {
        $data['state']=$_REQUEST['state']; 
        
       }
     
       if(isset($_REQUEST['country']))
       {
          
        $data['country']=$_REQUEST['country'];
       }
         if(isset($_REQUEST['email']))
       {
          
        $data['email']=$_REQUEST['email'];
       }
       
        //print_r($data);
     
        $res= $this->db->insert('agent_reg', $data); 
        return $res;
    }    
    
    
   }

    
?>