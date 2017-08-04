angular.module('BudgetServices', [])
.service('BudgetService', ['IncomeService', 'CategoryService', '$http',
	function(IncomeService, CategoryService, $http) {
		this.setData = function(Budget) {
			IncomeService.setData(Budget);
			angular.forEach(Budget.budgetCategories, function(BudgetCategory) {
				CategoryService.setData(BudgetCategory);
			});
			this.setTotalSpent(Budget);
			this.setTotalEstimated(Budget);
			this.setTotalSpentClass(Budget);
			this.setBudgetNet(Budget);
			this.setBudgetNetClass(Budget);
		};

		this.setBudgetNet = function(Budget) {
			Budget.net = Budget.actualIncome - Budget.totalSpent;
		};

		this.setBudgetNetClass = function(Budget) {
			Budget.netClass = Budget.net >= 0 ? 'text-success' : 'text-danger';
		};

		this.setTotalSpent = function(Budget) {
			Budget.totalSpent = 0.00;
			angular.forEach(Budget.budgetCategories, function(BudgetCategory) {
				Budget.totalSpent += BudgetCategory.spent;
			});
		};

		this.setTotalEstimated = function(Budget) {
			Budget.totalEstimated = 0.00;
			angular.forEach(Budget.budgetCategories, function(BudgetCategory) {
				Budget.totalEstimated += BudgetCategory.estimated;
			});
		};

		this.setTotalSpentClass = function(Budget) {
			Budget.totalSpentClass = Budget.totalSpent > Budget.actualIncome ? 'text-danger' : 'text-success';
		};

		this.getBudgets = function(options) {
			return $http.get('/api/v1/budgets', options).then(function(response) {
				return response.data;
			});
		};

		this.getBudget = function(id) {
			return $http.get('/api/v1/budgets/' + id).then(function(response) {
				return response.data;
			});
		};

		this.getBudgetTemplate = function(id) {
			return $http.get('/api/v1/budgets/templates/' + id).then(function(response) {
				return response.data;
			});
		};

		this.getBudgetTemplates = function() {
			return $http.get('/api/v1/budgets/templates').then(function(response) {
				return response.data;
			});
		};

		this.createBudget = function(Budget, options) {
			return $http.post('/api/v1/budgets', Budget, options).then(function(response) {
				return response.data;
			});
		};

		this.createBudgetTemplate = function(BudgetTemplate, options) {
			return $http.post('/api/v1/budgets/templates', BudgetTemplate, options).then(function(response) {
				return response.data;
			});
		};

		this.editBudget = function(Budget, options) {
			return $http.put('/api/v1/budgets/' + Budget.id, Budget, options).then(function(response) {
				return response.data;
			});
		};

		this.editBudgetTemplate = function(BudgetTemplate, options) {
			return $http.put('/api/v1/budgets/templates/' + BudgetTemplate.id, BudgetTemplate, options).then(function(response) {
				return response.data;
			});
		};

		this.getBudgetShareData = function(id) {
			return $http.get('/api/v1/budgets/' + id + '/share').then(function(response) {
				return response.data;
			});
		};

		this.getBudgetTemplateShareData = function(id) {
			return $http.get('/api/v1/budgets/templates/' + id + '/share').then(function(response) {
				return response.data;
			});
		};

		this.deleteBudget = function(id, options) {
			return $http.delete('/api/v1/budgets/' + id, options).then(function(response) {
				return response;
			});
		};

		this.deleteBudgetTemplate = function(id, options) {
			return $http.delete('/api/v1/budgets/templates/' + id, options).then(function(response) {
				return response;
			});
		};

		this.shareBudget = function(SharedBudget, options) {
			return $http.post('/api/v1/budgets/' + SharedBudget.id + '/share', SharedBudget, options).then(function(response) {
				return response;
			});
		};

		this.shareBudgetTemplate = function(SharedBudgetTemplate, options) {
			return $http.post('/api/v1/budgets/templates/' + SharedBudgetTemplate.id + '/share', SharedBudgetTemplate, options).then(function(response) {
				return response;
			});
		};
	}
])
.service('CategoryService', ['CategoryRowService', '$http',
	function(CategoryRowService, $http) {
		this.setData = function(BudgetCategory) {
			angular.forEach(BudgetCategory.budgetCategoryRows, function(BudgetCategoryRow) {
				CategoryRowService.setData(BudgetCategoryRow);
			});
			this.setSpent(BudgetCategory);
			this.setEstimated(BudgetCategory);
			this.setBudgetCategoryRemaining(BudgetCategory);
			this.setBudgetCategoryPanelClass(BudgetCategory);
		};

		this.setBudgetCategoryPanelClass = function(BudgetCategory) {
			if (BudgetCategory.name === 'Uncategorized') {
				BudgetCategory.panelClass = 'panel-primary';
			} else if (BudgetCategory.spent > BudgetCategory.estimated) {
				BudgetCategory.panelClass = 'panel-danger';
			} else {
				BudgetCategory.panelClass = 'panel-success';
			}
		};

		this.setEstimated = function(BudgetCategory) {
			BudgetCategory.estimated = 0.00;
			angular.forEach(BudgetCategory.budgetCategoryRows, function(BudgetCategoryRow) {
				BudgetCategory.estimated += BudgetCategoryRow.estimated;
			});
		};

		this.setSpent = function(BudgetCategory) {
			BudgetCategory.spent = 0.00;
			angular.forEach(BudgetCategory.budgetCategoryRows, function(BudgetCategoryRow) {
				BudgetCategory.spent += BudgetCategoryRow.spent;
			});
		};

		this.setBudgetCategoryRemaining = function(BudgetCategory) {
			BudgetCategory.remaining = BudgetCategory.estimated - BudgetCategory.spent;
		};

		this.getCategoriesForBudget = function(budgetId, options) {
			return $http.get('/api/v1/budgets/categories/bid/' + budgetId, options).then(function(response) {
				return response.data;
			});
		};

		this.editCategory = function(BudgetCategory, options) {
			return $http.put('/api/v1/budgets/categories/' + BudgetCategory.id, BudgetCategory, options).then(function(response) {
				return response;
			});
		};

		this.getCategorySortingConfig = function() {
			var that = this;
			return {
				handle: '.panel-heading',
				stop: function(event, ui) {
					angular.forEach(ui.item.sortable.sourceModel, function(BudgetCategory, index) {
						BudgetCategory.sortOrder = index;
						that.editCategory(BudgetCategory, { ignoreLoadingBar: true })
							.then(function() {

							},function() {
								console.error('Budget Category didn\'t save new sorting correctly');
							});
					});
				}
			};
		};
	}
])
.service('CategoryRowService', ['$http', 'orderByFilter', 'DateService',
	function($http, orderBy, DateService) {
		this.setData = function(BudgetCategoryRow) {
			this.setSpent(BudgetCategoryRow);
			this.setDates(BudgetCategoryRow);
			this.order(BudgetCategoryRow);
		};

		this.setSpent = function(BudgetCategoryRow) {
			BudgetCategoryRow.spent = 0.00;
			angular.forEach(BudgetCategoryRow.budgetCategoryRowExpenses, function(BudgetCategoryRowExpense) {
				BudgetCategoryRow.spent += BudgetCategoryRowExpense.expense.amount;
			});
		};

		this.update = function(BudgetCategoryRow, options) {
			return $http.put('/api/v1/budgets/categories/rows/' + BudgetCategoryRow.id, BudgetCategoryRow, options);
		};

		this.setDates = function(BudgetCategoryRow) {
			angular.forEach(BudgetCategoryRow.budgetCategoryRowExpenses, function(BudgetCategoryRowExpense) {
				if (!(BudgetCategoryRowExpense.expense.datetime instanceof Date)) {
					BudgetCategoryRowExpense.expense.datetime = DateService.getDate(BudgetCategoryRowExpense.expense.datetime);
				}
			});
		};

		this.order = function(BudgetCategoryRow) {
			BudgetCategoryRow.budgetCategoryRowExpenses = orderBy(BudgetCategoryRow.budgetCategoryRowExpenses,
			[
				function(BudgetCategoryRowExpense) {
					return BudgetCategoryRowExpense.expense.datetime;
				}, function(BudgetCategoryRowExpense) {
					return BudgetCategoryRowExpense.id;
				}
			], true);
		};

		this.getCategoryRowsForBudgetCategory = function(budgetCategoryId, options) {
			return $http.get('/api/v1/budgets/categories/rows/bcid/' + budgetCategoryId, options).then(function(response) {
				return response.data;
			});
		}
	}
])
.service('CategoryRowExpenseService', ['$http',
	function($http) {
		this.update = function(BudgetCategoryRowExpense, options) {
			return $http.put('/api/v1/budgets/categories/rows/expenses/' + BudgetCategoryRowExpense.id, BudgetCategoryRowExpense, options);
		};

		this.bulkUpdate = function(BudgetCategoryRowExpenses, options) {
			return $http.put('/api/v1/budgets/categories/rows/expenses/bulk', BudgetCategoryRowExpenses, options);
		};
	}
])
;