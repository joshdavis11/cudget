angular.module('BaseControllers', [])
.controller('MasterController', ['$scope', '$timeout', 'uibPaginationConfig', '$location',
	function($scope, $timeout, uibPaginationConfig, $location) {
		$scope.loading = false;
		var loading = false;

		$scope.$on('cfpLoadingBar:started', function() {
			loading = true;
			$timeout(function() {
				if (loading) {
					$scope.loading = true;
				}
			}, 200);
		});
		$scope.$on('cfpLoadingBar:completed', function() {
			$scope.loading = false;
			loading = false;
		});

		$scope.itemsPerPage = 10;
		uibPaginationConfig.maxSize = 5;
		uibPaginationConfig.boundaryLinks = true;
		uibPaginationConfig.previousText = '‹';
		uibPaginationConfig.nextText = '›';
		uibPaginationConfig.firstText = '«';
		uibPaginationConfig.lastText = '»';
		
		$scope.uibPageChange = function(currentPage) {
			$location.search('page', currentPage);
		}
	}
])
//Has to be $rootScope so color controller can update
.controller('HeadController', ['$rootScope',
	function($rootScope) {
		$rootScope.bootswatch = window.bootswatch;
	}
])
.controller('HeaderController', ['$rootScope', '$scope', '$location', 'PermissionService', 'HeaderService',
	function($rootScope, $scope, $location, PermissionService, HeaderService) {
		PermissionService.getPerms().then(function(perms) {
			$rootScope.perms = perms;

			PermissionService.getAuthUser().then(function(AuthUser) {
				$rootScope.perms.admin = AuthUser.id > 0 ? AuthUser.admin : false;
			});
		});

		$scope.isNavCollapsed = true;
		$scope.isActiveBaseUri = function(baseUri) {
			return HeaderService.isActiveBaseUri($location, baseUri);
		};

		$scope.isActiveUri = function(baseUri) {
			return HeaderService.isActiveUri($location, baseUri);
		};
	}
])
.controller('PreLoginHeaderController', ['$scope', '$location', 'HeaderService',
	function($scope, $location, HeaderService) {
		$scope.isNavCollapsed = true;
		$scope.isActiveBaseUri = function(baseUri) {
			return HeaderService.isActiveBaseUri($location, baseUri);
		};

		$scope.isActiveUri = function(baseUri) {
			return HeaderService.isActiveUri($location, baseUri);
		};
	}
])
.controller('FooterController', ['$scope',
	function($scope) {
	}
])
.controller('HomeController', ['TitleService',
	function(TitleService) {
		TitleService.setTitle('Home');
	}
])
.controller('LoginController', ['TitleService', '$scope',
	function(TitleService, $scope) {
		TitleService.setTitle('Login');
		$scope.csrf = window.csrf;
	}
])
.controller('PreLoginController', ['TitleService', '$scope',
	function(TitleService, $scope) {
		TitleService.setTitle('Cudget - The Custom Budgeting App!');
		$scope.csrf = window.csrf;
	}
])
.controller('SignUpController', ['TitleService', '$scope', '$http', '$window',
	function(TitleService, $scope, $http, $window) {
		let showPasswordVar = false;
		let showRepeatPasswordVar = false;

		TitleService.setTitle('Sign Up');
		$scope.User = {};
		$scope.creatingUser = true;
		$scope.submitDisabled = false;

		function hidePassword() {
			showPasswordVar = false;
			$scope.passwordType = 'password';
			$scope.showHidePasswordText = 'Show Password';
		}
		function showPassword() {
			showPasswordVar = true;
			$scope.passwordType = 'text';
			$scope.showHidePasswordText = 'Hide Password';
		}
		hidePassword();

		function hideRepeatPassword() {
			showRepeatPasswordVar = false;
			$scope.repeatPasswordType = 'password';
			$scope.showHideRepeatPasswordText = 'Show Password';
		}
		function showRepeatPassword() {
			showRepeatPasswordVar = true;
			$scope.repeatPasswordType = 'text';
			$scope.showHideRepeatPasswordText = 'Hide Password';
		}
		hideRepeatPassword();

		$scope.showHidePassword = function() {
			if (showPasswordVar) {
				hidePassword();
			} else {
				showPassword();
			}
		};

		$scope.showHideRepeatPassword = function() {
			if (showRepeatPasswordVar) {
				hideRepeatPassword();
			} else {
				showRepeatPassword();
			}
		};

		$scope.submit = function() {
			$http.post('/api/v1/signup', $scope.User).then(function() {
				$scope.submitDisabled = true;
				$http.post('/login', $scope.User).then(function() {
					$window.location.href = '/home';
				}, function() {
					$scope.submitDisabled = false;
				});
			}, function() {
				$scope.submitDisabled = false;
			});
		};
	}
])
.controller('404Controller', ['TitleService', 'AuthenticationService', '$scope',
	function(TitleService, AuthenticationService, $scope) {
		TitleService.setTitle('404 Page Not Found');
		$scope.url = '/';
		AuthenticationService.isAuthenticated().then(function(authenticated) {
			$scope.url = authenticated ? '/home' : '/';
		});
	}
])
;