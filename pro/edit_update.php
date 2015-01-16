
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
    
      echo '<tr><td><input type="text" value="'.$row["cityName"].'" id="txt'.$row['cityId'].'" readonly="true" /></td>';
      echo   '<td><button   id="'.$row['cityId'].'"  onclick="fun('.$row['cityId'].')">Edit</button>'
                 . '<button style="display:none" onclick=update('.$row['cityId'].');  id="up'.$row['cityId'].'">Update</button></td>'
                   . '<td><button style="display:none" onclick=cancel('.$row['cityId'].',"'.$row["cityName"].'");  id="can'.$row['cityId'].'" >Cancel</button></td></tr>';
}
mysql_close();
?>

                
            
            
        </table>
        <script>
        function fun(id){ 
          $('#up'+id).show();
          $('#can'+id).show();
          $('#'+id).hide();
          $('#txt'+id).attr('readonly',false);
         }
         
         function update(id)
         {
          //for update
            
            var city= $('#txt'+id).val();
            $.ajax({
               type:'POST',
               url:'',
               data:'data='+city,
               success:function(){
                $('#up'+id).hide();
                $('#can'+id).hide();
                $('#'+id).show();
                $('#txt'+id).attr('readonly',true);    
                location.reload();
               },
               error:function(){
                   
               }
            });
             
         }
         
         function cancel(id,name){
                $('#up'+id).hide();
                $('#can'+id).hide();
                $('#'+id).show();
                $('#txt'+id).attr('readonly',true);    
                $('#txt'+id).val(name);
         }
         
    </script>
        
    </body> 
</html>
















