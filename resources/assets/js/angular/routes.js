angular.module('CudgetRoutes', [])
.config(['$routeProvider', '$locationProvider',
	function ($routeProvider, $locationProvider) {
		$locationProvider.html5Mode(true);
		$routeProvider
		.when('/', {
			template: require('../../pug/views/prelogin.pug'),
			controller: 'PreLoginController'
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
						return PermissionService.checkPermission('budget_templates', 'add');
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
						return PermissionService.checkPermission('budget_templates', 'update')
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
						return PermissionService.checkPermission('budget_sharing', 'add')
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
						return PermissionService.checkPermission('budget_templates', 'view')
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
						return PermissionService.checkPermission('budget_templates', 'view')
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
				checkPermission: ['PermissionService',
					function(PermissionService) {
						return PermissionService.checkPermission('budgets', 'add')
					}
				],
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
				checkPermission: ['PermissionService',
					function(PermissionService) {
						return PermissionService.checkPermission('budgets', 'update')
					}
				],
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
						return PermissionService.checkPermission('budget_sharing', 'add')
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
				checkPermission: ['PermissionService',
					function(PermissionService) {
						return PermissionService.checkPermission('budgets', 'view')
					}
				],
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
				checkPermission: ['PermissionService',
					function(PermissionService) {
						return PermissionService.checkPermission('budgets', 'view')
					}
				],
				Budgets: ['BudgetService',
					function(BudgetService) {
						return BudgetService.getBudgets();
					}
				]
			}
		})
		.when('/import', {
			template: require('../../pug/views/import.pug'),
			controller: 'ImportController',
			resolve: {
				checkPermission: ['PermissionService',
					function(PermissionService) {
						return PermissionService.checkPermission('import', 'view')
					}
				]
			}
		})
		.when('/expenses/create', {
			template: require('../../pug/views/expenses/expense-create.pug'),
			controller: 'ExpenseCreateController',
			resolve: {
				checkPermission: ['PermissionService',
					function(PermissionService) {
						return PermissionService.checkPermission('expenses', 'add')
					}
				]
			}
		})
		.when('/expenses', {
			template: require('../../pug/views/expenses/expenses.pug'),
			controller: 'ExpenseController',
			reloadOnSearch: false,
			resolve: {
				checkPermission: ['PermissionService',
					function(PermissionService) {
						return PermissionService.checkPermission('expenses', 'view')
					}
				],
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
			controller: 'IncomeCreateController',
			resolve: {
				checkPermission: ['PermissionService',
					function(PermissionService) {
						return PermissionService.checkPermission('income', 'add')
					}
				]
			}
		})
		.when('/income', {
			template: require('../../pug/views/income/income.pug'),
			controller: 'IncomeController',
			reloadOnSearch: false,
			resolve: {
				checkPermission: ['PermissionService',
					function(PermissionService) {
						return PermissionService.checkPermission('income', 'view')
					}
				],
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
		.when('/settings/colors', {
			template: require('../../pug/views/settings/colors.pug'),
			controller: 'ColorsController',
			resolve: {
				checkPermission: ['PermissionService',
					function(PermissionService) {
						return PermissionService.checkPermission('color_scheme', 'view')
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
						return PermissionService.checkPermission('user_management', 'add')
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
						return PermissionService.checkPermission('user_management', 'update')
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
						return PermissionService.checkPermission('user_management', 'update')
					}
				],
				User: ['UsersService', '$route',
					function(UsersService, $route) {
						return UsersService.getUser($route.current.params.id);
					}
				]
			}
		})
		.when('/settings/users/:id/pin', {
			template: require('../../pug/views/settings/users/pin-form.pug'),
			controller: 'UserPinController',
			resolve: {
				checkPermission: ['PermissionService',
					function(PermissionService) {
						return PermissionService.checkPermission('user_management', 'update')
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
						return PermissionService.checkPermission('user_management', 'update')
					}
				],
				Permissions: ['UsersService', '$route',
					function(UsersService, $route) {
						return UsersService.getFormattedPermissions($route.current.params.id);
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
						return PermissionService.checkPermission('user_management', 'view')
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
						return PermissionService.checkPermission('settings', 'update')
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