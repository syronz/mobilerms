diakoApp.controller('logController',function($scope,$rootScope,$http,$log,$location,$routeParams,mainFactory,$indexedDB){

	$scope.objects = [];


	// $indexedDB.openStore('people', function(store){
	//      // store.insert({"ssn": "444-444-222-112","name": "John Doe", "age": 57,"author":"diako"}).then(function(e){ dsh(e);});
	//      store.getAll().then(function(people) {  
	//      	$scope.objects = people;
	//      	dsh('people',$scope.objects);
	//      });
	//  });

	mainFactory.checkLogin();

	$scope.logList = null;
	$rootScope.part = 'log';

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
		$http.get("control/logCtrl.php?action=list&order="+order+"&limit="+limitPage+"&search="+str).
		success(function(data, status, headers, config) {
			$scope.logList = data.rows;
			$scope.count = data.count;
			$rootScope.loading = false;
		}).
		error(function(data, status, headers, config) {
			$log.info(data);
		});
	}
	$scope.list();


	//select first input in model so we no need the mouse
	$('.modal').on('shown.bs.modal', function() {
		$(this).find('[autofocus]').focus();
	});


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
		// $scope.list();
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

	/*------------------------------------new -*/
	$scope.showDetail = function(item){
		item.showExtra = !item.showExtra;
		if(item.showExtra){
			$rootScope.loading = true;
			$http.get('control/logCtrl.php?action=showExtra&idLog='+item.id).
			success(function(data){
				dsh(data);
				if(data.result)
					item.extra = data.rows;
				$rootScope.loading = false;
			}).
			error(function(data,status,headers,config){
				dsh(data,status,headers,config)
			});
		}
		
	}

	//arr user list for show name of user ho do log action
	$scope.arrUser = [];
	$http.get('control/userCtrl.php?action=userList').
	success(function(data){
		dsh(data);
		var k =0;
		for(i=0; i<=data[data.length - 1].id; i++){
			if(i == data[k].id){
				$scope.arrUser.push(data[k].name);
				k++;
			}
			else
				$scope.arrUser.push('');
		}
	}).
	error(function(data,status,headers,config){
		dsh(data,status,headers,config)
	});



});
