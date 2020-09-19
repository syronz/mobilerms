diakoApp.controller('accountDeppController',function($scope,$rootScope,$http,$log,$location,$routeParams,mainFactory){

	mainFactory.checkLogin();
	console.log($routeParams);

	$scope.accountList = null;
	$rootScope.part = 'accountDepp';

	$scope.sortFiled = $routeParams.sortFiled;
	$scope.sortType = $routeParams.sortType;
	var order = $scope.sortFiled+' '+$scope.sortType;


	$scope.page = parseInt($routeParams.page);
	$scope.limit = parseInt($routeParams.limit);
	var limitPage = (($scope.page - 1) * $scope.limit)+','+$scope.limit;

	var str = '';
	if($routeParams.search != undefined)
		str = $routeParams.search;

	$scope.trn = {dollar:0,dinar:0};

	
	$scope.list = function(){
		$rootScope.loading = true;
		$http.get("control/accountCtrl.php?action=accountDeppList&order="+order+"&limit="+limitPage+"&search="+str).
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
		if($scope.modalType == 'add'){
			$rootScope.loading = true;
			$scope.lockForm = true;
			$http.post("control/accountCtrl.php?action=add",$scope.modal).
			success(function(data, status, headers, config) {
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
		}
		else if($scope.modalType == 'edit'){
			$rootScope.loading = true;
			$scope.lockForm = true;
			$http.post("control/accountCtrl.php?action=edit",$scope.modal).
			success(function(data, status, headers, config) {
				if(data.result){
					$scope.modal = {};
					$('#appModal').modal('hide');
					$scope.list();
				}
				else{
					dsh(data);
					$scope.errorMessage = data.message;
				}
				$rootScope.loading = false;
				$scope.lockForm = false;
			}).
			error(function(data, status, headers, config) {
				$log.info('error',data, status, headers, config);
			});
		}
		else if($scope.modalType == 'delete'){
			$rootScope.loading = true;
			$scope.lockForm = true;
			$http.post("control/accountCtrl.php?action=delete",$scope.modal).
			success(function(data, status, headers, config) {
				if(data.result){
					$scope.modal = {};
					$('#appModal').modal('hide');
					$scope.list();
				}
				else{
					dsh(data);
					$scope.errorMessage = data.message;
				}
				$rootScope.loading = false;
				$scope.lockForm = false;
			}).
			error(function(data, status, headers, config) {
				$log.info('error',data, status, headers, config);
			});
		}
		// dsh($scope.modal,52);
	};

	$scope.edit = function(item){
		$scope.errorMessage = null;
		$scope.modalType = 'edit';
		$scope.modalTitle = dic('Edit',$rootScope.langSelected) + ' ' + dic('Account',$rootScope.langSelected);
		$scope.modal = item;
		dsh(item);
	}

	$scope.add = function(){
		$scope.errorMessage = null;
		$scope.modalType = 'add';
		$scope.modalTitle = dic('Add',$rootScope.langSelected) + ' ' + dic('Account',$rootScope.langSelected);
		$scope.modal = {};
	}

	$scope.delete = function(item){
		$scope.errorMessage = null;
		$scope.modalType = 'delete';
		$scope.modalTitle = dic('Delete',$rootScope.langSelected) + ' ' + dic('Account',$rootScope.langSelected);
		$scope.modal = item;
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

	$scope.view = function(item){
		$location.path('accountDeppView/'+item.id+'/1/25/id/DESC');
	}

	$scope.getAccounts = function(state,type){
		$http.get("control/accountCtrl.php?action=accountList&accountType="+type).
		success(function(data, status, headers, config) {
			if(state == 'from')
				$scope.fromAccountList = data;
			else
				$scope.toAccountList = data;
		}).
		error(function(data, status, headers, config) {
			$log.info(data);
		});
	}

	$scope.getLastBalance = function(state,idAccount){
		$http.get('control/accountCtrl.php?action=lastBalanceDepp&id='+idAccount).
		success(function(data, status,headers, config){
			if(state == 'from')
				$scope.fromLastBalance = data;
			else
				$scope.toLastBalance = data;
		}).
		error(function(data, status, headers, config){
			dsh(data);
		});
	}

	$scope.transfer = function(){
		$scope.trn.toType = '';
		$scope.trn.to_id_account = '';
		$scope.toLastBalance = '';
		$scope.trn.dollar = '';
		$scope.trn.dinar = 0;
	}


	$http.get("control/dollar_rateCtrl.php?action=dollar_rateDepp").
	success(function(data, status, headers, config) {
		$scope.trn.dollarRate = parseFloat(data);
	}).
	error(function(data, status, headers, config) {
		$log.info(data);
	});


	$scope.saveTrn = function(){
		$rootScope.loading = true;
		$scope.lockForm = true;
		$http.post("control/accountCtrl.php?action=saveTrn",$scope.trn).
		success(function(data, status, headers, config) {
			if(data.result){
				$('#transferModal').modal('hide');
				$scope.list();
			}
			else{
				dsh(data);
				$scope.errorMessage = data.message;
			}
			$rootScope.loading = false;
			$scope.lockForm = false;
		}).
		error(function(data, status, headers, config) {
			$log.info('error',data, status, headers, config);
		});
	}

});
