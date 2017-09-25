angular.module('CudgetConfig', [])
.config(['cfpLoadingBarProvider', function(cfpLoadingBarProvider) {
	cfpLoadingBarProvider.includeSpinner = false;
	cfpLoadingBarProvider.parentSelector = '#loader';
	cfpLoadingBarProvider.loadingBarTemplate = '<div class="progress progress-striped active"><div class="progress-bar"></div>';
}])
.config(['$httpProvider', function($httpProvider) {
	$httpProvider.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
	$httpProvider.defaults.headers.common['X-CSRF-TOKEN'] = window.csrf;
	$httpProvider.interceptors.push(function($q) {
		return {
			responseError: function(response) {
				switch (response.status) {
					case 401:
						window.location.reload();
						break;
				}
			}
		};
	});
}]);