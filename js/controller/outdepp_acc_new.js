diakoApp.controller('outdepp_acc_newController',function($scope,$rootScope,$http,$log,$location,$routeParams,mainFactory,$indexedDB){

	$scope.objects = [];
	mainFactory.checkLogin();

	$rootScope.part = 'outdepp_acc_new';
	$rootScope.specialPrint = true;


	$scope.accountList = null;
	$scope.accountArr = [];
	$scope.invoice = {};

	$scope.invoice = {
		accountType : 'customer',
		type : 'active',
		pay : {},
		discount: 0.00, 
		discountAmount: 0.00,
		items:[{qty:1, description:'', cost:0,id_brand:0,id_model:0,id_product:0}]
	};

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


	/*------------------------------- START invoice outdepp NEW*/
	$scope.saveInvoice = function(){
		$rootScope.loading = $scope.lockForm = true;
		$http.post("control/outdeppCtrl.php?action=add",$scope.invoice).
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

	

	$scope.toggleError = function(){
		$scope.errorShow = !$scope.errorShow;
	}

});