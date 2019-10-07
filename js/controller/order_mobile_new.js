diakoApp.controller('order_mobile_newController',function($scope,$rootScope,$http,$log,$location,$routeParams,mainFactory,$indexedDB){

	$scope.objects = [];
	mainFactory.checkLogin();

	$rootScope.part = 'outdepp_piece_new';
	$rootScope.specialPrint = true;


	$scope.accountList = null;
	$scope.accountArr = [];
	$scope.invoice = {};

	var sample_invoice = {
		accountType : 'customer',
		type : 'active',
		pay : {},
		/*must be delete*/
		// id_account : 1,
		/*must be delete*/
		discount: 0.00, 
		items:[{iemi:'', description:'', cost:0,id_brand:0,id_model:0,id_repairman:0}]
	};

	$http.get("control/orderCtrl.php?action=getInvoice").
	success(function(data, status, headers, config) {

		sample_invoice.invoice = data.invoice;
	}).
	error(function(data, status, headers, config) {
		$log.info(data);
	});

	if($location.path() == "/outdepp_piece/new" || $scope.idoutdepp == null){
		$scope.invoice = sample_invoice;
	}
	else{
		$scope.invoice =  JSON.parse(localStorage["invoice"]);
	}


	

	$scope.getAccounts = function(){
		$http.get("control/accountCtrl.php?action=accountList&accountType="+$scope.invoice.accountType).
		success(function(data, status, headers, config) {
			$scope.accountList = data;

		}).
		error(function(data, status, headers, config) {
			$log.info(data);
		});
	}
	$scope.getAccounts();

	$scope.getDollarRate = function(){
		if(!$scope.invoice.dollarRate){
			$http.get("control/dollar_rateCtrl.php?action=dollar_rateDepp").
			success(function(data, status, headers, config) {
				$scope.invoice.dollarRate = parseFloat(data);
			}).
			error(function(data, status, headers, config) {
				$log.info(data);
			});
		}
	}


	/*------------------------------- START invoice outdepp NEW*/
	$http.get("control/mobile_repairmanCtrl.php?action=mobile_repairmanList").
		success(function(data, status, headers, config) {
			dsh(data);
			$scope.repairmanList = data;
		}).
		error(function(data, status, headers, config) {
			$log.info(data);
		});

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

	$scope.modelList = null;
	$scope.updateModelList = function(item){
		item.modelList = [];
		item.productList = [];
		$http.get("control/modelCtrl.php?action=modelListByBrand&idBrand="+item.id_brand.id).
		success(function(data, status, headers, config) {
			item.modelList = data;
			dsh(data);
		}).
		error(function(data, status, headers, config) {
			$log.info(data);
		});
	}

	

	$scope.updatePrice = function(item){
		item.cost = item.id_product.price;
		item.code = item.id_product.code;

		
	}
	$scope.getDollarRate();

	$scope.saveInvoice = function(){
		$rootScope.loading = $scope.lockForm = true;
		$http.post("control/orderCtrl.php?action=addMobile",$scope.invoice).
		success(function(data, status, headers, config) {
			if(data.result){
				$location.path('outdepp_piece/'+data.id);
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

	$scope.getPayAccounts = function(){
		if(!$scope.accountPayList){
			$http.get("control/accountCtrl.php?action=accountPayList").
			success(function(data, status, headers, config) {
				$scope.accountPayList = data;
				dsh(data,data[0]);
				$scope.invoice.pay.id_account = data[0];
			}).
			error(function(data, status, headers, config) {
				$log.info(data);
			});
		}
	}
	$scope.getPayAccounts();

	$scope.toggleError = function(){
		$scope.errorShow = !$scope.errorShow;
	}

	$scope.codeChange = function(e,item){
		if(e.keyCode == 13){
			console.log('itemcode',item);
			if(item.code){
				$http.get("control/productCtrl.php?action=productBycode&code="+item.code).
				success(function(data, status, headers, config) {
					dsh(data);
					// item.cost = data.cost;
					// $scope.brandList = [{"id":0,"name":data.brand}];
					item.id_brand = {"id":data.id_brand,"name":data.brand};

					item.modelList = [{"id":data.id_model,"name":data.model}];
					item.id_model = {"id":data.id_model,"name":data.model};

					item.productList = [{"id":data.id_product,"name":data.product}];
					item.id_product = {"id":data.id_product,"name":data.product};

					item.cost = data.cost;
					// $scope.loadStuff(item.stuffCat);
					// item.stuff = angular.toJson(item.stuff2.id);
				}).
				error(function(data, status, headers, config) {
					$log.info(data);
				});
				// $scope.addItem();
				// $scope.doFocus();
			}

		}
	}



	$scope.addItem = function() {
		$scope.invoice.items.push({iemi:'', description:'', cost:0,id_brand:0,id_model:0,id_repairman:0});    
	}
	
	$scope.removeItem = function(item) {
		$scope.invoice.items.splice($scope.invoice.items.indexOf(item), 1);    
	}

	$scope.invoice_sub_total = function() {
		var total = 0.00;
		angular.forEach($scope.invoice.items, function(item, key){
			total += parseFloat(item.cost);
		});
		return total;
	}
	$scope.calculate_discount = function() {
		return (($scope.invoice.discount * $scope.invoice_sub_total())/100);
	}
	$scope.calculate_grand_total = function() {
		localStorage["invoice"] = JSON.stringify($scope.invoice);
		return $scope.invoice_sub_total() - $scope.calculate_discount();
	} 

	$scope.printInfo = function() {
		window.print();
	}

	$scope.clearLocalStorage = function(){
		var confirmClear = confirm("Are you sure you would like to clear the invoice?");
		if(confirmClear){
			localStorage["invoice"] = "";
			$scope.invoice = sample_invoice;
		}
	}


	$scope.mobileChange = function(e,mobile){
		if(e.keyCode == 13){
			// console.log('mobilecode',mobile);
			$scope.invoice.id_account = null;
			$scope.errorShowMini = false;
			$http.get("control/accountCtrl.php?action=getAccountByMobile&mobile="+mobile).
			success(function(data, status, headers, config) {
				dsh(data);
				if(data.result){
					$scope.customer = data.info;
					if(data.customerType == 'exist'){
						$scope.invoice.id_account = data.info.id;
					}
					else{
						
					}
				}
				else{
					$scope.errorMessageMini = data.message;
					$scope.errorShowMini = true;
				}
			}).
			error(function(data, status, headers, config) {
				$log.info(data);
			});
		}
	}

	$scope.saveCustomer = function(){
		dsh($scope.customer);
		$scope.errorShowMini = false;
		$http.post("control/accountCtrl.php?action=addCustomer",$scope.customer).
		success(function(data, status, headers, config) {
			if(data.result){
				$scope.invoice.id_account = data.idCustomer;
			}
			else{
				console.log(data);
				$scope.errorMessageMini = data.message;
				$scope.errorShowMini = true;
			}
		}).
		error(function(data, status, headers, config) {
			$log.info('error',data, status, headers, config);
		});
	}

});

// $indexedDB.openStore('people', function(store){
// 	store.insert({ "ssn":$rootScope.part, "sortType": $scope.sortType,"sortFiled": $scope.sortFiled,"limit": $scope.limit}).then(function(e){ dsh(e);});

// });
// $indexedDB.openStore('people', function(store){
//      // store.insert({"ssn": "444-444-222-112","name": "John Doe", "age": 57,"author":"diako"}).then(function(e){ dsh(e);});
//      store.getAll().then(function(people) {  
//      	$scope.objects = people;
//      	dsh('people',$scope.objects);
//      });
//  });