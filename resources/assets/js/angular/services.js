require('./services/budget-services');
require('./services/expense-services');
require('./services/head-services');
require('./services/helper-services');
require('./services/income-services');
require('./services/settings-services');

angular.module('CudgetServices', [
	'HeadServices',
	'HelperServices',
	'BudgetServices',
	'IncomeServices',
	'ExpenseServices',
	'SettingsServices'
]);