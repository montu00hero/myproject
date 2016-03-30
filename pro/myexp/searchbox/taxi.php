<!DOCTYPE html>
<html lang="en">
    <head>
        <link rel="icon" href="" type="image/x-icon">
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <meta name="SKYPE_TOOLBAR" content="SKYPE_TOOLBAR_PARSER_COMPATIBLE"/>
        <meta name="description" content="">
        <meta name="author" content="">
        <title>JB Mall</title>
        <link href='http://fonts.googleapis.com/css?family=Lato:700,400' rel='stylesheet' type='text/css' />
        <link href='http://fonts.googleapis.com/css?family=Arimo' rel='stylesheet' type='text/css' />
        <link href='http://fonts.googleapis.com/css?family=Arimo:700' rel='stylesheet' type='text/css'>
        <!-- Bootstrap Core CSS -->
        <link href="css1/bootstrap.min.css" rel="stylesheet" />

        <!-- Custom CSS -->
        <link href="css1/custom.css" rel="stylesheet" />
        <link href="css1/media.css" rel="stylesheet" />

        <!-- Custom Fonts -->
        <link href="css1/font-awesome.min.css" rel="stylesheet" type="text/css">
        <link rel="stylesheet" href="css1/backslider.css" type="text/css" media="screen" />
        <link rel="stylesheet" href="css1/backslider2.css" type="text/css" media="screen" />
        <link rel="stylesheet" href="css1/datepicker.css" type="text/css" media="screen" />
        <link href="css1/owl.carousel.css" rel="stylesheet">
        <link href="taxi/css/jquery.timepicker.css" rel="stylesheet">
        <link type="text/css" href="js/autosuggest/css/jquery.coolautosuggest.css" rel="stylesheet" media="screen"/>

        <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
                <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
                <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
            <![endif]-->

    </head>
    <?php include 'header.php'; ?>
    <?php include 'taxi-banner1.php'; ?>
    <!-- full deal -->
   <?php include 'bus_deal.php' ?>
   <?php include 'footer.php'; ?>
<?php $city = "Bangalore"; ?>

    <script src="js/jquery-1.11.0.js"></script>
    <script src="js/autosuggest/js/jquery.coolautosuggest.js"></script>
    <script src="taxi/js/jquery.timepicker.min.js"></script>
    
    <script>
       (function(){
        $(function(){
            $(".taxiautocity").coolautosuggest({
                    url:"<?php echo $SITE_URL; ?>taxi/taxi-autosuggest.php?chars="
                });  
                    $('.index_textfield_classcalander').datepicker({
                    dateFormat: "dd-M-yy",
                    autoclose: true,
                    todayHighlight: true,
                    numberOfMonths:2,
                    showButtonPanel: false,
                    minDate:0
                 }); 
        })
 $(function(){
            $(".taxiautolocaltion").coolautosuggest({
                    url:"<?php echo $SITE_URL; ?>taxi/taxi_auto_locations.php?chars="
                });   
        })


  $(function(){
            $(".taxiairportlocaltion").coolautosuggest({
                    url:"<?php echo $SITE_URL; ?>taxi/taxi_auto_airport.php?chars="
                });   
                 
        })

  $(function(){
            $(".taxirailwaylocaltion").coolautosuggest({
                    url:"<?php echo $SITE_URL; ?>taxi/taxi_auto_railway.php?chars="
                });   
                 
        })

   $(function(){
            $(".taxiairport_railwaylocaltion").coolautosuggest({
                    url:"<?php echo $SITE_URL; ?>taxi/taxi_auto_airport_railway.php?&chars="
                });   
                 
        }) 


        $('#multidestination_add').on('click', function() { 
               var vals=$( ".d_city" ).last().val();
               if(vals!=""){
                multiDataList = '<div class="search_multiple_left_multicity"><div class="col-lg-5 col-md-6 col-sm-6  marginbotom10 marginbotomdty"> <span class="formlabel">Select source city</span>';
                            multiDataList += '<div class="relativemask"> <span class="maskimg tfrom"></span>';
                               multiDataList += '<input type="text" placeholder="Type Departure City" class="ft taxiautocity taxiautocity_multi s_city" name="origin" autocomplete="off" aria-required="true">';
                               multiDataList += '<div id="suggestions_holder" class="suggestions" style="display: none;"></div></div></div>';
                       multiDataList += '<div class="col-lg-5 col-md-6 col-sm-6  marginbotom10 marginbotomdty"> <span class="formlabel">Select destination city</span><div class="relativemask"> <span class="maskimg tto"></span><input type="text"  placeholder="Destination City" class="ft taxiautocity taxiautocity_multi d_city" name="destination" autocomplete="off" aria-required="true"><div id="suggestions_holder" class="suggestions" style="display: none;"></div></div></div>';
                    multiDataList += '<div class="col-lg-2"><div class="multidestination_minus"> <img class="multi_img mt5" src="taxi/images/icon/minus_icon.png"> </div></div></div>';
                $(multiDataList).appendTo('.search_multiple_destination');

                $(".taxiautocity").coolautosuggest({
                    url:"<?php echo $SITE_URL; ?>taxi/taxi-autosuggest.php?chars="
                }); 
              $( ".s_city" ).last().val(vals); 
            }       
        });
         $(document).on('click', '.multidestination_minus', function() {
            $(this).closest('.search_multiple_left_multicity').remove();
        });

   

       })(jQuery);
 
    //Auto Suggest for bust taxi_auto_railway.php


    </script>

            <script>
                $(function() {
                    $('.taxi_timepicker').timepicker({

                            'scrollDefault': 'now',
                            'showDuration': true
                    });
                });
            </script>
   

 
    <!-- Bootstrap Core JavaScript --> 
    <script src="js/bootstrap.min.js"></script> 
    <script type="text/javascript" src="js/backslider.js"></script> 
    <script type="text/javascript" src="js/backslider2.js"></script> 
    <!--<script type="text/javascript" src="js/bootstrap-datepicker.js"></script>-->
    <script src="js/owl.carousel.min.js"></script> 
    <script type="text/javascript" src="js/jquery.popupoverlay.js"></script> 
    <!-- Script to Activate the Carousel --> 
    <script type='text/javascript' src='js/jquery.customSelect.js'></script> 
    <script type='text/javascript' src='js/index_script.js'></script> 
      <script src="datepicker/jquery.ui.datepicker.js"></script>
    <link type="text/css" href="datepicker/jquery.ui.theme.css" rel="stylesheet" /> 

