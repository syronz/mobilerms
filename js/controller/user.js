diakoApp.controller('userController',function($scope,$rootScope,$http,$log,$location,$routeParams,mainFactory){

	mainFactory.checkLogin();
	console.log($routeParams);

	$scope.userList = null;
	$rootScope.part = 'user';

	$scope.sortFiled = $routeParams.sortFiled;
	$scope.sortType = $routeParams.sortType;
	var order = $scope.sortFiled+' '+$scope.sortType;


	$scope.page = parseInt($routeParams.page);
	$scope.limit = parseInt($routeParams.limit);
	var limitPage = (($scope.page - 1) * $scope.limit)+','+$scope.limit;

	var str = '';
	if($routeParams.search != undefined)
		str = $routeParams.search;

	$scope.permList = null;
	$scope.getColeges = function(){
		if($scope.permList == null){
			$http.get("control/permCtrl.php?action=permList").
			success(function(data, status, headers, config) {
				$scope.permList = data;
			}).
			error(function(data, status, headers, config) {
				$log.info(data);
			});
		}
	}

	$scope.totalDisplayed = 1000;
	$scope.totalDisplayedPlus = function(){
		dsh($scope.totalDisplayed);
		$scope.totalDisplayed +=1000;
		if($scope.totalDisplayed > $scope.permList.length)
			$scope.totalDisplayed = $scope.permList.length;
	}

	
	$scope.list = function(){
		$rootScope.loading = true;
		$http.get("control/userCtrl.php?action=list&order="+order+"&limit="+limitPage+"&search="+str).
		success(function(data, status, headers, config) {
			$scope.userList = data.rows;
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
			$rootScope.loading = $scope.lockForm = true;
			$http.post("control/userCtrl.php?action=add",$scope.modal).
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
				$rootScope.loading = $scope.lockForm = false;
			}).
			error(function(data, status, headers, config) {
				$log.info('error',data, status, headers, config);
			});
		}
		else if($scope.modalType == 'edit'){
			$rootScope.loading = $scope.lockForm = true;
			$http.post("control/userCtrl.php?action=edit",$scope.modal).
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
				$rootScope.loading = $scope.lockForm = false;
			}).
			error(function(data, status, headers, config) {
				$log.info('error',data, status, headers, config);
			});
		}
		else if($scope.modalType == 'delete'){
			$rootScope.loading = $scope.lockForm = true;
			$http.post("control/userCtrl.php?action=delete",$scope.modal).
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
				$rootScope.loading = $scope.lockForm = false;
			}).
			error(function(data, status, headers, config) {
				$log.info('error',data, status, headers, config);
			});
		}
	};

	//select first input in model so we no need the mouse
	$('.modal').on('shown.bs.modal', function() {
		dsh(this);
		$(this).find('[autofocus]').focus();
	});


	$scope.edit = function(item){
		$scope.getColeges();
		$scope.errorMessage = null;
		$scope.modalType = 'edit';
		$scope.modalTitle = dic('edit_user',$rootScope.langSelected);
		$scope.modal = item;
		$scope.modal.password = '';
	}

	$scope.add = function(){
		$('#appModal').modal('show');
		$scope.getColeges();
		$scope.errorMessage = null;
		$scope.modalType = 'add';
		$scope.modalTitle = dic('add_user',$rootScope.langSelected);
		$scope.modal = {};
	}

	$scope.delete = function(item){
		$scope.errorMessage = null;
		$scope.modalType = 'delete';
		$scope.modalTitle = dic('delete_user',$rootScope.langSelected);
		$scope.modal = item;
		$scope.modal.password = '';
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
	}

	$scope.checkSort = function(sort){
		if(sort == $scope.sortFiled)
			return 'sorting_'+$scope.sortType.toLowerCase();
		return 'sorting';
	}

	$scope.showKeyCode = function(){
		console.log('ok keycode');
	}

	$(document).keydown(function (e) {
		console.log(e.keyCode,e.altKey,e.ctrlKey,e.shiftKey);
		if(e.keyCode == 78 && e.altKey)
			$scope.add();
	});

});
