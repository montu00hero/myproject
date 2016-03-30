    
<div class="tabbable customtab">
    <ul class="nav nav-tabs taxiTabs">
        <li class="active mrgrgt"><a href="#outstation" data-toggle="tab">Outstation</a></li>
        <li class="mrgrgt"><a href="#local" data-toggle="tab">Local</a></li>
        <li class="mrgrgt"><a href="#transfer" data-toggle="tab">Transfer</a></li>
    </ul>
    <div class="tab-content">


        <div class="tab-content taxiTabContent">
<script type="text/javascript">
// $(function(){ 
//     var sidebar = $('#out');
//     sidebar'a.round','click',function(){
//        alert("cain");
//     });
// });
 
// }
function round_trip()
{
   var trip_type = document.getElementById('trip_type').value="RoundTrip"; 

    $(".search_multiple_left").css('display','block');
    $(".search_multiple_left_multicity").css('display','none');

     $(".taxiautocity_one").removeAttr('disabled');
     $(".taxiautocity_multi").attr('disabled','disabled');
}
function multi_city()
{
    var multi_city = document.getElementById('trip_type').value="MultiCity"; 

    $(".search_multiple_left_multicity").css('display','block');
    $(".search_multiple_left").css('display','none');

     $(".taxiautocity_multi").removeAttr('disabled');
     $(".taxiautocity_one").attr('disabled','disabled');
}
function one_way()
{
    var trip_type = document.getElementById('trip_type').value="OneWay";

     $(".search_multiple_left").css('display','block');
     $(".search_multiple_left_multicity").css('display','none');

      $(".taxiautocity_one").removeAttr('disabled');
     $(".taxiautocity_multi").attr('disabled','disabled');
}

