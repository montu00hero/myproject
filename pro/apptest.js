(function(){
    var app=angular.module('apptest',[]);
    
    app.controller("tcontroller",function($scope){
        $scope.nm=[
            {product_name:'intel',
             price:1522,
             mfd:'mar 01 2014',
            },
            {product_name:'Retox',
             price:1452,
             mfd:'apr 03 2014',},
            {product_name:'votro',
             price:152,
             mfd:'mar 04 2014',},
            {product_name:'del',
             price:1252,
             mfd:'may 14 2014',}
            
            
        ];
        
        
        
    });
    
    
})();
