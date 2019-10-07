diakoApp.directive('testDirective', function(){
	return{
		templateUrl:"template/directive/test.html",
		restrict:"E",
		// replace:true,
		scope:{
			accountInfo : '=info',
			broInfo : '@bro'
		},
		controller: function($scope){
			$scope.alert = function(){
				console.log($scope)
				// $scope.accountInfo.name = 'no';
			}
		}
	}
});