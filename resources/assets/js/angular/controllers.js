require('./controllers/base-controllers');
require('./controllers/expense-controllers');
require('./controllers/budget-controllers');
require('./controllers/income-controllers');
require('./controllers/settings-controllers');

angular.module('CudgetControllers', [
	'BaseControllers',
	'BudgetControllers',
	'ExpenseControllers',
	'IncomeControllers',
	'SettingsControllers'
]);