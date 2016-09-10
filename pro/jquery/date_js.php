 
<p id="demo"></p>
<p id="demo1"></p>
<p id="demo2"></p>
<p id="demo3"></p>

<script>
var d=new Date();
document.getElementById("demo").innerHTML= d;

var d1= new Date("October 13, 2014 11:13:00");
document.getElementById("demo1").innerHTML= d1;

var d2 = new Date(99,5,24,11,33,30,0);
document.getElementById("demo2").innerHTML= d2;

document.getElementById("demo3").innerHTML= new Date(99,5,24);

</script> 

<h6>JavaScript Date Methods</h6>
<h6>
Date Get Methods <br>

Get methods are used for getting a part of a date. Here are the most common (alphabetically):<br>
Method 	Description <br>
getDate() 	Get the day as a number (1-31) <br>
getDay() 	Get the weekday as a number (0-6) <br>
getFullYear() 	Get the four digit year (yyyy) <br>
getHours() 	Get the hour (0-23) <br>
getMilliseconds() 	Get the milliseconds (0-999) <br>
getMinutes() 	Get the minutes (0-59) <br>
getMonth() 	Get the month (0-11) <br>
getSeconds() 	Get the seconds (0-59) <br>
getTime() 	Get the time (milliseconds since January 1, 1970) <br>

</h6>


<p id="demo44"></p>
<p id="demo45"></p>
<p id="demo46"></p>
<p id="demo47"></p>
<p id="demo48"></p>
<p id="demo49"></p>
<p id="demo40"></p>
<p id="demo41"></p>
<p id="demo42"></p>

 <script>
var d = new Date();
document.getElementById("demo44").innerHTML = d.getDate();
document.getElementById("demo45").innerHTML = d.getDay();
document.getElementById("demo46").innerHTML = d.getFullYear();
document.getElementById("demo47").innerHTML = d.getHours();
document.getElementById("demo48").innerHTML = d.getMilliseconds();
document.getElementById("demo49").innerHTML = d.getMinutes();
document.getElementById("demo40").innerHTML = d.getMonth();
document.getElementById("demo41").innerHTML = d.getSeconds();
document.getElementById("demo42").innerHTML = d.getTime();
</script> 