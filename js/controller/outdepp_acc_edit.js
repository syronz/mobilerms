diakoApp.controller('outdepp_acc_editController',function($scope,$rootScope,$http,$log,$location,$routeParams,mainFactory,$indexedDB){

	$scope.objects = [];
	mainFactory.checkLogin();

	$rootScope.part = 'outdepp_acc_edit';
	$rootScope.specialPrint = false;

	$scope.loadEdit = function(){
		if($routeParams.id){
			$http.get("control/outdeppCtrl.php?action=outdeppEdit&id="+$routeParams.id).
			success(function(data, status, headers, config) {
				$scope.invoice = data;
				$scope.getBrands(data.base.id_depp);
				$scope.invoice.discount = data.base.discount;
				$scope.invoice.discountAmount = data.base.discount * $scope.invoice_sub_total() / 100;
				$scope.invoice.id_account = data.base.id_account;
				$scope.invoice.type = data.base.outdeppType;
				$scope.invoice.invoice = data.base.invoice;
				$scope.invoice.detail = data.base.detail;
				$scope.invoice.accountType = data.base.accountType;
				$http.get("control/accountCtrl.php?action=accountList&accountType="+$scope.invoice.accountType).
				success(function(data, status, headers, config) {
					$scope.accountList = data;

				}).
				error(function(data, status, headers, config) {
					$log.info(data);
				});
			}).
			error(function(data, status, headers, config) {
				$log.info(data);
			});
		}
	}

	$scope.saveEditedInvoice = function(){
		$rootScope.loading = $scope.lockForm = true;
		$http.post("control/outdeppCtrl.php?action=edit",$scope.invoice).
		success(function(data, status, headers, config) {
			if(data.result){
				$location.path('outdepp_acc/'+data.id);
			}
			else{
				console.log(data);
				$scope.errorMessage = data.message;
				$scope.errorShow = true;
			}
			$rootScope.loading = $scope.lockForm = false;
		}).
		error(function(data, status, headers, config) {
			$log.info('error',data, status, headers, config);
		});
	}

	$scope.getAccounts = function(){
		$http.get("control/accountCtrl.php?action=accountList&accountType="+$scope.invoice.accountType).
		success(function(data, status, headers, config) {
			$scope.accountList = data;

		}).
		error(function(data, status, headers, config) {
			$log.info(data);
		});
	}

	$scope.getDollarRate = function(){
		if(!$scope.invoice.dollarRate){
			$http.get("control/dollar_rateCtrl.php?action=dollar_rateDepp&idDepp="+$scope.invoice.id_depp).
			success(function(data, status, headers, config) {
				$scope.invoice.dollarRate = parseFloat(data);
			}).
			error(function(data, status, headers, config) {
				$log.info(data);
			});
		}
	}

	$scope.toggleError = function(){
		$scope.errorShow = !$scope.errorShow;
	}


});