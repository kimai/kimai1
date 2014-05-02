/**
 * This file is part of
 * Kimai - Open Source Time Tracking // http://www.kimai.org
 * (c) Kimai-Development-Team
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

var background  = new Array("#000000","#FFFF00","#76EE00","#CD3333","#B23AEE","#CDBE70");
var text        = new Array("#FFFFFF","#000000","#000000","#FFFFFF","#FFFFFF","#000000");

// FIXME extensions - search for these configs
// TAB_CHANGE_TRIGGER, TIMEFRAME_CHANGE_TRIGGER, BUZZER_RECORD_TRIGGER, REG_TIMEOUTS
// BUZZER_STOP_TRIGGER, CHANGE_CUSTOMER_TRIGGER, CHANGE_PROJECT_TRIGGER, CHANGE_ACTIVITY_TRIGGER

// gets called when any tab was clicked, regardless if the tab is already active or not
$.subscribe('tabs', createDemoLogger('tab'));
// gets called everytime the datepicker picks a date (equally for start and end)
$.subscribe('timeframe', createDemoLogger('timeframe'));
// fires when the buzzer was clicked for recording (also true for "record again" in ts_ext)
$.subscribe('buzzer-record', createDemoLogger('buzzer-record'));
// fires when the buzzer was hit in stop-state (true for stops in ts_ext)
$.subscribe('buzzer-stopped', createDemoLogger('buzzer-stopped'));
// gets called when any change happend on a users record
$.subscribe('users', createDemoLogger('users'));
// gets called when any change happend on a customer record
$.subscribe('customers', createDemoLogger('customers'));
// gets called when any change happend on a project record
$.subscribe('projects', createDemoLogger('projects'));
// gets called when any change happend on a activities record
$.subscribe('activities', createDemoLogger('activities'));
// fired when a filter is changed
$.subscribe('filter', createDemoLogger('filter'));
// fired when the window is resized and our ui needs to adapt
$.subscribe('resize', createDemoLogger('resize'));
// if you have timeouts running that do not need to run when the tab of your extension is not active, register them here.
// they will be terminated on tabchange and you have to restart them on triggerchange
$.subscribe('timeouts', createDemoLogger('timeouts'));
// fired when kimai is loaded (that will be fired only once unless your browser reloads the page)
$.subscribe('onload', createDemoLogger('onload'));

// ============================================================================================================
// Some demo implementations of the actions
// ============================================================================================================

$.subscribe('tabs', function (_, extensionId, tabId) {
    // this will be logged no matter which tab was activated ...
    $("#testdiv").append(" This has been put here on change to tab ["+tabId+" / "+extensionId+"] .");
    // ... but in most cases you want to check for the extensionId and only run code if its your own
    if (extensionId == 'demo_ext') {
        var target = Math.round(Math.random()*background.length);
        $("#testdiv").css("color",text[target]);
        $("#testdiv").css("background-color",background[target]);
    }
});

$.subscribe('timeframe', function (_, a) {
    var timespan = a.split("|");
    if (timespan[0] == '0-0-0') {
        $('#demo_timeframe > span.timeframe_target').html(timespan[1]);
    } else {
        $('#demo_timeframe > span.timeframe_target').html(timespan[0]);
    }
});

// returns a prepared logger with a name
function createDemoLogger(name) {
    return function(_, a) {
        console.log('Demo extension caught signal [' + name + ']', arguments);
    };
}

function demo_ext_onload() {
    $("#loader").hide();
}
