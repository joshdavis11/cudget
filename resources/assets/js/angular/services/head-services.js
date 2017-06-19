angular.module('HeadServices', [])
.service('TitleService', ['$rootScope',
	function($scope) {
		this.setTitle = function(title) {
			$scope.title = title;
		};
	}
])
;