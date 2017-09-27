angular.module('CudgetInterceptors', [])
.factory('HttpInterceptor', ['AssetsService',
	function(AssetsService) {
		return {
			response: function(response) {
				AssetsService.checkForJsUpdates(response);

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