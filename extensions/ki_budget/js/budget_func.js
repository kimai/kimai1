/**
 * This file is part of
 * Kimai - Open Source Time Tracking // http://www.kimai.org
 * (c) 2006-2009 Kimai-Development-Team
 *
 * Kimai is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; Version 3, 29 June 2007
 *
 * Kimai is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Kimai; If not, see <http://www.gnu.org/licenses/>.
 */

// ==========================
// Budget extension functions
// ==========================

function budget_extension_onload() {
	budget_extension_resize();
	$('#loader').hide();
	lists_visible(true);
	try {
		budget_extension_reload();
	} catch(e) {
		alert(e);
	}
}

function recalculate_budget_window() {
	// adjust length of the elements if the legend is longer than the space allows
	$('.project_overview').each(function () {
		try {
			var element = $(this).parent();
			var numberOfElements = element.nextUntil('br').andSelf().length;
			var numberOnLine = Math.floor(($(document).width() - 45) / 225);
			var height = Math.ceil(numberOfElements / numberOnLine) * 270;
			var br = element.nextAll('br').eq(0);
			br.css('line-height', height + 'px');
			var addHeight = $(this).next().find('.jqplot-table-legend').height() - 150;

			if ($(this).next().find('.jqplot-table-legend').height() > 150) {
				br = element.nextAll('br').eq(0);
				br.css('line-height', parseInt(br.css('line-height')) + addHeight + 'px');
				element.nextUntil('br').andSelf().slice(0, numberOnLine).each(function () {
					// only add the height if it's not added already before (like if
					// we make the windows smaller and then bigger again, we need to
					// add the height so some of the charts)
					if ($(this).height() < 250 + addHeight) {
						$(this).height(($(this).height() + addHeight));
					}
				});
				// in case we make the window bigger and some "long" elements are on a new page
				// and need the "normal" length
				element.nextUntil('br').andSelf().slice(numberOnLine).each(function () {
					$(this).height(250);
				});
			}
		} catch (err) {
			alert(err);
		}
	});
}

/**
 * If the extension is being shrinked so the sublists are shown larger
 * adjust to that.
 */
function budget_extension_set_heightTop() {
	budget_extension_get_dimensions();
	if (!extensionShrinkMode) {
		$('#budgetArea').css('height', budget_h);
	} else {
		$('#budgetArea').css('height', '20px');
	}
}

/**
 * Update the dimension variables to reflect new height and width.
 */
function budget_extension_get_dimensions() {
	scroller_width = 17;
	if (navigator.platform.substr(0, 3) == 'Mac') {
		scroller_width = 16;
	}

	(customerShrinkMode) ? subtableCount = 2 : subtableCount = 3;
	subtableWidth = (pageWidth() - 10) / subtableCount - 7;

	budget_w = pageWidth() - 24;
	budget_h = pageHeight() - 224 - headerHeight();
}

function budget_extension_resize() {
	budget_extension_set_heightTop();
}

/**
 * 
 * @param plotdata
 */
function budget_extension_plot(plotdata) {
	var target;
	for (var projectId in plotdata) {
		var background = false;
		for (var activityId in plotdata[projectId]) {
			if (activityId == 0) {
				target = 'budget_chartdiv_' + projectId;
			} else {
				target = 'budget_chartdiv_' + projectId + '_activity_' + activityId;
			}
			var $target = $('#' + target);
			if ($target.length == 0) {
				continue;
			}
			var actualData = [];
			// turn the object into an array, since the jqplot wants an array, even though it's considered bad practice
			for ( var index in plotdata[projectId][activityId]) {
				if (index == 'exceeded') {
					// here we could add some more highlighting, for example setting the background to a color
					// background = 'red';
					$target.parent().children('.budget').css('color', 'red');
				} else if (index == 'approved_exceeded') {
					// here we could add some more highlighting, for example setting the background to a color
					// if(background != 'red') {
					// background = 'yellow';
					$target.parent().children('.approved').css('color', 'red');
					// }
				} else if (index == 'approved' || index == 'budget_total' || index == 'billable_total' || index == 'approved_total' || index == 'total') {
					// do nothing, that just means we have not used up the approved budget or is a number
					// we use to display
					// but we don't want it in the chart anyways
				} else if (index == 'name') {
					// ignore
				} else {
					if (background == false) {
						background = $target.css('background-color');
					}
					actualData.push([index, plotdata[projectId][activityId][index]]);
				}
			}
			try {
				$.jqplot(target, [actualData], {
					seriesDefaults: {
						renderer: $.jqplot.PieRenderer,
						rendererOptions: {
							padding: 10,
							showDataLabels: true,
							// By default, data labels show the percentage of the donut/pie.
							// You can show the data 'value' or data 'label' instead.
							dataLabels: 'value'
						}
					},
					// Show the legend and put it outside the grid, but inside the
					// plot container, shrinking the grid to accomodate the legend.
					// A value of "outside" would not shrink the grid and allow
					// the legend to overflow the container.
					legend: {
						show: true,
						placement: 'outsideGrid'
					},
					seriesColors: chartColors,
					grid: {
						background: background, 
						borderWidth: 0,
						shadow: false
					}
				});
			}
			catch (err) {
				// probably no data, so remove the chart
				$target.remove();
			}
		}
	}
}

/**
 * reloads timesheet, customer, project and activity tables
 */
function budget_extension_reload() {
    $('#loader').show();
    $.ajax({
        dataType: 'html',
        url: budget_extension_path + 'processor.php',
        type: 'POST',
        data: {
            axAction: 'reload',
            axValue: filterUsers.join(":") + '|' + filterCustomers.join(":") + '|' + filterProjects.join(":"),
            first_day: new Date($.datepicker.parseDate(window.dateFormat, $('#pick_in').val())).getTime() / 1000,
            last_day: new Date($.datepicker.parseDate(window.dateFormat, $('#pick_out').val())).getTime() / 1000,
            id: 0
        },
        success: function(data) {
            $('#budgetArea').html(data);
            $('#loader').hide();
        },
        error: function(error) {
            $('#loader').hide();
            alert(error);
        }
    });
}