angular.module('CudgetRoutes', [])
.config(['$routeProvider', '$locationProvider',
	function ($routeProvider, $locationProvider) {
		$locationProvider.html5Mode(true);
		$routeProvider
		.when('/', {
			template: require('../../pug/views/prelogin.pug'),
			controller: 'PreLoginController'
		})
		.when('/signup', {
			template: require('../../pug/views/prelogin/signup.pug'),
			controller: 'SignUpController'
		})
		.when('/password/reset', {
			template: require('../../pug/views/prelogin/password/request.pug'),
			controller: 'PasswordRequestController'
		})
		.when('/home', {
			template: require('../../pug/views/home.pug'),
			controller: 'HomeController'
		})
		.when('/budgets/templates/create/:id?', {
			template: require('../../pug/views/budgets/templates/budget-template-create-form.pug'),
			controller: 'BudgetTemplateCreateController',
			resolve: {
				checkPermission: ['PermissionService',
					function(PermissionService) {
						return PermissionService.checkPermission('budgetTemplates', false);
					}
				],
				Budgets: ['BudgetService',
					function(BudgetService) {
						return BudgetService.getBudgets();
					}
				]
			}
		})
		.when('/budgets/templates/:id/edit', {
			template: require('../../pug/views/budgets/templates/budget-template-edit-form.pug'),
			controller: 'BudgetTemplateEditController',
			resolve: {
				checkPermission: ['PermissionService',
					function(PermissionService) {
						return PermissionService.checkPermission('budgetTemplates', false)
					}
				],
				BudgetTemplate: ['BudgetService', '$route',
					function(BudgetService, $route) {
						return BudgetService.getBudgetTemplate($route.current.params.id);
					}
				]
			}
		})
		.when('/budgets/templates/:id/share', {
			template: require('../../pug/views/budgets/budget-share-form.pug'),
			controller: 'BudgetTemplateShareController',
			resolve: {
				checkPermission: ['PermissionService',
						function(PermissionService) {
						return PermissionService.checkPermission('budgetTemplates', false)
					}
				],
				ShareData: ['BudgetService', '$route', '$location',
					function(BudgetService, $route, $location) {
						return BudgetService.getBudgetTemplateShareData($route.current.params.id).then(function(ShareData) {
							return ShareData;
						}, function() {
							$location.path('/budgets/templates');
						});
					}
				]
			}
		})
		.when('/budgets/templates/:id', {
			template: require('../../pug/views/budgets/budget.pug'),
			controller: 'BudgetTemplateController',
			resolve: {
				checkPermission: ['PermissionService',
					function(PermissionService) {
						return PermissionService.checkPermission('budgetTemplates', false)
					}
				],
				BudgetTemplate: ['BudgetService', '$route', '$location',
					function(BudgetService, $route, $location) {
						return BudgetService.getBudgetTemplate($route.current.params.id).then(function(BudgetTemplate) {
							return BudgetTemplate;
						}, function() {
							$location.path('/budgets/templates');
						});
					}
				]
			}
		})
		.when('/budgets/templates', {
			template: require('../../pug/views/budgets/templates/templates.pug'),
			controller: 'BudgetTemplatesController',
			resolve: {
				checkPermission: ['PermissionService',
					function(PermissionService) {
						return PermissionService.checkPermission('budgetTemplates', false)
					}
				],
				BudgetTemplates: ['BudgetService',
					function(BudgetService) {
						return BudgetService.getBudgetTemplates();
					}
				]
			}
		})
		.when('/budgets/create/:id?', {
			template: require('../../pug/views/budgets/budget-create-form.pug'),
			controller: 'BudgetCreateController',
			resolve: {
				BudgetTemplates: ['BudgetService',
					function(BudgetService) {
						return BudgetService.getBudgetTemplates();
					}
				]
			}
		})
		.when('/budgets/:id/edit', {
			template: require('../../pug/views/budgets/budget-edit-form.pug'),
			controller: 'BudgetEditController',
			resolve: {
				Budget: ['BudgetService', '$route',
					function(BudgetService, $route) {
						return BudgetService.getBudget($route.current.params.id);
					}
				]
			}
		})
		.when('/budgets/:id/share', {
			template: require('../../pug/views/budgets/budget-share-form.pug'),
			controller: 'BudgetShareController',
			resolve: {
				checkPermission: ['PermissionService',
					function(PermissionService) {
						return PermissionService.checkPermission('budgetTemplates', false)
					}
				],
				ShareData: ['BudgetService', '$route', '$location',
					function(BudgetService, $route, $location) {
						return BudgetService.getBudgetShareData($route.current.params.id).then(function(ShareData) {
							return ShareData;
						}, function() {
							$location.path('/budgets');
						});
					}
				]
			}
		})
		.when('/budgets/:id', {
			template: require('../../pug/views/budgets/budget.pug'),
			controller: 'BudgetController',
			resolve: {
				Budget: ['BudgetService', '$route', '$location',
					function(BudgetService, $route, $location) {
						return BudgetService.getBudget($route.current.params.id).then(function(Budget) {
							return Budget;
						}, function() {
							$location.path('/budgets');
						});
					}
				],
				IncomeCategories: ['IncomeCategoryService',
					function(IncomeCategoryService) {
						return IncomeCategoryService.getIncomeCategories();
					}
				],
				ExpenseCategories: ['ExpenseCategoryService',
					function(ExpenseCategoryService) {
						return ExpenseCategoryService.getExpenseCategories();
					}
				]
			}
		})
		.when('/budgets', {
			template: require('../../pug/views/budgets/budgets.pug'),
			controller: 'BudgetsController',
			resolve: {
				Budgets: ['BudgetService',
					function(BudgetService) {
						return BudgetService.getBudgets();
					}
				]
			}
		})
		.when('/banking/update', {
			template: require('../../pug/views/banking/update.pug'),
			controller: 'UpdateController',
			resolve: {
				checkPermission: ['PermissionService',
					function(PermissionService) {
						return PermissionService.checkPermission('update', false)
					}
				]
			}
		})
		.when('/banking/import', {
			template: require('../../pug/views/banking/import.pug'),
			controller: 'ImportController',
			resolve: {
				checkPermission: ['PermissionService',
					function(PermissionService) {
						return PermissionService.checkPermission('import', false)
					}
				]
			}
		})
		.when('/expenses/create', {
			template: require('../../pug/views/expenses/expense-create.pug'),
			controller: 'ExpenseCreateController'
		})
		.when('/expenses', {
			template: require('../../pug/views/expenses/expenses.pug'),
			controller: 'ExpenseController',
			reloadOnSearch: false,
			resolve: {
				Expenses: ['ExpenseService',
					function(ExpenseService) {
						return ExpenseService.getExpenses();
					}
				],
				ExpenseCategories: ['ExpenseCategoryService',
					function(ExpenseCategoryService) {
						return ExpenseCategoryService.getExpenseCategories();
					}
				]
			}
		})
		.when('/income/create', {
			template: require('../../pug/views/income/income-create.pug'),
			controller: 'IncomeCreateController'
		})
		.when('/income', {
			template: require('../../pug/views/income/income.pug'),
			controller: 'IncomeController',
			reloadOnSearch: false,
			resolve: {
				Incomes: ['IncomeService',
					function(IncomeService) {
						return IncomeService.getIncome();
					}
				],
				IncomeCategories: ['IncomeCategoryService',
					function(IncomeCategoryService) {
						return IncomeCategoryService.getIncomeCategories();
					}
				]
			}
		})
		.when('/reports/budget-daily-expenses', {
			template: require('../../pug/views/reports/budgets.pug'),
			controller: 'BudgetDailyExpensesReportController',
			reloadOnSearch: false,
			resolve: {
				Budgets: ['BudgetService',
					function(BudgetService) {
						return BudgetService.getBudgets();
					}
				]
			}
		})
		.when('/reports/budget-expenses-by-category', {
			template: require('../../pug/views/reports/budgets.pug'),
			controller: 'BudgetExpensesByCategoryReportController',
			reloadOnSearch: false,
			resolve: {
				Budgets: ['BudgetService',
					function(BudgetService) {
						return BudgetService.getBudgets();
					}
				]
			}
		})
		.when('/reports/budget-daily-net', {
			template: require('../../pug/views/reports/budgets.pug'),
			controller: 'BudgetDailyNetReportController',
			reloadOnSearch: false,
			resolve: {
				Budgets: ['BudgetService',
					function(BudgetService) {
						return BudgetService.getBudgets();
					}
				]
			}
		})
		.when('/settings/colors', {
			template: require('../../pug/views/settings/colors.pug'),
			controller: 'ColorsController',
			resolve: {
				checkPermission: ['PermissionService',
					function(PermissionService) {
						return PermissionService.checkPermission('colorScheme', false)
					}
				]
			}
		})
		.when('/settings/users/create', {
			template: require('../../pug/views/settings/users/user-form.pug'),
			controller: 'UserCreateController',
			resolve: {
				checkPermission: ['PermissionService',
					function(PermissionService) {
						return PermissionService.checkPermission('user_management', true)
					}
				]
			}
		})
		.when('/settings/users/:id/edit', {
			template: require('../../pug/views/settings/users/user-form.pug'),
			controller: 'UserEditController',
			resolve: {
				checkPermission: ['PermissionService',
					function(PermissionService) {
						return PermissionService.checkPermission('user_management', true)
					}
				],
				User: ['UsersService', '$route',
					function(UsersService, $route) {
						return UsersService.getUser($route.current.params.id);
					}
				]
			}
		})
		.when('/settings/users/:id/password', {
			template: require('../../pug/views/settings/users/password-form.pug'),
			controller: 'UserPasswordController',
			resolve: {
				checkPermission: ['PermissionService',
					function(PermissionService) {
						return PermissionService.checkPermission('user_management', true)
					}
				],
				User: ['UsersService', '$route',
					function(UsersService, $route) {
						return UsersService.getUser($route.current.params.id);
					}
				]
			}
		})
		.when('/settings/users/:id/permissions', {
			template: require('../../pug/views/settings/users/permissions.pug'),
			controller: 'UserPermissionsController',
			resolve: {
				checkPermission: ['PermissionService',
					function(PermissionService) {
						return PermissionService.checkPermission('user_management', true)
					}
				],
				UserPermissions: ['PermissionService', '$route',
					function(PermissionService, $route) {
						return PermissionService.getUserPerms($route.current.params.id);
					}
				],
				User: ['UsersService', '$route',
					function(UsersService, $route) {
						return UsersService.getUser($route.current.params.id);
					}
				]
			}
		})
		.when('/settings/users', {
			template: require('../../pug/views/settings/users/users.pug'),
			controller: 'UsersController',
			resolve: {
				checkPermission: ['PermissionService',
					function(PermissionService) {
						return PermissionService.checkPermission('user_management', true)
					}
				],
				Users: ['UsersService',
					function(UsersService) {
						return UsersService.getUsers();
					}
				]
			}
		})
		.when('/settings', {
			template: require('../../pug/views/settings/settings.pug'),
			controller: 'SettingsController',
			resolve: {
				checkPermission: ['PermissionService',
					function(PermissionService) {
						return PermissionService.checkPermission('settings', true)
					}
				]
			}
		})
		.when('/login', {
			template: require('../../pug/views/login.pug'),
			controller: 'LoginController'
		})
		.when('/403', {
			template: require('../../pug/views/errors/403.pug')
		})
		.otherwise({
			template: require('../../pug/views/errors/404.pug'),
			controller: '404Controller'
		});
	}]
);