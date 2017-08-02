angular.module('BudgetDirectives', [])
.directive('budgetCategory', ['BudgetService', 'CategoryRowService', '$uibModal', 'orderByFilter',
	function(BudgetService, CategoryRowService, $uibModal, orderBy) {
		return {
			restrict: 'A',
			template: require('../../../pug/views/budgets/directives/budget-category.pug'),
			link: function($scope) {
				$scope.BudgetCategory.budgetCategoryRows = orderBy($scope.BudgetCategory.budgetCategoryRows, 'sortOrder', false);

				$scope.editCategory = function(BudgetCategory) {
					var OriginalBudgetCategory = angular.copy(BudgetCategory);
					var modalInstance = $uibModal.open({
						template: require('../../../pug/views/budgets/modals/category.pug'),
						controller: 'CategoryEditController',
						scope: $scope,
						resolve: {
							BudgetCategory: BudgetCategory
						}
					});

					modalInstance.result.then(function() {
						//setMessage()
						BudgetService.setData($scope.Budget);
					}, function() {
						angular.copy(OriginalBudgetCategory, BudgetCategory);
					});
				};

				$scope.deleteCategory = function(BudgetCategory) {
					var modalInstance = $uibModal.open({
						template: require('../../../pug/views/budgets/modals/category-delete.pug'),
						controller: 'CategoryDeleteController',
						scope: $scope,
						resolve: {
							BudgetCategory: BudgetCategory
						}
					});

					modalInstance.result.then(function(BudgetCategory) {
						//setMessage()
						var index = $scope.Budget.budgetCategories.map(function(ExistingBudgetCategory) {
							return ExistingBudgetCategory.id;
						}).indexOf(BudgetCategory.id);
						$scope.Budget.budgetCategories.splice(index, 1);
						BudgetService.setData($scope.Budget);
					});
				};

				$scope.addCategoryItem = function() {
					var modalInstance = $uibModal.open({
						template: require('../../../pug/views/budgets/modals/category-item.pug'),
						controller: 'CategoryRowCreateController',
						scope: $scope,
						resolve: {
							budgetCategoryId: $scope.BudgetCategory.id
						}
					});

					modalInstance.result.then(function(BudgetCategoryRow) {
						//setMessage()
						BudgetCategoryRow.budgetCategoryRowExpenses = [];
						$scope.BudgetCategory.budgetCategoryRows.push(BudgetCategoryRow);
						BudgetService.setData($scope.Budget);
					});
				};

				$scope.categoryRowSorting = {
					disabled: $scope.BudgetCategory.name === 'Uncategorized',
					connectWith: [".js-sortable-rows"],
					placeholder: "list-group-item border-dashed",
					forcePlaceholderSize: true,
					dropOnEmpty: true,
					receive: function(event, ui) {
						ui.item.sortable.moved.budgetCategoryId = $scope.BudgetCategory.id;
					},
					stop: function(event, ui) {
						angular.forEach(ui.item.sortable.sourceModel, updateBudgetCategoryRowSorting);
						angular.forEach(ui.item.sortable.droptargetModel, updateBudgetCategoryRowSorting);

						function updateBudgetCategoryRowSorting(BudgetCategoryRow, index) {
							BudgetCategoryRow.sortOrder = index;
							CategoryRowService.update(BudgetCategoryRow, { ignoreLoadingBar: true })
								.error(function() {
									console.error('Budget Category Row didn\'t save new sorting correctly');
								});
						}
						BudgetService.setData($scope.Budget);
					}
				};
			}
		};
	}
])
.directive('budgetCategoryItem', ['BudgetService', '$uibModal', 'CategoryRowExpenseService',
	function(BudgetService, $uibModal, CategoryRowExpenseService) {
		return {
			restrict: 'A',
			template: require('../../../pug/views/budgets/directives/budget-category-item.pug'),
			link: function($scope) {
				$scope.editCategoryItem = function(BudgetCategoryRow) {
					var OriginalBudgetCategoryRow = angular.copy(BudgetCategoryRow);
					var modalInstance = $uibModal.open({
						template: require('../../../pug/views/budgets/modals/category-item.pug'),
						controller: 'CategoryRowEditController',
						scope: $scope,
						resolve: {
							BudgetCategoryRow: BudgetCategoryRow
						}
					});

					modalInstance.result.then(function() {
						//setMessage()
						BudgetService.setData($scope.Budget);
					}, function() {
						angular.copy(OriginalBudgetCategoryRow, BudgetCategoryRow);
					});
				};

				$scope.deleteCategoryItem = function(BudgetCategoryRow) {
					var modalInstance = $uibModal.open({
						template: require('../../../pug/views/budgets/modals/category-item-delete.pug'),
						controller: 'CategoryRowDeleteController',
						scope: $scope,
						resolve: {
							BudgetCategoryRow: BudgetCategoryRow
						}
					});

					modalInstance.result.then(function(BudgetCategoryRow) {
						//setMessage()
						var index = $scope.$parent.BudgetCategory.budgetCategoryRows.map(function(ExistingBudgetCategoryRow) {
							return ExistingBudgetCategoryRow.id;
						}).indexOf(BudgetCategoryRow.id);
						$scope.$parent.BudgetCategory.budgetCategoryRows.splice(index, 1);
						BudgetService.setData($scope.Budget);
					});
				};

				$scope.addCategoryItemExpense = function() {
					var modalInstance = $uibModal.open({
						template: require('../../../pug/views/budgets/modals/category-item-expense.pug'),
						controller: 'CategoryRowExpenseCreateController',
						scope: $scope
					});

					modalInstance.result.then(function(BudgetCategoryRowExpense) {
						//setMessage()
						angular.forEach($scope.ExpenseCategories, function(ExpenseCategory) {
							if (ExpenseCategory.id === BudgetCategoryRowExpense.expense.expenseCategoryId) {
								BudgetCategoryRowExpense.expense.expenseCategory = ExpenseCategory;
								return false;
							}
						});
						$scope.BudgetCategoryRow.budgetCategoryRowExpenses.push(BudgetCategoryRowExpense);
						BudgetService.setData($scope.Budget);
					});
				};

				$scope.showEmptyPlaceholder = $scope.BudgetCategoryRow.budgetCategoryRowExpenses.length === 0;
				$scope.categoryRowExpenseSorting = {
					connectWith: '.js-sortable-expenses',
					placeholder: 'sortable-expenses-placeholder',
					forcePlaceholderSize: true,
					dropOnEmpty: true,
					items: '> tr.js-category-item-expense',
					activate: function() {
						$scope.showEmptyPlaceholder = $scope.BudgetCategoryRow.budgetCategoryRowExpenses.length === 0;
					},
					deactivate: function() {
						$scope.showEmptyPlaceholder = $scope.BudgetCategoryRow.budgetCategoryRowExpenses.length === 0;
					},
					over: function() {
						$scope.showEmptyPlaceholder = false;
					},
					out: function() {
						$scope.showEmptyPlaceholder = $scope.BudgetCategoryRow.budgetCategoryRowExpenses.length === 0;
					},
					receive: function(event, ui) {
						ui.item.sortable.moved.budgetCategoryRowId = $scope.BudgetCategoryRow.id;
					},
					stop: function(event, ui) {
						let BudgetCategoryRowExpenses = [];
						Array.prototype.push.apply(BudgetCategoryRowExpenses, ui.item.sortable.sourceModel);
						Array.prototype.push.apply(BudgetCategoryRowExpenses, ui.item.sortable.droptargetModel);

						CategoryRowExpenseService.bulkUpdate(BudgetCategoryRowExpenses, { ignoreLoadingBar: true });
						BudgetService.setData($scope.Budget);
					}
				};
			}
		};
	}
])
.directive('budgetCategoryItemExpense', ['BudgetService', '$uibModal',
	function(BudgetService, $uibModal) {
		return {
			restrict: 'A',
			template: require('../../../pug/views/budgets/directives/budget-category-item-expense.pug'),
			link: function($scope) {
				$scope.editCategoryItemExpense = function() {
					var OriginalBudgetCategoryRowExpense = angular.copy($scope.BudgetCategoryRowExpense);
					var modalInstance = $uibModal.open({
						template: require('../../../pug/views/budgets/modals/category-item-expense.pug'),
						controller: 'CategoryRowExpenseEditController',
						scope: $scope
					});

					modalInstance.result.then(function() {
						//setMessage()
						angular.forEach($scope.ExpenseCategories, function(ExpenseCategory) {
							if (ExpenseCategory.id === $scope.BudgetCategoryRowExpense.expense.expenseCategoryId) {
								$scope.BudgetCategoryRowExpense.expense.expenseCategory = ExpenseCategory;
								return false;
							}
						});
						BudgetService.setData($scope.Budget);
					}, function() {
						angular.copy(OriginalBudgetCategoryRowExpense, $scope.BudgetCategoryRowExpense);
					});
				};

				$scope.deleteCategoryItemExpense = function() {
					var modalInstance = $uibModal.open({
						template: require('../../../pug/views/budgets/modals/category-item-expense-delete.pug'),
						controller: 'CategoryRowExpenseDeleteController',
						scope: $scope
					});

					modalInstance.result.then(function() {
						//setMessage()
						var index = $scope.$parent.BudgetCategoryRow.budgetCategoryRowExpenses.map(function(ExistingBudgetCategoryRowExpense) {
							return ExistingBudgetCategoryRowExpense.id;
						}).indexOf($scope.BudgetCategoryRowExpense.id);
						$scope.$parent.BudgetCategoryRow.budgetCategoryRowExpenses.splice(index, 1);
						BudgetService.setData($scope.Budget);
					});
				};
			}
		};
	}
])
;