angular.module('ExpenseServices', [])
.service('ExpenseService', ['$http',
	function($http) {
		this.getExpenses = function() {
			return $http.get('/api/v1/expenses').then(function(Expenses) {
				return Expenses.data;
			});
		};

		this.delete = function(id, options) {
			return $http.delete('/api/v1/expenses/' + id, options);
		};

		this.create = function(Expense, options) {
			return $http.post('/api/v1/expenses', Expense, options);
		};

		this.update = function(Expense, options) {
			return $http.put('/api/v1/expenses/' + Expense.id, Expense, options);
		};

		this.addToBudget = function(expenseId, budgetCategoryRowId, options) {
			return $http.post('/api/v1/budgets/categories/rows/expenses', {'expense' : {'id': expenseId}, 'budgetCategoryRowId': budgetCategoryRowId}, options).then(function(response) {
				return response.data;
			});
		};
	}
])
.service('ExpenseCategoryService', ['$http',
	function($http) {
		this.getExpenseCategories = function() {
			var ExpenseCategories = [];
			return $http.get('/api/v1/expenses/categories')
				.then(function(RetrievedExpenseCategories) {
					ExpenseCategories = RetrievedExpenseCategories.data;
					addNewCategoryOption();
					return ExpenseCategories;
				}, addNewCategoryOption);

			/**
			 * Add the new category option
			 */
			function addNewCategoryOption() {
				ExpenseCategories.unshift({'id': 'new', 'name': '+ Add New Expense Category'});
			}
		};
	}
])
;