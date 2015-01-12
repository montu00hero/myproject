 (function(){
            var app=angular.module('Jsonapps',[]);
            
            app.controller('jsonController',function($scope){
            
             $scope.nam=[{ Name:'sam',
                              desig:'operator',
                              sal:2000,
                              dept:'govt',
                            },
                            { Name:'wam',
                              desig:'cpm',
                              sal:12000,
                              dept:'govt',
                             }
                          ];
               
                
                
            });
            
            
        })();