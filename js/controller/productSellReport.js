diakoApp.controller('productSellReportController',function($scope,$rootScope,$http,$log,$location,$routeParams,mainFactory,$filter){

	$scope.objects = [];
	mainFactory.checkLogin();

	$rootScope.part = 'productSellReport';
	$rootScope.specialPrint = false;

	

	var str = '';
	if($routeParams.search != undefined)
		str = $routeParams.search;

	dsh($routeParams);

	$scope.getProductSellReportData = function(start,end,cat,brand,product,limit,order,orderType){
		$scope.productSellReportData = {};
		$rootScope.loading = true;
		$http.get("control/reportCtrl.php?action=productSellReport&start="+start+"&end="+end+"&cat="+cat+"&brand="+brand+"&product="+product+"&limit="+limit+"&order="+order+"&orderType="+orderType).
		success(function(data, status, headers, config) {
			if(data.result){
				$scope.productSellReportData = data.rows;
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

	var order = orderType = '';

	if(!$routeParams.start){
		$scope.productSellReportData = {};
		$rootScope.loading = true;
		$http.get("control/reportCtrl.php?action=productSellReport&limit=10000").
		success(function(data, status, headers, config) {
			if(data.result){
				$scope.productSellReportData = data.rows;
			}
			else{
				dsh(data);
			}
			$rootScope.loading = false;
		}).
		error(function(data, status, headers, config) {
			$log.info(data);
		});

		$scope.id_cat = 'all';
		$scope.id_brand = 'all';
		$scope.id_product = 'all';
		$scope.firstDate = new Date();
		$scope.lastDate = new Date();
		order = 'total_profit';
		orderType = 'DESC';

	}
	else{
		$scope.getProductSellReportData($routeParams.start,$routeParams.end,$routeParams.cat,$routeParams.brand,$routeParams.product,$routeParams.limit,$routeParams.order,$routeParams.orderType);

		$scope.id_cat = 'all';
		$scope.id_brand = 'all';
		$scope.id_product = 'all';
		$scope.firstDate = new Date($routeParams.start);
		$scope.lastDate = new Date($routeParams.end);
		order = $routeParams.order;
		orderType = $routeParams.orderType;
	}



	


	$scope.sorting = function(order2){
		if(orderType != 'DESC')
			orderType = 'DESC';
		else
			orderType = 'ASC';
		order = order2;
		$location.path($scope.part+'/'+$filter('date')($scope.firstDate, 'y-MM-dd')+'/'+$filter('date')($scope.lastDate, 'y-MM-dd')+'/'+$scope.id_cat+'/'+$scope.id_brand+'/'+$scope.id_product+'/10000/'+order+'/'+orderType);
	}

	$scope.checkSort = function(sort){
		if(sort == order)
			return 'sorting_'+order.toLowerCase();
		return 'sorting';
	}


	$http.get("control/catCtrl.php?action=catList").
	success(function(data, status, headers, config) {
		$scope.catList = data;
	}).
	error(function(data, status, headers, config) {
		dsh(data);
	});

	$scope.brandList = null;
	$scope.getBrands = function(){
		$http.get("control/brandCtrl.php?action=getBrandByCat&idCat="+$scope.id_cat).
		success(function(data, status, headers, config) {
			$scope.brandList = data;

		}).
		error(function(data, status, headers, config) {
			$log.info(data);
		});
	}


	$scope.productList = null;
	$scope.getProducts = function(){
		$http.get("control/productCtrl.php?action=productListByBrand&idBrand="+$scope.id_brand).
		success(function(data, status, headers, config) {
			$scope.productList = data;
		}).
		error(function(data, status, headers, config) {
			$log.info(data);
		});
	}

	$scope.newProductSellReport = function(){
		dsh($filter('date')($scope.firstDate, 'y-MM-dd'));
		$location.path($scope.part+'/'+$filter('date')($scope.firstDate, 'y-MM-dd')+'/'+$filter('date')($scope.lastDate, 'y-MM-dd')+'/'+$scope.id_cat+'/'+$scope.id_brand+'/'+$scope.id_product+'/10000/'+order+'/'+orderType);
		// :start/:end/:cat/:brand/:product/:limit/:sortFiled/:sortType
	}


	

});