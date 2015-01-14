
<html>
    <head>
      <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
    </head>
    <body>
        <table>
        <tr>
         <td>City Name</td>
         <td>Action</td>
         </td>
         
<?php
$conn=  mysql_connect("localhost","root","");

mysql_select_db("page");

$qu="select cityId,cityName from cities";

 $res=mysql_query($qu,$conn);

 while ($row = mysql_fetch_array($res)) {
    
      echo ' <tr><td>'.$row["cityName"].'</td>';
      echo   '<td><button style="display:block"  id="'.$row['cityName'].'"  onclick="fun()">Edit</button><button style="display:none" id="update">Update</button></td></tr>';
}
mysql_close();
?>

                
            
            
        </table>
        <script>
            
        $('#edit').click(function(){
        
        $('#edit').hide();
         $('#update').show();
        
        });
    </script>
        
    </body> 
</html>
















