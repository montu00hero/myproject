(function () {
  var app = angular.module('store', []);

  app.controller('StoreController', function ($scope) {
    $scope.product = {
      name: 'silk',
      price: 1200,
      description: 'it is of good quality',
      value1:true,     //if it is true the html emement
      soldOut:false,
    };
  });
})();