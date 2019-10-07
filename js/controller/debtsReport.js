diakoApp.controller('debtsReportController',function($scope,$rootScope,$http,$log,$location,$routeParams,mainFactory,$filter){

	$scope.objects = [];
	mainFactory.checkLogin();

	$rootScope.part = 'debtsReport';
	$rootScope.specialPrint = false;

	

	var str = '';
	if($routeParams.search != undefined)
		str = $routeParams.search;

	$scope.getDebtsReportData = function(day,accountType,cat,brand,product,limit,order,orderType){
		$scope.debtsReportData = {};
		$rootScope.loading = true;
		$http.get("control/reportCtrl.php?action=debtsReport&day="+day+"&accountType="+accountType).
		success(function(data, status, headers, config) {
			if(data.result){
				$scope.debtsReportData = data.rows;
			}
			else{
				dsh(data);
			}
			$rootScope.loading = false;
		}).
		error(function(data, status, headers, config) {
			$log.info(data);
		});
	}

	var order = orderType = '';

	if(!$routeParams.day){
		$scope.debtsReportData = {};
		$rootScope.loading = true;
		$http.get("control/reportCtrl.php?action=debtsReport").
		success(function(data, status, headers, config) {
			if(data.result){
				$scope.debtsReportData = data.rows;
			}
			else{
				dsh(data);
			}
			$rootScope.loading = false;
		}).
		error(function(data, status, headers, config) {
			$log.info(data);
		});

		$scope.day = 15;
		$scope.accountType = 'partner';

	}
	else{
		$scope.getDebtsReportData($routeParams.day,$routeParams.accountType);

		$scope.day = parseInt($routeParams.day);
		$scope.accountType = $routeParams.accountType;
	}


	$scope.newDebtsReport = function(){
		$location.path($scope.part+'/'+$scope.day+'/'+$scope.accountType);
		// :start/:end/:cat/:brand/:product/:limit/:sortFiled/:sortType
	}

	$scope.toggleStatus = function(item){
		$scope.lockForm = $rootScope.loading = true;
		$http.get("control/outdeppCtrl.php?action=toggleStatus&id="+item.id).
		success(function(data, status, headers, config) {
			if(data.result){
				item.status = data.status;
			}
			$scope.lockForm = $rootScope.loading = false;
		}).
		error(function(data, status, headers, config) {
			$log.info(data);
		});
	}


	

});