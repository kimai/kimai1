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

/**
 * Javascript functions used in the export extension.
 */



/**
 * The extension was loaded, do some setup stuff.
 */
function export_extension_onload() {
	export_extension_resize();
	$("#loader").hide();
	lists_visible(true);

	$('#export_extension_select_filter').click(function(){
		export_extension_select_filter();
	});

	$('#export_extension_select_location').click(function(){
		export_extension_select_location();
	});

	$('#export_extension_select_timeformat').click(function(){
		export_extension_select_timeformat();
	});

	$('#export_extension_export_pdf').click(function(){
		this.blur();
		floaterShow('../extensions/ki_export/floaters.php','PDF',0,0,600);
	});

	$('#export_extension_export_xls').click(function(){
		this.blur();
		floaterShow('../extensions/ki_export/floaters.php','XLS',0,0,600);
	});
	$('#export_extension_export_csv').click(function(){
		this.blur();
		floaterShow('../extensions/ki_export/floaters.php','CSV',0,0,600);
	});

	$('#export_extension_print').click(function(){
		this.blur();
		floaterShow('../extensions/ki_export/floaters.php','print',0,0,600);
	});

	$('.helpfloater').click(function(){
		this.blur();
		floaterShow('../extensions/ki_export/floaters.php','help_timeformat',0,0,600);
	});

	export_extension_select_filter();
	export_extension_reload();
}



/**
 * Show the tab which allows filtering.
 */
function export_extension_select_filter() {
	$('#export_extension_select_filter').addClass("pressed");
	$('#export_extension_tab_filter').css("display","block");

	$('#export_extension_select_location').removeClass("pressed");
	$('#export_extension_tab_location').css("display","none");
	$('#export_extension_select_timeformat').removeClass("pressed");
	$('#export_extension_tab_timeformat').css("display","none");

}

/**
 * Show the tab via which the default location can be set.
 */
function export_extension_select_location() {
	$('#export_extension_select_location').addClass("pressed");
	$('#export_extension_tab_location').css("display","block");

	$('#export_extension_select_filter').removeClass("pressed");
	$('#export_extension_tab_filter').css("display","none");
	$('#export_extension_select_timeformat').removeClass("pressed");
	$('#export_extension_tab_timeformat').css("display","none");
}

/**
 * Show the tab which lets the user define the date and time format.
 */
function export_extension_select_timeformat() {
	$('#export_extension_select_timeformat').addClass("pressed");
	$('#export_extension_tab_timeformat').css("display","block");

	$('#export_extension_select_filter').removeClass("pressed");
	$('#export_extension_tab_filter').css("display","none");
	$('#export_extension_select_location').removeClass("pressed");
	$('#export_extension_tab_location').css("display","none");

}


/////////////////////////////////////////////////////
// mitgebracht von ts_ext:


/**
 * Update the dimension variables to reflect new height and width.
 */
function export_extension_get_dimensions() {
	scroller_width = 17;
	if (navigator.platform.substr(0,3)=='Mac') {
		scroller_width = 16;
	}

	(customerShrinkMode)?subtableCount=2:subtableCount=3;
	subtableWidth = (pageWidth()-10)/subtableCount-7 ;

	export_width = pageWidth()-24;
	export_height = pageHeight()-274-headerHeight()-28;
}

/**
 * The window has been resized, we have to adjust to the new space.
 */
function export_extension_resize() {
	export_extension_set_tableWrapperWidths();
	export_extension_set_heightTop();
}

/**
 * Set width of table and faked table head.
 */
function export_extension_set_tableWrapperWidths() {
	export_extension_get_dimensions();
	$("#export_head,#xp").css("width",export_width);
	export_extension_set_TableWidths();
}

/**
 * If the extension is being shrinked so the sublists are shown larger
 * adjust to that.
 */
function export_extension_set_heightTop() {
	export_extension_get_dimensions();
	if (!extensionShrinkMode) {
		$("#xp").css("height", export_height);
	} else {
		$("#xp").css("height", "20px");
	}

	export_extension_set_TableWidths();
}

/**
 * Set the width of the table.
 */
function export_extension_set_TableWidths() {
	export_extension_get_dimensions();
	// set table widths   
	var $xp = $("#xp");
	var $export_head = $("#export_head");
	
	($xp.innerHeight()-$xp.find("table").outerHeight()>0)?scr=0:scr=scroller_width; // width of export table depending on scrollbar or not
	$xp.find("table").css("width",export_width-scr);

	$export_head.find("table").css("width", "100%");

	$xp.find("td.billable").css("width", $export_head.find("td.billable").width());
	
	$export_head.find("td.time").css("width", $xp.find("td.time").width());
	$export_head.find("td.wage").css("width", $xp.find("td.wage").width());
	$export_head.find("td.rate").css("width", $xp.find("td.rate").width());
	$export_head.find("td.billable").css("width", $xp.find("td.billable").width());
	$export_head.find("td.status").css("width", $xp.find("td.status").width());
	$export_head.find("td.budget").css("width", $xp.find("td.budget").width());
	$export_head.find("td.approved").css("width", $xp.find("td.approved").width());
	$export_head.find("td.customer").css("width", $xp.find("td.customer").width());
	$export_head.find("td.project").css("width", $xp.find("td.project").width());
	$export_head.find("td.activity").css("width", $xp.find("td.activity").width());
	$export_head.find("td.description").css("width", $xp.find("td.description").width());
	$export_head.find("td.comment").css("width", $xp.find("td.comment").width());
	$export_head.find("td.location").css("width", $xp.find("td.location").width());
	$export_head.find("td.moreinfo").css("width",
		$xp.find("td.comment").width()+
		$xp.find("td.location").width()+
		$xp.find("td.trackingNumber").width()
	);
}