$('.multi-field-wrapper').each(function() {
    var $wrapper = $('.multi-fields', this);
    $(".add-field", $(this)).click(function(e) {
        $('.multi-field:first-child', $wrapper).clone(true).appendTo($wrapper).find('input').val('').focus();
    });
    $('.multi-field .remove-field', $wrapper).click(function() {
        if ($('.multi-field', $wrapper).length > 1)
            $(this).parent('.multi-field').remove();
    });
});
</script> 

            <div class="tab-pane active" id="outstation">
                <form action="taxi-load.php" method="get" name="taxi_form" id="taxi_f"  >
                    <div class="col-md-9 mt20 mb10" id="out">
                    
                        <div class="col-md-2 nopadr"><a class="btn btn-primary round inactive" id="1" href="#" onclick="round_trip();">Round</a>
                        </div>
                        <div class="col-md-2 nopad"><a class="btn btn-primary inactive" id="2" href="#"onclick="one_way();" >Oneway</a>
                        </div>
                        <div class="col-md-2 nopad"><a class="btn btn-primary inactive" id="3" href="#" onclick="multi_city();">Multicity</a>
                        </div>
                    </div>
                    <div class="search_multiple_left">
                    <div class="col-md-12 search_multiple_destination">
                        <div class="col-lg-5 col-md-6 col-sm-6  marginbotom10 marginbotomdty"> <span class="formlabel">Select source city</span>
                            <div class="relativemask"> <span class="maskimg tfrom"></span>
                               <input type="text" required="" placeholder="Type Departure City" class="ft taxiautocity taxiautocity_one" name="origin" autocomplete="off" aria-required="true"><div id="suggestions_holder" class="suggestions" style="display: none;"></div>
                           </div>  
                       </div>
                       <div class="col-lg-5 col-md-6 col-sm-6  marginbotom10 marginbotomdty"> <span class="formlabel">Select destination city</span>
                        <div class="relativemask"> <span class="maskimg tto"></span>
                            <input type="text" required="" placeholder="Destination City" class="ft taxiautocity taxiautocity_one" name="destination" autocomplete="off" aria-required="true"><div id="suggestions_holder" class="suggestions" style="display: none;"></div>
                        </div>
                    </div>
                   
                </div>
                </div>
                <div class="search_multiple_left_multicity" style='display:none;' >
                    <div class="col-md-12 search_multiple_destination">
                        <div class="col-lg-5 col-md-6 col-sm-6  marginbotom10 marginbotomdty"> <span class="formlabel">Select source city</span>
                            <div class="relativemask"> <span class="maskimg tfrom"></span>
                               <input disabled='disabled' type="text" required="" placeholder="Type Departure City" class="ft taxiautocity taxiautocity_multi s_city" name="origin" autocomplete="off" aria-required="true"><div id="suggestions_holder" class="suggestions" style="display: none;"></div>
                           </div>  
                       </div>
                       <div class="col-lg-5 col-md-6 col-sm-6  marginbotom10 marginbotomdty"> <span class="formlabel">Select destination city</span>
                        <div class="relativemask"> <span class="maskimg tto"></span>
                            <input disabled='disabled' type="text" required="" placeholder="Destination City" class="ft taxiautocity taxiautocity_multi d_city" name="destination" autocomplete="off" aria-required="true"><div id="suggestions_holder" class="suggestions" style="display: none;"></div>
                        </div>
                    </div>
                    <div class="col-lg-2">
                    <div id="multidestination_add"> <img class="multi_img mt5" src="taxi/images/icon/plus_icon1.png"> </div>
                    </div>
                </div>
                </div>

                <div class="col-md-12 marginbotom10">
                    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4 fiveh"> <span class="formlabel">travel start date</span>
                        <div class="relativemask"> <span class="maskimg caln"></span>
                            <input type="text" required="" value="" class="forminput index_textfield_classcalander" id="taxi_start_date1" placeholder="Select Date" name="start_date" aria-required="true">                           
                        </div>
                    </div>  
                    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4 fiveh"> <span class="formlabel">travel start time</span>
                        <div class="relativemask"> <span class="maskimg caln"></span>
                            <input type="text" required="" value="" class="forminput taxi_timepicker" id="taxi_start_time1" placeholder="Select Date" name="start_time" aria-required="true">                           
                        </div>
                    </div>    
                    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4 fiveh"> <span class="formlabel">travel end date</span>
                        <div class="relativemask"> <span class="maskimg caln"></span>
                            <input type="text" required="" value="" class="forminput index_textfield_classcalander" id="taxi_end_date1" placeholder="Select Date" name="end_date" aria-required="true">                           
                        </div>
                    </div>                   

                </div>
                <div class="col-md-12 marginbotom10">
                    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4 fiveh"> 
                        <div class="formsubmit">
                        <button class="srchbutn comncolor" >Search Taxi</button>
                        </div>
                    </div></div>
                   <!--As default trip_type will be round Trip - If customer changes it will be changed -->
                    <input type="hidden" name="travel_type" id = "travel_type" value="1">
                     <input type="hidden" name="trip_type" id="trip_type" value="RoundTrip">
                </form>
            </div>

            <div class="tab-pane " id="local">
             <form action="taxi-load.php" method="post" name="taxi_form2" id="taxi_form2">
                <div class="col-md-9 mt20 mb10"><div class="col-md-2 nopadr"><a class="btn btn-primary inactive" onclick="full_day();" href="#">Fullday</a></div><div class="col-md-2 nopad"><a class="btn btn-primary inactive" onclick="half_day();" href="#">Halfday</a>
                <script type="text/javascript">
function full_day()
{
    var trip_type = document.getElementById('trip_type_local').value="FullDay";
}
function half_day()
{
    var trip_type = document.getElementById('trip_type_local').value="Half_Day";
}
                </script>
                </div></div>
                <div class="col-md-12">
                    <div class="col-lg-6 col-md-6 col-sm-6  marginbotom10 marginbotomdty"> <span class="formlabel">Select source city</span>
                        <div class="relativemask"> <span class="maskimg tfrom"></span>
                           <input type="text" require="" placeholder="Type Departure City" class="ft taxiautocity" name="origin" autocomplete="off" aria-required="true">
                       </div>  
                   </div>
                   <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4 fiveh"> <span class="formlabel">travel start date</span>
                    <div class="relativemask"> <span class="maskimg caln"></span>
                        <input type="text" require="" value="" class="forminput index_textfield_classcalander" id="taxi_start_date2" placeholder="Select Date" name="start_date" aria-required="true">                           

                    </div>
                </div>  


            </div>
            <div class="col-md-12">
               <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4 fiveh"> <span class="formlabel">travel start time</span>
                <div class="relativemask"> <span class="maskimg caln"></span>
                    <input type="text" require="" value="" class="forminput taxi_timepicker" id="taxi_start_time2" placeholder="Select Date" name="start_time" aria-required="true">                           
                </div>
            </div>    
            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4 fiveh"> <span class="formlabel">travel end date</span>
                <div class="relativemask"> <span class="maskimg caln"></span>
                    <input type="text" require="" value="" class="forminput index_textfield_classcalander" id="taxi_end_date2" placeholder="Select Date" name="end_date" aria-required="true">                           
                </div>
            </div>                   

        </div>
        <div class="col-md-12 marginbotom10">
            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4 fiveh"> 
                <div class="formsubmit">
                    <button class="srchbutn comncolor" type="submit" name="local">Search Taxi</button>
                </div>
            </div></div>
            <input type="hidden" name="travel_type" id= "travel_type" value="2">
            <input type="hidden" name="trip_type" id="trip_type_local" value="FullDay">
              
            </form>
        </div>
        <script type="text/javascript">
        
