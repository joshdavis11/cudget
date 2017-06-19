angular.module('IncomeDirectives', [])
.directive('incomeCategoryChooser', ['IncomeCategoryService', '$http', '$animate',
	function(IncomeCategoryService, $http, $animate) {
		return {
			restrict: 'ACE',
			template: require('../../../pug/views/income/directives/income-category-chooser.pug'),
			scope: {
				required: '=iccRequired',
				ngModel: '=',
				name: '@iccName'
			},
			link: function($scope, element) {
				$scope.hasError = false;
				$animate.enabled(false, element);

				if (!$scope.$parent.IncomeCategories) {
					IncomeCategoryService.getIncomeCategories().then(function(IncomeCategories) {
						$scope.IncomeCategories = IncomeCategories;
					});
				} else {
					$scope.IncomeCategories = $scope.$parent.IncomeCategories;
				}

				$scope.save = function() {
					if ($scope.newCategory) {
						$scope.hasError = false;
						$http.post('/api/v1/income/categories', {'name': $scope.newCategory})
							.success(function(IncomeCategory) {
								IncomeCategoryService.getIncomeCategories().then(function(IncomeCategories) {
									$scope.$parent.IncomeCategories = $scope.IncomeCategories = IncomeCategories;
									$scope.ngModel = IncomeCategory.id;
								});
							});
					} else {
						$scope.hasError = true;
					}
				}
			}
		};
	}
])
;