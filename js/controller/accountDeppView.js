diakoApp.controller('accountDeppViewController',function($scope,$rootScope,$http,$log,$location,$routeParams,mainFactory,$filter){

	mainFactory.checkLogin();

	$scope.accountList = null;
	$rootScope.part = 'accountDeppView';

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
		if (!$routeParams.start) {
			start = end = '';
		}
		else{
			start = $routeParams.start;
			end = $routeParams.end;
		}
		$http.get("control/accountCtrl.php?action=accountDeppViewList&id="+$routeParams.id+"&order="+order+"&limit="+limitPage+"&search="+str+"&start="+start+"&end="+end).
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
		if($scope.modalType == 'edit'){
			$rootScope.loading = true;
			$scope.lockForm = true;
			$http.post("control/accountCtrl.php?action=editAcc",$scope.modal).
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
			$http.post("control/accountCtrl.php?action=deleteAcc",$scope.modal).
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
		$scope.modalTitle = dic('Edit',$rootScope.langSelected) + ' ' + dic('Transaction',$rootScope.langSelected);
		$scope.modal = item;
		$scope.modal.dollar = parseFloat(item.dollar);
		$scope.modal.dinar = parseInt(item.dinar);
	}

	$scope.add = function(){
		$scope.errorMessage = null;
		$scope.modalType = 'add';
		$scope.modalTitle = dic('Add',$rootScope.langSelected) + ' ' + dic('Transaction',$rootScope.langSelected);
		$scope.modal = {};
	}

	$scope.delete = function(item){
		$scope.errorMessage = null;
		$scope.modalType = 'delete';
		$scope.modalTitle = dic('Delete',$rootScope.langSelected) + ' ' + dic('Transaction',$rootScope.langSelected);
		$scope.modal = item;
		$scope.modal.dollar = parseFloat(item.dollar);
		$scope.modal.dinar = parseInt(item.dinar);
	}

	$scope.sorting = function(sort){
		dsh(sort);
		if($scope.sortType != 'DESC')
			$scope.sortType = 'DESC';
		else
			$scope.sortType = 'ASC';
		$scope.sortFiled = sort;
		order = $scope.sortFiled+' '+$scope.sortType;
		$location.path($scope.part+'/'+$routeParams.id+'/'+$scope.page+'/'+$scope.limit+'/'+$scope.sortFiled+'/'+$scope.sortType+'/'+str);
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

	$scope.getPayAccounts = function(){
		if(!$scope.accountPayList){
			$http.get("control/accountCtrl.php?action=accountList").
			success(function(data, status, headers, config) {
				$scope.accountPayList = data;
			}).
			error(function(data, status, headers, config) {
				$log.info(data);
			});
		}
	}
	$scope.getPayAccounts();

	$http.get("control/accountCtrl.php?action=accountInfo&id="+$routeParams.id).
	success(function(data, status, headers, config) {
		if(data.result)
			$scope.accountInfo = data.data;
	}).
	error(function(data, status, headers, config) {
		$log.info(data);
	});


	$scope.updateLimit = function(v){
		var str = '';
		if($routeParams.search != undefined)
			str = $routeParams.search;
		$location.path($scope.part+'/'+$routeParams.id+'/'+$scope.page+'/'+v+'/'+$scope.sortFiled+'/'+$scope.sortType+'/'+str);
	}
	$scope.goPage = function(page){
		var str = '';
		if($routeParams.search != undefined)
			str = $routeParams.search;
		$location.path($scope.part+'/'+$routeParams.id+'/'+page+'/'+$scope.limit+'/'+$scope.sortFiled+'/'+$scope.sortType+'/'+str);
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

	$scope.transferTo = function(){
		$scope.trn.toType = '';
		$scope.trn.to_id_account = '';
		$scope.toLastBalance = '';
		$scope.trn.dollar = '';
		$scope.trn.dinar = 0;

		$scope.trn.fromType = $scope.accountInfo.type;
		$scope.getAccounts('from',$scope.trn.fromType);
		$scope.trn.from_id_account = $scope.accountInfo.id;

		$scope.getLastBalance('from',$scope.trn.from_id_account);
	}

	$scope.transferFrom = function(){
		$scope.trn.fromType = '';
		$scope.trn.from_id_account = '';
		$scope.fromLastBalance = '';
		$scope.trn.dollar = '';
		$scope.trn.dinar = 0;

		$scope.trn.toType = $scope.accountInfo.type;
		$scope.getAccounts('to',$scope.trn.toType);
		$scope.trn.to_id_account = $scope.accountInfo.id;

		$scope.getLastBalance('to',$scope.trn.to_id_account);
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


	$scope.toggleShowFilter = function(){
		$scope.showFilter = !$scope.showFilter;
	}

	$scope.showByDateFilter = function(){
		dsh($filter('date')($scope.firstDate, 'y-m-d'));
		// $location.path()
		$location.path($scope.part+'/'+$routeParams.id+'/'+$scope.page+'/'+$scope.limit+'/'+$scope.sortFiled+'/'+$scope.sortType+'/'+$filter('date')($scope.firstDate, 'y-MM-dd')+'/'+$filter('date')($scope.lastDate, 'y-MM-dd'));
	}

	if($routeParams.start){
		$scope.showFilter = true;
		$scope.firstDate = new Date($routeParams.start);
		$scope.lastDate = new Date($routeParams.end);
	}

	



});
