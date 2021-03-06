diakoApp.controller('indepp_accController',function($scope,$rootScope,$http,$log,$location,$routeParams,mainFactory,$indexedDB){

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

	$scope.indepp_accList = null;
	$rootScope.part = 'indepp_acc';
	$rootScope.specialPrint = true;

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

	if($location.path() == "/indepp_acc/new" || $scope.idIndepp == null){
		$scope.invoice = sample_invoice;
	}
	else{
		$scope.invoice =  JSON.parse(localStorage["invoice"]);
	}


	// $scope.getAccounts = function(){
	// 	$http.get("control/accountCtrl.php?action=accountList&accountType="+$scope.invoice.accountType).
	// 	success(function(data, status, headers, config) {
	// 		$scope.accountList = data;

	// 	}).
	// 	error(function(data, status, headers, config) {
	// 		$log.info(data);
	// 	});
	// }
	// $scope.getAccounts();



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
		$http.get("control/indeppCtrl.php?action=list&order="+order+"&limit="+limitPage+"&search="+str).
		success(function(data, status, headers, config) {
			$scope.indepp_accList = data.rows;
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
			$rootScope.loading = true;
			$scope.lockForm = true;
			$http.post("control/indeppCtrl.php?action=delete",$scope.modal).
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

	$scope.delete = function(item){
		$scope.errorMessage = null;
		$scope.modalType = 'delete';
		$scope.modalTitle = dic('Delete',$rootScope.langSelected) + ' ' + dic('indepp_acc',$rootScope.langSelected);
		$scope.modal = item;
	}

	$scope.view = function(item){
		$location.path('indepp_acc/'+item.id);
	}

	$scope.edit = function(item){
		$location.path('indepp_acc/edit/'+item.id);
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
		if(e.keyCode == 78 && e.altKey)
			$scope.add();
	});

	/*------------------------------- START invoice indepp NEW*/

	$scope.saveInvoice = function(){
		$rootScope.loading = true;
		$scope.lockForm = true;
		$http.post("control/indeppCtrl.php?action=add",$scope.invoice).
		success(function(data, status, headers, config) {
			if(data.result){
				$location.path('indepp_acc/'+data.id);
			}
			else{
				console.log(data);
				$scope.errorMessage = data.message;
				$scope.errorShow = true;
			}
			$rootScope.loading = false;
			$scope.lockForm = false;
		}).
		error(function(data, status, headers, config) {
			$log.info('error',data, status, headers, config);
		});
	}

	$scope.toggleError = function(){
		$scope.errorShow = !$scope.errorShow;
	}

	/*------------------------------- END invoice indepp NEW*/

	/*------------------------------- Start invoice indepp PRINT*/
	$scope.loadPrint = function(){
		dsh($routeParams);
		if($routeParams.id){
			$http.get("control/indeppCtrl.php?action=indeppPrint&id="+$routeParams.id).
			success(function(data, status, headers, config) {
				$scope.invoicePrint = data;
				$scope.invoicePrint.base.date = new Date($scope.invoicePrint.base.date);
				$scope.pay = {id_account : $scope.invoicePrint.base.id_account,id_depp : $scope.invoicePrint.base.id_depp,id_indepp:$scope.invoicePrint.base.id};
			}).
			error(function(data, status, headers, config) {
				$log.info(data);
			});
		}
	}
	
	

	/*------------------------------- END invoice indepp PRINT*/


	/*------------------------------- Start invoice indepp EDIT*/
	$scope.loadEdit = function(){
		if($routeParams.id){
			$http.get("control/indeppCtrl.php?action=indeppEdit&id="+$routeParams.id).
			success(function(data, status, headers, config) {
				$scope.invoice = data;
				$scope.getBrands(data.base.id_depp);
				$scope.invoice.discount = data.base.discount;
				$scope.invoice.id_account = data.base.id_account;
				$scope.invoice.type = data.base.indeppType;
				$scope.invoice.invoice = data.base.invoice;
				$scope.invoice.detail = data.base.detail;
				$scope.invoice.accountType = data.base.accountType;
			}).
			error(function(data, status, headers, config) {
				$log.info(data);
			});
		}
	}

	$scope.saveEditedInvoice = function(){
		$rootScope.loading = true;
		$scope.lockForm = true;
		$http.post("control/indeppCtrl.php?action=edit",$scope.invoice).
		success(function(data, status, headers, config) {
			if(data.result){
				$location.path('indepp_acc/'+data.id);
				dsh(data);
			}
			else{
				console.log(data);
				$scope.errorMessage = data.message;
				$scope.errorShow = true;
			}
			$rootScope.loading = false;
			$scope.lockForm = false;
		}).
		error(function(data, status, headers, config) {
			$log.info('error',data, status, headers, config);
		});
	}

	

	/*------------------------------- END invoice indepp EDIT*/

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



