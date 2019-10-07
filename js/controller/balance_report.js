diakoApp.controller('balance_reportController',function($scope,$rootScope,$http,$log,$location,$routeParams,mainFactory,$indexedDB,$filter){

	$scope.objects = [];
	mainFactory.checkLogin();

	$rootScope.part = 'balance_report';
	$rootScope.specialPrint = false;
	console.log($routeParams);

	$scope.date = $routeParams.date;
	$scope.idDepp = $routeParams.idDepp ? $routeParams.idDepp : 0;

	$scope.getBalanceReportData = function(date,idDepp){
		$scope.balance_reportData = {};
		$rootScope.loading = true;
		$http.get("control/reportCtrl.php?action=balanceReport&date="+date+"&idDepp="+idDepp).
		success(function(data, status, headers, config) {
			if(data.result){
				$scope.balance_reportData = data.rows;
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
	$scope.getBalanceReportData($scope.date,$scope.idDepp);

	// $scope.newBalanceReport = function(){
	// 	$scope.range = $scope.range? 'all': 'depp';
	// 	dsh($filter('date')($scope.date, 'y-m-d'));
	// 	$scope.getbalance_reportData($filter('date')($scope.date, 'yyyy-MM-dd'),$filter('date')($scope.idDepp, 'yyyy-MM-dd'),$scope.range);
	// 	$scope.range = null;
	// }
	

});