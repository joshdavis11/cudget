import {
	includes
} from 'lodash';

angular.module('HeadServices', [])
.service('TitleService', ['$rootScope',
	function($rootScope) {
		this.setTitle = function(title) {
			$rootScope.title = title;
		};
	}
])
.service('AssetsService', ['MessageService', '$sce',
	function(MessageService, $sce) {
		this.checkForJsUpdates = function(response) {
			let appJs = response.headers('Cudget-app.js');
			let manifestJs = response.headers('Cudget-manifest.js');
			let vendorJs = response.headers('Cudget-vendor.js');
			if (
				(appJs && !document.getElementById('cudget-app-js').src.includes(appJs)) ||
				(manifestJs && !document.getElementById('cudget-manifest-js').src.includes(manifestJs)) ||
				(vendorJs && !document.getElementById('cudget-vendor-js').src.includes(vendorJs))
			) {
				MessageService.message($sce.trustAsHtml('A new version of the app is available. Please <a onclick="window.location.reload();">reload</a>.')).warning();
			}
		};
	}
])
.service('HeaderService', ['$rootScope',
	function() {
		this.isActiveBaseUri = function($location, baseUri) {
			if (baseUri === '/') {
				return $location.path() === baseUri;
			}
			return $location.path().indexOf(baseUri) > -1;
		};

		this.isActiveUri = function($location, baseUri) {
			return $location.path() === baseUri;
		};
	}
])
.service('MessageService', ['$rootScope',
	function() {
		let data = {
			showMessage: false,
			messageClass: null,
			message: '',
			html: ''
		};

		this.getData = function() {
			return data;
		};

		this.message = function(message) {
			data.message = message;

			return this;
		};

		function show(classAfterDash) {
			data.messageClass = 'alert-' + classAfterDash;
			data.showMessage = true;

			return this;
		}

		this.error = function() {
			return show('danger');
		};

		this.info = function() {
			return show('info');
		};

		this.success = function() {
			return show('success');
		};

		this.warning = function() {
			return show('warning');
		};
	}
])
;