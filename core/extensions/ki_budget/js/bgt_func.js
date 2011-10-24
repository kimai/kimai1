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

// =============
// BGT EXT funcs
// =============

function bgt_ext_onload() {
    bgt_ext_resize();
    $("#loader").hide();
    lists_visible(true);
    try {
    bgt_ext_reload();
    } catch(e) {
    	alert(e);
    }
}

/**
 * If the extension is being shrinked so the sublists are shown larger
 * adjust to that.
 */
function bgt_ext_set_heightTop() {
	bgt_ext_get_dimensions();
    if (!extShrinkMode) {
        $("#bgt").css("height", bgt_h);
    } else {
        $("#bgt").css("height", "20px");
    }
    
    xp_ext_set_TableWidths();
}

/**
 * Update the dimension variables to reflect new height and width.
 */
function bgt_ext_get_dimensions() {
    scroller_width = 17;
    if (navigator.platform.substr(0,3)=='Mac') {
        scroller_width = 16;
    }

    (kndShrinkMode)?subtableCount=2:subtableCount=3;
    subtableWidth = (pageWidth()-10)/subtableCount-7 ;
    
    bgt_w = pageWidth()-24;
    bgt_h = pageHeight()-184-headerHeight()-28;
}

//function bgt_ext_get_dimensions() {
//    bgt_h = pageHeight()-224;//-headerHeight()-28;
//}

function bgt_ext_resize() {
    bgt_ext_set_heightTop();
}


function bgt_ext_plot(plotdata) {
	var target;
    for (var projectId in plotdata) {
    	var background = false;
        for (var eventId in plotdata[projectId]) {
        	if(eventId == 0) {
        		target = 'bgt_chartdiv_'+projectId;
        	} else {
        		target = 'bgt_chartdiv_'+projectId+'_evt_'+eventId;
        	}
            if ($('#'+target).length == 0) {
            	continue;
            }
        var actualData = new Array();
        // turn the object into an array, since the jqplot wants an array, eventhough it's considered bad practice
        for( var index in plotdata[projectId][eventId]) {
        	if(index == 'exceeded') {
        		// here we could add some more highlighting, for example setting the background to a color
//        		background = 'red';
        		$('#'+target).parent().children('.budget').css('color', 'red');
        	} else if(index == 'approved_exceeded') {
        		// here we could add some more highlighting, for example setting the background to a color
//        		if(background != 'red') {
//        		background = 'yellow';
        		$('#'+target).parent().children('.approved').css('color', 'red');
//        		}
        	} else if(index == 'approved' || index == 'budget_total' || index == 'billable_total' || index == 'approved_total' || index == 'total') {
        		// do nothing, that just means we have not used up the approved budget or is a number
        		// we use to display
        		// but we don't want it in the chart anyways
        	} else {
        		if(background == false) {
        		background = $('#'+target).css("background-color");
        		}
        	actualData.push(new Array(index, plotdata[projectId][eventId][index]));
        	}
        }
        try {
            $.jqplot(target,  [actualData], {              
              seriesDefaults:{renderer:$.jqplot.PieRenderer,
                  rendererOptions: {padding:10,
                      showDataLabels: true,
//                      // By default, data labels show the percentage of the donut/pie.
//                      // You can show the data 'value' or data 'label' instead.
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
              seriesColors:chartColors,
              grid:{background: background, borderWidth:0, shadow:false}
          });
        }
        catch (err) {
        	// probably no data, so remove the chart
        	$('#'+target).remove();
//        	alert(err.toSource());
        	
        }
    }
	}
}

// ----------------------------------------------------------------------------------------
// reloads timesheet, customer, project and event tables
//
function bgt_ext_reload() {
	$.ajax({
		dataType: "html",
		url: bgt_ext_path+'processor.php',
		data: {
			axAction: "reload",
			axValue: filterUsr.join(":")+'|'+filterKnd.join(":")+'|'+filterPct.join(":"),
			first_day: new Date($('#pick_in').val()).getTime()/1000, last_day: new Date($('#pick_out').val()).getTime()/1000,
			id: 0
		},
		success: function( data ) {
            $('#bgt').html(data);
			},
		error: function(error) {
				alert(error);
			}
	});
}