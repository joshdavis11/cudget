import {
	isEmpty,
	isString
} from 'lodash';

import {
	forEach
} from 'angular';

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
		$rootScope.perms = PermissionService.getMyUserPermsFormatted();

		let AuthUser = PermissionService.getAuthUser();
		$rootScope.perms.admin = AuthUser.id > 0 ? AuthUser.admin : false;

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
.controller('SignUpController', ['TitleService', '$scope', '$http', '$location', 'MessageService',
	function(TitleService, $scope, $http, $location, MessageService) {
		let showPasswordVar = false;
		let showRepeatPasswordVar = false;

		TitleService.setTitle('Sign Up');
		$scope.User = {};
		$scope.create = 'Create Account';
		$scope.creatingUser = true;
		$scope.submitDisabled = false;
		$scope.cancel = '/';

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
			$scope.submitDisabled = true;
			$http.post('/api/v1/signup', $scope.User).then(function() {
				MessageService.message('You will receive an email shortly containing a verification link. Please wait until you\'ve received this email before logging in.').info();
				$location.path('/login');
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
		$scope.url = AuthenticationService.isAuthenticated() ? '/home' : '/';
	}
])
.controller('MessageController', ['$scope', 'MessageService',
	function($scope, MessageService) {
		$scope.data = MessageService.getData();
		$scope.closeMessage = function() {
			$scope.data.showMessage = false;
		};

		function getMessageInfo(messages) {
			let pageLoadMessage = '';
			forEach(messages, function(arrayOfMessages) {
				if(isString(arrayOfMessages)) {
					pageLoadMessage += arrayOfMessages + '<br />';
				}
				else {
					forEach(arrayOfMessages, function(message) {
						pageLoadMessage += message + '<br />';
					});
				}
			});

			return pageLoadMessage;
		}


		if(!isEmpty(window.info)) {
			MessageService.message(window.info).info();
		}

		if(!isEmpty(window.errors)) {
			let pageLoadMessage = getMessageInfo(window.errors);

			MessageService.message(pageLoadMessage).error();
		}
	}
])
;