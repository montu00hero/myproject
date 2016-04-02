<link type="text/css" href="datepicker.css" rel="stylesheet" /> 

 <script src="jquery.ui.datepicker.js"></script>



<script>
 $('#taxi_start_date1').datepicker({
                                    dateFormat: "dd/mm/yy",
                                    autoclose: true,
                                    todayHighlight: true,
                                    numberOfMonths:2,
                                    showButtonPanel: false,
                                    minDate:0,
                                    onClose: function( selectedDate ) {
                                        var date2 = $('#taxi_start_date1').datepicker('getDate', '+1d'); 
                                        date2.setDate(date2.getDate()+1); 
                                        $( "#taxi_end_date1" ).datepicker("setDate", date2 );
                                        $( "#taxi_end_date1" ).datepicker( "option", "minDate", date2 );
                                    }
                                });
             $('#taxi_end_date1').datepicker({
                        dateFormat: "dd/mm/yy",
                        autoclose: true,
                        todayHighlight: true,
                        numberOfMonths:2,
                        showButtonPanel: false,
                        minDate:0
                    });
                    
     </script>               

     
     
    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4 fiveh"> <span class="formlabel">travel start date</span>
       <div class="relativemask"> <span class="maskimg caln"></span>
           <input type="text" required="" value="" class="forminput index_textfield_classcalander" id="taxi_start_date1" placeholder="Select Date" name="start_date" aria-required="true">                           
       </div>
   </div>  

   <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4 fiveh"> <span class="formlabel">travel end date</span>
       <div class="relativemask"> <span class="maskimg caln"></span>
           <input type="text" required="" value="" class="forminput index_textfield_classcalander" id="taxi_end_date1" placeholder="Select Date" name="end_date" aria-required="true">                           
       </div>
   </div>        

     
<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

