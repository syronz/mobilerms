diakoApp.directive('newCustomer', function(){
	return{
		templateUrl:"template/directive/newCustomer.html",
		restrict:"E",
		replace:true,
		scope:false,
		controller: function($scope,$http){
			$scope.mobileChange = function(e,mobile){
				if(e.keyCode == 13){
					$scope.invoice.id_account = null;
					$scope.errorShowMini = false;
					$http.get("control/accountCtrl.php?action=getAccountByMobile&mobile="+mobile).
					success(function(data, status, headers, config) {
						if(data.result){
							$scope.customer = data.info;
							if(data.customerType == 'exist'){
								$scope.invoice.id_account = data.info.id;
							}
							else{
								
							}
						}
						else{
							$scope.errorMessageMini = data.message;
							$scope.errorShowMini = true;
						}
					}).
					error(function(data, status, headers, config) {
						$log.info(data);
					});
				}
			}

			$scope.saveCustomer = function(){
				$scope.errorShowMini = false;
				$http.post("control/accountCtrl.php?action=addCustomer",$scope.customer).
				success(function(data, status, headers, config) {
					if(data.result){
						$scope.invoice.id_account = data.idCustomer;
					}
					else{
						console.log(data);
						$scope.errorMessageMini = data.message;
						$scope.errorShowMini = true;
					}
				}).
				error(function(data, status, headers, config) {
					$log.info('error',data, status, headers, config);
				});
			}
		}//end of controller
	}
});