window.onload = function()
{
     $('.hotel').hide();
      $('.railway').hide();
       $(".railway :input").attr("disabled", true);
      $(".hotel :input").attr("disabled", true);
       $(".airport :input").attr("disabled", false);
    $('.airport').show();
}
        
function railway()
{
    var trip_type = document.getElementById('trip_type_transfer').value="Railway";
    $('.airport').hide();
     $('.hotel').hide();
       $(".hotel :input").attr("disabled", true);
      $(".airport :input").attr("disabled", true);
       $(".railway :input").attr("disabled", false);
      $('.railway').show();
    
}
function hotel()
{
    var trip_type = document.getElementById('trip_type_transfer').value="Hotel";
     $('.airport').hide();
      $('.railway').hide(); 
       $(".railway :input").attr("disabled", true);
      $(".airport :input").attr("disabled", true);
       $(".hotel :input").attr("disabled", false);
      $('.hotel').show();
   // $("#transfer_origin").removeClass("taxiairportlocaltion");
   // $("#transfer_origin").addClass("taxiautolocaltion");

}
function airport()
{
    var trip_type = document.getElementById('trip_type_transfer').value="Airport";
     $('.hotel').hide();
      $('.railway').hide();
      $(".railway :input").attr("disabled", true);
      $(".hotel :input").attr("disabled", true);
      $(".airport :input").attr("disabled", false);
    $('.airport').show();
}
        </script>

        <div class="tab-pane " id="transfer">
         <form action="taxi-load.php" method="post" name="taxi_form3" id="taxi_form3">
         
            <div class="col-md-10 mt20 mb10"><div class="col-md-2 nopadr"><a class="btn btn-primary inactive" onclick="airport();" href="#">Airport</a></div><div class="col-md-2 nopad"><a class="btn btn-primary inactive" onclick="hotel();" href="#">Area/Hotel</a></div><div class="col-md-2 nopad"><a class="btn btn-primary inactive" onclick="railway();" href="#">Railway Station</a></div></div>
            <div class="col-md-12"><div class="col-lg-12 col-md-12 col-sm-12  marginbotom10 marginbotomdty"> <span class="formlabel">Select source city</span>
                <div class="relativemask"> <span class="maskimg tmap"></span>
                   <input type="text" require="" placeholder="Type Departure City" class="ft taxiautocity"  name="origin" autocomplete="off" aria-required="true"><div id="suggestions_holder" class="suggestions" style="display: none;"></div>
               </div>  
           </div>
       </div>
       <div class="col-md-12">


       <div class="airport">
        <div class="col-lg-6 col-md-6 col-sm-6  marginbotom10 marginbotomdty"> <span class="formlabel">Select Pickup Location</span>
            <div class="relativemask"> <span class="maskimg tfrom"></span>
               <input type="text" require="" placeholder="Type pick up  City" class="ft taxiairportlocaltion" id="transfer_origin" name="pick_origin" autocomplete="off" aria-required="true" ><div id="suggestions_holder" class="suggestions" style="display: none;" ></div>
           </div>  
       </div>
       <div class="col-lg-6 col-md-6 col-sm-6  marginbotom10 marginbotomdty"> <span class="formlabel">Select Drop Location</span>
        <div class="relativemask"> <span class="maskimg tto"></span>
            <input type="text" require="" placeholder="Destination City" class="ft taxiautolocaltion" name="drop_destination" autocomplete="off" aria-required="true"><div id="suggestions_holder" class="suggestions" style="display: none;"></div>
        </div> 
         </div>
    </div>

