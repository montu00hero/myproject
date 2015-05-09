<script>
alert($(this).parents('div.row').children('div:last-child').find('.pax').prop('class'));

  //------------------minus---------

    $(document).on('click', '.minus.child', function () {
        var sp = parseFloat($(this).siblings('.datemix').text());
        if (sp > 0) {
            $(this).siblings('.datemix').text(sp - 1);
            $(this).siblings('.pax').val(sp - 1);
        } else {
            $(this).siblings('.datemix').text(0); // Otherwise put a 0 there
            $(this).siblings('.pax').val(0); // Otherwise put a 0 there
        }
    });



    $(document).on('click', '.minus.adult', function () {
        var sp = parseFloat($(this).siblings('.datemix').text());
        if (sp > 0) { //alert($(this).parents('div.row').children('div:last-child').find('.pax').val());
            var inf =parseFloat($(this).parents('div.row').children('div:last-child').find('.pax').val());
            var adt = $(this).siblings('.pax').val();
           
          
            if (adt <= inf) {    alert(inf);
                //$('#infant').val(adt - 1);
                //$('#infant').siblings('.datemix').text(adt - 1);
                $(this).parents('div.row').children('div:last-child').find('.datemix').text(adt-1);
                $(this).parents('div.row').children('div:last-child').find('.pax').val(adt-1);
                
                
            }
            $(this).siblings('.datemix').text(sp - 1);
            $(this).siblings('.pax').val(sp - 1);


        } else {
            $(this).siblings('.datemix').text(0); // Otherwise put a 0 there
            $(this).siblings('.pax').val(0); // Otherwise put a 0 there
        }
    });



    $(document).on('click', '.minus.infant', function () {
        var sp = parseFloat($(this).siblings('.datemix').text());
        if (sp > 0) {
            $(this).siblings('.datemix').text(sp - 1);
            $(this).siblings('.pax').val(sp - 1);
        } else {
            $(this).siblings('.datemix').text(0); // Otherwise put a 0 there
            $(this).siblings('.pax').val(0); // Otherwise put a 0 there
        }
    });




//-----------------------------------------------------------------


//--------------------flight (+)-----------------------------------

    $(document).on('click', '.plus.child', function () {

        var sp = parseFloat($(this).siblings('.datemix').text());
        if (sp >= 0 && sp < 9) {

            $(this).siblings('.datemix').text(sp + 1);
            $(this).siblings('.pax').val(sp + 1);
        }
    });

    $(document).on('click', '.plus.adult', function () {

        var sp = parseFloat($(this).siblings('.datemix').text());
        if (sp >= 0 && sp < 9) {

        
            $(this).siblings('.datemix').text(sp + 1);
            $(this).siblings('.pax').val(sp + 1);
        }
    });


    $(document).on('click', '.plus.infant', function () {
        var sp = parseFloat($(this).siblings('.datemix').text());
        if (sp >= 0 && sp < 9){
            var adt = parseInt($(this).parents("div.row").children("div:first").find(".datemix").text());
            var inf = $(this).siblings('.pax').val();
            if (adt > inf){
                $(this).siblings('.datemix').text(sp + 1);
                $(this).siblings('.pax').val(sp + 1);
            }
        }
    });

    //--------------------------------------------------------------










