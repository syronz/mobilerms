diakoApp.controller('snapshotController',function($scope,$rootScope,$http,$log,$location,$routeParams,mainFactory,$indexedDB,$filter){

	$scope.objects = [];
	mainFactory.checkLogin();

	$rootScope.part = 'snapshot';
	$rootScope.specialPrint = false;

	$scope.firstDate = new Date();
	$scope.lastDate = new Date();

	$scope.getSnapshotData = function(firstDate,lastDate,range){
		$scope.snapshotData = {};
		$rootScope.loading = true;
		$http.get("control/reportCtrl.php?action=snapshot&firstDate="+firstDate+"&lastDate="+lastDate+"&range="+range).
		success(function(data, status, headers, config) {
			if(data.result){
				$scope.snapshotData = data.rows;
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
	$scope.getSnapshotData(0,0,0);

	$scope.newSnapShot = function(){
		var range = $scope.range? 'all': 'depp';
		
		$scope.getSnapshotData($filter('date')($scope.firstDate, 'yyyy-MM-dd'),$filter('date')($scope.lastDate, 'yyyy-MM-dd'),range);
		// $scope.range = !$scope.range;
	}
	

});