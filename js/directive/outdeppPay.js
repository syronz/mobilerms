diakoApp.directive('outdeppPay', function(){
	return{
		templateUrl:"template/directive/outdeppPay.html",
		restrict:"E",
		replace:true,
		scope:false,
		controller: function($scope,$http){
			$scope.getPayAccounts = function(){
				if(!$scope.accountPayList){
					$http.get("control/accountCtrl.php?action=accountPayList").
					success(function(data, status, headers, config) {
						$scope.accountPayList = data;
						$scope.invoice.pay.id_account = data[0];
					}).
					error(function(data, status, headers, config) {
						$log.info(data);
					});
				}
			}
			$scope.getPayAccounts();
		}//end of controller
	}
});