function dsh(){
	for(a in arguments)
		console.log(arguments[a]);
}

var crmApp = angular.module('crmApp', ['ngRoute']);

crmApp.config(function($routeProvider) {
	$routeProvider
	.when('/:lang', {
		templateUrl : 'page/home.html',
		controller  : 'profileController'
	})
	.when('/profile/:lang', {
		templateUrl : 'page/profile.html',
		controller  : 'profileController'
	})
	.when('/credit/:lang', {
		templateUrl : 'page/credit.html',
		controller  : 'creditController'
	})


	.when('/tran/:page/:limit/:sortFiled/:sortType/:lang', {
		templateUrl : 'page/tran.html',
		controller  : 'tranController'
	})
	.when('/tran/:page/:limit/:sortFiled/:sortType/:search/:lang', {
		templateUrl : 'page/tran.html',
		controller  : 'tranController'
	})

	.otherwise({
		redirectTo: '/ku'
	});


	;


})

;

crmApp.controller('mainCtrl',function($scope,$rootScope,$http,$log,$location,$routeParams,mainFactory){

	$http.get("erp/control/cardCtrl.php?action=getCustomerInfo").
	success(function(data, status, headers, config) {
		$rootScope.customerName = data.account.name;
	}).
	error(function(data, status, headers, config) {
		$log.info(data);
	});

	$scope.setLang = function(l){
		dsh(l);
		if(l == 'ku'){
			$rootScope.langSelected = 'ku';
			$rootScope.tr = 'rtl';
		}
		else{
			$rootScope.langSelected = 'en';
			$rootScope.tr = 'ltr';
		}
		$location.path($location.path().substring(0,$location.path().length - 2) + $rootScope.langSelected);
	}

	// $rootScope.go = function(w){
		
	// }
	

	mainFactory.checkLogin();
	$scope.doPrint = function(){
		if(1){
			var printDivCSS = new String ("<style>.noPrint,h6,.img{display:none}h2,h3,h5{text-align:center}*{font-family:'Unikurd Xani'}table{border-collapse:collapse;border:1px solid gray;width:80%;margin:10px auto}.facture_footer,.facture_header{background-image:url();background-repeat:no-repeat;width:100%}td{border:1px dotted gray;text-align:right;padding-right:3px;font-size:9pt;font-family:arial;padding-top:1px;padding-bottom:1px}tr:nth-child(even){background-color:#EEE}h5{margin-top:30px;margin-bottom:5px}.clear{clear:both}h2,h3{font-size:15px}.facture_header{height:150px;background-size:650px 140px}.facture_footer{height:100px;background-size:650px 92px}th{height:150%;background-color:#DDD}a{text-decoration:none}tr td:last-child,tr th:last-child{display:none;}</style>");
				// var styles = '<style> td{text-decoration:none;} </style>+'<link href="css/printStyle.css" rel="stylesheet">'';
				window.frames["print_frame"].document.body.innerHTML= printDivCSS + document.getElementById("printThis").innerHTML;
				window.frames["print_frame"].window.focus();
				window.frames["print_frame"].window.print();
			}
			else{
				window.frames["print_frame"].document.body.innerHTML= document.getElementById("printThis").innerHTML;
				window.frames["print_frame"].window.focus();
				window.frames["print_frame"].window.print();
			}
		}

		$scope.doSearch = function(){
			var str = '';
			if($scope.searchStr != undefined)
				str = $scope.searchStr;
			$location.path($rootScope.part+'/'+$routeParams.page+'/'+$routeParams.limit+'/'+$routeParams.sortFiled+'/'+$routeParams.sortType+'/'+str);
		}


		// $scope.doLogout = function(){
		// 	$http.get("control.php?action=logout")
		// 	.then(function(response){
		// 		console.log('doLogout',response);
		// 	});
		// }

		$scope.dic = function(word){
			return dic(word,$rootScope.langSelected);
		}

		$(document).keydown(function (e) {
			// console.log(e.keyCode,e.altKey,e.ctrlKey,e.shiftKey);
			if(e.keyCode == 80 && e.altKey)
				$scope.doPrint();
			if(e.keyCode == 76 && e.altKey)
				$scope.toExcel();
		});

		$scope.toExcel = function(){
			dsh($routeParams,$rootScope.part,window.location);
			var allowedParts = ['user','department','highschool','newOld','student','perm','college'];

			dsh(allowedParts.indexOf($rootScope.part));

			if(allowedParts.indexOf($rootScope.part) > -1){
				var str = '';
				if($routeParams.search != undefined)
					str = $routeParams.search;

				window.location = window.location.origin + window.location.pathname + 'toExcel.php?part='+
				$rootScope.part+'&search='+str+'&sortFiled='+$routeParams.sortFiled+
				'&sortType='+$routeParams.sortType;
			}
			else
				alert(dic('Nothing_to_export',$rootScope.langSelected));
		}
	});
