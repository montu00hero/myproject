<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>

<form action="javascript:void(0);" method="post">
    <table>
        <tr><td>Name : </td><td><input type="text" name="name" class="required"></td></tr>
        <tr><td>Address : </td><td><textarea name="address" class="required"></textarea></tr>
        <tr><td>Day : </td><td><select name="day" class="required">
                                <option value="">--Day--</option>
                                <option value='1'>1</option><option value='2'>2</option><option value='3'>3</option><option value='4'>4</option><option value='5'>5</option><option value='6'>6</option><option value='7'>7</option><option value='8'>8</option><option value='9'>9</option><option value='10'>10</option><option value='11'>11</option><option value='12'>12</option><option value='13'>13</option><option value='14'>14</option><option value='15'>15</option><option value='16'>16</option><option value='17'>17</option><option value='18'>18</option><option value='19'>19</option><option value='20'>20</option><option value='21'>21</option><option value='22'>22</option><option value='23'>23</option><option value='24'>24</option><option value='25'>25</option><option value='26'>26</option><option value='27'>27</option><option value='28'>28</option><option value='29'>29</option><option value='30'>30</option><option value='31'>31</option>                            </select></td></tr>
        <tr><td>Month : </td><td><select name="day" class="required">
                                <option value="">--Month--</option>
                                <option value='1'>Jan</option><option value='2'>Feb</option><option value='3'>Mar</option><option value='4'>Apr</option><option value='5'>May</option><option value='6'>Jun</option><option value='7'>Jul</option><option value='8'>Aug</option><option value='9'>Sep</option><option value='10'>Oct</option><option value='11'>Nov</option><option value='12'>Dec</option>                            </select></td></tr>
        <tr><td>Year : </td><td><select name="day" class="required">
                                <option value="">--Year--</option>
                                <option value='2011'>2011</option><option value='2012'>2012</option><option value='2013'>2013</option><option value='2014'>2014</option><option value='2015'>2015</option>                            </select></td></tr>
        <tr><td></td><td></td></tr>
        <tr><td></td><td><input type="button" value="Done" class="submit"></td></tr>
    </table>
</form>
<script type="text/javascript">
    $(function(){
        $(".submit").click(function(){
            var submit = 1;
            $(".required").each(function(){
                if($(this).val().trim()==''){
                    submit = 0;
                    $(this).css("border","2px solid red");
		    $(this).css("color","red");
                }
            });
            if(submit==1)
                $("form").prop("action", "").submit();
            return false;
        });
        
        $("body").click(function(){
            $(".required").css("border","");
            $(".required").css("color","");
        });
    });
</script>