import {
	isEmpty,
	isString
} from 'lodash';

import {
	forEach
} from 'angular';

import moment from 'moment';

angular.module('BaseControllers', [])
.controller('MasterController', ['$scope', '$timeout', 'uibPaginationConfig', '$location', '$window', 'AuthenticationService', 'CSRFService',
	function($scope, $timeout, uibPaginationConfig, $location, $window, AuthenticationService, CSRFService) {
		$scope.loading = false;
		let loading = false;

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
		};

		let currentDateTime = moment();
		const sessionExpireMinutes = 120;
		function checkAuthenticationStatus() {
			let newCurrentDateTime = moment();
			if (currentDateTime <= newCurrentDateTime.subtract(sessionExpireMinutes, 'minutes')) {
				currentDateTime = newCurrentDateTime.add(sessionExpireMinutes, 'minutes').clone();
				if (AuthenticationService.isAuthenticated()) {
					AuthenticationService.isAuthenticatedNow().then(function(authenticated) {
						//If not authenticated, redirect to login
						if (!authenticated) {
							$window.location.href = '/login';
						}
						//If we are still authenticated, reset the CSRF token because we're probably using the remember token
						else {
							CSRFService.resetCSRF();
						}
					});
				}
				//If not authenticated, just reload the page to get the new CSRF token
				else {
					$window.location.reload();
				}
			}
		}

		$window.onfocus = function() {
			checkAuthenticationStatus();
		};
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
.controller('LoginController', ['TitleService', '$scope', 'CSRFService',
	function(TitleService, $scope, CSRFService) {
		TitleService.setTitle('Cudget - Login');
		$scope.csrf = CSRFService.getCSRF();
	}
])
.controller('PreLoginController', ['TitleService', '$scope',
	function(TitleService, $scope) {
		TitleService.setTitle('Cudget');
	}
])
.controller('SignUpController', ['TitleService', '$scope', '$http', '$location', 'MessageService',
	function(TitleService, $scope, $http, $location, MessageService) {
		TitleService.setTitle('Cudget - Sign Up');
		$scope.User = {};
		$scope.create = 'Create Account';
		$scope.creatingUser = true;
		$scope.submitDisabled = false;
		$scope.cancel = '/';

		$scope.submit = function() {
			$scope.submitDisabled = true;
			$http.post('/api/v1/signup', $scope.User).then(function() {
				MessageService.message('You will receive an email shortly containing a verification link. Please wait until you\'ve received this email before logging in.').info();
				$location.path('/login');
			}, function() {
				MessageService.message('Something wasn\'t quite right there... Please fix any errors and try again.').error();
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