function dsh(){
	for(a in arguments)
		console.log(arguments[a]);
}

var diakoApp = angular.module('diakoApp', ['ngRoute','indexedDB','chart.js']);

diakoApp.config(function($routeProvider) {
	$routeProvider
	.when('/', {
		templateUrl : 'template/dashboard.html',
		controller  : 'dashboardController'
	})

	.when('/login', {
		templateUrl : 'template/login.html',
		controller  : 'loginController'
	})
	.when('/perm/:page/:limit/:sortFiled/:sortType', {
		templateUrl : 'template/perm.html',
		controller  : 'permController'
	})
	.when('/perm/:page/:limit/:sortFiled/:sortType/:search', {
		templateUrl : 'template/perm.html',
		controller  : 'permController'
	})

	.when('/user/:page/:limit/:sortFiled/:sortType', {
		templateUrl : 'template/user.html',
		controller  : 'userController'
	})
	.when('/user/:page/:limit/:sortFiled/:sortType/:search', {
		templateUrl : 'template/user.html',
		controller  : 'userController'
	})

//new part
	.when('/city/:page/:limit/:sortFiled/:sortType', {
		templateUrl : 'template/city.html',
		controller  : 'cityController'
	})
	.when('/city/:page/:limit/:sortFiled/:sortType/:search', {
		templateUrl : 'template/city.html',
		controller  : 'cityController'
	})

	.when('/branch/:page/:limit/:sortFiled/:sortType', {
		templateUrl : 'template/branch.html',
		controller  : 'branchController'
	})
	.when('/branch/:page/:limit/:sortFiled/:sortType/:search', {
		templateUrl : 'template/branch.html',
		controller  : 'branchController'
	})

	.when('/depp/:page/:limit/:sortFiled/:sortType', {
		templateUrl : 'template/depp.html',
		controller  : 'deppController'
	})
	.when('/depp/:page/:limit/:sortFiled/:sortType/:search', {
		templateUrl : 'template/depp.html',
		controller  : 'deppController'
	})

	.when('/cat/:page/:limit/:sortFiled/:sortType', {
		templateUrl : 'template/cat.html',
		controller  : 'catController'
	})
	.when('/cat/:page/:limit/:sortFiled/:sortType/:search', {
		templateUrl : 'template/cat.html',
		controller  : 'catController'
	})

	.when('/brand/:page/:limit/:sortFiled/:sortType', {
		templateUrl : 'template/brand.html',
		controller  : 'brandController'
	})
	.when('/brand/:page/:limit/:sortFiled/:sortType/:search', {
		templateUrl : 'template/brand.html',
		controller  : 'brandController'
	})

	.when('/product/:page/:limit/:sortFiled/:sortType', {
		templateUrl : 'template/product.html',
		controller  : 'productController'
	})
	.when('/product/:page/:limit/:sortFiled/:sortType/:search', {
		templateUrl : 'template/product.html',
		controller  : 'productController'
	})

	.when('/user_depp/:page/:limit/:sortFiled/:sortType', {
		templateUrl : 'template/user_depp.html',
		controller  : 'user_deppController'
	})
	.when('/user_depp/:page/:limit/:sortFiled/:sortType/:search', {
		templateUrl : 'template/user_depp.html',
		controller  : 'user_deppController'
	})

	.when('/depp_cat/:page/:limit/:sortFiled/:sortType', {
		templateUrl : 'template/depp_cat.html',
		controller  : 'depp_catController'
	})
	.when('/depp_cat/:page/:limit/:sortFiled/:sortType/:search', {
		templateUrl : 'template/depp_cat.html',
		controller  : 'depp_catController'
	})

	.when('/account/:page/:limit/:sortFiled/:sortType', {
		templateUrl : 'template/account.html',
		controller  : 'accountController'
	})
	.when('/account/:page/:limit/:sortFiled/:sortType/:search', {
		templateUrl : 'template/account.html',
		controller  : 'accountController'
	})

	.when('/dollar_rate/:page/:limit/:sortFiled/:sortType', {
		templateUrl : 'template/dollar_rate.html',
		controller  : 'dollar_rateController'
	})
	.when('/dollar_rate/:page/:limit/:sortFiled/:sortType/:search', {
		templateUrl : 'template/dollar_rate.html',
		controller  : 'dollar_rateController'
	})

	.when('/indepp_piece/:page/:limit/:sortFiled/:sortType', {
		templateUrl : 'template/indepp_piece.html',
		controller  : 'indepp_pieceController'
	})
	.when('/indepp_piece/:page/:limit/:sortFiled/:sortType/:search', {
		templateUrl : 'template/indepp_piece.html',
		controller  : 'indepp_pieceController'
	})
	.when('/indepp_piece/new', {
		templateUrl : 'template/indepp_piece_new.html',
		controller  : 'indepp_pieceController'
	})
	.when('/indepp_piece/:id', {
		templateUrl : 'template/indepp_piece_print.html',
		controller  : 'indepp_pieceController'
	})
	.when('/indepp_piece/edit/:id', {
		templateUrl : 'template/indepp_piece_edit.html',
		controller  : 'indepp_pieceController'
	})


	.when('/card/:idAccount', {
		templateUrl : 'template/card.html',
		controller  : 'cardController'
	})

	.when('/brandDepp/:page/:limit/:sortFiled/:sortType', {
		templateUrl : 'template/brandDepp.html',
		controller  : 'brandDeppController'
	})
	.when('/brandDepp/:page/:limit/:sortFiled/:sortType/:search', {
		templateUrl : 'template/brandDepp.html',
		controller  : 'brandDeppController'
	})

	.when('/model/:page/:limit/:sortFiled/:sortType', {
		templateUrl : 'template/model.html',
		controller  : 'modelController'
	})
	.when('/model/:page/:limit/:sortFiled/:sortType/:search', {
		templateUrl : 'template/model.html',
		controller  : 'modelController'
	})

	.when('/store/:page/:limit/:sortFiled/:sortType', {
		templateUrl : 'template/store.html',
		controller  : 'storeController'
	})
	.when('/store/:page/:limit/:sortFiled/:sortType/:search', {
		templateUrl : 'template/store.html',
		controller  : 'storeController'
	})
	.when('/store/jard', {
		templateUrl : 'template/store_jard.html',
		controller  : 'storeController'
	})
	.when('/store/jard/:date', {
		templateUrl : 'template/store_jard_date.html',
		controller  : 'storeController'
	})

	.when('/accountDepp/:page/:limit/:sortFiled/:sortType', {
		templateUrl : 'template/accountDepp.html',
		controller  : 'accountDeppController'
	})
	.when('/accountDepp/:page/:limit/:sortFiled/:sortType/:search', {
		templateUrl : 'template/accountDepp.html',
		controller  : 'accountDeppController'
	})

	.when('/accountDeppView/:id/:page/:limit/:sortFiled/:sortType', {
		templateUrl : 'template/accountDeppView.html',
		controller  : 'accountDeppViewController'
	})
	.when('/accountDeppView/:id/:page/:limit/:sortFiled/:sortType/:search', {
		templateUrl : 'template/accountDeppView.html',
		controller  : 'accountDeppViewController'
	})
	.when('/accountDeppView/:id/:page/:limit/:sortFiled/:sortType/:start/:end', {
		templateUrl : 'template/accountDeppView.html',
		controller  : 'accountDeppViewController'
	})

	.when('/outdepp_piece/:page/:limit/:sortFiled/:sortType', {
		templateUrl : 'template/outdepp_piece.html',
		controller  : 'outdepp_pieceController'
	})
	.when('/outdepp_piece/:page/:limit/:sortFiled/:sortType/:search', {
		templateUrl : 'template/outdepp_piece.html',
		controller  : 'outdepp_pieceController'
	})
	.when('/outdepp_piece/new', {
		templateUrl : 'template/outdepp_piece_new.html',
		controller  : 'outdepp_piece_newController'
	})
	.when('/outdepp_piece/:id', {
		templateUrl : 'template/outdepp_piece_print.html',
		controller  : 'outdepp_pieceController'
	})
	.when('/outdepp_piece/edit/:id', {
		templateUrl : 'template/outdepp_piece_edit.html',
		controller  : 'outdepp_piece_editController'
	})

	.when('/mobile_repairman/:page/:limit/:sortFiled/:sortType', {
		templateUrl : 'template/mobile_repairman.html',
		controller  : 'mobile_repairmanController'
	})
	.when('/mobile_repairman/:page/:limit/:sortFiled/:sortType/:search', {
		templateUrl : 'template/mobile_repairman.html',
		controller  : 'mobile_repairmanController'
	})

	.when('/order_mobile/new', {
		templateUrl : 'template/order_mobile_new.html',
		controller  : 'order_mobile_newController'
	})

	.when('/order_mobile/:page/:limit/:sortFiled/:sortType', {
		templateUrl : 'template/order_mobile.html',
		controller  : 'order_mobileController'
	})
	.when('/order_mobile/:page/:limit/:sortFiled/:sortType/:search', {
		templateUrl : 'template/order_mobile.html',
		controller  : 'order_mobileController'
	})
	.when('/order_mobile/:id', {
		templateUrl : 'template/order_mobile_print.html',
		controller  : 'order_mobile_printController'
	})

	.when('/cash/:page/:limit/:sortFiled/:sortType', {
		templateUrl : 'template/cashDeppView.html',
		controller  : 'cashDeppViewController'
	})
	.when('/cash/:page/:limit/:sortFiled/:sortType/:search', {
		templateUrl : 'template/cashDeppView.html',
		controller  : 'cashDeppViewController'
	})
//---------
	.when('/indepp_mobile/:page/:limit/:sortFiled/:sortType', {
		templateUrl : 'template/indepp_mobile.html',
		controller  : 'indepp_mobileController'
	})
	.when('/indepp_mobile/:page/:limit/:sortFiled/:sortType/:search', {
		templateUrl : 'template/indepp_mobile.html',
		controller  : 'indepp_mobileController'
	})
	.when('/indepp_mobile/new', {
		templateUrl : 'template/indepp_mobile_new.html',
		controller  : 'indepp_mobileController'
	})
	.when('/indepp_mobile_serial/new', {
		templateUrl : 'template/indepp_mobile_new_serial.html',
		controller  : 'indepp_mobileController'
	})
	.when('/indepp_mobile/:id', {
		templateUrl : 'template/indepp_print_two_step.html',
		controller  : 'indepp_mobileController'
	})
	.when('/indepp_mobile/edit/:id', {
		templateUrl : 'template/indepp_mobile_edit.html',
		controller  : 'indepp_mobileController'
	})

	.when('/snapshot/', {
		templateUrl : 'template/snapshot.html',
		controller  : 'snapshotController'
	})

	.when('/outdepp_mobile/:page/:limit/:sortFiled/:sortType', {
		templateUrl : 'template/outdepp_mobile.html',
		controller  : 'outdepp_mobileController'
	})
	.when('/outdepp_mobile/:page/:limit/:sortFiled/:sortType/:search', {
		templateUrl : 'template/outdepp_mobile.html',
		controller  : 'outdepp_mobileController'
	})
	.when('/outdepp_mobile/new', {
		templateUrl : 'template/outdepp_mobile_new.html',
		controller  : 'outdepp_mobile_newController'
	})
	.when('/outdepp_mobile/:id', {
		templateUrl : 'template/outdepp_mobile_print.html',
		controller  : 'outdepp_mobileController'
	})
	.when('/outdepp_mobile/:id/partner', {
		templateUrl : 'template/outdepp_mobile_print_partner.html',
		controller  : 'outdepp_mobileController'
	})
	.when('/outdepp_mobile/edit/:id', {
		templateUrl : 'template/outdepp_mobile_edit.html',
		controller  : 'outdepp_mobile_editController'
	})

	.when('/dashboard/',{
		templateUrl : 'template/dashboard.html',
		controller  : 'dashboardController'
	})
	.when('/dashboard/:year/:month/:idDepp',{
		templateUrl : 'template/dashboard.html',
		controller  : 'dashboardController'
	})

	.when('/balance_report/:date/:idDepp',{
		templateUrl : 'template/balance_report.html',
		controller  : 'balance_reportController'
	})
	.when('/balance_report/:date',{
		templateUrl : 'template/balance_report.html',
		controller  : 'balance_reportController'
	})
	.when('/log/:page/:limit/:sortFiled/:sortType', {
		templateUrl : 'template/log.html',
		controller  : 'logController'
	})
	.when('/log/:page/:limit/:sortFiled/:sortType/:search', {
		templateUrl : 'template/log.html',
		controller  : 'logController'
	})

	.when('/accountView/:id/:page/:limit/:sortFiled/:sortType', {
		templateUrl : 'template/accountView.html',
		controller  : 'accountViewController'
	})
	.when('/accountView/:id/:page/:limit/:sortFiled/:sortType/:search', {
		templateUrl : 'template/accountView.html',
		controller  : 'accountViewController'
	})


	.when('/indepp_acc/:page/:limit/:sortFiled/:sortType', {
		templateUrl : 'template/indepp_acc.html',
		controller  : 'indepp_accController'
	})
	.when('/indepp_acc/:page/:limit/:sortFiled/:sortType/:search', {
		templateUrl : 'template/indepp_acc.html',
		controller  : 'indepp_accController'
	})
	.when('/indepp_acc/new', {
		templateUrl : 'template/indepp_acc_new.html',
		controller  : 'indepp_accController'
	})
	.when('/indepp_acc/:id', {
		templateUrl : 'template/indepp_print_two_step.html',
		controller  : 'indepp_accController'
	})
	.when('/indepp_acc/edit/:id', {
		templateUrl : 'template/indepp_acc_edit.html',
		controller  : 'indepp_accController'
	})
	.when('/outdepp_acc/:page/:limit/:sortFiled/:sortType', {
		templateUrl : 'template/outdepp_acc.html',
		controller  : 'outdepp_accController'
	})
	.when('/outdepp_acc/:page/:limit/:sortFiled/:sortType/:search', {
		templateUrl : 'template/outdepp_acc.html',
		controller  : 'outdepp_accController'
	})
	.when('/outdepp_acc/new', {
		templateUrl : 'template/outdepp_acc_new.html',
		controller  : 'outdepp_acc_newController'
	})
	.when('/outdepp_acc/:id', {
		templateUrl : 'template/outdepp_print_two_step.html',
		controller  : 'outdepp_accController'
	})
	.when('/outdepp_acc/edit/:id', {
		templateUrl : 'template/outdepp_acc_edit.html',
		controller  : 'outdepp_acc_editController'
	})

	.when('/range/:page/:limit/:sortFiled/:sortType', {
		templateUrl : 'template/range.html',
		controller  : 'rangeController'
	})
	.when('/range/:page/:limit/:sortFiled/:sortType/:search', {
		templateUrl : 'template/range.html',
		controller  : 'rangeController'
	})

	.when('/productSellReport/', {
		templateUrl : 'template/productSellReport.html',
		controller  : 'productSellReportController'
	})
	.when('/productSellReport/:start/:end/:cat/:brand/:product/:limit/:order/:orderType', {
		templateUrl : 'template/productSellReport.html',
		controller  : 'productSellReportController'
	})

	.when('/debtsReport/', {
		templateUrl : 'template/debtsReport.html',
		controller  : 'debtsReportController'
	})
	.when('/debtsReport/:day/:accountType', {
		templateUrl : 'template/debtsReport.html',
		controller  : 'debtsReportController'
	})

	.when('/debtsReportSimple/', {
		templateUrl : 'template/debtsReportSimple.html',
		controller  : 'debtsReportSimpleController'
	})
	.when('/debtsReportSimple/:day/:accountType', {
		templateUrl : 'template/debtsReportSimple.html',
		controller  : 'debtsReportSimpleController'
	})




	;


})
.config(function ($indexedDBProvider) {
    $indexedDBProvider
      .connection('myIndexedDB')
      .upgradeDatabase(1, function(event, db, tx){
        var objStore = db.createObjectStore('people', {keyPath: 'ssn'});
        objStore.createIndex('name_idx', 'name', {unique: false});
        objStore.createIndex('age_idx', 'age', {unique: false});
      });
  })
