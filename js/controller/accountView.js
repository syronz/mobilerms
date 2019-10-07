diakoApp.controller('accountViewController',function($scope,$rootScope,$http,$log,$location,$routeParams,mainFactory){

	mainFactory.checkLogin();

	$scope.accountList = null;
	$rootScope.part = 'accountView';

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
		$http.get("control/accountCtrl.php?action=accountViewList&id="+$routeParams.id+"&order="+order+"&limit="+limitPage+"&search="+str).
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

	

	



});
