require('./controllers/banking-controllers');
require('./controllers/base-controllers');
require('./controllers/expense-controllers');
require('./controllers/budget-controllers');
require('./controllers/income-controllers');
require('./controllers/report-controllers');
require('./controllers/settings-controllers');

angular.module('CudgetControllers', [
	'BankingControllers',
	'BaseControllers',
	'BudgetControllers',
	'ExpenseControllers',
	'IncomeControllers',
	'ReportControllers',
	'SettingsControllers'
]);