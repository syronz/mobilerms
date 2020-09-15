diakoApp.directive('indeppBodyTwoStepSerial', function(){
	return{
		templateUrl:"template/directive/indeppBodyTwoStepSerial.html",
		restrict:"E",
		replace:true,
		scope:false,
		controller: function($scope,$http){
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

			$scope.productList = null;
			$scope.updateProductList = function(item){
				$http.get("control/productCtrl.php?action=productListByBrand&idBrand="+item.id_brand.id).
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
				item.price_buy = item.id_product.price_buy;

				$scope.getDollarRate();
			}

			// $scope.codeChange = function(e,item){
			// 	if(e.keyCode == 13){
			// 		console.log('itemcode',item);
			// 		if(item.code){
			// 			$http.get("control/productCtrl.php?action=productBycode&code="+item.code).
			// 			success(function(data, status, headers, config) {
			// 				item.id_brand = {"id":data.id_brand,"name":data.brand};

			// 				item.modelList = [{"id":data.id_model,"name":data.model}];
			// 				item.id_model = {"id":data.id_model,"name":data.model};

			// 				item.productList = [{"id":data.id_product,"name":data.product}];
			// 				item.id_product = {"id":data.id_product,"name":data.product};

			// 				item.cost = data.cost;
			// 				item.price_buy = data.price_buy;
			// 			}).
			// 			error(function(data, status, headers, config) {
			// 				$log.info(data);
			// 			});
			// 		}
			// 	}
			// }

			$scope.addItem = function() {
				$scope.invoice.items.push({qty:1, cost:0, description:"",id_brand:0,id_model:0});    
			}

			$scope.removeItem = function(item) {
				$scope.invoice.items.splice($scope.invoice.items.indexOf(item), 1);    
			}

			$scope.invoice_sub_total = function() {
				var total = 0.00;
				angular.forEach($scope.invoice.items, function(item, key){
					total += (item.qty * item.cost + item.qty * item.toll);
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

			$scope.clearLocalStorage = function(){
				var confirmClear = confirm("Are you sure you would like to clear the invoice?");
				if(confirmClear){
					localStorage["invoice"] = "";
					$scope.invoice = sample_invoice;
				}
			}
		}//end of controller
	}
});
