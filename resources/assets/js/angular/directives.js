require('./directives/budget-directives');
require('./directives/validation-directives');

angular.module('CudgetDirectives', [
	'BudgetDirectives',
	'IncomeDirectives',
	'ExpenseDirectives',
	'ValidationDirectives'
]);