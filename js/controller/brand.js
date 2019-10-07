diakoApp.controller('brandController',function($scope,$rootScope,$http,$log,$location,$routeParams,mainFactory,$indexedDB){

	$scope.objects = [];


	// $indexedDB.openStore('people', function(store){
	//      // store.insert({"ssn": "444-444-222-112","name": "John Doe", "age": 57,"author":"diako"}).then(function(e){ dsh(e);});
	//      store.getAll().then(function(people) {  
	//      	$scope.objects = people;
	//      	dsh('people',$scope.objects);
	//      });
	//  });



	mainFactory.checkLogin();

	$scope.brandList = null;
	$rootScope.part = 'brand';

	$scope.sortFiled = $routeParams.sortFiled;
	$scope.sortType = $routeParams.sortType;
	var order = $scope.sortFiled+' '+$scope.sortType;


	$scope.page = parseInt($routeParams.page);
	$scope.limit = parseInt($routeParams.limit);
	var limitPage = (($scope.page - 1) * $scope.limit)+','+$scope.limit;

	var str = '';
	if($routeParams.search != undefined)
		str = $routeParams.search;

	$scope.catList = null;
	$scope.getCats = function(){
		if($scope.catList == null){
			$http.get("control/catCtrl.php?action=catList").
			success(function(data, status, headers, config) {
				$scope.catList = data;

			}).
			error(function(data, status, headers, config) {
				$log.info(data);
			});
		}
	}
	$scope.getCats();









	$scope.list = function(){
		$rootScope.loading = true;
		$http.get("control/brandCtrl.php?action=list&order="+order+"&limit="+limitPage+"&search="+str).
		success(function(data, status, headers, config) {
			$scope.brandList = data.rows;
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
		if($scope.modalType == 'add'){
			$http.post("control/brandCtrl.php?action=add",$scope.modal).
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
			$http.post("control/brandCtrl.php?action=edit",$scope.modal).
			success(function(data, status, headers, config) {
				if(data.result){
					$scope.modal = {};
					$('#appModal').modal('hide');
					$scope.list();
				}
				else{
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
			$http.post("control/brandCtrl.php?action=delete",$scope.modal).
			success(function(data, status, headers, config) {
				if(data.result){
					$scope.modal = {};
					$('#appModal').modal('hide');
					$scope.list();
				}
				else{
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

//select first input in model so we no need the mouse
$('.modal').on('shown.bs.modal', function() {
	$(this).find('[autofocus]').focus();
});


$scope.edit = function(item){
	$scope.getCats();
	$scope.errorMessage = null;
	$scope.modalType = 'edit';
	$scope.modalTitle = dic('Edit',$rootScope.langSelected) + ' ' + dic('Brand',$rootScope.langSelected);
	$scope.modal = item;
}

$scope.add = function(){
	$('#appModal').modal('show');
	$scope.getCats();
	$scope.errorMessage = null;
	$scope.modalType = 'add';
	$scope.modalTitle = dic('Add',$rootScope.langSelected) + ' ' + dic('Brand',$rootScope.langSelected);
	$scope.modal = {};
}

$scope.delete = function(item){
	$scope.errorMessage = null;
	$scope.modalType = 'delete';
	$scope.modalTitle = dic('Delete',$rootScope.langSelected) + ' ' + dic('Brand',$rootScope.langSelected);
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



});
