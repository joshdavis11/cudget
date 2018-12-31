angular.module('CudgetInterceptors', [])
.factory('HttpInterceptor', ['AssetsService', 'AuthenticationService', '$location',
	function(AssetsService, AuthenticationService, $location) {
		return {
			response: function(response) {
				if (AuthenticationService.isAuthenticated()) {
					AssetsService.checkForJsUpdates(response);
				}

				return response;
			},
			responseError: function(response) {
				switch (response.status) {
					case 401:
					case 500:
						window.location.reload();
						break;
					case 403:
						$location.path('/403');
						break;
				}

				return response;
			}
		};
	}
])
;