<html>
    <title>Title:Auto Complete</title>
    <head>
       
        <script src="angular.min.js" type="text/javascript"></script>
        
        <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
       
        
    </head>
    <body>
        
        <input autocomplete="on" type="text" id="box" />
        <div ng-app="" ng-controller="appController">
            <table>
                <tr ng-repeat="x in master">
                    <td>{{x.firstname}}</td>
                </tr>
            </table>    
        </div>
        
        <script>
            function appController($scope)
            {
                $scope.master={firstname:"ram",lastname:"kumar"};
                $scope.reset=function(){
                    $scope.user=angular.copy($scope.master);
                };
        $scope.reset();
            }
        </script>   
        
        
        
        
   <script>
       $("#box").keypress(function(){
           
          
           var wor=$("#box").val();
           
           $.ajax({
               type:"POST",
               data:"data="+wor,
               url:"connects.php" ,
               success:function(msg){
                
              var res=msg; 
                  
               },
               error:function(){
                   
               }
               
           });
           
//             function control($scope)
//                   {
//                       $scope.cities=res;
//                       
//                   }
          });  

        </script>
     </body>
</html>