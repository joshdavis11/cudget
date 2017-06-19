require('./directives/budget-directives');
require('./directives/expense-directives');
require('./directives/income-directives');
require('./directives/validation-directives');

angular.module('CudgetDirectives', [
	'BudgetDirectives',
	'IncomeDirectives',
	'ExpenseDirectives',
	'ValidationDirectives'
]);