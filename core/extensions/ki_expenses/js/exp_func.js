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

// ======================== Expenses init ========================

// set path of extension
var expense_extension_path = "../extensions/ki_expenses/";
var expenses_total = '';

var scroller_width;
var drittel;
var expenses_width;
var expenses_height;

var expense_timeframe_changed_hook_flag = 0;
var expense_customers_changed_hook_flag = 0;
var expense_projects_changed_hook_flag = 0;
var expense_activities_changed_hook_flag = 0;

$.subscribe('resize', function (_, activeTab) {
    if (activeTab == 'ki_expenses') {
        expense_extension_resize();
    }
});

$.subscribe('filter', function (_) {
    expense_extension_reload();
});

$.subscribe('timeframe', function(_, timeframe) {
    expense_extension_timeframe_changed();
});

$.subscribe('tabs', function (_, activeTab, tabId) {
    if (activeTab == 'ki_expenses') {
        expense_extension_triggerchange();
    }
});

// ======================== Expenses functions ========================

/**
 * Called when the extension loaded. Do some initial stuff.
 */
function expense_extension_onload() {
    expense_extension_applyHoverIntent();
    expense_extension_resize();
    $("#loader").hide();
    lists_visible(true);
}

/**
 * Update the dimension variables to reflect new height and width.
 */
function expense_extension_get_dimensions() {
    scroller_width = 17;
    if (navigator.platform.substr(0,3)=='Mac') {
        scroller_width = 16;
    }

    (customerShrinkMode)?subtableCount=2:subtableCount=3;
    subtableWidth = (pageWidth()-10)/subtableCount-7 ;
    
    expenses_width = pageWidth()-24;
    expenses_height = pageHeight()-224-headerHeight()-28;
}

/**
 * Hover a row if the mouse is over it for more than half a second.
 */
function expense_extension_applyHoverIntent() {
    $('#expenses tr').hoverIntent({
        sensitivity: 1,
        interval: 500,
        over:
          function() { 
              $('#expenses tr').removeClass('hover');
              $(this).addClass('hover');},
        out:
          function() {
              $(this).removeClass('hover');
          }
    });
}

/**
 * The window has been resized, we have to adjust to the new space.
 */
function expense_extension_resize() {
    expense_extension_set_tableWrapperWidths();
    expense_extension_set_heightTop();
}

/**
 * Set width of table and faked table head.
 */
function expense_extension_set_tableWrapperWidths() {
    expense_extension_get_dimensions();
    $("#expenses_head,#expenses").css("width",expenses_width);
    expense_extension_set_TableWidths();
}

/**
 * If the extension is being shrinked so the sublists are shown larger
 * adjust to that.
 */
function expense_extension_set_heightTop() {
    expense_extension_get_dimensions();
    if (!extensionShrinkMode) {
        $("#expenses").css("height", expenses_height);
    } else {
        $("#expenses").css("height", "70px");
    }
    
    expense_extension_set_TableWidths();
}

/**
 * Set the width of the table.
 */
function expense_extension_set_TableWidths() {
    expense_extension_get_dimensions();
    // set table widths   
    ($("#expenses").innerHeight()-$("#expenses table").outerHeight()>0)?scr=0:scr=scroller_width; // width of expenses table depending on scrollbar or not
    $("#expenses table").css("width",expenses_width-scr);
    $("div#expenses > div > table > tbody > tr > td.refundable").css("width", $("#expenses_head > table > tbody > tr > td.refundable").width());  
    $("#expenses_head > table > tbody > tr > td.time").css("width", $("div#expenses > div > table > tbody > tr > td.time").width());
    $("#expenses_head > table > tbody > tr > td.value").css("width", $("div#expenses > div > table > tbody > tr > td.value").width());
    $("#expenses_head > table > tbody > tr > td.refundable").css("width", $("div#expenses > div > table > tbody > tr > td.refundable").width());
    // stretch customer column in faked expenses table head
    $("#expenses_head > table > tbody > tr > td.customer").css("width", $("div#expenses > div > table > tbody > tr > td.customer").width());    
    // stretch project column in faked expenses table head
    $("#expenses_head > table > tbody > tr > td.project").css("width", $("div#expenses > div > table > tbody > tr > td.project").width());
    $("#expenses_head > table > tbody > tr > td.designation").css("width", $("div#expenses > div > table > tbody > tr > td.designation").width());
}