//end mainCtrl

crmApp.controller('profileController',function($scope,$rootScope,$http,$log,$location,$routeParams,mainFactory){

	$http.get("erp/control/cardCtrl.php?action=getCustomerInfo").
	success(function(data, status, headers, config) {
		$scope.customer = data;
		$rootScope.customerName = data.account.name;
		dsh(data)
	}).
	error(function(data, status, headers, config) {
		$log.info(data);
	});

});



crmApp.controller('tranController',function($scope,$rootScope,$http,$log,$location,$routeParams,mainFactory){
	mainFactory.checkLogin();

	$scope.tranList = null;
	$rootScope.part = 'tran';

	$scope.sortFiled = $routeParams.sortFiled;
	$scope.sortType = $routeParams.sortType;
	var order = $scope.sortFiled+' '+$scope.sortType;

	$scope.page = parseInt($routeParams.page);
	$scope.limit = parseInt($routeParams.limit);
	var limitPage = (($scope.page - 1) * $scope.limit)+','+$scope.limit;

	var str = '';
	if($routeParams.search != undefined)
		str = $routeParams.search;
	
	$scope.list = function(){
		$rootScope.loading = true;
		$http.get("erp/control/cardCtrl.php?action=tranListCrm&order="+order+"&limit="+limitPage+"&search="+str).
		success(function(data, status, headers, config) {
			$scope.tranList = data.rows;
			$scope.count = data.count;
			$rootScope.loading = false;
		}).
		error(function(data, status, headers, config) {
			$log.info(data);
		});
	}
	$scope.list();

	$scope.sorting = function(sort){
		dsh(sort);
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

});



crmApp.controller('creditController',function($scope,$rootScope,$http,$log,$location,$routeParams,mainFactory){
	$scope.part = dic('Credit',$rootScope.langSelected);
	$scope.addCredit = function(){
		$http.post("erp/control/cardCtrl.php?action=addCreditCrm",$scope.credit).
		success(function(data, status, headers, config) {
			if(data.result){
				$scope.success = true;
				$scope.successMessage = data.message;
				$scope.errorMessage = '';
			}
			else{
				dsh(data);
				$scope.errorMessage = data.message;
			}
		}).
		error(function(data, status, headers, config) {
			$log.info('error',data, status, headers, config);
		});
	}

	$scope.toggle = function(){
		$scope.success = !$scope.success;
		$scope.credit.credit = '';
	}
});


crmApp.directive('d', dictio);
function dictio($rootScope,$http) {
	return {
		restrict: 'E',
		replace: true,
		transclude: true,
		scope: '=',
		template: '<k ng-transclude></k>',
		link: function(scope, element, attrs) {
			if(!$rootScope.langSelected){
				$rootScope.langSelected = 'ku';
				$rootScope.tr = 'rtl';
				element[0].textContent = dic(element[0].textContent,$rootScope.langSelected);

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

crmApp.factory('mainFactory', function($http,$location,$rootScope) {
	return {
		checkLogin: function() {
			$http.get("erp/control/cardCtrl.php?action=checkCardLogin")
			.then(function(response){
				dsh(response)
				if(response.data != 'true'){
					// $location.path('login');
					window.location = window.location.origin + '/hi/login.html';
				}
			});
			// return "Hello, World!";
		},
		getLang: function(){
			$http.get("erp/control/cardCtrl.php?action=getLang")
			.then(function(response){
				$rootScope.langSelected = response.data;
				console.log('getLang',response.data);
			});
		}

	};
});


crmApp.directive('pagination', pagination);
function pagination($rootScope,$location) {
	return {
		restrict: 'E',
		replace: true,
		scope: '=',
		transclude: true,
		// templateUrl: '<button ng-click="tClick()">directiveBTN</button>',
		templateUrl: 'erp/template/pagination.html',
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












