diakoApp.directive('outdeppHeader', function(){
	return{
		templateUrl:"template/directive/outdeppHeader.html",
		restrict:"E",
		replace:true,
		scope:false,
		controller: function($scope,$http){
			if($scope.invoice){
				$http.get("control/outdeppCtrl.php?action=getInvoice").
				success(function(data, status, headers, config) {
					$scope.invoice.invoice = data.invoice;
				}).
				error(function(data, status, headers, config) {
					$log.info(data);
				});
			}
			

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