diakoApp.controller('cashDeppViewController',function($scope,$rootScope,$http,$log,$location,$routeParams,mainFactory){

	mainFactory.checkLogin();

	$scope.accountList = null;
	$rootScope.part = 'cash';

	$scope.sortFiled = $routeParams.sortFiled;
	$scope.sortType = $routeParams.sortType;
	var order = $scope.sortFiled+' '+$scope.sortType;


	$scope.page = parseInt($routeParams.page);
	$scope.limit = parseInt($routeParams.limit);
	var limitPage = (($scope.page - 1) * $scope.limit)+','+$scope.limit;

	var str = '';
	if($routeParams.search != undefined)
		str = $routeParams.search;

	
	$scope.list = function(){
		$rootScope.loading = true;
		$http.get("control/accountCtrl.php?action=cashDeppViewList&id="+$routeParams.id+"&order="+order+"&limit="+limitPage+"&search="+str).
		success(function(data, status, headers, config) {
			$scope.accountList = data.rows;
			$scope.count = data.count;
			$rootScope.loading = false;
		}).
		error(function(data, status, headers, config) {
			$log.info(data);
		});
	}
	$scope.list();


	$scope.errorMessage = null;
	$scope.saveModal = function(){
		$rootScope.loading = true;
		$scope.lockForm = true;
		$scope.modal.id_account = $scope.idCash;
		$http.post("control/accountCtrl.php?action=transferMoney",$scope.modal).
		success(function(data, status, headers, config) {
			console.log(data);
			if(data.result){
				$scope.modal = {};
				$('#appModal').modal('hide');
				$scope.list();
			}
			else{
				console.log(data);
				$scope.errorMessage = data.message;
			}
			$rootScope.loading = false;
			$scope.lockForm = false;
		}).
		error(function(data, status, headers, config) {
			$log.info('error',data, status, headers, config);
		});
	};



	$scope.transferMoney = function(){
		$scope.errorMessage = null;
		$scope.modalType = 'transferMoney';
		$scope.modal = {};
	}


	$scope.sorting = function(sort){
		dsh(sort);
		if($scope.sortType != 'DESC')
			$scope.sortType = 'DESC';
		else
			$scope.sortType = 'ASC';
		$scope.sortFiled = sort;
		order = $scope.sortFiled+' '+$scope.sortType;
		$location.path($scope.part+'/'+$scope.page+'/'+$scope.limit+'/'+$scope.sortFiled+'/'+$scope.sortType+'/'+str);
		// $scope.list();
	}

	$scope.checkSort = function(sort){
		if(sort == $scope.sortFiled)
			return 'sorting_'+$scope.sortType.toLowerCase();
		return 'sorting';
	}

	$scope.card = function(item){
		dsh(item);
		$location.path('card/'+item.id);
	}


	$http.get("control/accountCtrl.php?action=idCashInDepp").
	success(function(data, status, headers, config) {
		$scope.idCash = data.idCash;

		$http.get("control/accountCtrl.php?action=accountInfo&id="+$scope.idCash).
		success(function(data, status, headers, config) {
			if(data.result)
				$scope.accountInfo = data.data;
		}).
		error(function(data, status, headers, config) {
			$log.info(data);
		});

		$http.get("control/accountCtrl.php?action=lastBalance&id="+$scope.idCash).
		success(function(data, status, headers, config) {
			// dsh(data);
			$scope.lastBalance = data;
		}).
		error(function(data, status, headers, config) {
			$log.info(data);
		});

		$http.get("control/dollar_rateCtrl.php?action=dollar_rateDepp").
		success(function(data, status, headers, config) {
			$scope.dollarRate = data;
		}).
		error(function(data, status, headers, config) {
			$log.info(data);
		});
	}).
	error(function(data, status, headers, config) {
		$log.info(data);
	});



	

	$scope.accountType = 'company';
	$scope.getAccounts = function(){
		$http.get("control/accountCtrl.php?action=accountList&accountType="+$scope.accountType).
		success(function(data, status, headers, config) {
			$scope.accountTransferList = data;

		}).
		error(function(data, status, headers, config) {
			$log.info(data);
		});
	}
	$scope.getAccounts();





});
