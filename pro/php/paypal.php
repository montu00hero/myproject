

<?php 

   define('PROJECT_NAME', 'TESTING');
   define('PROJECT_URL', $_SERVER['DOCUMENT_ROOT']);
  
// for test
define("PAYPAL_URL", "https://www.sandbox.paypal.com/cgi-bin/webscr");
define("PAYPAL_BUSINESS_EMAIL", "mukeshmailto-facilitator@gmail.com");

// for live
//define("PAYPAL_URL", "https://www.paypal.com/cgi-bin/webscr");
//define("PAYPAL_BUSINESS_EMAIL", "amr@amrtours.net");

define("PAYPAL_RETURN_URL", PROJECT_URL."/CRS_Response.php");
define("PAYPAL_CANCEL_RETURN_URL", PAYPAL_RETURN_URL);
define("PAYMENT_GATEWAY_CHARGES", 2.5);

$paynowprice=200.00;
$booking_for='test';

?>




<html>
    <head>
        <title><?php echo PROJECT_NAME;?> :: Taking to paying site</title>
        <!--<link rel="shortcut icon" href="assets/images/fav.ico" type="image/x-icon" />-->
    <script>
       // var hash = '<?php echo $hash ?>';
        //function submitPayuForm() {
        //if(hash == '') {
        //return;
        //}
        //var payuForm = document.forms.payuForm;
        //payuForm.submit();
        //}
    </script>
    </head>
    <body onload="submitPayuForm()">
        <form action="<?php echo PAYPAL_URL; ?>" method="post" name="payuForm">
            <input name = "business" value = "<?php echo PAYPAL_BUSINESS_EMAIL; ?>" type="hidden">
            <input name = "return" value = "<?php echo PAYPAL_RETURN_URL; ?>" type = "hidden">
            <input name = "cancel_return" value = "<?php echo $_SERVER['HTTP_REFERER']; ?>" type = "hidden">
            <input name = "cmd" value = "_cart" type = "hidden">
            <input name = "upload" value = "1" type = "hidden">
            <input name = "no_note" value = "0" type = "hidden">
            <input name = "bn" value = "PP-BuyNowBF" type = "hidden">
            <input name = "tax" value = "0" type = "hidden">
            <input name = "rm" value = "2" type = "hidden">
            <input name = "handling_cart" value = "0" type = "hidden">
            <input name = "currency_code" value = "USD" type = "hidden">
            <input name = "lc" value = "GB" type = "hidden">
            <input name = "cbt" value = "Return to My Site" type = "hidden">
            <input name = "custom" value = "" type = "hidden">

            <div id = "item_1" class = "itemwrap">
                <input name = "item_name_1" value = "<?php echo $booking_for;?>" type = "hidden">
                <!--
                                <input name = "quantity_1" value = "1" type = "hidden">
                -->
                <input name = "amount_1" value = "<?php echo $paynowprice;?>" type = "hidden">
                <input name = "shipping_1" value = "0" type = "hidden">
                <!--
                                        <input name = "submit"  type = "submit">
                -->
            </div>	

        </form>
        <table width="100%" border="0" cellspacing="0" cellpadding="0" style="margin-top:20px">
            <tr>
                <td align="center" valign="middle"><table width="689" border="0" cellpadding="0" cellspacing="0" class="tableborder">
                        <tr>
                            <td height="84" align="center" valign="middle">
                                <img src="images/logo.jpg" title="<?php echo PROJECT_NAME;?>"/>
                            </td>
                        </tr>
                        <tr>
                            <td height="30" align="center" valign="baseline" class="underline">&nbsp;</td>
                        </tr>
                        <tr>
                            <td height="30" align="center" valign="baseline" class="text1 style1" style="font-family:Verdana, Geneva, sans-serif; font-size:11px; color:#666; line-height:18px;">You are currently redirecting to the payment gateway page.</td>
                        </tr>
                        <tr>
                            <td height="100" align="center" valign="middle"><img src="resources/updates/update1/img/loading.gif" alt="" title="" align="center" /></td>
                        </tr>
                        <tr>
                            <td height="30" align="center" valign="baseline" class="text1 style1" style="font-family:Verdana, Geneva, sans-serif; font-size:11px; color:#666; line-height:18px;">Please do not refresh the screen or press backspace key.</td>
                        </tr>
                        <tr>
                            <td height="30" align="center" valign="baseline">&nbsp;</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    <script>
        function submitPayuForm() {
            document.forms.payuForm.submit();
        }
    </script>
    </body>
</html>