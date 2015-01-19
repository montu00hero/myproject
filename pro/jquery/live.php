
<html>
    
    <head>
        <script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
        <script type="text/javascript">
        $(function()
            {
                    $(".test").live("mouseover", function()
                    {
                            $(this).css("background-color", "blue");
                    }).live("mouseout", function()
                    {
                            $(this).css("background-color", "green");
                    });
            });

        function AddBox()
            {
                    var div = $("<div></div>").addClass("test1").text("Another box");
                    $("#divTestArea2").append(div);
            }
        </script>        
    </head>
    <body>
        <div id="divTestArea2">
        <a href="javascript:void(0);" onclick="AddBox();">Add box</a>
        <div class="test">This is a box</div>
        </div>
    </body>
    
</html>


