import {
	forEach
} from 'angular';

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
		$scope.breadcrumb = 'Create New User';
		$scope.title = 'Create A New User';
		TitleService.setTitle($scope.title);
		$scope.User = {};
		$scope.create = 'Create New User';
		$scope.creatingUser = true;
		$scope.cancel = '/settings/users';

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
		$scope.create = 'Save Changes';
		$scope.creatingUser = false;
		$scope.User = User;
		$scope.cancel = '/settings/users';

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
.controller('UserPermissionsController', ['User', 'UserPermissions', '$scope', 'TitleService', 'PermissionService',
	function(User, UserPermissions, $scope, TitleService, PermissionService) {
		TitleService.setTitle('User Permissions');
		$scope.creatingUser = false;

		$scope.User = User;
		$scope.User.Permissions = {};
		forEach(UserPermissions, function(UserPermission) {
			$scope.User.Permissions[UserPermission.permission.definition] = UserPermission;
		});

		PermissionService.getPermissions().then(function(permissions) {
			$scope.Permissions = permissions;
			forEach($scope.Permissions, function(Permission) {
				Permission.clicked = User.admin || ($scope.User.Permissions[Permission.definition] && $scope.User.Permissions[Permission.definition].id > 0);
			});
		});

		$scope.clickColumn = function(Permission) {
			if (!$scope.User.Permissions[Permission.definition]) {
				$scope.User.Permissions[Permission.definition] = {
					permission: Permission,
					permissionId: Permission.id,
					userId: $scope.User.id,
				};
			}

			// Permission.clicked = !Permission.clicked;
			if($scope.User.Permissions[Permission.definition].id) {
				PermissionService.deleteUserPermission($scope.User.Permissions[Permission.definition], { ignoreLoadingBar: true }).then(function() {
					$scope.User.Permissions[Permission.definition] = null;
				});
			}
			else {
				PermissionService.createUserPermission($scope.User.Permissions[Permission.definition], { ignoreLoadingBar: true }).then(function(UserPermission) {
					$scope.User.Permissions[Permission.definition].id = UserPermission.id;
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
.controller('UserFormController', ['$scope', 'UsersService', 'MessageService',
	function($scope, UsersService, MessageService) {
		let showPasswordVar = false;
		let showRepeatPasswordVar = false;

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

		$scope.emailExists = false;
		$scope.checkEmailExists = function() {
			if (!$scope.User.email) {
				$scope.emailExists = false;
				return;
			}
			UsersService.checkEmailExists($scope.User.email).then((response) => {
				$scope.emailExists = response.data.exists;
				$scope.userForm.email.$setValidity('duplicate', !$scope.emailExists);
			});
		};

		$scope.usernameExists = false;
		$scope.checkUsernameExists = function() {
			if (!$scope.User.username) {
				$scope.usernameExists = false;
				return;
			}
			UsersService.checkUsernameExists($scope.User.username).then((response) => {
				$scope.usernameExists = response.data.exists;
				$scope.userForm.username.$setValidity('duplicate', !$scope.usernameExists);
			});
		};

		$scope.valid = function() {
			if (!$scope.userForm.$valid) {
				MessageService.message('Something wasn\'t quite right there... Please fix any errors and try again.').error();
				return false;
			}

			return true;
		};
	}
])
;