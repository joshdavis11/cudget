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
.controller('HeaderController', ['$rootScope', '$scope', 'PermissionService', '$location',
	function($rootScope, $scope, PermissionService, $location) {
		PermissionService.getPerms().then(function(perms) {
			$rootScope.perms = perms;

			PermissionService.getAuthUser().then(function(AuthUser) {
				$rootScope.perms.admin = AuthUser.admin;
			});
		});

		$scope.isNavCollapsed = true;
		$scope.isActiveBaseUri = function(baseUri) {
			if (baseUri === '/') {
				return $location.path() === baseUri;
			}
			return $location.path().indexOf(baseUri) > -1;
		};

		$scope.isActiveUri = function(baseUri) {
			return $location.path() === baseUri;
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
.controller('404Controller', ['TitleService',
	function(TitleService) {
		TitleService.setTitle('404 Page Not Found');
	}
])
;