<script src="js/jquery.validate.min.js"></script>   
<!--script type='text/javascript' src='js/index_script.js'></script--> 
    <div id="fadeandscale" class="wellme">
        <div class="signdiv">
            <div class="insigndiv">
                <div class="leftpul"> <a class="logspecify facecolor"> <span class="fa fa-facebook"></span>
                        <div class="mensionsoc">Login with Facebook</div>
                    </a> <a class="logspecify tweetcolor"> <span class="fa fa-twitter"></span>
                        <div class="mensionsoc">Login with Twitter</div>
                    </a> <a class="logspecify gpluses"> <span class="fa fa-google-plus"></span>
                        <div class="mensionsoc">Login with Google Plus</div>
                    </a> </div>
                <div class="centerpul">
                    <div class="orbar"><strong>Or</strong></div>
                </div>
                <div class="ritpul">
                    <div class="rowput"> <span class="fa fa-user"></span>
                        <input class="form-control logpadding" type="text" placeholder="Username">
                    </div>
                    <div class="rowput"> <span class="fa fa-lock"></span>
                        <input class="form-control logpadding" type="text" placeholder="Password">
                    </div>
                    <div class="misclog"> <a class="rember">
                            <input type="checkbox" />
                            Remember me</a> <a class="forgtpsw">Forgot password?</a> </div>
                    <div class="clear"></div>
                    <button class="submitlogin">Login</button>
                    <div class="clear"></div>
                    <div class="dntacnt">Don't have an account? <a class="fadeandscale_close fadeandscalereg_open">Sign up</a> </div>
                </div>
            </div>
        </div>
    </div>
    <div id="fadeandscalereg" class="wellme">
        <div class="signdiv">
            <div class="insigndiv">
                <div class="leftpul"> <a class="logspecify facecolor"> <span class="fa fa-facebook"></span>
                        <div class="mensionsoc">Sign up with Facebook</div>
                    </a> <a class="logspecify tweetcolor"> <span class="fa fa-twitter"></span>
                        <div class="mensionsoc">Sign up with Twitter</div>
                    </a> <a class="logspecify gpluses"> <span class="fa fa-google-plus"></span>
                        <div class="mensionsoc">Sign up with Google Plus</div>
                    </a> </div>
                <div class="centerpul">
                    <div class="orbar"><strong>Or</strong></div>
                </div>
                <a class="logspecify mymail"> <span class="fa fa-envelope"></span>
                    <div class="mensionsoc">Sign up with email</div>
                </a>
                <div class="signupul">
                    <div class="rowput"> <span class="fa fa-user"></span>
                        <input class="form-control logpadding" type="text" placeholder="First name">
                    </div>
                    <div class="rowput"> <span class="fa fa-user"></span>
                        <input class="form-control logpadding" type="text" placeholder="Last name">
                    </div>
                    <div class="rowput"> <span class="fa fa-envelope"></span>
                        <input class="form-control logpadding" type="text" placeholder="Your email">
                    </div>
                    <div class="rowput"> <span class="fa fa-lock"></span>
                        <input class="form-control logpadding" type="text" placeholder="Password">
                    </div>
                    <div class="rowput"> <span class="fa fa-lock"></span>
                        <input class="form-control logpadding" type="text" placeholder="Confirm password">
                    </div>
                    <div class="misclog"> <a class="rember">
                            <input type="checkbox" />
                            Tell me about travel apt news</a> </div>
                    <div class="clear"></div>
                    <div class="signupterms"> By signing up, I agree to Travel apt's <a>Terms of Service</a>,<a> Privacy Policy</a>, <a>Guest Refund Policy</a>, and <a>Host Guarantee Terms</a>. </div>
                    <div class="clear"></div>
                    <button class="submitlogin">Sign up</button>
                </div>
                <div class="clear"></div>
                <div class="dntacnt">Don't have an account? <a class="fadeandscalereg_close fadeandscale_open">Sign in</a> </div>
            </div>
        </div>
    </div>
</body>
</html>

