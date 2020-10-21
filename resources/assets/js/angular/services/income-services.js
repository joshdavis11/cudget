angular.module('IncomeServices', [])
.service('IncomeService', ['orderByFilter', '$http', 'DateService',
	function(orderBy, $http, DateService) {
		this.setData = function(Budget) {
			this.setBudgetActualIncome(Budget);
			this.setBudgetIncomeClass(Budget);
			this.setDates(Budget);
			this.order(Budget);
		};

		this.setBudgetActualIncome = function(Budget) {
			Budget.actualIncome = 0.00;
			angular.forEach(Budget.budgetIncome, function(BudgetIncome) {
				Budget.actualIncome += BudgetIncome.income.amount;
			});
		};

		this.setBudgetIncomeClass = function(Budget) {
			Budget.incomeClass = Budget.actualIncome >= Budget.income ? 'label-success' : 'label-danger';
		};

		this.setDates = function(Budget) {
			angular.forEach(Budget.budgetIncome, function(BudgetIncome) {
				if (!(BudgetIncome.income.datetime instanceof Date)) {
					BudgetIncome.income.datetime = DateService.getDate(BudgetIncome.income.datetime);
				}
			});
		};

		this.order = function(Budget) {
			Budget.budgetIncome = orderBy(Budget.budgetIncome,
			[
				function(BudgetIncome) {
					return BudgetIncome.income.datetime;
				}, function(BudgetIncome) {
					return BudgetIncome.id;
				}
			], true);
		};

		this.getIncome = function() {
			return $http.get('/api/v1/income').then(function(response) {
				return response.data;
			})
		};

		this.delete = function(id, options) {
			return $http.delete('/api/v1/income/' + id, options);
		};

		this.create = function(Income, options) {
			return $http.post('/api/v1/income', Income, options);
		};

		this.update = function(Income, options) {
			return $http.put('/api/v1/income/' + Income.id, Income, options);
		};

		this.addToBudget = function(incomeId, budgetId, options) {
			return $http.post('/api/v1/budgets/income', {'income' : {'id': incomeId}, 'budgetId': budgetId}, options).then(function(response) {
				return response.data;
			});
		};
	}
])
;