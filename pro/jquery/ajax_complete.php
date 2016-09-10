
<html>
    <head>
        <script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>

        
    </head>
    <body>


        
<h6>1.Whenever an Ajax request is about to be sent, jQuery checks whether 
    there are any other outstanding Ajax requests. 
    If none are in progress, jQuery triggers the ajaxStart event. 
    Any and all handlers that have been registered with the .ajaxStart() method are
    executed at this time.<br></h6>       

<h6>2.Whenever an Ajax request is about to be sent, jQuery triggers the ajaxSend event. 
    Any and all handlers that have been registered with the .ajaxSend() method
    are executed at this time.<br></h6>       
 
<h6>3.Whenever an Ajax request completes successfully, jQuery triggers the ajaxSuccess event.
    Any and all handlers that have been registered with the .ajaxSuccess() method 
    are executed at this time.<br></h6>        

<h6>4.Whenever an Ajax request completes with an error, jQuery triggers the ajaxError event. 
    Any and all handlers that have been registered with the .ajaxError() method are executed at this time. 
    Note: This handler is not called for cross-domain script and cross-domain JSONP requests.
    <br></h6>        

<h6>5.Whenever an Ajax request completes, jQuery triggers the ajaxComplete event.
    Any and all handlers that have been registered with the .ajaxComplete() method are 
    executed at this time.<br></h6>
        
<h6>6.Whenever an Ajax request completes, jQuery checks whether there are any other outstanding 
    Ajax requests. If none remain, jQuery triggers the ajaxStop event. 
    Any and all handlers that have been registered with the .ajaxStop() method are executed at this time. 
    The ajaxStop event is also triggered if the last outstanding Ajax 
    request is cancelled by returning false within the beforeSend callback function. 
    <br></h6>        
<div>
    <h4 id="id2"></h4>
</div>
    </body>
</html>
<script> 
$(function(){
 $('#id2').load('www.google.com');
});

$(document).ajaxComplete(function(){
    alert("axaj completed");
});

$( document ).ajaxStart(function() {
   alert( "Triggered ajaxStart handler." );
});

$( document ).ajaxSend(function() {
  alert( "Triggered ajaxSend handler." );
});

$(document).ajaxSuccess(function() {
  alert( "Triggered ajaxSuccess handler." );
});

$( document ).ajaxError(function() {
  alert( "Triggered ajaxError handler." );
});

$( document ).ajaxStop(function() {
  alert( "Triggered ajaxStop handler." );
});
</script>