<div>
    <a href="http://www.yahoo.com" >Click</a>
     
</div>
<script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
<script>
  // preventDefault() is to prevent the default action of a particular element


    $("a").click(function(event){

        event.preventDefault();

       // window.open('http://www.google.com','Google');

        $("div").append("event:"+event.type);

    });


</script>