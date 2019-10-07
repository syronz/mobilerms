diakoApp.directive('indeppHeader', function(){
	return{
		templateUrl:"template/directive/indeppHeader.html",
		restrict:"E",
		replace:true,
		scope:false,
		controller: function($scope,$http){
			$scope.getAccounts = function(){
				if($scope.invoice){
					$http.get("control/accountCtrl.php?action=accountList&accountType="+$scope.invoice.accountType).
					success(function(data, status, headers, config) {
						$scope.accountList = data;
						
					}).
					error(function(data, status, headers, config) {
						$log.info(data);
					});
				}
				
			}
			$scope.getAccounts();
		}//end of controller
	}
});