<div class="hotel">
        <div class="col-lg-6 col-md-6 col-sm-6  marginbotom10 marginbotomdty"> <span class="formlabel">Select Pickup Location</span>
            <div class="relativemask"> <span class="maskimg tfrom"></span>
               <input type="text" require="" placeholder="Type pick up  City" class="ft taxiautolocaltion" id="transfer_origin" name="pick_origin" autocomplete="off" aria-required="true" ><div id="suggestions_holder" class="suggestions" style="display: none;" ></div>
           </div>  
       </div>
       <div class="col-lg-6 col-md-6 col-sm-6  marginbotom10 marginbotomdty"> <span class="formlabel">Select Drop Location</span>
        <div class="relativemask"> <span class="maskimg tto"></span>
            <input type="text" require="" placeholder="Destination City" class="ft taxiairport_railwaylocaltion" name="drop_destination" autocomplete="off" aria-required="true"><div id="suggestions_holder" class="suggestions" style="display: none;"></div>
        </div> 
         </div>
    </div>

    <div class="railway">
        <div class="col-lg-6 col-md-6 col-sm-6  marginbotom10 marginbotomdty"> <span class="formlabel">Select Pickup Location</span>
            <div class="relativemask"> <span class="maskimg tfrom"></span>
               <input type="text" require="" placeholder="Type pick up  City" class="ft taxirailwaylocaltion" id="transfer_origin" name="pick_origin" autocomplete="off" aria-required="true"><div id="suggestions_holder" class="suggestions" style="display: none;"></div>
           </div>  
       </div>
       <div class="col-lg-6 col-md-6 col-sm-6  marginbotom10 marginbotomdty"> <span class="formlabel">Select Drop Location</span>
        <div class="relativemask"> <span class="maskimg tto"></span>
            <input type="text" require="" placeholder="Destination City" class="ft taxiautolocaltion" name="drop_destination" autocomplete="off" aria-required="true"><div id="suggestions_holder" class="suggestions" style="display: none;"></div>
        </div> 
         </div>
    </div>


    <div class="col-md-12 marginbotom10">
        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4 fiveh"> <span class="formlabel">travel start date</span>
            <div class="relativemask"> <span class="maskimg caln"></span>
                <input type="text" require="" value="" class="forminput index_textfield_classcalander" id="taxi_start_date3" placeholder="Select Date" name="start_date" aria-required="true">                           
            </div>
        </div>  
        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4 fiveh"> <span class="formlabel">travel start time</span>
            <div class="relativemask"> <span class="maskimg caln"></span>
                <input type="text" require="" value="" class="forminput taxi_timepicker" id="taxi_start_time3" placeholder="Select Start Time" name="start_time" aria-required="true">                           
            </div>
        </div>    
        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4 fiveh"> <span class="formlabel">travel end date</span>
            <div class="relativemask"> <span class="maskimg caln"></span>
                <input type="text" require="" value="" class="forminput index_textfield_classcalander" id="taxi_end_date3" placeholder="Select Date" name="end_date" aria-required="true">                           
            </div>
        </div>                   

    </div>

    <div class="col-md-12 marginbotom10">
        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4 fiveh"> 
            <div class="formsubmit">
                <button class="srchbutn comncolor" type="submit" name="tranfer">Search Taxi</button>
            </div>
        </div></div> 
         <input type="hidden" name="trip_type" id="trip_type_transfer" value="Airport">
        <input type="hidden" name="travel_type" id= "travel_type" value="3">
    </div>
    </form>
</div>
</div>
</div>
</div>
<style>
a.active {
    
    background-color: #428bca;
    border-color: #357ebd;
    color: #fff;
}

a.inactive {
    border:0;
    background:0;
}
</style>
<?php
if(($_SERVER['HTTP_HOST']=='192.168.0.46') || ($_SERVER['HTTP_HOST']=='localhost'))
{          
 $SITE_URL='http://192.168.0.46/projects/JBMALL1/';
}
else
{
    $SITE_URL='http://www.jbmall.in/';
}      
?>
