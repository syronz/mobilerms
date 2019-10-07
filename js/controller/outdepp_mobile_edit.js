diakoApp.controller('outdepp_mobile_editController',function($scope,$rootScope,$http,$log,$location,$routeParams,mainFactory,$indexedDB){

	$scope.objects = [];
	mainFactory.checkLogin();

	$rootScope.part = 'outdepp_mobile_edit';
	$rootScope.specialPrint = false;

	$scope.loadEdit = function(){
		if($routeParams.id){
			$http.get("control/outdeppCtrl.php?action=outdeppEdit&id="+$routeParams.id).
			success(function(data, status, headers, config) {
				$scope.invoice = data;
				$scope.getBrands(data.base.id_depp);
				$scope.invoice.discount = data.base.discount;
				$scope.invoice.discountAmount = data.base.discount * $scope.invoice_sub_total() / 100;
				$scope.invoice.id_account = data.base.id_account;
				$scope.invoice.type = data.base.outdeppType;
				$scope.invoice.invoice = data.base.invoice;
				$scope.invoice.detail = data.base.detail;
				$scope.invoice.accountType = data.base.accountType;
				$http.get("control/accountCtrl.php?action=accountList&accountType="+$scope.invoice.accountType).
				success(function(data, status, headers, config) {
					$scope.accountList = data;

				}).
				error(function(data, status, headers, config) {
					$log.info(data);
				});
			}).
			error(function(data, status, headers, config) {
				$log.info(data);
			});
		}
	}

	$scope.saveEditedInvoice = function(){
		$rootScope.loading = $scope.lockForm = true;
		$http.post("control/outdeppCtrl.php?action=edit",$scope.invoice).
		success(function(data, status, headers, config) {
			if(data.result){
				$location.path('outdepp_mobile/'+data.id);
			}
			else{
				console.log(data);
				$scope.errorMessage = data.message;
				$scope.errorShow = true;
			}
			$rootScope.loading = $scope.lockForm = false;
		}).
		error(function(data, status, headers, config) {
			$log.info('error',data, status, headers, config);
		});
	}

	$scope.brandList = null;
	$scope.getBrands = function(idDepp){
		if(idDepp)
			$scope.invoice.id_depp = idDepp;
		$http.get("control/brandCtrl.php?action=brandListDepp").
		success(function(data, status, headers, config) {
			$scope.brandList = data;

		}).
		error(function(data, status, headers, config) {
			$log.info(data);
		});
	}
	$scope.getBrands();

	$scope.getAccounts = function(){
		$http.get("control/accountCtrl.php?action=accountList&accountType="+$scope.invoice.accountType).
		success(function(data, status, headers, config) {
			$scope.accountList = data;

		}).
		error(function(data, status, headers, config) {
			$log.info(data);
		});
	}

	$scope.getDollarRate = function(){
		if(!$scope.invoice.dollarRate){
			$http.get("control/dollar_rateCtrl.php?action=dollar_rateDepp&idDepp="+$scope.invoice.id_depp).
			success(function(data, status, headers, config) {
				$scope.invoice.dollarRate = parseFloat(data);
			}).
			error(function(data, status, headers, config) {
				$log.info(data);
			});
		}
	}

	$scope.addItem = function() {
		$scope.invoice.items.push({qty:1, cost:0, description:"",id_brand:0,id_model:0});    
	}
	
	$scope.removeItem = function(item) {
		$scope.invoice.items.splice($scope.invoice.items.indexOf(item), 1);    
	}

	$scope.invoice_sub_total = function() {
		var total = 0.00;
		angular.forEach($scope.invoice.items, function(item, key){
			total += (item.qty * item.cost);
		});
		return total;
	}


	$scope.calculate_discount = function() {
		$scope.invoice.discountAmount = (($scope.invoice.discount * $scope.invoice_sub_total())/100);
	}

	$scope.calculate_discountAmount = function() {
		$scope.invoice.discount = $scope.invoice.discountAmount / $scope.invoice_sub_total() * 100;
	}



	$scope.calculate_grand_total = function() {
		localStorage["invoice"] = JSON.stringify($scope.invoice);
		return $scope.invoice_sub_total() - $scope.invoice.discountAmount;
	} 

	$scope.codeChange = function(e,item){
		if(e.keyCode == 13){
			console.log('itemcode',item);
			if(item.code){
				$http.get("control/productCtrl.php?action=productBycode&code="+item.code).
				success(function(data, status, headers, config) {
					item.id_brand = {"id":data.id_brand,"name":data.brand};

					item.modelList = [{"id":data.id_model,"name":data.model}];
					item.id_model = {"id":data.id_model,"name":data.model};

					item.productList = [{"id":data.id_product,"name":data.product}];
					item.id_product = {"id":data.id_product,"name":data.product};

					item.cost = data.cost;
					item.price_buy = data.price_buy;
				}).
				error(function(data, status, headers, config) {
					$log.info(data);
				});
			}
		}
	}



	$scope.updateProductList = function(item){
		$http.get("control/productCtrl.php?action=productListByBrand&idBrand="+item.id_brand.id).
		success(function(data, status, headers, config) {
			item.productList = data;
		}).
		error(function(data, status, headers, config) {
			$log.info(data);
		});
	}

	$scope.updatePrice = function(item){
		item.cost = item.id_product.price;
		item.code = item.id_product.code;
		item.price_buy = item.id_product.price_buy;

		$scope.getDollarRate();
	}

	$scope.toggleError = function(){
		$scope.errorShow = !$scope.errorShow;
	}




});