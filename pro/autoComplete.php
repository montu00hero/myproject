<html>
    <title>Title:Auto Complete</title>
    <head>
        <style>
            table th{background-color: tan}
            
            table tr:nth-child(even){background-color: f1f1f1}
            table tr:nth-child(odd){background-color: ffffff}
            
        </style>
    </head>
    <body>
          <script src="angular.min.js" type="text/javascript"></script>
            <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
          
        <div ng-app="" ng-controller="appController1">
         
            <input autocomplete="true" type="text" ng-keypress="fun();" id="box" />
            <form>
               <table border="1">
                      <thead>
                        <tr>
                         <th>
                           City Name(s)
                         </th>                          
                        </tr>
                      </thead>
                      <tbody>
                        <tr ng-repeat="x in master">
                          <td>{{x.city}}</td>
                        </tr>
                      </tbody>
               </table>
           </form>
        </div>
      <div id="a"></div>
        <script>
            
        </script>   
        
        
    <!--    
        
 <script>
               var resp=[{"city":"name"},{'city':'g'}];
      /*  $("#box").keypress(function(){
           
          
           var wor=$("#box").val();
           
           $.ajax({
               type:"POST",
               data:"data="+wor,
               url:"connects.php" ,
               success:function(msg){
      
              window.resp=msg; 
         $('#a').html(resp);
                  
               },
               error:function(){
                   
               }
                
           });
           
//             function control($scope)
//                   {
//                       $scope.cities=res;
//                       
//                   }
    // a();

    
    }); */
   
       //  function a(){ alert(resp);}  
              function appController1($scope)
            {  
                $("#box").keypress(function(){
           
          
           var wor=$("#box").val();
           
           $.ajax({
               type:"POST",
               data:"data="+wor,
               url:"connects.php" ,
               success:function(msg){
              // $scope.master=[{'city':'goa'},{'city':'jaipur'}];//msg; 
              resp=msg; 
         //    alert($scope.master);
           // $('#is').val(resp);
     //    $('#a').html(resp);
                  
               },
               error:function(){
                   
               }
                
           });

    
    }); //$scope.master=[{'city':'goa'},{'city':'jaipur'}];
        $scope.master=resp;        
             $scope.fun=function(){   
              $scope.master=resp;
            //  console.log('ffffff',$scope.master);
                };
           
            }   
        
          

        </script> -->
        
     
<script>
function appController1($scope,$http) {
  
   $scope.fun=function(){  
       
        var wor=$("#box").val();
        
        $http.get("connects.php?data="+wor)
      .success(function(response) {$scope.master = response;});
   }
   
       $scope.fun();
}
</script>
        
        
     </body>
</html>