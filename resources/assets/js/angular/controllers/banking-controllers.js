angular.module('BankingControllers', [])
.controller('UpdateController', ['$http', 'TitleService', '$location',
	function($http, TitleService, $location) {
		TitleService.setTitle('Update Income and Expenses');
		$http.post('/api/v1/banking/update')
			.then(function(response) {
				console.log(response.headers('Location'));
				$location.url(response.headers('Location') || '/expenses?filter=without');
			});
	}
])
.controller('ImportController', ['$scope', '$http', 'TitleService', 'Upload', '$location',
	function($scope, $http, TitleService, Upload, $location) {
		TitleService.setTitle('Import Income and Expenses');
		$scope.Import = {};
		$scope.submitDisabled = false;
		$http.get('/api/v1/banking/import/accounts')
			.success(function(importAccounts) {
				$scope.ImportTypes = importAccounts || [];
			})
			.finally(function() {
				$scope.ImportTypes.push({"id": "other", "name": "Other"});
			});

		$scope.submit = function() {
			$scope.submitDisabled = true;
			Upload.upload({
				url: '/api/v1/banking/import',
				data: $scope.Import,
				ignoreLoadingBar: true
			}).then(function(response) {
				$location.path(response.headers('Location') || '/budgets');
			}, function() {
				$scope.submitDisabled = false;
			});
		};
	}
])
;