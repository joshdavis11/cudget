angular.module('HelperServices', [])
.service('PermissionService', ['$http', '$location',
	function($http, $location) {
		var that = this;
		var authUserPromise = $http.get('/api/v1/auth/user').then(function(response) {
			return response.data;
		});
		var permsPromise = $http.get('/api/v1/perms').then(function(response) {
			return that.formatPermissions(response.data);
		});

		this.getAuthUser = function() {
			return authUserPromise;
		};

		this.getPerms = function() {
			return permsPromise;
		};

		this.formatPermissions = function(Perms) {
			var Permissions = {};
			angular.forEach(Perms, function(Permission) {
				if (!Permissions[Permission.section]) {
					Permissions[Permission.section] = {};
				}
				Permissions[Permission.section][Permission.permission] = Permission;
			});

			return Permissions;
		};

		this.checkPermission = function(section, permission) {
			return this.getAuthUser().then(function(AuthUser) {
				if (AuthUser.admin) {
					return;
				}

				return that.getPerms().then(function(permissions) {
					if (!permissions[section] || !permissions[section][permission] || !permissions[section][permission].access) {
						$location.path('/403');
					}
				});
			});
		};

		this.getSections = function() {
			return [
				{
					name: 'Budgets',
					value: 'budgets',
					create: true,
					delete: true,
					edit: true,
					view: true
				},
				{
					name: 'Budget Templates',
					value: 'budget_templates',
					create: true,
					delete: true,
					edit: true,
					view: true
				},
				{
					name: 'Share Budgets',
					value: 'budget_sharing',
					create: true,
					delete: true,
					edit: false,
					view: true
				},
				{
					name: 'Expenses',
					value: 'expenses',
					create: true,
					delete: true,
					edit: true,
					view: true
				},
				{
					name: 'Expense Categories',
					value: 'expense_categories',
					create: true,
					delete: true,
					edit: true,
					view: true
				},
				{
					name: 'Import Income and Expenses',
					value: 'import',
					create: false,
					delete: false,
					edit: false,
					view: true
				},
				{
					name: 'Income',
					value: 'income',
					create: true,
					delete: true,
					edit: true,
					view: true
				},
				{
					name: 'Income Categories',
					value: 'income_categories',
					create: true,
					delete: true,
					edit: true,
					view: true
				},
				{
					name: 'Settings',
					value: 'settings',
					create: false,
					delete: false,
					edit: false,
					view: true
				},
				{
					name: 'User Management',
					value: 'user_management',
					create: true,
					delete: true,
					edit: true,
					view: true
				},
				{
					name: 'Color Scheme',
					value: 'color_scheme',
					create: false,
					delete: false,
					edit: true,
					view: true
				},
			];
		};
	}
])
.service('LoadingService', [
	function() {
		var numberOfRequests = 0;
		var finishedRequests = 0;
		var previousNumberOfRequests = 0;
		var previousFinishedRequests = 0;

		function start(numRequests) {
			if (numRequests > 0) {
				numberOfRequests = numRequests;
			} else {
				numberOfRequests = 1;
			}
			previousNumberOfRequests = numberOfRequests;
			previousFinishedRequests = 0;
		}

		function done() {
			++previousFinishedRequests;
			if (++finishedRequests >= numberOfRequests) {
				numberOfRequests = finishedRequests = 0;
				return true;
			}
			return false;
		}

		function percentComplete() {
			return (previousFinishedRequests / previousNumberOfRequests) * 100;
		}

		return {
			start: start,
			done: done,
			percentComplete: percentComplete
		};
	}
])
.service('DateService', [
	function() {
		this.getDate = function(date) {
			if (date instanceof Date) {
				return date;
			}
			return new Date(date.replace(/-/g, '/'));
		};
	}
])
;