function expense_extension_triggerchange() {
    $('#display_total').html(expenses_total);
    if (expense_timeframe_changed_hook_flag) {
        expense_extension_reload();
        expense_customers_changed_hook_flag = 0;
        expense_projects_changed_hook_flag = 0;
        expense_activities_changed_hook_flag = 0;
    }
    if (expense_customers_changed_hook_flag) {
        expense_extension_triggerCHK();
        expense_projects_changed_hook_flag = 0;
        expense_activities_changed_hook_flag = 0;
    }
    if (expense_projects_changed_hook_flag) {
        expense_extension_triggerCHP();
    }
    if (expense_activities_changed_hook_flag) {
        expense_extension_triggerCHE();
    }
    
    expense_timeframe_changed_hook_flag = 0;
    expense_customers_changed_hook_flag = 0;
    expense_projects_changed_hook_flag = 0;
    expense_activities_changed_hook_flag = 0;
}

function expense_extension_timeframe_changed() {
    if ($('.ki_expenses').css('display') == "block") {
        expense_extension_reload();
    } else {
        expense_timeframe_changed_hook_flag++;
    }
}

function expense_extension_triggerCHK() {
    if ($('.ki_expenses').css('display') == "block") {
        expense_extension_reload();
    } else {
        expense_customers_changed_hook_flag++;
    }
}

function expense_extension_triggerCHP() {
    if ($('.ki_expenses').css('display') == "block") {
        expense_extension_reload();
    } else {
        expense_projects_changed_hook_flag++;
    }
}

function expense_extension_triggerCHE() {
    if ($('.ki_expenses').css('display') == "block") {
        expense_extension_reload();
    } else {
        expense_activities_changed_hook_flag++;
    }
}


// ----------------------------------------------------------------------------------------
// reloads timesheet, customer, project and activity tables

function expense_extension_reload() {
    $.post(expense_extension_path + "processor.php", { axAction: "reload_exp", axValue: filterUsers.join(":")+'|'+filterCustomers.join(":")+'|'+filterProjects.join(":"), id: 0,
        first_day: new Date($('#pick_in').val()).getTime()/1000, last_day: new Date($('#pick_out').val()).getTime()/1000 },
        function(data) {
            $("#expenses").html(data);

            // set expenses table width
            ($("#expenses").innerHeight()-$("#expenses table").outerHeight() > 0 ) ? scr=0 : scr=scroller_width; // width of exp table depending on scrollbar or not
            $("#expenses table").css("width",expenses_width-scr);
            // stretch refundable column in faked expenses table head
            $("#expenses_head > table > tbody > tr > td.refundable").css("width", $("div#expenses > div > table > tbody > tr > td.refundable").width());
            // stretch customer column in faked expenses table head
            $("#expenses_head > table > tbody > tr > td.customer").css("width", $("div#expenses > div > table > tbody > tr > td.customer").width());
            // stretch project column in faked expenses table head
            $("#expenses_head > table > tbody > tr > td.project").css("width", $("div#expenses > div > table > tbody > tr > td.project").width());
            expense_extension_applyHoverIntent();
        }
    );
}


// ----------------------------------------------------------------------------------------
// delete a timesheet record immediately
//
function expense_quickdelete(id) {
    $('#expensesEntry'+id+'>td>a').blur();
    
    if (confirmText != undefined) {
      var check = confirm(confirmText);
      if (check == false) return;
    }
    
    $('#expensesEntry'+id+'>td>a').removeAttr('onClick');
    $('#expensesEntry'+id+'>td>a.quickdelete>img').attr("src","../skins/standard/grfx/loading13.gif");
    
    $.post(expense_extension_path + "processor.php", { axAction: "quickdelete", axValue: 0, id: id },
        function(result){
            if (result.errors.length == 0) {
                expense_extension_reload();
            } else {
                var messages = [];
                for (var index in result.errors)
                    messages.push(result.errors[index]);
                alert(messages.join("\n"));
            }
        }
    );
}

// ----------------------------------------------------------------------------------------
// edit a timesheet record
//
function expense_editRecord(id) {
    floaterShow(expense_extension_path + "floaters.php","add_edit_record",0,id,600);
}

// ----------------------------------------------------------------------------------------
// shows comment line for expense entry
//
function comment(id) {
    $('#expense_c'+id).toggle();
    return false;
}
// ----------------------------------------------------------------------------------------
// pastes the current date and time in the outPoint field of the
// change dialog for timesheet entries 
//
//         $view->pasteValue = date("d.m.Y - H:i:s",$kga['now']);
//
function expense_pasteNow(value) {
    
    now = new Date();

    H = now.getHours();
    i = now.getMinutes();
    s = now.getSeconds();
    
    if (H<10) H = "0"+H;
    if (i<10) i = "0"+i;
    if (s<10) s = "0"+s;
    
    time  = H + ":" + i + ":" + s;
    
    $("#edit_time").val(time);
}
