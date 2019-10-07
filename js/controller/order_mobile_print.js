diakoApp.controller('order_mobile_printController',function($scope,$rootScope,$http,$log,$location,$routeParams,mainFactory,$indexedDB){




	$scope.objects = [];
	mainFactory.checkLogin();

	$scope.order_mobile_printList = null;
	$rootScope.part = 'order_mobile_print';
	$rootScope.specialPrint = true;


	$scope.invoice = {};

	var sample_invoice = {
		accountType : 'company',
		type : 'active',
		pay : {},
		/*must be delete
		id_account : 1,
		/*must be delete*/

		discount: 0.00, 
		items:[ {qty:1, description:'', cost:0,id_brand:0,id_model:0,id_product:0}]
	};

	if($location.path() == "/order_mobile_print/new" || $scope.idorder == null){
		$scope.invoice = sample_invoice;
	}
	else{
		$scope.invoice =  JSON.parse(localStorage["invoice"]);
	}


	/*------------------------------- Start invoice order PRINT*/
	$scope.loadPrint = function(){
		if($routeParams.id){
			$http.get("control/orderCtrl.php?action=orderPrint&id="+$routeParams.id).
			success(function(data, status, headers, config) {
				//dsh(data);
				$scope.invoicePrint = data;
				$scope.invoicePrint.base.date = new Date($scope.invoicePrint.base.date);
				$scope.pay = {id_account : $scope.invoicePrint.base.id_account,id_depp : $scope.invoicePrint.base.id_depp,id_order:$scope.invoicePrint.base.id};


			}).
			error(function(data, status, headers, config) {
				$log.info(data);
			});
		}
	}


	$scope.savePayin = function(){
		$http.post("control/orderCtrl.php?action=savePayin",$scope.pay).
		success(function(data, status, headers, config) {
			$rootScope.loading = $scope.lockForm = true;
			if(data.result){
				$http.get("control/orderCtrl.php?action=orderPayins&idorder="+$routeParams.id).
				success(function(data, status, headers, config) {
					dsh('result_get',data);
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

	/*------------------------------- END invoice order PRINT*/


	


	

});



