diakoApp.controller('debtsReportSimpleController',function($scope,$rootScope,$http,$log,$location,$routeParams,mainFactory,$filter){

	$scope.objects = [];
	mainFactory.checkLogin();

	$rootScope.part = 'debtsReportSimple';
	$rootScope.specialPrint = false;

	

	var str = '';
	if($routeParams.search != undefined)
		str = $routeParams.search;

	$scope.getDebtsReportSimpleData = function(day,accountType,cat,brand,product,limit,order,orderType){
		$scope.debtsReportSimpleData = {};
		$rootScope.loading = true;
		$http.get("control/reportCtrl.php?action=debtsReportSimple&day="+day+"&accountType="+accountType).
		success(function(data, status, headers, config) {
			if(data.result){

				$scope.debtsReportSimpleData = data.rows;
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
		$scope.debtsReportSimpleData = {};
		$rootScope.loading = true;
		$http.get("control/reportCtrl.php?action=debtsReportSimple").
		success(function(data, status, headers, config) {
			if(data.result){
				dsh(data);
				$scope.debtsReportSimpleData = data.rows;
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
		$scope.getDebtsReportSimpleData($routeParams.day,$routeParams.accountType);

		$scope.day = parseInt($routeParams.day);
		$scope.accountType = $routeParams.accountType;
	}


	$scope.newDebtsReportSimple = function(){
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