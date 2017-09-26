angular.module('ExpenseControllers', [])
.controller('ExpenseController', ['Expenses', 'ExpenseCategories', '$scope', 'TitleService', 'ExpenseService', 'ExpenseCategoryService', 'filterFilter', '$uibModal', '$location', '$routeParams',
	function(Expenses, ExpenseCategories, $scope, TitleService, ExpenseService, ExpenseCategoryService, filterFilter, $uibModal, $location, $routeParams) {
		TitleService.setTitle('Expenses');
		$scope.filterVal = $routeParams.filter || '';

		var OriginalExpenses = Expenses;
		$scope.Expenses = angular.copy(OriginalExpenses);
		$scope.currentPage = parseInt($routeParams.page) || 1;
		$scope.ExpenseCategories = ExpenseCategories;

		$scope.filter = function() {
			switch ($scope.filterVal) {
				case 'with':
					$scope.Expenses = filterFilter(OriginalExpenses, function(Expense) {
						return Expense.budgetCategoryRowExpense && Expense.budgetCategoryRowExpense.id;
					});
					$location.search('filter', 'with');
					break;
				case 'without':
					$scope.Expenses = filterFilter(OriginalExpenses, function(Expense) {
						return !(Expense.budgetCategoryRowExpense && Expense.budgetCategoryRowExpense.id);
					});
					$location.search('filter', 'without');
					break;
				default:
					$scope.Expenses = OriginalExpenses;
					$location.search('filter', null);
					break;
			}
		};
		$scope.filter();

		$scope.editExpense = function(Expense) {
			var OriginalExpense = angular.copy(Expense);
			var modalInstance = $uibModal.open({
				template: require('../../../pug/views/expenses/modals/expense.pug'),
				controller: 'ExpenseEditController',
				scope: $scope,
				resolve: {
					Expense: Expense
				}
			});

			modalInstance.result.then(function() {
				if (!Expense.expenseCategoryId) {
					Expense.expenseCategory = null;
				}
				else {
					angular.forEach($scope.ExpenseCategories, function(ExpenseCategory) {
						if (ExpenseCategory.id === Expense.expenseCategoryId) {
							Expense.expenseCategory = ExpenseCategory;
							return false;
						}
					});
				}
			}, function() {
				angular.copy(OriginalExpense, Expense);
			});
		};

		$scope.deleteExpense = function(Expense) {
			var modalInstance = $uibModal.open({
				template: require('../../../pug/views/expenses/modals/expense-delete.pug'),
				controller: 'ExpenseDeleteController',
				scope: $scope,
				resolve: {
					Expense: Expense
				}
			});

			modalInstance.result.then(function(expenseId) {
				//setMessage()
				var index = $scope.Expenses.map(function(Expense) {
					return Expense.id;
				}).indexOf(expenseId);
				$scope.Expenses.splice(index, 1);

				var index2 = OriginalExpenses.map(function(Expense) {
					return Expense.id;
				}).indexOf(expenseId);
				OriginalExpenses.splice(index2, 1);
			});
		};

		$scope.addToBudget = function(Expense) {
			var modalInstance = $uibModal.open({
				template: require('../../../pug/views/modals/add-to-budget.pug'),
				controller: 'AddExpenseToBudgetController',
				scope: $scope,
				resolve: {
					Expense: Expense
				}
			});

			modalInstance.result.then(function(budgetCategoryRowId) {
				Expense.budgetCategoryRowExpense = {
					id: budgetCategoryRowId
				};

				var index = $scope.Expenses.map(function(OldExpense) {
					return OldExpense.id;
				}).indexOf(Expense.id);
				$scope.Expenses.splice(index, 1);
			});
		};
	}
])
.controller('ExpenseDeleteController', ['$scope', '$uibModalInstance', 'Expense', 'ExpenseService',
	function($scope, $uibModalInstance, Expense, ExpenseService) {
		$scope.description = Expense.description;

		$scope.close = $scope.cancel = function() {
			$uibModalInstance.dismiss();
		};

		$scope.delete = function() {
			ExpenseService.delete(Expense.id, { ignoreLoadingBar: true }).then(function() {
				$uibModalInstance.close(Expense.id);
			});
		};
	}
])
.controller('ExpenseEditController', ['$scope', 'Expense', 'ExpenseService', 'DateService', '$uibModalInstance',
	function($scope, Expense, ExpenseService, DateService, $uibModalInstance) {
		$scope.Expense = Expense;
		$scope.Expense.datetime = DateService.getDate($scope.Expense.datetime);
		$scope.showFooterButtons = false;

		$scope.close = $scope.cancel = function() {
			$uibModalInstance.dismiss();
		};

		$scope.submit = function() {
			ExpenseService.update($scope.Expense, { ignoreLoadingBar: true }).then(function() {
				$uibModalInstance.close();
			});
		};
	}
])
.controller('ExpenseCreateController', ['$scope', 'ExpenseService', '$location',
	function($scope, ExpenseService, $location) {
		$scope.Expense = {};
		$scope.showFooterButtons = true;
		$scope.isOpenExpenseDate = false;

		$scope.submit = function() {
			ExpenseService.create($scope.Expense, { ignoreLoadingBar: true }).then(function() {
				$location.path('/expenses');
			});
		};
	}
])
.controller('AddExpenseToBudgetController', ['$scope', 'Expense', 'ExpenseService', 'BudgetService', 'CategoryService', 'CategoryRowService', '$uibModalInstance',
	function($scope, Expense, ExpenseService, BudgetService, CategoryService, CategoryRowService, $uibModalInstance) {
		$scope.title = Expense.description;
		$scope.Budgets = [];
		$scope.BudgetCategories = [];
		$scope.BudgetCategoryRows = [];
		$scope.showBudgetCategories = false;
		$scope.showBudgetCategoryRows = false;
		$scope.expense = true;
		$scope.form = {
			budgetId: 0,
			budgetCategoryId: 0,
			budgetCategoryRowId: 0
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

		$scope.budgetCategoryChosen = function() {
			if ($scope.form.budgetCategoryId) {
				CategoryRowService.getCategoryRowsForBudgetCategory($scope.form.budgetCategoryId, { ignoreLoadingBar: true }).then(function(BudgetCategoryRows) {
					$scope.BudgetCategoryRows = BudgetCategoryRows;
					$scope.showBudgetCategoryRows = true;
				});
			} else {
				$scope.showBudgetCategoryRows = false;
			}
		};

		$scope.close = $scope.cancel = function() {
			$uibModalInstance.dismiss();
		};

		$scope.submit = function() {
			ExpenseService.addToBudget(Expense.id, $scope.form.budgetCategoryRowId, { ignoreLoadingBar: true }).then(function() {
				$uibModalInstance.close($scope.form.budgetCategoryRowId);
			});
		};
	}
])
;