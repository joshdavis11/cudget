import {
	copy,
	forEach
} from 'angular';

import moment from 'moment';

angular.module('HelperServices', [])
.service('AuthenticationService', ['$location',
	function($location) {
		let AuthUser = window.AuthUser;

		this.isAuthenticated = function() {
			return AuthUser.id > 0;
		};

		this.redirectIfNotAuthenticated = function() {
			let authenticated = this.isAuthenticated();
			if(!authenticated) {
				$location.path('/login');
			}

			return authenticated;
		};

		this.redirectIfAuthenticated = function() {
			let authenticated = this.isAuthenticated();
			if(authenticated) {
				$location.path('/home');
			}

			return authenticated;
		};
	}
])
.service('AuthenticationHttpService', ['$http', '$location',
	function($http) {
		this.isAuthenticatedNow = function() {
			return $http.get('/web/authenticated').then(function(response) {
				return response && response.data ? response.data.authenticated : {};
			});
		};
	}
])
.service('PermissionService', ['$http', '$location',
	function($http, $location) {
		let AuthUser = window.AuthUser;
		let myPerms = window.perms;
		let myFormattedPerms = null;

		this.getAuthUser = function() {
			return AuthUser;
		};

		this.getMyUserPermsFormatted = function() {
			if (!myFormattedPerms) {
				if (!myPerms) {
					this.getMyUserPerms().then(function(permissions) {
						myPerms = permissions;
						myFormattedPerms = this.formatPermissions(permissions);
					});
				} else {
					myFormattedPerms = this.formatPermissions(myPerms);
				}
			}

			return copy(myFormattedPerms);
		};

		this.getMyUserPerms = function() {
			return $http.get('/api/v1/myperms').then(function(response) {
				return response ? response.data : {};
			});
		};

		this.getUserPermsFormatted = function(userId) {
			let that = this;
			return this.getUserPerms(userId).then(function(permissions) {
				return that.formatPermissions(permissions);
			});
		};

		this.getUserPerms = function(userId) {
			return $http.get('/api/v1/users/' + userId + '/permissions').then(function(response) {
				return response ? response.data : {};
			});
		};

		this.formatPermissions = function(Perms) {
			let Permissions = {};
			forEach(Perms, function(Permission) {
				Permissions[Permission.permission.definition] = true;
			});

			return Permissions;
		};

		this.updateAuthUser = function() {
			$http.get('/api/v1/auth/user').then(function(response) {
				AuthUser = response.data;
			});
		};

		this.resetMyPerms = function() {
			let that = this;
			this.getPerms().then(function(permissions) {
				window.perms = myPerms = permissions;
				myFormattedPerms = that.formatPermissions(myPerms);
			});
		};

		this.checkPermission = function(section, adminOnly) {
			if (this.getAuthUser().admin) {
				return;
			}

			if (adminOnly || !this.getMyUserPermsFormatted()[section]) {
				$location.path('/403');
			}
		};

		this.getPermissions = function() {
			return $http.get('/api/v1/permissions').then(function(response) {
				return response ? response.data : {};
			});
		};

		this.deleteUserPermission = function(UserPermission, options) {
			let that = this;
			return $http.delete('/api/v1/users/' + UserPermission.userId + '/permissions/' + UserPermission.id, options).then(function(response) {
				if (that.getAuthUser().id === UserPermission.userId) {
					that.resetMyPerms();
				}
				return response ? response.data : {};
			});
		};

		this.createUserPermission = function(UserPermission, options) {
			let that = this;
			return $http.post('/api/v1/users/' + UserPermission.userId + '/permissions', UserPermission, options).then(function(response) {
				if (that.getAuthUser().id === UserPermission.userId) {
					that.resetMyPerms();
				}
				return response ? response.data : {};
			});
		};
	}
])
.service('LoadingService', [
	function() {
		let numberOfRequests = 0;
		let finishedRequests = 0;
		let previousNumberOfRequests = 0;
		let previousFinishedRequests = 0;

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

		this.getLocalDatetime = function(date) {
			return moment.utc(date).local().format('YYYY-MM-DD HH:mm:ss')
		};
	}
])
.service('CSRFService', ['$http',
	function($http) {
		this.getCSRF = function() {
			return window.csrf;
		};

		this.resetCSRF = function() {
			$http.get('/web/csrf').then((response) => {
				if (response.data.csrf !== window.csrf) {
					window.csrf = response.data.csrf;
					$http.defaults.headers.common['X-CSRF-TOKEN'] = window.csrf;
				}
			})
		};
	}
])
;