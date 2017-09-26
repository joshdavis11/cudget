angular.module('IncomeControllers', [])
.controller('IncomeController', ['Incomes', 'IncomeCategories', '$scope', 'TitleService', 'IncomeService', 'IncomeCategoryService', 'filterFilter', '$uibModal', '$location', '$routeParams',
	function(Incomes, IncomeCategories, $scope, TitleService, IncomeService, IncomeCategoryService, filterFilter, $uibModal, $location, $routeParams) {
		TitleService.setTitle('Income');
		$scope.filterVal = $routeParams.filter || '';

		var OriginalIncomes = Incomes;
		$scope.Incomes = angular.copy(OriginalIncomes);
		$scope.currentPage = parseInt($routeParams.page) || 1;
		$scope.IncomeCategories = IncomeCategories;

		$scope.filter = function() {
			switch ($scope.filterVal) {
				case 'with':
					$scope.Incomes = filterFilter(OriginalIncomes, function(Income) {
						return Income.budgetIncome && Income.budgetIncome.id;
					});
					$location.search('filter', 'with');
					break;
				case 'without':
					$scope.Incomes = filterFilter(OriginalIncomes, function(Income) {
						return !(Income.budgetIncome && Income.budgetIncome.id);
					});
					$location.search('filter', 'without');
					break;
				default:
					$scope.Incomes = angular.copy(OriginalIncomes);
					$location.search('filter', null);
					break;
			}
		};
		$scope.filter();

		$scope.editIncome = function(Income) {
			var OriginalIncome = angular.copy(Income);
			var modalInstance = $uibModal.open({
				template: require('../../../pug/views/income/modals/income.pug'),
				controller: 'IncomeEditController',
				scope: $scope,
				resolve: {
					Income: Income
				}
			});

			modalInstance.result.then(function() {
				if (!Income.incomeCategoryId) {
					Income.incomeCategory = null;
				}
				else {
					angular.forEach($scope.IncomeCategories, function(IncomeCategory) {
						if (IncomeCategory.id === Income.incomeCategoryId) {
							Income.incomeCategory = IncomeCategory;
							return false;
						}
					});
				}
			}, function() {
				angular.copy(OriginalIncome, Income);
			});
		};

		$scope.deleteIncome = function(Income) {
			var modalInstance = $uibModal.open({
				template: require('../../../pug/views/income/modals/income-delete.pug'),
				controller: 'IncomeDeleteController',
				scope: $scope,
				resolve: {
					Income: Income
				}
			});

			modalInstance.result.then(function(incomeId) {
				//setMessage()
				var index = $scope.Incomes.map(function(Income) {
					return Income.id;
				}).indexOf(incomeId);
				$scope.Incomes.splice(index, 1);

				var index2 = OriginalIncomes.map(function(Income) {
					return Income.id;
				}).indexOf(incomeId);
				OriginalIncomes.splice(index2, 1);
			});
		};

		$scope.addToBudget = function(Income) {
			var modalInstance = $uibModal.open({
				template: require('../../../pug/views/modals/add-to-budget.pug'),
				controller: 'AddIncomeToBudgetController',
				scope: $scope,
				resolve: {
					Income: Income
				}
			});

			modalInstance.result.then(function(BudgetIncome) {
				Income.budgetIncome = BudgetIncome;

				var index = $scope.Incomes.map(function(OldIncome) {
					return OldIncome.id;
				}).indexOf(Income.id);
				$scope.Incomes.splice(index, 1);
			});
		};
	}
])
.controller('IncomeDeleteController', ['$scope', '$uibModalInstance', 'Income', 'IncomeService',
	function($scope, $uibModalInstance, Income, IncomeService) {
		$scope.description = Income.description;

		$scope.close = $scope.cancel = function() {
			$uibModalInstance.dismiss();
		};

		$scope.delete = function() {
			IncomeService.delete(Income.id, { ignoreLoadingBar: true }).then(function() {
				$uibModalInstance.close(Income.id);
			});
		};
	}
])
.controller('IncomeEditController', ['$scope', 'Income', 'IncomeService', 'DateService', '$uibModalInstance',
	function($scope, Income, IncomeService, DateService, $uibModalInstance) {
		$scope.Income = Income;
		$scope.Income.datetime = DateService.getDate($scope.Income.datetime);
		$scope.showFooterButtons = false;

		$scope.close = $scope.cancel = function() {
			$uibModalInstance.dismiss();
		};

		$scope.submit = function() {
			IncomeService.update($scope.Income, { ignoreLoadingBar: true }).then(function() {
				$uibModalInstance.close();
			});
		};
	}
])
.controller('IncomeCreateController', ['$scope', 'IncomeService', '$location',
	function($scope, IncomeService, $location) {
		$scope.Income = {};
		$scope.showFooterButtons = true;
		$scope.isOpenIncomeDate = false;

		$scope.submit = function() {
			IncomeService.create($scope.Income, { ignoreLoadingBar: true }).then(function() {
				$location.path('/income');
			});
		};
	}
])
.controller('AddIncomeToBudgetController', ['$scope', 'Income', 'IncomeService', 'BudgetService', 'CategoryService', 'CategoryRowService', '$uibModalInstance',
	function($scope, Income, IncomeService, BudgetService, CategoryService, CategoryRowService, $uibModalInstance) {
		$scope.title = Income.description;
		$scope.Budgets = [];
		$scope.BudgetCategories = [];
		$scope.BudgetCategoryRows = [];
		$scope.showBudgetCategories = false;
		$scope.showBudgetCategoryRows = false;
		$scope.expense = false;
		$scope.form = {
			budgetId: 0
		};

		BudgetService.getBudgets({ ignoreLoadingBar: true }).then(function(Budgets) {
			$scope.Budgets = Budgets;
		});

		$scope.budgetChosen = function() {
			if ($scope.form.budgetId) {
				CategoryService.getCategoriesForBudget($scope.form.budgetId, { ignoreLoadingBar: true }).then(function(BudgetCategories) {
					$scope.BudgetCategories = BudgetCategories;
					$scope.showBudgetCategories = true;
				});
			} else {
				$scope.showBudgetCategories = false;
			}
		};

		$scope.close = $scope.cancel = function() {
			$uibModalInstance.dismiss();
		};

		$scope.submit = function() {
			IncomeService.addToBudget(Income.id, $scope.form.budgetId, { ignoreLoadingBar: true }).then(function(BudgetIncome) {
				$uibModalInstance.close(BudgetIncome);
			});
		};
	}
])
;