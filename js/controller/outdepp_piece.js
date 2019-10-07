diakoApp.controller('outdepp_pieceController',function($scope,$rootScope,$http,$log,$location,$routeParams,mainFactory,$indexedDB){

	// $scope.idCat = 5;
	// $http.get("control/userCtrl.php?action=authUserPart").
	// success(function(data, status, headers, config) {
	// 	$scope.userDeppAuth = data;
	// 	sample_invoice.userDeppAuth = data[0];
	// }).
	// error(function(data, status, headers, config) {
	// 	$log.info(data);
	// });


	$scope.objects = [];
	mainFactory.checkLogin();

	$scope.outdepp_pieceList = null;
	$rootScope.part = 'outdepp_piece';
	$rootScope.specialPrint = false;

	$scope.sortFiled = $routeParams.sortFiled;
	$scope.sortType = $routeParams.sortType;
	var order = $scope.sortFiled+' '+$scope.sortType;


	$scope.page = parseInt($routeParams.page);
	$scope.limit = parseInt($routeParams.limit);
	var limitPage = (($scope.page - 1) * $scope.limit)+','+$scope.limit;

	var str = '';
	if($routeParams.search != undefined)
		str = $routeParams.search;

	$scope.accountList = null;
	$scope.accountArr = [];
	$scope.invoice = {};

	var sample_invoice = {
		accountType : 'company',
		type : 'active',
		pay : {},
		discount: 0.00, 
		items:[ {qty:1, description:'', cost:0,id_brand:0,id_model:0,id_product:0}]
	};

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


	// $scope.userDeppList = null;
	// if($scope.userDeppList == null){
	// 	$http.get("control/user_deppCtrl.php?action=userDeppList").
	// 	success(function(data, status, headers, config) {
	// 		$scope.userDeppList = data;
	// 	}).
	// 	error(function(data, status, headers, config) {
	// 		$log.info(data);
	// 	});
	// }

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

	$scope.list = function(){
		$rootScope.loading = true;
		$http.get("control/outdeppCtrl.php?action=list&order="+order+"&limit="+limitPage+"&search="+str).
		success(function(data, status, headers, config) {
			$scope.outdepp_pieceList = data.rows;
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
		if($scope.modalType == 'delete'){
			$rootScope.loading = $scope.lockForm = true;
			$http.post("control/outdeppCtrl.php?action=delete",{id:$scope.modal.id}).
			success(function(data, status, headers, config) {
				console.log(data);
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

	$scope.delete = function(item){
		$scope.errorMessage = null;
		$scope.modalType = 'delete';
		$scope.modalTitle = dic('Delete',$rootScope.langSelected) + ' ' + dic('outdepp_piece',$rootScope.langSelected);
		$scope.modal = item;
	}

	$scope.view = function(item){
		$location.path('outdepp_piece/'+item.id);
	}

	$scope.edit = function(item){
		$location.path('outdepp_piece/edit/'+item.id);
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
		if(e.keyCode == 78 && e.altKey){
			location.hash = 'outdepp_piece/new';
		}
	});

	$scope.toggleStatus = function(item){
		$scope.lockForm = $rootScope.loading = true;
		$http.get("control/outdeppCtrl.php?action=toggleStatus&id="+item.id).
		success(function(data, status, headers, config) {
			if(data.result){
				item.status = data.status;
			}
			$scope.lockForm = $rootScope.loading = false;
		}).
		error(function(data, status, headers, config) {
			$log.info(data);
		});
	}

	/*------------------------------- START invoice outdepp NEW
	$scope.brandList = null;
	$scope.getBrands = function(idDepp){
		if(idDepp)
			$scope.invoice.id_depp = idDepp;
		$http.get("control/brandCtrl.php?action=brandListDepp&idDepp="+$scope.invoice.id_depp).
		success(function(data, status, headers, config) {
			$scope.brandList = data;

		}).
		error(function(data, status, headers, config) {
			$log.info(data);
		});
	}



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

	$scope.productList = null;
	$scope.updateProductList = function(item){
		$http.get("control/productCtrl.php?action=productListByModel&idModel="+item.id_model.id).
		success(function(data, status, headers, config) {
			item.productList = data;
			dsh(data);
		}).
		error(function(data, status, headers, config) {
			$log.info(data);
		});
	}

	$scope.updatePrice = function(item){
		item.cost = item.id_product.price;
		item.code = item.id_product.code;

		$scope.getDollarRate();
	}

	$scope.saveInvoice = function(){
		dsh($scope.invoice);
		$http.post("control/outdeppCtrl.php?action=add",$scope.invoice).
		success(function(data, status, headers, config) {
			if(data.result){
				$location.path('outdepp_piece/'+data.id);
			}
			else{
				console.log(data);
				$scope.errorMessage = data.message;
				$scope.errorShow = true;
			}
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
					item.id_brand = {"id":data.id_brand,"name":data.brand};

					item.modelList = [{"id":data.id_model,"name":data.model}];
					item.id_model = {"id":data.id_model,"name":data.model};

					item.productList = [{"id":data.id_product,"name":data.product}];
					item.id_product = {"id":data.id_product,"name":data.product};

					item.cost = data.cost;
				}).
				error(function(data, status, headers, config) {
					$log.info(data);
				});
			}

		}
	}





	/*------------------------------- END invoice outdepp NEW*/

	/*------------------------------- Start invoice outdepp PRINT*/
	$scope.loadPrint = function(){
		dsh($routeParams);
		if($routeParams.id){
			$http.get("control/outdeppCtrl.php?action=outdeppPrint&id="+$routeParams.id).
			success(function(data, status, headers, config) {
				$rootScope.specialPrint = true;
				$scope.invoicePrint = data;
				$scope.invoicePrint.base.date = new Date($scope.invoicePrint.base.date);
				// $rootScope.userDeppAuthSelect = {id : $scope.invoicePrint.base.id_depp};
				$scope.pay = {id_account : $scope.invoicePrint.base.id_account,id_depp : $scope.invoicePrint.base.id_depp,id_outdepp:$scope.invoicePrint.base.id};
			}).
			error(function(data, status, headers, config) {
				$log.info(data);
			});
		}
	}

	$scope.savePayin = function(){
		dsh($scope.pay);
		$http.post("control/outdeppCtrl.php?action=savePayin",$scope.pay).
		success(function(data, status, headers, config) {
			if(data.result){
				
				$http.get("control/outdeppCtrl.php?action=outdeppPayins&idoutdepp="+$routeParams.id).
				success(function(data, status, headers, config) {
					$scope.invoicePrint.base.totalPays = data.base.totalPays;
					$scope.invoicePrint.pays = data.pays;
					$scope.pay.payDinar = 0;
					$scope.pay.payDollar = 0;
					$scope.pay.detail = '';
					dsh(data);
				}).
				error(function(data, status, headers, config) {
					$log.info(data);
				});

				dsh(data);
			}
			else{
				console.log(data);
				$scope.errorMessage = data.message;
				$scope.errorShow = true;
			}
		}).
		error(function(data, status, headers, config) {
			$log.info('error',data, status, headers, config);
		});
	}

	/*------------------------------- END invoice outdepp PRINT*/


	/*------------------------------- Start invoice outdepp EDIT
	$scope.loadEdit = function(){
		if($routeParams.id){
			$http.get("control/outdeppCtrl.php?action=outdeppEdit&id="+$routeParams.id).
			success(function(data, status, headers, config) {
				$scope.invoice = data;
				$scope.getBrands(data.base.id_depp);
				$scope.invoice.discount = data.base.discount;
				$scope.invoice.id_account = data.base.id_account;
				$scope.invoice.type = data.base.outdeppType;
				$scope.invoice.invoice = data.base.invoice;
				$scope.invoice.detail = data.base.detail;
			}).
			error(function(data, status, headers, config) {
				$log.info(data);
			});
		}
	}

	$scope.saveEditedInvoice = function(){
		dsh($scope.invoice);
		$http.post("control/outdeppCtrl.php?action=edit",$scope.invoice).
		success(function(data, status, headers, config) {
			if(data.result){
				dsh(data);
			}
			else{
				console.log(data);
				$scope.errorMessage = data.message;
				$scope.errorShow = true;
			}
		}).
		error(function(data, status, headers, config) {
			$log.info('error',data, status, headers, config);
		});
	}

	

	

	$scope.logoRemoved = false;
	$scope.printMode = false;

	$scope.addItem = function() {
		$scope.invoice.items.push({qty:1, cost:0, description:"",id_brand:0,id_model:0});    
	}
	$scope.removeLogo = function(element) {
		var elem = angular.element("#remove_logo");
		if(elem.text() == "Show Logo"){
			elem.text("Remove Logo");
			$scope.logoRemoved = false;
		}
		else{
			elem.text("Show Logo");
			$scope.logoRemoved = true;
		}
	}

	$scope.editLogo = function(){
		$("#imgInp").trigger("click");
	}

	$scope.showLogo = function() {
		$scope.logoRemoved = false;
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
/*------------------------------- END invoice outdepp EDIT*/
});





