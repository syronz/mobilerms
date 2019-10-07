diakoApp.controller('cardController',function($scope,$rootScope,$http,$log,$location,$routeParams,mainFactory){

	mainFactory.checkLogin();
	$rootScope.part = 'card';

	$scope.account = {
		card_number : '5338-8826-2815',
		password : '123456789',
		idAccount:$routeParams.idAccount
	};

	$scope.success = false;

	$scope.sendCard = function(){
		$http.post("control/cardCtrl.php?action=add",$scope.account).
		success(function(data, status, headers, config) {
			if(data.result){
				$scope.success = true;
				$scope.getAccountCard();
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


	$scope.cardList = null;
	$scope.getAccountCard = function(){
		$http.get("control/cardCtrl.php?action=cardList&idAccount="+$routeParams.idAccount).
		success(function(data, status, headers, config) {
			if(data.result){
				$scope.cardList = data.rows;
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
	$scope.getAccountCard();


	$scope.addCredit = function(){
		$rootScope.loading = true;
		$scope.lockForm = true;
		$http.post("control/cardCtrl.php?action=addCredit",$scope.account).
		success(function(data, status, headers, config) {
			if(data.result){
				$scope.success = true;
				$scope.successMessage = data.message;
				$scope.errorMessage = '';
				$scope.getAccountCard();
			}
			else{
				dsh(data);
				$scope.errorMessage = data.message;
			}
			$rootScope.loading = false;
			$scope.lockForm = false;
		}).
		error(function(data, status, headers, config) {
			$log.info('error',data, status, headers, config);
		});
	}

	$scope.toggle = function(){
		$scope.success = !$scope.success;
		$scope.account.credit = '';
	}

	

});
