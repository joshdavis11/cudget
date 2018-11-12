angular.module('BudgetControllers', [])
.controller('BudgetsController', ['Budgets', 'TitleService', 'BudgetService', '$scope', '$location', '$routeParams',
	function(Budgets, TitleService, BudgetService, $scope, $location, $routeParams) {
		TitleService.setTitle('Budgets');

		$scope.Budgets = Budgets;
		$scope.currentPage = parseInt($routeParams.page) || 1;

		$scope.deleteBudget = function(id) {
			var index = $scope.Budgets.map(function(Budget) {
				return Budget.id;
			}).indexOf(id);

			BudgetService.deleteBudget(id, { ignoreLoadingBar: true }).then(function() {
				$scope.Budgets.splice(index, 1);
			});
		};

		var cogClicked = false;
		$scope.viewBudget = function(id) {
			if (!cogClicked) {
				$location.path('/budgets/' + id);
			}
			cogClicked = false;
		};
		$scope.stopViewBudget = function() {
			cogClicked = true;
		};
	}
])
.controller('BudgetTemplatesController', ['BudgetTemplates', 'TitleService', 'BudgetService', '$scope', '$location', '$routeParams',
	function(BudgetTemplates, TitleService, BudgetService, $scope, $location, $routeParams) {
		$scope.BudgetTemplates = {};
		TitleService.setTitle('Budget Templates');

		$scope.BudgetTemplates = BudgetTemplates;
		$scope.currentPage = parseInt($routeParams.page) || 1;

		$scope.deleteBudgetTemplate = function(id) {
			var index = $scope.BudgetTemplates.map(function(BudgetTemplate) {
				return BudgetTemplate.id;
			}).indexOf(id);

			BudgetService.deleteBudgetTemplate(id, { ignoreLoadingBar: true }).then(function() {
				$scope.BudgetTemplates.splice(index, 1);
			});
		};

		var cogClicked = false;
		$scope.viewBudgetTemplate = function(id) {
			if (!cogClicked) {
				$location.path('/budgets/templates/' + id);
			}
			cogClicked = false;
		};
		$scope.stopViewBudgetTemplate = function() {
			cogClicked = true;
		};
	}
])
.controller('BudgetCreateController', ['BudgetTemplates', 'BudgetService', 'TitleService', '$scope', '$routeParams', '$location',
	function(BudgetTemplates, BudgetService, TitleService, $scope, $routeParams, $location) {
		$scope.breadcrumb = 'Create Budget';
		TitleService.setTitle('Create Budget');
		$scope.cancel = '/budgets';

		$scope.isOpenStart = false;
		$scope.isOpenEnd = false;

		$scope.Budget = {
			template: false
		};
		$scope.BudgetTemplates = BudgetTemplates;

		if ($routeParams.id) {
			$scope.Budget.newOrExisting = 'existing';
			$scope.Budget.budgetId = parseInt($routeParams.id);
			$scope.cancel = '/budgets/templates';
		}

		$scope.submit = function() {
			BudgetService.createBudget($scope.Budget, { ignoreLoadingBar: true }).then(function() {
				$location.path('/budgets');
			});
		};
	}
])
.controller('BudgetTemplateCreateController', ['Budgets', 'BudgetService', 'TitleService', '$scope', '$routeParams', '$location',
	function(Budgets, BudgetService, TitleService, $scope, $routeParams, $location) {
		TitleService.setTitle('Create Budget Template');
		$scope.cancel = '/budgets/templates';

		$scope.BudgetTemplate = {
			template: true
		};
		$scope.Budgets = Budgets;
		if ($routeParams.id) {
			$scope.BudgetTemplate.newOrExisting = 'existing';
			$scope.BudgetTemplate.budgetId = parseInt($routeParams.id);
			$scope.cancel = '/budgets';
		}

		$scope.submit = function() {
			BudgetService.createBudgetTemplate($scope.BudgetTemplate, { ignoreLoadingBar: true }).then(function() {
				$location.path('/budgets/templates');
			});
		};
	}
])
.controller('BudgetEditController', ['Budget', 'TitleService', 'BudgetService', 'DateService', '$scope', '$location',
	function(Budget, TitleService, BudgetService, DateService, $scope, $location) {
		$scope.breadcrumb = 'Edit Budget';

		$scope.isOpenStart = false;
		$scope.isOpenEnd = false;

		$scope.Budget = Budget;
		$scope.Budget.start = $scope.Budget.start ? DateService.getDate($scope.Budget.start) : null;
		$scope.Budget.end = $scope.Budget.end ? DateService.getDate($scope.Budget.end) : null;

		$scope.title = 'Edit <span class="italic">&#34;' + $scope.Budget.name + '&#34;</span>';
		TitleService.setTitle('Edit "' + $scope.Budget.name + '"');

		$scope.submit = function() {
			BudgetService.editBudget($scope.Budget, { ignoreLoadingBar: true }).then(function() {
				$location.path('/budgets');
			});
		};
	}
])
.controller('BudgetTemplateEditController', ['BudgetTemplate', 'TitleService', 'BudgetService', '$scope', '$location',
	function(BudgetTemplate, TitleService, BudgetService, $scope, $location) {
		$scope.breadcrumb = 'Edit Budget Template';
		$scope.cancel = '/budgets/templates';

		$scope.BudgetTemplate = BudgetTemplate;
		$scope.title = 'Edit <span class="italic">&#34;' + $scope.BudgetTemplate.name + '&#34;</span>';
		TitleService.setTitle('Edit "' + $scope.BudgetTemplate.name + '"');

		$scope.submit = function() {
			BudgetService.editBudgetTemplate($scope.BudgetTemplate, { ignoreLoadingBar: true }).then(function() {
				$location.path('/budgets/templates');
			});
		};
	}
])
.controller('BudgetShareController', ['ShareData', 'TitleService', 'BudgetService', '$scope', '$routeParams', '$location',
	function(ShareData, TitleService, BudgetService, $scope, $routeParams, $location) {
		$scope.Users = ShareData.Users;
		$scope.Budget = ShareData.Budget;
		TitleService.setTitle('Share "' + $scope.Budget.name + '"');
		$scope.cancel = '/budgets';

		$scope.SharedBudget = {
			id: parseInt($routeParams.id)
		};
		$scope.submit = function() {
			BudgetService.shareBudget($scope.SharedBudget, { ignoreLoadingBar: true }).then(function() {
				$location.path('/budgets');
			});
		};
	}
])
.controller('BudgetTemplateShareController', ['ShareData', 'TitleService', 'BudgetService', '$scope', '$routeParams', '$location',
	function(ShareData, TitleService, BudgetService, $scope, $routeParams, $location) {
		ShareData = ShareData || {};
		$scope.Users = ShareData.Users || {};
		$scope.Budget = ShareData.BudgetTemplate || {};
		TitleService.setTitle('Share "' + $scope.Budget.name + '"');
		$scope.cancel = '/budgets/templates';

		$scope.SharedBudget = {
			id: parseInt($routeParams.id)
		};
		$scope.submit = function() {
			BudgetService.shareBudgetTemplate($scope.SharedBudget, { ignoreLoadingBar: true }).then(function() {
				$location.path('/budgets/templates');
			});
		};
	}
])
.controller('BudgetController', ['Budget', 'ExpenseCategories', 'IncomeCategories', 'TitleService', 'BudgetService', 'CategoryService', '$scope', '$uibModal', 'orderByFilter',
	function(Budget, ExpenseCategories, IncomeCategories, TitleService, BudgetService, CategoryService, $scope, $uibModal, orderBy) {
		$scope.Budget = Budget;
		BudgetService.setData(Budget);
		Budget.budgetCategories = orderBy(Budget.budgetCategories, 'sortOrder', false);

		TitleService.setTitle('View "' + $scope.Budget.name + '"');

		$scope.IncomeCategories = IncomeCategories;
		$scope.ExpenseCategories = ExpenseCategories;

		$scope.editIncome = function(BudgetIncome) {
			var OriginalBudgetIncome = angular.copy(BudgetIncome);
			var modalInstance = $uibModal.open({
				template: require('../../../pug/views/budgets/modals/income.pug'),
				controller: 'BudgetIncomeEditController',
				scope: $scope,
				resolve: {
					BudgetIncome: BudgetIncome
				}
			});

			modalInstance.result.then(function() {
				if (!BudgetIncome.income || !BudgetIncome.income.incomeCategoryId) {
					BudgetIncome.income.incomeCategory = null;
				}
				else {
					angular.forEach($scope.IncomeCategories, function(IncomeCategory) {
						if (IncomeCategory.id === BudgetIncome.income.incomeCategoryId) {
							BudgetIncome.income.incomeCategory = IncomeCategory;
							return false;
						}
					});
				}
				BudgetService.setData($scope.Budget);
			}, function() {
				angular.copy(OriginalBudgetIncome, BudgetIncome);
			});
		};

		$scope.addIncome = function() {
			var modalInstance = $uibModal.open({
				template: require('../../../pug/views/budgets/modals/income.pug'),
				controller: 'BudgetIncomeCreateController',
				scope: $scope,
				resolve: {
					budgetId: $scope.Budget.id
				}
			});

			modalInstance.result.then(function(BudgetIncome) {
				angular.forEach($scope.IncomeCategories, function(IncomeCategory) {
					if (IncomeCategory.id === BudgetIncome.income.incomeCategoryId) {
						BudgetIncome.income.incomeCategory = IncomeCategory;
						return false;
					}
				});
				$scope.Budget.budgetIncome.push(BudgetIncome);
				BudgetService.setData($scope.Budget);
			});
		};

		$scope.deleteIncome = function(BudgetIncome) {
			var modalInstance = $uibModal.open({
				template: require('../../../pug/views/budgets/modals/income-delete.pug'),
				controller: 'BudgetIncomeDeleteController',
				scope: $scope,
				resolve: {
					BudgetIncome: BudgetIncome
				}
			});

			modalInstance.result.then(function(BudgetIncome) {
				//setMessage()
				var index = $scope.Budget.budgetIncome.map(function(ExistingBudgetIncome) {
					return ExistingBudgetIncome.id;
				}).indexOf(BudgetIncome.id);
				$scope.Budget.budgetIncome.splice(index, 1);
				BudgetService.setData($scope.Budget);
			});
		};

		$scope.addCategory = function() {
			var modalInstance = $uibModal.open({
				template: require('../../../pug/views/budgets/modals/category.pug'),
				controller: 'CategoryCreateController',
				scope: $scope,
				resolve: {
					budgetId: $scope.Budget.id
				}
			});

			modalInstance.result.then(function(BudgetCategory) {
				//setMessage()
				BudgetCategory.budgetCategoryRows = [];
				$scope.Budget.budgetCategories.push(BudgetCategory);
				BudgetService.setData($scope.Budget);
			});
		};

		$scope.categorySorting = CategoryService.getCategorySortingConfig();
	}
])
.controller('BudgetTemplateController', ['BudgetTemplate', 'TitleService', 'BudgetService', 'CategoryService', '$scope', '$uibModal', 'orderByFilter',
	function(BudgetTemplate, TitleService, BudgetService, CategoryService, $scope, $uibModal, orderBy) {
		$scope.Budget = BudgetTemplate;
		TitleService.setTitle('View "' + $scope.Budget.name + '"');

		BudgetService.setData($scope.Budget);
		$scope.Budget.budgetCategories = orderBy($scope.Budget.budgetCategories, 'sortOrder', false);

		$scope.addCategory = function() {
			var modalInstance = $uibModal.open({
				template: require('../../../pug/views/budgets/modals/category.pug'),
				controller: 'CategoryCreateController',
				scope: $scope,
				resolve: {
					budgetId: $scope.Budget.id
				}
			});

			modalInstance.result.then(function(BudgetCategory) {
				//setMessage()
				BudgetCategory.budgetCategoryRows = [];
				$scope.Budget.budgetCategories.push(BudgetCategory);
				BudgetService.setData($scope.Budget);
			});
		};

		$scope.categorySorting = CategoryService.getCategorySortingConfig();
	}
])
.controller('BudgetIncomeEditController', ['$scope', '$http', '$uibModalInstance', 'BudgetIncome', 'DateService',
	function($scope, $http, $uibModalInstance, BudgetIncome, DateService) {
		BudgetIncome.income.datetime = DateService.getDate(BudgetIncome.income.datetime);
		$scope.BudgetIncome = BudgetIncome;
		$scope.title = 'Edit';
		$scope.edit = true;
		$scope.isOpenIncomeDate = false;

		$scope.close = $scope.cancel = function() {
			$uibModalInstance.dismiss();
		};

		$scope.submit = function() {
			$http.put('/api/v1/budgets/income/' + BudgetIncome.id, BudgetIncome, { ignoreLoadingBar: true })
				.then(function() {
					$uibModalInstance.close();
				});
		};
	}
])
.controller('BudgetIncomeCreateController', ['$scope', '$http', '$uibModalInstance', 'budgetId',
	function($scope, $http, $uibModalInstance, budgetId) {
		$scope.BudgetIncome = {
			budgetId: budgetId
		};
		$scope.title = 'Add Income';
		$scope.edit = false;
		$scope.isOpenIncomeDate = false;

		$scope.close = $scope.cancel = function() {
			$uibModalInstance.dismiss();
		};

		$scope.submit = function() {
			$http.post('/api/v1/budgets/income', $scope.BudgetIncome, { ignoreLoadingBar: true })
				.then(function(response) {
					$uibModalInstance.close(response.data);
				});
		};
	}
])
.controller('BudgetIncomeDeleteController', ['$scope', '$http', '$uibModalInstance', 'BudgetIncome',
	function($scope, $http, $uibModalInstance, BudgetIncome) {
		$scope.description = BudgetIncome.income.description;

		$scope.close = $scope.cancel = function() {
			$uibModalInstance.dismiss();
		};

		$scope.delete = function() {
			$http.delete('/api/v1/budgets/income/' + BudgetIncome.id, { ignoreLoadingBar: true })
				.success(function() {
					$uibModalInstance.close(BudgetIncome);
				});
		};
	}
])
.controller('CategoryEditController', ['$scope', '$http', '$uibModalInstance', 'BudgetCategory',
	function($scope, $http, $uibModalInstance, BudgetCategory) {
		$scope.BudgetCategory = BudgetCategory;
		$scope.title = 'Edit';
		$scope.edit = true;

		$scope.close = $scope.cancel = function() {
			$uibModalInstance.dismiss();
		};

		$scope.submit = function() {
			$http.put('/api/v1/budgets/categories/' + BudgetCategory.id, BudgetCategory, { ignoreLoadingBar: true })
				.success(function() {
					$uibModalInstance.close();
				});
		};
	}
])
.controller('CategoryCreateController', ['$scope', '$http', '$uibModalInstance', 'budgetId',
	function($scope, $http, $uibModalInstance, budgetId) {
		$scope.BudgetCategory = {
			budgetId: budgetId
		};
		$scope.title = 'Add A Budget Category';
		$scope.edit = false;

		$scope.close = $scope.cancel = function() {
			$uibModalInstance.dismiss();
		};

		$scope.submit = function() {
			$http.post('/api/v1/budgets/categories', $scope.BudgetCategory, { ignoreLoadingBar: true })
				.then(function(response) {
					$uibModalInstance.close(response.data);
				});
		};
	}
])
.controller('CategoryDeleteController', ['$scope', '$http', '$uibModalInstance', 'BudgetCategory',
	function($scope, $http, $uibModalInstance, BudgetCategory) {
		$scope.name = BudgetCategory.name;

		$scope.close = $scope.cancel = function() {
			$uibModalInstance.dismiss();
		};

		$scope.delete = function() {
			$http.delete('/api/v1/budgets/categories/' + BudgetCategory.id, { ignoreLoadingBar: true })
				.success(function() {
					$uibModalInstance.close(BudgetCategory);
				});
		};
	}
])
.controller('CategoryRowEditController', ['$scope', '$http', '$uibModalInstance', 'BudgetCategoryRow', 'CategoryRowService',
	function($scope, $http, $uibModalInstance, BudgetCategoryRow, CategoryRowService) {
		$scope.BudgetCategoryRow = BudgetCategoryRow;
		$scope.title = 'Edit';
		$scope.edit = true;

		$scope.close = $scope.cancel = function() {
			$uibModalInstance.dismiss();
		};

		$scope.submit = function() {
			CategoryRowService.update(BudgetCategoryRow, { ignoreLoadingBar: true })
				.success(function() {
					$uibModalInstance.close();
				});
		};
	}
])
.controller('CategoryRowCreateController', ['$scope', '$http', '$uibModalInstance', 'budgetCategoryId',
	function($scope, $http, $uibModalInstance, budgetCategoryId) {
		$scope.BudgetCategoryRow = {
			budgetCategoryId: budgetCategoryId
		};
		$scope.title = 'Add A New Item';
		$scope.edit = false;

		$scope.close = $scope.cancel = function() {
			$uibModalInstance.dismiss();
		};

		$scope.submit = function() {
			$http.post('/api/v1/budgets/categories/rows', $scope.BudgetCategoryRow, { ignoreLoadingBar: true })
				.then(function(response) {
					$uibModalInstance.close(response.data);
				});
		};
	}
])
.controller('CategoryRowDeleteController', ['$scope', '$http', '$uibModalInstance', 'BudgetCategoryRow',
	function($scope, $http, $uibModalInstance, BudgetCategoryRow) {
		$scope.name = BudgetCategoryRow.name;

		$scope.close = $scope.cancel = function() {
			$uibModalInstance.dismiss();
		};

		$scope.delete = function() {
			$http.delete('/api/v1/budgets/categories/rows/' + BudgetCategoryRow.id, { ignoreLoadingBar: true })
				.success(function() {
					$uibModalInstance.close(BudgetCategoryRow);
				});
		};
	}
])
.controller('CategoryRowExpenseEditController', ['$scope', 'CategoryRowExpenseService', 'DateService', '$uibModalInstance',
	function($scope, CategoryRowExpenseService, DateService, $uibModalInstance) {
		$scope.BudgetCategoryRowExpense.expense.datetime = DateService.getDate($scope.BudgetCategoryRowExpense.expense.datetime);
		$scope.edit = true;
		$scope.title = 'Edit Expense';

		$scope.close = $scope.cancel = function() {
			$uibModalInstance.dismiss();
		};

		$scope.submit = function() {
			CategoryRowExpenseService.update($scope.BudgetCategoryRowExpense, { ignoreLoadingBar: true })
				.then(function() {
					$uibModalInstance.close();
				});
		};
	}
])
.controller('CategoryRowExpenseCreateController', ['$scope', '$http', '$uibModalInstance',
	function($scope, $http, $uibModalInstance) {
		$scope.BudgetCategoryRowExpense = {
			budgetCategoryRowId: $scope.BudgetCategoryRow.id
		};
		$scope.title = 'Add New Expense';
		$scope.edit = false;
		$scope.isOpenExpenseDate = false;

		$scope.close = $scope.cancel = function() {
			$uibModalInstance.dismiss();
		};

		$scope.submit = function() {
			$http.post('/api/v1/budgets/categories/rows/expenses', $scope.BudgetCategoryRowExpense, { ignoreLoadingBar: true })
				.then(function(response) {
					$uibModalInstance.close(response.data);
				});
		};
	}
])
.controller('CategoryRowExpenseDeleteController', ['$scope', '$http', '$uibModalInstance',
	function($scope, $http, $uibModalInstance) {
		$scope.close = $scope.cancel = function() {
			$uibModalInstance.dismiss();
		};

		$scope.delete = function() {
			$http.delete('/api/v1/budgets/categories/rows/expenses/' + $scope.BudgetCategoryRowExpense.id, { ignoreLoadingBar: true })
				.success(function() {
					$uibModalInstance.close();
				});
		};
	}
])
;