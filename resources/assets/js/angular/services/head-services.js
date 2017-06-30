angular.module('HeadServices', [])
.service('TitleService', ['$rootScope',
	function($rootScope) {
		this.setTitle = function(title) {
			$rootScope.title = title;
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
;