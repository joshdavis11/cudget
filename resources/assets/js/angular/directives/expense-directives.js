angular.module('ExpenseDirectives', [])
.directive('expenseCategoryChooser', ['ExpenseCategoryService', '$http', '$animate',
	function(ExpenseCategoryService, $http, $animate) {
		return {
			restrict: 'ACE',
			template: require('../../../pug/views/expenses/directives/expense-category-chooser.pug'),
			scope: {
				required: '=iccRequired',
				ngModel: '=',
				name: '@iccName'
			},
			link: function($scope, element) {
				$scope.hasError = false;
				$animate.enabled(false, element);

				if (!$scope.$parent.ExpenseCategories) {
					ExpenseCategoryService.getExpenseCategories().then(function(ExpenseCategories) {
						$scope.ExpenseCategories = ExpenseCategories;
					});
				} else {
					$scope.ExpenseCategories = $scope.$parent.ExpenseCategories;
				}

				$scope.save = function() {
					if ($scope.newCategory) {
						$scope.hasError = false;
						$http.post('/api/v1/expenses/categories', {'name': $scope.newCategory})
							.success(function(ExpenseCategory) {
								ExpenseCategoryService.getExpenseCategories().then(function(ExpenseCategories) {
									$scope.$parent.ExpenseCategories = $scope.ExpenseCategories = ExpenseCategories;
									$scope.ngModel = ExpenseCategory.id;
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