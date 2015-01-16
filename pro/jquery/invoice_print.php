<!DOCTYPE html>
<html>
<head>
     <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
    <title><?php echo $this->projecttitle; ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
 
</head>
<body>
 <?php $total1 = ($bookinginfo->amount * $bookinginfo->currencyprice); ?>
 <div><center>
   <table  id="form_id" style="display:none; width:450px;">
  <form>
   <tr><TH COLSPAN=2>Add Markup</TH></tr>  
   <tr>
    <td>Enter Value</td> 
     <td>    
       <input type="text" name="markup" value="<?php echo $_REQUEST['markup']; ?>" />
      </td>
    </tr>
    <tr>
      <td>
       Select
      </td>
      <td>
       <input type="radio" name="per" value="1" checked="checked"> %  
       <input type="radio" name="per" value="2" <?php if($_REQUEST["per"] == "2") { echo 'checked="checked"'; } ?> > +
       <input type="hidden" name="total_amount" value=<?php echo $total1 ?> >
       </td>
    <tr>
      <td></td>
       <td> 
      <input  type="submit" value="Add" name="submit" /> 
     </td>
      </tr>
     </form>

  </table></center>

  <div id="email_display" style="display:none;" >
    <center>
     <input type="text" id="email_box" value="" onblur="" name="" />
     <input type="button" id="" value="Send" onclick="send();" />
    </center>
  </div>

 </div>

<input type="button" id="send_mail" onclick="show_email();" value="e-mail" />
<input type="button" id="add_markup" onclick="show_add_markup();" value="Add Markup" />

<table width="920" border="0" class="my_profile_name_ex_tab_whit" align="left" cellpadding="0" cellspacing="0" style="margin:15px 20px 0 15px;font-family:Arial, Helvetica, sans-serif; font-size:14px; border:1px solid #ccc;">
<tr>
	<td colspan="2" width="496" align="left" valign="top" style="text-align:center;font-family:MAIAN; font-size:20px;">Global Wings Tours India Pvt Ltd </td>
</tr>
<tr>
	<td style="text-align:left;font-family:Arial, Helvetica, sans-serif;font-size: 15px;width:66%"><br/><strong>Address :</strong><br />7, Sri Ganesh Temple Road, Near-Sukh Sagar, Kammanahalli,
<br />Ganesh Temple Rd, Jal Vayu Vihar, Kacharakanahalli,
<br />Bangalore, Karnataka 560043. 
<br /><strong>Tel : </strong>080-4094-2098<br/><strong>Fax :</strong> 080-4094-2098<br/><strong>E-mail:</strong> <a href="mailto:techsupport@globalwings.in"> techsupport@globalwings.in</a> </td>
 <td style="text-align:left;font-family:Arial, Helvetica, sans-serif;font-size: 15px;"> <div class="logo"><a href="http://www.globalwings.in/B2B/hotel/home"><img src="http://www.globalwings.in/B2B/images/logo.jpg"></a></div> </td>
</tr>
  <tr>
    <td colspan="2" width="496" align="left" valign="top" bgcolor="#e2e2e1" style="text-align:justify;font-family:MAIAN; font-size:14px;">
    <table width="100%" border="0" cellpadding="5px" style="line-height:18px;">
      <tr>
        <td colspan="2" align="center" bgcolor="#517ba5" style="color:#fff; font-weight:bold; font-family:Arial, Helvetica, sans-serif;font-size:19px;"><strong>I&nbsp;&nbsp;N&nbsp;&nbsp;V&nbsp;&nbsp;O&nbsp;&nbsp;I&nbsp;&nbsp;C&nbsp;&nbsp;E</strong></td>
        </tr>
      
    </table></td>
  </tr>
  <tr>
    <td colspan="2" align="left" valign="top" class="my_profile_name_ex_tab">
    <table width="100%" class="my_profile_name_ex_tab_whit" style=" font-weight:normal;">
    <tr>
      <td  align="left">&nbsp;</td>
      <td align="left">&nbsp;</td>
      <td align="left">&nbsp;</td>
    </tr>
    <tr><td width="19%"  align="left">Booking Number</td>
      <td width="2%" align="left">:</td>
      <td width="84%" align="left"><?php echo $bookinginfo->booking_number; ?></td></tr>
