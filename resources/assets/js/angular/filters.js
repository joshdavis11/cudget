angular.module('CudgetFilters', [])
.filter('mysqlDateToISO8601', ['DateService',
	function(DateService) {
		return function(input) {
			return DateService.getDate(input).toISOString();
		};
	}
])
.filter('localDatetime', ['DateService',
	function(DateService) {
		return function(input) {
			return DateService.getLocalDatetime(input);
		};
	}
])
.filter('standardDate', ['$filter',
	function($filter) {
		return function(input) {
			if (input) {
				return $filter('date')($filter('mysqlDateToISO8601')(input), 'MM/dd/yyyy');
			}
			return input;
		};
	}
])
.filter('standardDatetime', ['$filter',
	function($filter) {
		return function(input) {
			if (input) {
				return $filter('date')($filter('mysqlDateToISO8601')(input), 'MM/dd/yyyy H:mm:ss');
			}
			return input;
		};
	}
])
.filter('mediumDatetime', ['$filter',
	function($filter) {
		return function(input) {
			if (input) {
				return $filter('date')($filter('mysqlDateToISO8601')(input), 'MMM d, y h:mm:ss a');
			}
			return input;
		};
	}
])
;