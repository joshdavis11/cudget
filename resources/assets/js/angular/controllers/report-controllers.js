import moment from 'moment';
import {forEach} from 'angular';
import {
	format,
	time
} from 'd3';

angular.module('ReportControllers', [])
.controller('BudgetDailyExpensesReportController', ['$scope', 'Budgets', 'BudgetService', 'TitleService',
	function($scope, Budgets, BudgetService, TitleService) {
		$scope.reportTitle = 'Budget Daily Expenses Report';
		TitleService.setTitle($scope.reportTitle);
		$scope.Budget = { id: null };
		$scope.Budgets = Budgets;

		$scope.budgetChange = function() {
			if ($scope.Budget && $scope.Budget.id) {
				BudgetService.getBudgetExpenses($scope.Budget.id).then(function(Expenses) {
					let values = [];
					let map = {};
					let startDate = moment($scope.Budget.start);
					let endDate = moment($scope.Budget.end);

					forEach(Expenses, function(Expense) {
						let index = moment(Expense.datetime).format('YYYY-MM-DD');
						if (!map[index]) {
							map[index] = 0.0;
						}
						map[index] += parseFloat(Expense.amount);
					});

					do {
						values.push([startDate.valueOf(), map[startDate.format('YYYY-MM-DD')] || 0.0]);
					} while (startDate.add(1, 'days') <= endDate);

					$scope.data = [
						{
							"key": "Quantity",
							"bar": true,
							"values": values
						}
					];

					$scope.api.refresh();
				});
			}
			else {
				setDefaultReportData();
				$scope.api.refresh();
			}
		};

		function setDefaultReportData() {
			$scope.data = [
				{
					"key": "Quantity",
					"bar": true,
					"values": []
				}
			];
		}
		setDefaultReportData();

		$scope.options = {
			chart: {
				noData: 'Please choose a budget',
				type: 'historicalBarChart',
				height: 450,
				margin: {
					top: 20,
					right: 120,
					bottom: 90,
					left: 120
				},
				x: function(d) {
					return d[0];
				},
				y: function(d) {
					return d[1];
				},
				showValues: true,
				valueFormat: function(d) {
					return format(',.2f')(d);
				},
				duration: 100,
				xAxis: {
					axisLabel: 'Date',
					tickFormat: function(d) {
						return time.format('%x')(new Date(d))
					},
					rotateLabels: 60,
					showMaxMin: false,
				},
				yAxis: {
					axisLabel: 'Amount Spent ($)',
					axisLabelDistance: 50,
					tickFormat: function(d) {
						return '$' + format(',.2f')(d);
					}
				},
				tooltip: {
					keyFormatter: function(d) {
						return time.format('%x')(new Date(d));
					}
				},
				zoom: {
					enabled: true,
					scaleExtent: [1, 10],
					useFixedDomain: false,
					useNiceScale: false,
					horizontalOff: false,
					verticalOff: true,
					unzoomEventType: 'dblclick.zoom'
				}
			}
		};
	}
])
.controller('BudgetExpensesByCategoryReportController', ['$scope', 'Budgets', 'BudgetService', 'TitleService',
	function($scope, Budgets, BudgetService, TitleService) {
		$scope.reportTitle = 'Budget Expenses By Category Report';
		TitleService.setTitle($scope.reportTitle);
		$scope.Budget = { id: null };
		$scope.Budgets = Budgets;

		$scope.budgetChange = function() {
			if ($scope.Budget && $scope.Budget.id) {
				BudgetService.getBudget($scope.Budget.id).then(function(Budget) {
					BudgetService.setData(Budget);

					$scope.data = [];
					forEach(Budget.budgetCategories, function(BudgetCategory) {
						$scope.data.push({ key: BudgetCategory.name, value: BudgetCategory.spent });
					});

					$scope.api.refresh();
				});
			}
			else {
				setDefaultReportData();
				$scope.api.refresh();
			}
		};

		function setDefaultReportData() {
			$scope.data = [];
		}
		setDefaultReportData();

		$scope.options = {
			chart: {
				noData: 'Please choose a budget',
				type: 'pieChart',
				height: 500,
				showLabels: true,
				duration: 500,
				labelSunbeamLayout: false, //false with percent so it shows horizontally
				labelType: 'percent',
				x: function(d) {
					return d.key;
				},
				y: function(d) {
					return d.value;
				},
				valueFormat: function(d) {
					return '$' + format(',.2f')(d);
				},
			}
		};
	}
])
.controller('BudgetDailyNetReportController', ['$q', '$scope', 'Budgets', 'BudgetService', 'TitleService',
	function($q, $scope, Budgets, BudgetService, TitleService) {
		$scope.reportTitle = 'Budget Daily Net Report';
		TitleService.setTitle($scope.reportTitle);
		$scope.Budget = { id: null };
		$scope.Budgets = Budgets;

		$scope.budgetChange = function() {
			if ($scope.Budget && $scope.Budget.id) {
				let promises = [BudgetService.getBudgetExpenses($scope.Budget.id), BudgetService.getBudgetIncome($scope.Budget.id)];
				$q.all(promises).then(function(responses) {
					let Expenses = responses[0], Incomes = responses[1];
					let values = [];
					let map = {};
					let startDate = moment($scope.Budget.start);
					let endDate = moment($scope.Budget.end);
					let currentAmount = 0.0;

					forEach(Expenses, function(Expense) {
						let index = moment(Expense.datetime).format('YYYY-MM-DD');
						if (!map[index]) {
							map[index] = 0.0;
						}
						map[index] -= parseFloat(Expense.amount);
					});

					forEach(Incomes, function(Income) {
						let index = moment(Income.datetime).format('YYYY-MM-DD');
						if (!map[index]) {
							map[index] = 0.0;
						}
						map[index] += parseFloat(Income.amount);
					});

					do {
						currentAmount += map[startDate.format('YYYY-MM-DD')] || 0.0;
						values.push({x: startDate.valueOf(), y: currentAmount});
					} while (startDate.add(1, 'days') <= endDate);

					$scope.data = [
						{
							key: "Net",
							values: values
						}
					];

					$scope.api.refresh();
				});
			}
			else {
				setDefaultReportData();
				$scope.api.refresh();
			}
		};

		function setDefaultReportData() {
			$scope.data = [];
		}
		setDefaultReportData();

		$scope.options = {
			chart: {
				noData: 'Please choose a budget',
				type: "lineChart",
				height: 450,
				margin: {
					top: 20,
					right: 120,
					bottom: 90,
					left: 120
				},
				x: function(d) {
					return d.x;
				},
				y: function(d) {
					return d.y;
				},
				useInteractiveGuideline: true,
				dispatch: {},
				xAxis: {
					axisLabel: "Date",
					tickFormat: function(d) {
						return time.format('%x')(new Date(d))
					},
					rotateLabels: 60,
					showMaxMin: false,
				},
				yAxis: {
					axisLabel: "Net ($)",
					axisLabelDistance: 50,
					tickFormat: function(d){
                        return '$' + format(',.2f')(d);
                    },
				}
			}
		};
	}
])
;