<tr><td align="left">Invoice Date</td>
  <td align="left">:</td>
  <td align="left"><?php echo $bookinginfo->voucher_date;?></td></tr>
<tr><td align="left">Hotel Name</td>
  <td align="left">:</td>
  <td align="left"><?php echo $bookinginfo->hotel_name; ?></td></tr>
<tr><td align="left">Address</td>
  <td align="left">:</td>
  <td align="left"><?php echo $hotel_info[0]->address.",&nbsp;".$bookinginfo->city; ?></td></tr>
<tr><td align="left">Phone</td>
  <td align="left">:</td>
  <td align="left"><?php echo $hotel_info[0]->phone;?></td></tr>
<tr>
  <td align="left">Room Type</td>
  <td align="left">:</td>
  <td align="left"><?php echo $bookinginfo->room_type; ?></td>
</tr>
   <tr>
    <td colspan="3" align="left" valign="top" class="my_profile_name_ex_tab">
    <table width="100%" border="1" bordercolor="#999999" cellpadding="5" cellspacing="0">
    <tr><td bgcolor="#517ba5"><strong style="color:#fff;">Check-In Date</strong></td>
      <td bgcolor="#517ba5"><strong style="color:#fff;">Check-Out Date</strong></td>
      <td bgcolor="#517ba5"><strong style="color:#fff;">Total Nights</strong></td>
      <td bgcolor="#517ba5"><strong style="color:#fff;">Total Price </strong></td>
    </tr>
    <?php
    $date1 = date("Y-m-d",strtotime($bookinginfo->check_in));
   $date2 = date("Y-m-d",strtotime($bookinginfo->check_out));
   $no_of_day = round(abs(strtotime($date1)-strtotime($date2))/86400);
	?>
 <tr><td><?php echo $bookinginfo->check_in; ?></td>
        <td><?php echo $bookinginfo->check_out; ?></td>
        <td><?php echo $no_of_day; ?></td>
        <?php $total = ($bookinginfo->amount * $bookinginfo->currencyprice); ?>
        <td><?php 
            
          $markup=$_REQUEST["markup"];
        
           $total_amt=$_REQUEST["total_amount"];     

        if($_REQUEST["per"]=="1")
         {    
               $markup=$_REQUEST["markup"];
        
           $total_amt=$_REQUEST["total_amount"];    
	    $new_tot_amt=$total_amt + ($markup/100)*$total_amt;
            
           echo $amt=$bookinginfo->agentselectcurrency."  ". ceil($new_tot_amt);             
 
	   
         }

        if($_REQUEST["per"]=="2")
        { 
                $markup=$_REQUEST["markup"];
        
           $total_amt=$_REQUEST["total_amount"];   
	     
             $new_tot_amt=$total_amt + $markup;
           
           echo $amt=$bookinginfo->agentselectcurrency."  ". $new_tot_amt;  	   

        }              
         if(!isset($_REQUEST["per"])){
          echo $amt=$bookinginfo->agentselectcurrency."  ".number_format($total, 2, ".", ""); 
          }
            ?>
           
          </td>
    </tr></table>
    </td>
  </tr>
  <tr>
     <td>&nbsp;</td>
  </tr>
  <tr>
  	<td colspan="3" style="font-size:12px">
    	<table cellpadding="0" cellspacing="0" width="100%">
        	<tr>
            	<td style="width:70%"><strong>Terms Of Payment</strong></td>
                <td></td>
            </tr>
            <tr>
            	<td></td>
                <td>For Global Wings Tours India Pvt Ltd.</td>
            </tr>
            <tr>
            	<td>1. CASH : Payment is to be made directly to our cashier.<br/>
                	2. CHEQUE : Cheques should be drawn in favour of Company's Name and payable at Bangalore<br/>
                    3. RECEIPTS : Official Receipt duly signed will be considered valid.<br/>
                  
				</td>
                <td></td>
            </tr>
            <tr>
            	<td> E.&O.E. Subject To  Jurisdiction Only </td>
                <td>Auth. Signatory</td>
            </tr>
            <tr>
            	<td>&nbsp;</td>
                <td>&nbsp;</td>
            </tr>
        </table>
    </td>
  </tr>
  <tr>
     <td style="font-size:12px;" colspan="3">
       <br /> 
  	Note: Please Note This is Payment Purpose Only Not a valid as a Hotel voucher 	 
  	This is Computer generated reciept so no need Signature <br />
  	<br />
