diakoApp.directive('outdeppPrintPay', function(){
	return{
		templateUrl:"template/directive/outdeppPrintPay.html",
		restrict:"E",
		replace:true,
		scope:false,
		controller: function($scope,$http,$rootScope,$routeParams){
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

			$scope.savePayin = function(){
				$rootScope.loading = $scope.lockForm = true;
				$http.post("control/outdeppCtrl.php?action=savePayin",$scope.pay).
				success(function(data, status, headers, config) {
					if(data.result){
						$http.get("control/outdeppCtrl.php?action=outdeppPayins&idoutdepp="+$routeParams.id).
						success(function(data, status, headers, config) {
							$scope.invoicePrint.base.totalPays = data.base.totalPays;
							$scope.invoicePrint.pays = data.pays;
							$scope.pay.payDinar = 0;
							$scope.pay.payDollar = 0;
							$scope.pay.detail = '';

							$rootScope.loading = $scope.lockForm = false;
						}).
						error(function(data, status, headers, config) {
							$log.info(data);
						});

						dsh(data);
					}
					else{
						console.log(data);
						$scope.errorMessage = data.message;
						$scope.errorShow = true;
					}
				}).
				error(function(data, status, headers, config) {
					$log.info('error',data, status, headers, config);
				});
			}
		}//end of controller
	}
});