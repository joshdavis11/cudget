require('./config');
require('./controllers');
require('./datepicker');
require('./directives');
require('./filters');
require('./routes');
require('./services');

angular.module('Cudget', [
	'ngRoute',
	'ngResource',
	'ngSanitize',
	'angular-loading-bar',
	'ngAnimate',
	'ngFileUpload',
	'ui.bootstrap',
	'ui.sortable',
	'ui.mask',
	'CudgetControllers',
	'CudgetServices',
	'CudgetRoutes',
	'CudgetFilters',
	'CudgetDirectives',
	'CudgetDatepicker',
	'CudgetConfig'
]);