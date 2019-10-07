diakoApp.controller('storeController',function($scope,$rootScope,$http,$log,$location,$routeParams,mainFactory,$indexedDB,$filter){

	$scope.objects = [];


	// $indexedDB.openStore('people', function(store){
	//      // store.insert({"ssn": "444-444-222-112","name": "John Doe", "age": 57,"author":"diako"}).then(function(e){ dsh(e);});
	//      store.getAll().then(function(people) {  
	//      	$scope.objects = people;
	//      	dsh('people',$scope.objects);
	//      });
	//  });



	mainFactory.checkLogin();

	$scope.storeList = null;
	$rootScope.part = 'store';

	$scope.sortFiled = $routeParams.sortFiled;
	$scope.sortType = $routeParams.sortType;
	var order = $scope.sortFiled+' '+$scope.sortType;


	$scope.page = parseInt($routeParams.page);
	$scope.limit = parseInt($routeParams.limit);
	var limitPage = (($scope.page - 1) * $scope.limit)+','+$scope.limit;

	var str = '';
	if($routeParams.search != undefined)
		str = $routeParams.search;

	$scope.brandList = null;
	$scope.getBrands = function(){
		if($scope.brandList == null){
			$http.get("control/brandCtrl.php?action=brandList").
			success(function(data, status, headers, config) {
				$scope.brandList = data;

			}).
			error(function(data, status, headers, config) {
				$log.info(data);
			});
		}
	}
	$scope.getBrands();


	$scope.modelList = null;
	$scope.updateModelList = function(){
		$http.get("control/modelCtrl.php?action=modelListByBrand&idBrand="+$scope.modal.id_brand).
		success(function(data, status, headers, config) {
			$scope.modelList = data;
			dsh(data);
		}).
		error(function(data, status, headers, config) {
			$log.info(data);
		});
	}






	$scope.list = function(){
		$rootScope.loading = true;
		$http.get("control/storeCtrl.php?action=list&order="+order+"&limit="+limitPage+"&search="+str).
		success(function(data, status, headers, config) {
			$scope.storeList = data.rows;
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
			$http.post("control/storeCtrl.php?action=add",$scope.modal).
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
			$http.post("control/storeCtrl.php?action=edit",$scope.modal).
			success(function(data, status, headers, config) {
				if(data.result){
					$scope.modal = {};
					$('#appModal').modal('hide');
					$scope.list();
				}
				else{
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
			$http.post("control/storeCtrl.php?action=delete",$scope.modal).
			success(function(data, status, headers, config) {
				if(data.result){
					$scope.modal = {};
					$('#appModal').modal('hide');
					$scope.list();
				}
				else{
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
		$(this).find('[autofocus]').focus();
	});


	$scope.edit = function(item){
		$scope.getBrands();
		$scope.errorMessage = null;
		$scope.modalType = 'edit';
		$scope.modalTitle = dic('Edit',$rootScope.langSelected)+ ' ' + dic('store',$rootScope.langSelected);
		$scope.modal = item;
	}

	$scope.add = function(){
		$('#appModal').modal('show');
		$scope.getBrands();
		$scope.errorMessage = null;
		$scope.modalType = 'add';
		$scope.modalTitle = dic('Add',$rootScope.langSelected)+ ' ' + dic('store',$rootScope.langSelected);
		$scope.modal = {};
	}

	$scope.delete = function(item){
		$scope.errorMessage = null;
		$scope.modalType = 'delete';
		$scope.modalTitle = dic('Delete',$rootScope.langSelected)+ ' ' + dic('store',$rootScope.langSelected);
		$scope.modal = item;
	}

	$scope.sorting = function(sort){

		$indexedDB.openStore('people', function(store){
			store.getAll().then(function(people) {  
				$scope.objects = people;
				dsh('people',$scope.objects);
			});

			store.upsert({ "ssn":$rootScope.part, "sortType": $scope.sortType,"sortFiled": $scope.sortFiled,"limit": $scope.limit}).then(function (e) {
				dsh(e);
			});
		});

		// $indexedDB.openStore('people', function(store){
		// 	store.insert({ "ssn":$rootScope.part, "sortType": $scope.sortType,"sortFiled": $scope.sortFiled,"limit": $scope.limit}).then(function(e){ dsh(e);});

		// });

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


	$(document).keydown(function (e) {
		if(e.keyCode == 78 && e.altKey)
			$scope.add();
	});


	$scope.getJard = function(){
		$rootScope.loading = true;
		$http.get("control/storeCtrl.php?action=jardList").
		success(function(data, status, headers, config) {
			$scope.storeJardList = data.rows;
			$scope.total = data.total;
			$scope.totalQty = data.totalQty;
			$rootScope.loading = false;
		}).
		error(function(data, status, headers, config) {
			$log.info(data);
		});
	}
	$scope.list();

	$scope.showJardDate = function(){
		console.log('go to date' + $filter('date')($scope.dateJard, 'y-MM-dd'));
		window.location = '#/store/jard/' + $filter('date')($scope.dateJard, 'y-MM-dd');
	}

	$scope.getJardDate = function(){
		$scope.dateJard = $routeParams.date;

		$rootScope.loading = true;
		$http.get("control/storeCtrl.php?action=jardListDate&date="+$routeParams.date).
		success(function(data, status, headers, config) {
			$scope.storeJardList = data.rows;
			$scope.total = data.total;
			$scope.totalQty = data.totalQty;
			$rootScope.loading = false;
		}).
		error(function(data, status, headers, config) {
			$log.info(data);
		});
	}



});
