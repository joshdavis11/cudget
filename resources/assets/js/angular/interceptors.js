angular.module('CudgetInterceptors', [])
.factory('HttpInterceptor', ['AssetsService', 'AuthenticationService',
	function(AssetsService, AuthenticationService) {
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
						window.location.reload();
						break;
				}
			}
		};
	}
])
;