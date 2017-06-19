angular.module('SettingsControllers', [])
.controller('SettingsController', ['TitleService',
	function(TitleService) {
		TitleService.setTitle('Settings');
	}
])
.controller('ColorsController', ['$rootScope', '$scope', 'ColorsService', 'TitleService',
	function($rootScope, $scope, ColorsService, TitleService) {
		TitleService.setTitle('Color Scheme');

		$scope.colors = [];
		angular.forEach(ColorsService.getColors(), function(color) {
			$scope.colors.push({
				name: color,
				active: color === $rootScope.bootswatch
			});
		});

		$scope.setActiveColor = function(newColor) {
			angular.forEach($scope.colors, function(color) {
				color.active = color.name === newColor.name;
			});
			$rootScope.bootswatch = newColor.name;

			ColorsService.updateColor(newColor.name, { ignoreLoadingBar: true });
		};
	}
])
.controller('UsersController', ['Users', '$scope', 'TitleService', 'UsersService', '$uibModal',
	function(Users, $scope, TitleService, UsersService, $uibModal) {
		TitleService.setTitle('User Management');
		$scope.Users = Users;

		$scope.deleteUser = function(User) {
			var modalInstance = $uibModal.open({
				template: require('../../../pug/views/settings/users/modals/user-delete.pug'),
				controller: 'UserDeleteController',
				scope: $scope,
				resolve: {
					User: User
				}
			});

			modalInstance.result.then(function(DeletedUser) {
				//setMessage()
				var index = $scope.Users.map(function(User) {
					return User.id;
				}).indexOf(DeletedUser.id);
				$scope.Users.splice(index, 1);
			});
		};
	}
])
.controller('UserCreateController', ['$scope', 'TitleService', 'UsersService', '$location',
	function($scope, TitleService, UsersService, $location) {
		var showPasswordVar = false;
		var showRepeatPasswordVar = false;
		var showPinVar = false;
		var showRepeatPinVar = false;

		$scope.breadcrumb = 'Create New User';
		$scope.title = 'Create A New User';
		TitleService.setTitle($scope.title);
		$scope.User = {};
		$scope.creatingUser = true;

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

		function hidePin() {
			showPinVar = false;
			$scope.pinType = 'password';
			$scope.showHidePinText = 'Show Pin';
		}
		function showPin() {
			showPinVar = true;
			$scope.pinType = 'text';
			$scope.showHidePinText = 'Hide Pin';
		}
		hidePin();

		function hideRepeatPin() {
			showRepeatPinVar = false;
			$scope.repeatPinType = 'password';
			$scope.showHideRepeatPinText = 'Show Pin';
		}
		function showRepeatPin() {
			showRepeatPinVar = true;
			$scope.repeatPinType = 'text';
			$scope.showHideRepeatPinText = 'Hide Pin';
		}
		hideRepeatPin();

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

		$scope.showHidePin = function() {
			if (showPinVar) {
				hidePin();
			} else {
				showPin();
			}
		};

		$scope.showHideRepeatPin = function() {
			if (showRepeatPinVar) {
				hideRepeatPin();
			} else {
				showRepeatPin();
			}
		};

		$scope.submit = function() {
			UsersService.createUser($scope.User, { ignoreLoadingBar: true }).then(function() {
				$location.path('/settings/users');
			});
		};
	}
])
.controller('UserEditController', ['User', '$scope', 'TitleService', 'UsersService', '$location',
	function(User, $scope, TitleService, UsersService, $location) {
		$scope.breadcrumb = $scope.title = 'Edit User';
		TitleService.setTitle($scope.title);
		$scope.creatingUser = false;
		$scope.User = User;

		$scope.submit = function() {
			UsersService.editUser($scope.User, { ignoreLoadingBar: true }).then(function() {
				$location.path('/settings/users');
			}, function() {

			});
		};
	}
])
.controller('UserPasswordController', ['User', '$scope', 'TitleService', 'UsersService', '$location',
	function(User, $scope, TitleService, UsersService, $location) {
		var showPasswordVar = false;
		var showRepeatPasswordVar = false;

		$scope.breadcrumb = $scope.title = 'Change Password';
		TitleService.setTitle($scope.title);
		$scope.creatingUser = false;

		$scope.User = User;

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
			UsersService.changePassword($scope.User, { ignoreLoadingBar: true }).then(function() {
				$location.path('/settings/users');
			}, function() {

			});
		};
	}
])
.controller('UserPinController', ['User', '$scope', 'TitleService', 'UsersService', '$location',
	function(User, $scope, TitleService, UsersService, $location) {
		var showPinVar = false;
		var showRepeatPinVar = false;

		$scope.breadcrumb = $scope.title = 'Change Pin';
		TitleService.setTitle($scope.title);
		$scope.creatingUser = false;

		$scope.User = User;

		function hidePin() {
			showPinVar = false;
			$scope.pinType = 'password';
			$scope.showHidePinText = 'Show Pin';
		}
		function showPin() {
			showPinVar = true;
			$scope.pinType = 'text';
			$scope.showHidePinText = 'Hide Pin';
		}
		hidePin();

		function hideRepeatPin() {
			showRepeatPinVar = false;
			$scope.repeatPinType = 'password';
			$scope.showHideRepeatPinText = 'Show Pin';
		}
		function showRepeatPin() {
			showRepeatPinVar = true;
			$scope.repeatPinType = 'text';
			$scope.showHideRepeatPinText = 'Hide Pin';
		}
		hideRepeatPin();

		$scope.showHidePin = function() {
			if (showPinVar) {
				hidePin();
			} else {
				showPin();
			}
		};

		$scope.showHideRepeatPin = function() {
			if (showRepeatPinVar) {
				hideRepeatPin();
			} else {
				showRepeatPin();
			}
		};

		$scope.submit = function() {
			UsersService.changePin($scope.User, { ignoreLoadingBar: true }).then(function() {
				$location.path('/settings/users');
			}, function() {

			});
		};
	}
])
.controller('UserPermissionsController', ['User', 'Permissions', '$scope', 'TitleService', 'UsersService', 'PermissionService',
	function(User, Permissions, $scope, TitleService, UsersService, PermissionService) {
		TitleService.setTitle('User Permissions');
		$scope.creatingUser = false;

		$scope.User = User;
		$scope.User.Permissions = Permissions;
		$scope.Sections = PermissionService.getSections();

		$scope.clickColumn = function(canDoIt, section, permission) {
			if (!canDoIt) {
				return;
			}

			if (!$scope.User.Permissions[section]) {
				$scope.User.Permissions[section] = {};
			}
			if (!$scope.User.Permissions[section][permission]) {
				$scope.User.Permissions[section][permission] = {
					access: false,
					permission: permission,
					section: section,
					userId: $scope.User.id
				};
			}

			$scope.User.Permissions[section][permission].access = !$scope.User.Permissions[section][permission].access;
			if ($scope.User.Permissions[section][permission].id) {
				UsersService.updatePermission($scope.User.Permissions[section][permission], { ignoreLoadingBar: true });
			} else {
				UsersService.createPermission($scope.User.Permissions[section][permission], { ignoreLoadingBar: true }).then(function(UserPermission) {
					$scope.User.Permissions[section][permission] = UserPermission;
				});
			}
		};
	}
])
.controller('UserDeleteController', ['$scope', '$uibModalInstance', 'User', 'UsersService',
	function($scope, $uibModalInstance, User, UsersService) {
		$scope.name = User.name;

		$scope.close = $scope.cancel = function() {
			$uibModalInstance.dismiss();
		};

		$scope.delete = function() {
			UsersService.deleteUser(User.id, { ignoreLoadingBar: true }).then(function() {
				$uibModalInstance.close(User);
			});
		};
	}
])
;