function export_extension_tab_changed() {
	if (xp_timeframe_changed_hook_flag) {
		export_extension_reload();
		xp_customers_changed_hook_flag = 0;
		xp_projects_changed_hook_flag = 0;
		xp_activities_changed_hook_flag = 0;
	}
	if (xp_customers_changed_hook_flag) {
		export_extension_customers_changed();
		xp_projects_changed_hook_flag = 0;
		xp_activities_changed_hook_flag = 0;
	}
	if (xp_projects_changed_hook_flag) {
		export_extension_projects_changed();
	}
	if (xp_activities_changed_hook_flag) {
		export_extension_activities_changed();
	}

	xp_timeframe_changed_hook_flag = 0;
	xp_customers_changed_hook_flag = 0;
	xp_projects_changed_hook_flag = 0;
	xp_activities_changed_hook_flag = 0;
	if ($('.ki_export').html() != '')
		export_extension_reload();
}

function export_extension_timeframe_changed() {
	if ($('.ki_export').css('display') == "block") {
		export_extension_reload();
	} else {
		xp_timeframe_changed_hook_flag++;
	}
}

function export_extension_customers_changed() {
	if ($('.ki_export').css('display') == "block") {
		export_extension_reload();
	} else {
		xp_customers_changed_hook_flag++;
	}
}

function export_extension_projects_changed() {
	if ($('.ki_export').css('display') == "block") {
		export_extension_reload();
	} else {
		xp_projects_changed_hook_flag++;
	}
}

function export_extension_activities_changed() {
	if ($('.ki_export').css('display') == "block") {
		export_extension_reload();
	} else {
		xp_activities_changed_hook_flag++;
	}
}


// ----------------------------------------------------------------------------------------
// reloads timesheet, customer, project and activity tables
//
function export_extension_reload() {

	// don't reload if extension is not loaded  
	if ($('.ki_export').html() =='')
		return;

	$.post(export_extension_path + "processor.php", { 
			axAction: "reload", 
			axValue: filterUsers.join(":")+'|'+filterCustomers.join(":")+'|'+filterProjects.join(":")+'|'+filterActivities.join(":"),
			id: 0, 
			timeformat: $("#export_extension_timeformat").val(), 
			dateformat: $("#export_extension_dateformat").val(), 
			default_location: $("#export_extension_default_location").val(),
			filter_cleared:$('#export_extension_tab_filter_cleared').val(),
			filter_refundable:$('#export_extension_tab_filter_refundable').val(),
			filter_type: $('#export_extension_tab_filter_type').val(),
			first_day: new Date($('#pick_in').val()).getTime()/1000, 
			last_day: new Date($('#pick_out').val()).getTime()/1000  
		},
		function(data) {
			$("#xp").html(data);

			// set export table width
			($("#xp").innerHeight()-$("#xp table").outerHeight() > 0 ) ? scr=0 : scr=scroller_width; // width of export table depending on scrollbar or not
			$("#xp table").css("width",export_width-scr);
			// stretch customer column in faked export table head
			$("#export_head").find("td.customer").css("width", $("#xp").find("td.customer").width());
			// stretch project column in faked export table head
			$("#export_head").find("td.project").css("width", $("#xp").find("td.project").width());
			export_extension_resize();
		}
	);
}



/**
 * Toggle the enabled state of a column.
 */
function export_toggle_column(name) {
	if ($("#export_head > table > tbody > tr ."+name).hasClass('disabled')) {
		returnfunction = new Function("data","if (data!=1) return;\
                    $('#export_head > table > tbody > tr ."+name+"').removeClass('disabled');\
                    $('#xp > div > table > tbody > tr > td."+name+"').removeClass('disabled'); ");
		$.post(export_extension_path + "processor.php", { axAction: "toggle_header", axValue: name },
			returnfunction
		);
	}
	else {
		returnfunction = new Function("data","if (data!=1) return;\
                    $('#export_head > table > tbody > tr ."+name+"').addClass('disabled'); \
                    $('#xp > div > table > tbody > tr > td."+name+"').addClass('disabled'); ");
		$.post(export_extension_path + "processor.php", { axAction: "toggle_header", axValue: name },
			returnfunction
		);
	}
}

/**
 * Toggle the cleared state of an entry.
 */
function export_toggle_cleared(id) {
	path = "#xp"+id+">td.cleared>a";
	if ($(path).hasClass("is_cleared")) {
		returnfunction = new Function("data","if (data!=1) return;\
                    $('"+path+"').removeClass('is_cleared');\
                    $('"+path+"').addClass('isnt_cleared');");
		$.post(export_extension_path + "processor.php", { axAction: "set_cleared", axValue: 0, id: id },
			returnfunction
		);
	}
	else {
		returnfunction = new Function("data","if (data!=1) return;\
                    $('"+path+"').removeClass('isnt_cleared');\
                    $('"+path+"').addClass('is_cleared');");
		$.post(export_extension_path + "processor.php", { axAction: "set_cleared", axValue: 1, id: id },
			returnfunction
		);
	}
	$(path).blur();
}

/**
 * Create a list of enabled columns.
 */
function export_enabled_columns() {
	columns = ['date','from','to','time','dec_time','rate','wage','budget','approved','status','billable','customer','project','activity','description','comment','location','trackingNumber','user','cleared'];
	columnsString = '';
	firstColumn = true;
	$(columns).each(function () {
		if (!$('#export_head .'+this).hasClass('disabled')) {
			columnsString += (firstColumn?'':'|') + this;
			firstColumn = false;
		}
	});
	return columnsString;
}