Thanks for choosing <strong>Globalwings.in.</strong> </td>
  </tr>
  <tr>
    <td>&nbsp;</td>
  </tr>
 <tr>


 <td align="center"><a href="#" onClick="inv_printer(); return false;" ><img  src="<?php echo base_url(); ?>images/print.png"></a>
<br />

</td>
</tr> 
</table>

</body>
</html>

<?php  

  // echo $a="<html> <h2>". base_url()."</h2></html>";

    $html='<html><body><table width="920" border="0" class="my_profile_name_ex_tab_whit" align="left" cellpadding="0" cellspacing="0" style="margin:15px 20px 0 15px;font-family:Arial, Helvetica, sans-serif; font-size:14px; border:1px solid #ccc;">
<tr>
	<td colspan="2" width="496" align="left" valign="top" style="text-align:center;font-family:MAIAN; font-size:20px;">Global Wings Tours India Pvt Ltd </td>
</tr>
<tr>
	<td style="text-align:left;font-family:Arial, Helvetica, sans-serif;font-size: 15px;width:66%"><br/><strong>Address :</strong><br />7, Sri Ganesh Temple Road, Near-Sukh Sagar, Kammanahalli,
<br />Ganesh Temple Rd, Jal Vayu Vihar, Kacharakanahalli,
<br />Bangalore, Karnataka 560043. 
<br /><strong>Tel : </strong>080-4094-2098<br/><strong>Fax :</strong> 080-4094-2098<br/><strong>E-mail:</strong> <a href="mailto:techsupport@globalwings.in"> techsupport@globalwings.in</a> </td>
 <td style="text-align:left;font-family:Arial, Helvetica, sans-serif;font-size: 15px;"> <div class="logo"><a href="http://www.globalwings.in/B2B/hotel/home"><img src="http://www.globalwings.in/B2B/images/logo.jpg"></a></div> </td>
</tr>
  <tr>
    <td colspan="2" width="496" align="left" valign="top" bgcolor="#e2e2e1" style="text-align:justify;font-family:MAIAN; font-size:14px;">
    <table width="100%" border="0" cellpadding="5px" style="line-height:18px;">
      <tr>
        <td colspan="2" align="center" bgcolor="#517ba5" style="color:#fff; font-weight:bold; font-family:Arial, Helvetica, sans-serif;font-size:19px;"><strong>I&nbsp;&nbsp;N&nbsp;&nbsp;V&nbsp;&nbsp;O&nbsp;&nbsp;I&nbsp;&nbsp;C&nbsp;&nbsp;E</strong></td>
        </tr>
      
    </table></td>
  </tr>
  <tr>
    <td colspan="2" align="left" valign="top" class="my_profile_name_ex_tab">
    <table width="100%" class="my_profile_name_ex_tab_whit" style=" font-weight:normal;">
    <tr>
      <td  align="left">&nbsp;</td>
      <td align="left">&nbsp;</td>
      <td align="left">&nbsp;</td>
    </tr>
    <tr><td width="19%"  align="left">Booking Number</td>
      <td width="2%" align="left">:</td>
      <td width="84%" align="left">'. $bookinginfo->booking_number. '</td></tr>
<tr><td align="left">Invoice Date</td>
  <td align="left">:</td>
  <td align="left">'. $bookinginfo->voucher_date .'</td></tr>
<tr><td align="left">Hotel Name</td>
  <td align="left">:</td>
  <td align="left">'. $bookinginfo->hotel_name.'</td></tr>
