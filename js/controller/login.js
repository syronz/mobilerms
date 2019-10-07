diakoApp.controller('loginController', function($scope, $rootScope,$http,$location) {
	$rootScope.page = 'Login';

	$scope.firstVisit = true;

	// if(!$rootScope.langSelected)
	// 	$rootScope.langSelected = 'en';

	// $scope.l = function(word){
	// 	console.log(word,dic.w);
	// 	return word;
	// 	return dic.w[word][$rootScope.langSelected];
	// };

	$scope.loginInfo = {username:'',password:''};
	$scope.doLogin = function(){
		$rootScope.loading = true;
		$scope.lockForm = true;
		$http.post("control/userCtrl.php?action=login",$scope.loginInfo)
		.then(function(response){
			dsh(response);
			if(response.data.result){
				$http.get("control/userCtrl.php?action=authUserPart").
				success(function(data, status, headers, config) {
					$rootScope.userDeppAuth = data.departments;
					$rootScope.userDeppAuthSelect = data.departments[data.idDepp];
				}).
				error(function(data, status, headers, config) {
					$log.info(data);
				});

				
				$rootScope.langSelected = response.data.lang;
				if($rootScope.langSelected == 'ku')
					$rootScope.tr = 'rtl';
				else
					$rootScope.tr = 'ltr';

				$location.path("dashboard");
			}
			else
				$scope.warning = response.data.message.content;

			$rootScope.loading = false;
			$scope.lockForm = false;
		});
	}

	$scope.doLogout = function(){
		$http.get("control/userCtrl.php?action=logout")
		.then(function(response){
			console.log(response);
		});
	}

});