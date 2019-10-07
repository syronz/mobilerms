diakoApp.controller('dashboardController',function($scope,$rootScope,$http,$log,$location,$routeParams,mainFactory){

	mainFactory.checkLogin();
	$rootScope.part = 'dashboard';

	$scope.name = 'dashboard';

	$scope.yearReport = $routeParams.year ? $routeParams.year : new Date().getFullYear().toString();
	$scope.monthReport = $routeParams.month ? $routeParams.month : ('0' + (new Date().getMonth() + 1).toString()).substr(-2);
	$scope.userDeppReportSelect = $routeParams.idDepp ? $routeParams.idDepp : '0';

	// dsh($scope.yearReport,$scope.monthReport);


	//get basic info, 4 box top of the page
	$scope.getBasicInfo = function(){
		$rootScope.loading = true;
		$http.get('control/reportCtrl.php?action=dashboardBasicInfo&year='+$scope.yearReport+'&month='+$scope.monthReport+'&idDepp='+$scope.userDeppReportSelect).
		success(function(data, status, headers, config){
			if(data.result)
				$scope.basicInfo = data.rows;
			$rootScope.loading = false;
		}).
		error(function(data, status, headers, config){
			dsh(data);
		})
	}
	$scope.getBasicInfo();

	$scope.newDashboardReport = function(){
		$location.path('dashboard/'+$scope.yearReport+'/'+$scope.monthReport+'/'+$scope.userDeppReportSelect);
	}

	//for select year,month and depp
	// $scope.userDeppReportSelect = '0';
	$http.get("control/userCtrl.php?action=authUserPart").
	success(function(data, status, headers, config) {
		$scope.userDeppReport = data.departments;
	}).
	error(function(data, status, headers, config) {
		$log.info(data);
	});
	
	// chart about sell in 30 days
	$scope.labelsDailySell = ["1", "2", "3", "4", "5", "6", "7", "8","9","10","11","12","13","14","15","16","17","18","19","20","21","22","23","24","25","26","27","28","29","30","31"];
	$scope.seriesDailySell = ['This Month', 'Previous Month'];

	$http.get('control/reportCtrl.php?action=dailySell&year='+$scope.yearReport+'&month='+$scope.monthReport+'&idDepp='+$scope.userDeppReportSelect).
	success(function(data, status, headers, config){
		if(data.result)
			$scope.dataDailySell = data.rows;
		$rootScope.loading = false;
	}).
	error(function(data, status, headers, config){
		dsh(data);
	});

	$scope.datasetOverrideDailySell = [{ yAxisID: 'y-axis-1' }];
	$scope.optionsDailySell = {
		scales: {
			yAxes: [{
				id: 'y-axis-1',
				type: 'linear',
				display: true,
				position: 'left'
			}]
		}
	};



	//snapshot
	$scope.getSnapshotData = function(firstDate,lastDate,range){
		$scope.snap = {};
		$rootScope.loading = true;
		$http.get("control/reportCtrl.php?action=snapshot&firstDate="+firstDate+"&lastDate="+lastDate+"&range="+range).
		success(function(data, status, headers, config) {
			if(data.result){
				$scope.snap = data.rows[0];
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

	var today = new Date();
	var dd = today.getDate();
	var mm = today.getMonth()+1; 
	var yyyy = today.getFullYear();

	dd = dd<10 ? '0'+dd : dd;
	mm = mm < 10 ? '0'+mm : mm;

	today = yyyy + '-' + mm + '-' + dd;

	$scope.getSnapshotData(today,today,'depp');



	/*START----------------------------------------- Dollar Rate Chart */
	$scope.DRate = {};

	$http.get('control/reportCtrl.php?action=dollarRateChart').
	success(function(data, status, headers, config){
		if(data.result){
			$scope.DRate.data = [data.rows.dollarRate,[]];
			$scope.DRate.labels = data.rows.labels;
		}
		$rootScope.loading = false;
	}).
	error(function(data, status, headers, config){
		dsh(data);
	});

	$scope.DRate.colors = ['#45b7cd', '#ff6384', '#ff8e72'];
    $scope.DRate.datasetOverride = [
      {
        label: "Line chart",
        borderWidth: 3,
        hoverBackgroundColor: "rgba(255,99,132,0.4)",
        hoverBorderColor: "rgba(255,99,132,1)",
        type: 'line'
      }
    ];
    /*END------------------------------------------- Dollar Rate Chart */



    /*START----------------------------------------- productSellReport */

    $http.get('control/reportCtrl.php?action=productSellReport').
	success(function(data, status, headers, config){
		if(data.result){
			$scope.productList = data.rows;
		}
		else{
			dsh(data);
		}
	}).
	error(function(data, status, headers, config){
		dsh(data);
	});
	/*END------------------------------------------- productSellReport */


});