</script>




  					<div class="search-engine-content">
  						<div class="tab-content ">
  							<div class="tab-pane active" id="flight">
  								<form name="flight" id="flight_form" action="<?php echo WEB_URL;?>/flight/search" method="get" autocomplete="off">
  									<div class="intabs">
  										<h3 class="tabinhed">Book Domestic &amp; International Flight Tickets </h3>
  										<label class="tripmen">
  											<input type="radio" class="triprad iradio_flat-blue" name="trip_type" value="oneway"/>
  											<strong>One Way</strong> </label>
  											<label class="tripmen">
  												<input type="radio" class="triprad iradio_flat-blue" name="trip_type" value="circle" checked/>
  												<strong>Round Trip</strong> </label>
  												<label class="tripmen">
  													<input type="radio" class="triprad iradio_flat-blue" name="trip_type" value="multicity"/>
  													<strong>Multi City</strong> </label>
  													<div class="clear"></div>
  													<div class="multyflightwrap">
  														<div class="full normal">
  															<div class="row">
  																<div class="col-lg-6 col-md-6 col-sm-6  marginbotom10 disover"> <span class="formlabel">From</span>
  																	<div class="relativemask"> <span class="maskimg mfrom"></span>
  																		<input type="text" class="ft fromflight" placeholder="Region, City or Hotel Name" name="from" required/>
  																	</div>
  																</div>
  																<div class="col-lg-6 col-md-6 col-sm-6  marginbotom10 disover"> <span class="formlabel">To</span>
  																	<div class="relativemask"> <span class="maskimg mto"></span>
  																		<input type="text" class="ft departflight" placeholder="Region, City or Hotel Name" name="to" required/>
  																	</div>
  																</div>
  															</div>
  															<div class="clearfix"></div>
  															<div class="row marginbotom10 nopad">
  																<div class="col-lg-4 col-md-4 col-sm-4 col-xs-4 mefullwd fiveh"> <span class="formlabel">Departure</span>
  																	<div class="relativemask"> <span class="maskimg caln"></span>
  																		<input  type="text" class="forminput" name="depature" id="depature"  placeholder="Select Date" required/>
  																	</div>
  																</div>
  																<div class="col-lg-4 col-md-4 col-sm-4 col-xs-4 mefullwd fiveh"> <span class="formlabel">Return</span>
  																	<div class="relativemask"> <span class="maskimg caln"></span>
  																		<input type="text" class="forminput" name="return" id="return" placeholder="Select Date" required/>
  																	</div>
  																</div>
  																<div class="col-lg-4 col-md-4 col-sm-4 col-xs-4 mefullwd fiveh"> <span class="formlabel">Class</span>
  																	<div class="selectedwrap">
  																		<select class="mySelectBoxClass flyinputsnor" id="class" name="v_class" required>
  																			<option value="All">ALL</option>
  																			<option value="Economy">Economy</option>
  																			<option value="PremiumEconomy">PremiumEconomy</option>
  																			<option value="Business">Business</option>
  																			<option value="PremiumBusiness">PremiumBusiness</option>
  																		</select>
  																	</div>
  																</div>
  															</div>
  															<div class="row nopad roundtrip">
                                                                                                                            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4 mefullwd fiveh"> <span class="formlabel">Adult</span>
                                                                                                                                <div class="selectedwrapnum">
                                                                                                                                    <div class="persnm padult"></div>
                                                                                                                                    <div class="onlynumwrap">
                                                                                                                                        <div class="onlynum"> <span class="btnminus meex cmnum adult minus">-</span>
                                                                                                                                            <div class="datemix meex">1</div>
                                                                                                                                            <input class="pax" type="hidden" id="adult" name="adult" value="1" required>
                                                                                                                                            <span class="btnplus meex cmnum adult plus">+</span> </div>
                                                                                                                                    </div>
                                                                                                                                </div>
                                                                                                                            </div>
                                                                                                                            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4 mefullwd fiveh"> <span class="formlabel">Children(2-12 yrs)</span>
                                                                                                                                <div class="selectedwrapnum">
                                                                                                                                    <div class="persnm pachildrn"></div>
                                                                                                                                    <div class="onlynumwrap">
                                                                                                                                        <div class="onlynum"> <span class="btnminus meex cmnum child minus">-</span>
                                                                                                                                            <div class="datemix meex">0</div>
                                                                                                                                            <input class="pax" type="hidden" id="child" name="child" value="0" required>
                                                                                                                                            <span class="btnplus meex cmnum child plus">+</span> </div>
                                                                                                                                    </div>
                                                                                                                                </div>
                                                                                                                            </div>
                                                                                                                            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4 mefullwd fiveh"> <span class="formlabel">Infant(0-2 yrs)</span>
                                                                                                                                <div class="selectedwrapnum">
                                                                                                                                    <div class="persnm painf"></div>
                                                                                                                                    <div class="onlynumwrap">
                                                                                                                                        <div class="onlynum"> <span class="btnminus meex cmnum infant minus">-</span>
                                                                                                                                            <div class="datemix meex">0</div>
                                                                                                                                            <input class="pax" type="hidden" id="infant" name="infant" value="0" required>
                                                                                                                                            <span class="btnplus meex cmnum infant plus">+</span> 
                                                                                                                                        </div>
                                                                                                                                    </div>
                                                                                                                                </div>
                                                                                                                            </div>
                                                                                                                        </div>
                                                                                                                </div>

  																	<!--    multycity     -->
  																	<div class="full multicity" style="display:none;">
  																		<div class="addedCities">
  																			<div class="row nopad multyflight">
  																				<div class="col-md-12 nopad col-xs-12 mefullwd left marbotom20">
  																					<div class="col-md-9 col-xs-9 mefullwd nopad">
  																						<div class="col-md-6 col-sm-6 col-xs-6 mefullwd"> <span class="formlabel">From</span>
  																							<div class="relativemask"> <span class="maskimg mfrom"></span>
  																								<input type="text" class="ft fromflight" placeholder="Region, City or Hotel Name" name="mfrom[0]" id="from1" required/>
  																							</div>
  																						</div>
  																						<div class="col-md-6 col-sm-6 col-xs-6 mefullwd"> <span class="formlabel">To</span>
  																							<div class="relativemask"> <span class="maskimg mto"></span>
  																								<input type="text" class="ft fromflight" placeholder="Region, City or Hotel Name" name="mto[0]" id="to1" required/>
  																							</div>
  																						</div>
  																					</div>
  																					<div class="col-md-3 col-sm-3 col-xs-3 mefullwd"> <span class="formlabel">Departure</span>
  																						<div class="relativemask"> <span class="maskimg caln"></span>
  																							<input  type="text" class="forminput" name="mdepature[0]" id="depature1"   placeholder="Select Date" required/>
  																						</div>
  																					</div>
  																				</div>
  																				<div class="col-md-2"></div>
  																			</div>
  																			<div class="row nopad multyflight">
  																				<div class="col-md-12 nopad col-xs-12 mefullwd left marbotom20">
  																					<div class="col-md-9 col-xs-9 mefullwd nopad">
  																						<div class="col-md-6 col-sm-6  col-xs-6 mefullwd"> <span class="formlabel">From</span>
  																							<div class="relativemask"> <span class="maskimg mfrom"></span>
  																								<input type="text" class="ft fromflight" placeholder="Select Origin" name="mfrom[1]" id="from2" required/>
  																							</div>
  																						</div>
  																						<div class="col-md-6 col-sm-6 mefullwd col-xs-6"> <span class="formlabel">To</span>
  																							<div class="relativemask"> <span class="maskimg mto"></span>
  																								<input type="text" class="ft fromflight" placeholder="Select Origin" name="mto[1]" id="to2" required/>
  																							</div>
  																						</div>
  																					</div>
  																					<div class="col-md-3 col-sm-3 mefullwd col-xs-3"> <span class="formlabel">Departure</span>
  																						<div class="relativemask"> <span class="maskimg caln"></span>
  																							<input  type="text" class="forminput" name="mdepature[1]" id="depature2"   placeholder="Select Date" required/>
  																						</div>
  																					</div>
  																				</div>
  																			</div>
  																		</div>
  																		<div class="clearfix"></div>
  																		<div class="row">
  																			<div class="col-md-9">
  																				<div class="col-md-6 nopad col-sm-6">
  																					<div class="col-md-12 c-padding-left-null mefullwd fiveh"> <span class="formlabel">Class</span>
  																						<div class="selectedwrap">
  																							<select class="mySelectBoxClass flyinputsnor" id="class" name="class" required>
  																								<option value="First">First</option>
  																								<option value="Business">Business</option>
  																								<option selected="selected" value="Economy">Economy</option>
  																								<option value="Main">Main</option>
  																								<option value="Premium">Premium</option>
  																							</select>
  																						</div>
  																					</div>
  																				</div>
  																			</div>
  																			<div class="mefullwd col-md-3 pull-right nopadadd"> <span class="formlabel">&nbsp;</span>
  																				<div class="addflight">
  																					<p class="multi-add-text">Add More City</p>
  																					<span class="fa fa-plus"></span></div>
  																				</div>
  																			</div>
  																			<div class="row nopad left marbotom20 col-md-12">
  																				<div class="col-md-4 col-sm-4 mefullwd fiveh"> <span class="formlabel">Adult</span>
  																					<div class="selectedwrapnum">
  																						<div class="persnm padult"></div>
  																						<div class="onlynumwrap">
  																							<div class="onlynum"> <span class="btnminus meex cmnum adult minus">-</span>
  																								<div class="datemix meex">1</div>
  																								<input class="pax multi_adt" type="hidden" id="adult" name="adult" value="1" required>
  																								<!-- Tardigrade --> 
  																								<span class="btnplus meex cmnum adult plus">+</span> </div>
  																							</div>
  																						</div>
  																					</div>
  																					<div class="col-md-4 col-sm-4 mefullwd fiveh"> <span class="formlabel">Children(2-12 yrs)</span>
  																						<div class="selectedwrapnum">
  																							<div class="persnm pachildrn"></div>
  																							<div class="onlynumwrap">
  																								<div class="onlynum"> <span class="btnminus meex cmnum child minus">-</span>
  																									<div class="datemix meex">0</div>
  																									<input class="pax multi_chd" type="hidden" id="child" name="child" value="0" required>
  																									<span class="btnplus meex cmnum child plus">+</span> </div>
  																								</div>
  																							</div>
  																						</div>
  																						<div class="col-md-4 col-sm-4 mefullwd fiveh"> <span class="formlabel">Infant(0-2 yrs)</span>
  																							<div class="selectedwrapnum">
  																								<div class="persnm painf"></div>
  																								<div class="onlynumwrap">
  																									<div class="onlynum"> <span class="btnminus meex cmnum infant minus">-</span>
  																										<div class="datemix meex">0</div>
  																										<input class="pax multi_inf" type="hidden" id="infant" name="infant" value="0" required>
  																										<span class="btnplus meex cmnum infant plus">+</span> </div>
  																									</div>
  																								</div>
  																							</div>
  																						</div>
  																					</div>
