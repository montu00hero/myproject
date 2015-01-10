(function(){
    
   var app=angular.module('abc',[]);

app.controller('mycontroller',function($scope){
    $scope.items=[{name:'item1',
                  price:122,
                  desc:'good',
                  soldOut:true,
                  },
                  {name:'item2',
                  price:125,
                  desc:'good1',
                  soldOut:false,
                  },
                  {
                  name:'item3',
                  price:128,
                  desc:'good2',
                  soldOut:true,
                  }];
    
    
}); 
    
})();