<tr><td align="left">Address</td>
  <td align="left">:</td>
  <td align="left">'. $hotel_info[0]->address.",&nbsp;".$bookinginfo->city .'</td></tr>
<tr><td align="left">Phone</td>
  <td align="left">:</td>
  <td align="left">'. $hotel_info[0]->phone .'</td></tr>
<tr>
  <td align="left">Room Type</td>
  <td align="left">:</td>
  <td align="left">'. $bookinginfo->room_type .'</td>
</tr>
   <tr>
    <td colspan="3" align="left" valign="top" class="my_profile_name_ex_tab">
    <table width="100%" border="1" bordercolor="#999999" cellpadding="5" cellspacing="0">
    <tr><td bgcolor="#517ba5"><strong style="color:#fff;">Check-In Date</strong></td>
      <td bgcolor="#517ba5"><strong style="color:#fff;">Check-Out Date</strong></td>
      <td bgcolor="#517ba5"><strong style="color:#fff;">Total Nights</strong></td>
      <td bgcolor="#517ba5"><strong style="color:#fff;">Total Price </strong></td>
    </tr>
   
  
 <tr><td>'. $bookinginfo->check_in .'</td>
        <td>'. $bookinginfo->check_out .'</td>
        <td>'. $no_of_day .'</td>
       
        <td>
            '.$amt.'
          </td>
    </tr></table>
    </td>
  </tr>
  <tr>
     <td>&nbsp;</td>
  </tr>
  <tr>
  	<td colspan="3" style="font-size:12px">
    	<table cellpadding="0" cellspacing="0" width="100%">
        	<tr>
            	<td style="width:70%"><strong>Terms Of Payment</strong></td>
                <td></td>
            </tr>
            <tr>
            	<td></td>
                <td>For Global Wings Tours India Pvt Ltd.</td>
            </tr>
            <tr>
            	<td>1. CASH : Payment is to be made directly to our cashier.<br/>
                	2. CHEQUE : Cheques should be drawn in favour of Companys Name and payable at Bangalore<br/>
                    3. RECEIPTS : Official Receipt duly signed will be considered valid.<br/>
                  
				</td>
                <td></td>
            </tr>
            <tr>
            	<td> E.&O.E. Subject To  Jurisdiction Only </td>
                <td>Auth. Signatory</td>
            </tr>
            <tr>
            	<td>&nbsp;</td>
                <td>&nbsp;</td>
            </tr>
        </table>
    </td>
  </tr>
  <tr>
     <td style="font-size:12px;" colspan="3">
       <br /> 
  	Note: Please Note This is Payment Purpose Only Not a valid as a Hotel voucher 	 
  	This is Computer generated reciept so no need Signature <br />
  	<br />
Thanks for choosing <strong>Globalwings.in.</strong> </td>
  </tr>
  <tr>
    <td>&nbsp;</td>
  </tr>
 
</table>

</body>
</html>';

?>  

 
<input id="send" type='hidden' value='<?php echo ($html) ?>' /> 
  

<script>

function send()
{

 if($("#email_box").val()!='')
     {
            
               var str=escape($("#send").val()) ;
                 var sdata=$("#email_box").val()+'??*?'+str;   
           /// alert(sdata);
            //   alert('<?php echo base_url();?>');
              $.ajax({
                  type:"POST",
                  url:"<?php echo base_url();?>report/send_email",
                  data:"edata="+sdata,
                  success:function(msg){
                        alert(msg);
                    
                  },
                  error:function(){
                    alert("Operation failed");
                  }
                  
              });
              }             

      }

function show_email()
{
document.getElementById("email_display").style.display = "block";
document.getElementById("form_id").style.display = "none";
}

function show_add_markup()
{
document.getElementById("email_display").style.display = "none";
document.getElementById("form_id").style.display = "block";
document.getElementById("add_markup").style.display = "none";
}

function inv_printer()
{ 
document.getElementById("send_mail").style.visibility = "hidden";
document.getElementById("add_markup").style.visibility = "hidden";
document.getElementById("form_id").style.visibility = "hidden";
javascript:window.print();

}

</script>



