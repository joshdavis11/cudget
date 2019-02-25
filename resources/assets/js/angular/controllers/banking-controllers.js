angular.module('BankingControllers', [])
.controller('AccountsController', ['$scope', '$http', 'TitleService',
	function($scope, $http, TitleService) {
		TitleService.setTitle('Accounts');
		$scope.Institutions = [];
		$http.get('/api/v1/banking/accounts')
			.then(function(response) {
				$scope.Institutions = response.data;
			});

		$scope.updateIncludeInUpdates = function(Account) {
			$http.post('/api/v1/banking/accounts/' + Account.id, {
				"includeInUpdates": Account.includeInUpdates
			});
		};

		$scope.fixAccount = function(plaidDataId) {
			$http.get('/api/v1/banking/publicToken/' + plaidDataId, { ignoreLoadingBar: true }).then(function(response) {
				window.Plaid.create({
					clientName: 'Cudget',
					env: window.PlaidData.env,
					key: window.PlaidData.publicKey,
					product: ['transactions'],
					token: response.data,
					onSuccess: function(publicToken, metadata) {
					},
				}).open();
			});
		};
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
.controller('UpdateController', ['$http', 'TitleService', '$location',
	function($http, TitleService, $location) {
		TitleService.setTitle('Update Income and Expenses');
		$http.post('/api/v1/banking/update')
			.then(function(response) {
				$location.url(response.data.budgetId ? '/budgets/' + response.data.budgetId : '/expenses?filter=without');
			});
	}
])
;