;




diakoApp.directive('d', dictio);
function dictio($rootScope,$http) {
	return {
		restrict: 'E',
		replace: true,
		transclude: true,
		scope: '=',
		template: '<k ng-transclude></k>',
		link: function(scope, element, attrs) {
			if(!$rootScope.langSelected){

				// $rootScope.langSelected = 'ku';
				// $rootScope.tr = 'rtl';
				

				$http.get("control/userCtrl.php?action=getLang")
				.then(function(response){
					$rootScope.langSelected = response.data;
					$rootScope.tr = $rootScope.langSelected == 'en'?'ltr':'rtl';
					element[0].textContent = dic(element[0].textContent,$rootScope.langSelected);
				});
			
				// $http.get("control/userCtrl.php?action=getLang")
				// .then(function(response){
				// 	$rootScope.langSelected = response.data;
				// 	element[0].textContent = dic(element[0].textContent,$rootScope.langSelected);
				// });
			}
			else
				element[0].textContent = dic(element[0].textContent,$rootScope.langSelected);
		},
	}
};
function dic(word,lang){
	if(!w[word]){
		console.log('Not Translate For *'+ word+'*');
		return word;
	}
	else
		return w[word][lang];
};

diakoApp.factory('mainFactory', function($http,$location,$rootScope) {
	return {
		checkLogin: function() {
			$http.get("control/userCtrl.php?action=checkLogin")
			.then(function(response){
				if(response.data != 'true')
					$location.path('login');
			});
			// return "Hello, World!";
		},
		getLang: function(){
			$http.get("control/userCtrl.php?action=getLang")
			.then(function(response){
				$rootScope.langSelected = response.data;
				console.log('getLang',response.data);
			});
		}

	};
});


diakoApp.directive('pagination', pagination);
function pagination($rootScope,$location) {
	return {
		restrict: 'E',
		replace: true,
		scope: '=',
		transclude: true,
		// templateUrl: '<button ng-click="tClick()">directiveBTN</button>',
		templateUrl: 'template/pagination.html',
		controller: function ($scope,$routeParams) {
			this.page = 25;
			$scope.tClick = function(){
				console.log('test worked',$routeParams,$rootScope.page);
			}
			$scope.updateLimit = function(v){
				var str = '';
				if($routeParams.search != undefined)
					str = $routeParams.search;
				$location.path($scope.part+'/'+$scope.page+'/'+v+'/'+$scope.sortFiled+'/'+$scope.sortType+'/'+str);
			}
			$scope.goPage = function(page){
				var str = '';
				if($routeParams.search != undefined)
					str = $routeParams.search;
				$location.path($scope.part+'/'+page+'/'+$scope.limit+'/'+$scope.sortFiled+'/'+$scope.sortType+'/'+str);
			}
		},
	}
};


//console code in future active by Ctrl+Shift+Alt+c
/*
$(document).keydown(function (e) {
    console.log(e.keyCode,e.altKey,e.ctrlKey,e.shiftKey);
});
*/












