angular.module('SettingsServices', [])
.service('ColorsService', ['$http',
	function($http) {
		this.getColors = function() {
			return [
				'cerulean',
				'cosmo',
				'cyborg',
				'darkly',
				'flatly',
				'journal',
				'lumen',
				'paper',
				'readable',
				'sandstone',
				'simplex',
				'slate',
				'spacelab',
				'superhero',
				'united',
				'yeti'
			];
		};

		this.updateColor = function(color, options) {
			return $http.put('/api/v1/settings/configuration', {bootswatch: color}, options).then(function(response) {
				return response.data;
			});
		};
	}
])
.service('UsersService', ['$http', 'PermissionService',
	function($http, PermissionService) {
		this.getUsers = function() {
			return $http.get('/api/v1/users').then(function(response) {
				return response.data;
			});
		};

		this.getUser = function(id) {
			return $http.get('/api/v1/users/' + id).then(function(response) {
				return response.data;
			});
		};

		this.createUser = function(User, options) {
			return $http.post('/api/v1/users', User, options);
		};

		this.editUser = function(User, options) {
			return $http.put('/api/v1/users/' + User.id, User, options);
		};

		this.changePassword = function(User, options) {
			return $http.put('/api/v1/users/' + User.id + '/password', User, options);
		};

		this.changePin = function(User, options) {
			return $http.put('/api/v1/users/' + User.id + '/pin', User, options);
		};

		this.deleteUser = function(id, options) {
			return $http.delete('/api/v1/users/' + id, options);
		};

		this.getPermissions = function(userId) {
			return $http.get('/api/v1/users/' + userId + '/permissions').then(function(response) {
				return response.data;
			});
		};

		this.getFormattedPermissions = function(userId) {
			return this.getPermissions(userId).then(function(Perms) {
				return PermissionService.formatPermissions(Perms);
			});
		};

		this.updatePermission = function(UserPermission, options) {
			return $http.put('/api/v1/users/' + UserPermission.userId + '/permissions/' + UserPermission.id, UserPermission, options).then(function(response) {
				return response.data;
			});
		};

		this.createPermission = function(UserPermission, options) {
			return $http.post('/api/v1/users/' + UserPermission.userId + '/permissions', UserPermission, options).then(function(response) {
				return response.data;
			});
		};
	}
])
;