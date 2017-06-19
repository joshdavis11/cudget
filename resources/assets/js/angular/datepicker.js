angular.module('CudgetDatepicker', [])
.config(['uibDatepickerConfig', 'uibDatepickerPopupConfig',
	function(uibDatepickerConfig, uibDatepickerPopupConfig) {
		uibDatepickerConfig.formatDay = 'dd';
		uibDatepickerConfig.formatMonth = 'MM';
		uibDatepickerConfig.formatYear = 'yyyy';
		uibDatepickerConfig.showWeeks = false;

		uibDatepickerPopupConfig.showButtonBar = false;
		uibDatepickerPopupConfig.onOpenFocus = false;
	}]
);