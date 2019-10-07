diakoApp.directive('indeppPrintPay', function(){
	return{
		templateUrl:"template/directive/indeppPrintPay.html",
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

			$scope.savePayout = function(){
				$rootScope.loading = true;
				$scope.lockForm = true;
				$http.post("control/indeppCtrl.php?action=savePayout",$scope.pay).
				success(function(data, status, headers, config) {
					if(data.result){
						
						$http.get("control/indeppCtrl.php?action=indeppPayins&idIndepp="+$routeParams.id).
						success(function(data, status, headers, config) {
							$scope.invoicePrint.base.totalPays = data.base.totalPays;
							$scope.invoicePrint.pays = data.pays;
							$scope.pay.payDinar = 0;
							$scope.pay.payDollar = 0;
							$scope.pay.detail = '';
							dsh(data);

							$rootScope.loading = false;
							$scope.lockForm = false;
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