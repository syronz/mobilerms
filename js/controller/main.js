diakoApp.controller('mainCtrl', function($scope,$rootScope,$location,$http,mainFactory,$routeParams) {
	$scope.toggle = true;
	$scope.toggleSidebar = function() {
		$scope.toggle = !$scope.toggle;
	};
	

	// mainFactory.checkLogin();
	// $rootScope.userDeppAuthSelect = {id : 6}; // must be deleted
	$http.get("control/userCtrl.php?action=authUserPart").
	success(function(data, status, headers, config) {
		$rootScope.userDeppAuth = data.departments;
		$rootScope.userDeppAuthSelect = data.departments[data.idDepp];
	}).
	error(function(data, status, headers, config) {
		$log.info(data);
	});

	$rootScope.updateSelectedDepp = function(depp){
		$rootScope.userDeppAuthSelect = depp;
		$http.get("control/userCtrl.php?action=updateAuthUserPart&id="+depp.id).
		success(function(data, status, headers, config) {
			dsh(data);
		}).
		error(function(data, status, headers, config) {
			dsh(data);
		});
		// dsh(depp);
		$location.path('dashboard');
	}

	$rootScope.getSelectedDepp = function(){
		if($rootScope.userDeppAuthSelect != undefined)
			return $rootScope.userDeppAuthSelect;
		else{
			$http.get("control/userCtrl.php?action=authUserPart").
			success(function(data, status, headers, config) {
				return data[0];
			}).
			error(function(data, status, headers, config) {
				$log.info(data);
			});
		}
	}


	$scope.doPrint = function(){
		// dsh($routeParams);
		var idRow = $routeParams.id ? $routeParams.id : 0;
		if(!$rootScope.specialPrint){
			var printDivCSS = new String ("<style>.noPrint,h6,.img{display:none}h2,h3,h5{text-align:center}*{font-family:'Unikurd Xani'}table{border-collapse:collapse;border:1px solid gray;width:100%;margin:10px auto}.facture_footer,.facture_header{background-image:url();background-repeat:no-repeat;width:100%}td{border:1px dotted gray;text-align:right;padding-right:3px;font-size:11pt;font-family:arial;padding-top:1px;padding-bottom:1px}tr:nth-child(even){background-color:#EEE}h5{margin-top:30px;margin-bottom:5px}.clear{clear:both}h2,h3{font-size:15pt}.facture_header{height:150px;background-size:650px 140px}.facture_footer{height:100px;background-size:650px 92px}th{font-size:12pt;height:150%;background-color:#DDD}a{text-decoration:none}tr td:last-child,tr th:last-child{display:none;}</style>");
			// var styles = '<style> td{text-decoration:none;} </style>+'<link href="css/printStyle.css" rel="stylesheet">'';
			window.frames["print_frame"].document.body.innerHTML= printDivCSS + document.getElementById("printThis").innerHTML;
			window.frames["print_frame"].window.focus();
			window.frames["print_frame"].window.print();

			$http.post('control/logCtrl.php?action=logPrint',{data:'<meta charset="utf-8"> '+printDivCSS+document.getElementById('printThis').innerHTML,part:$rootScope.part,id:idRow}).
			success(function(data){
				dsh(data);
			}).
			error(function(data){
				dsh(data);
			})
		}
		else{
			window.frames["print_frame"].document.body.innerHTML= "<style>.noPrint{display:none}</style>" + document.getElementById("printThis").innerHTML;
			window.frames["print_frame"].window.focus();
			window.frames["print_frame"].window.print();

			$http.post('control/logCtrl.php?action=logPrint',{data:'<meta charset="utf-8"> <style>.noPrint{display:none}</style>'+document.getElementById('printThis').innerHTML,part:$rootScope.part,id:idRow}).
			success(function(data){
				dsh(data);
			}).
			error(function(data){
				dsh(data);
			})
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