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

 // ===========
 // ADMIN PANEL
 // ===========

function ap_ext_onload() {
    ap_ext_resize();  
	subactivelink = "#ap_ext_sub1";
    $("#loader").hide();
}

function ap_ext_resize() {

	scroller_width = 14;
	if (navigator.platform.substr(0,3)=='Mac') {
	    scroller_width = 16;
	}
	
	pagew = pageWidth();
	drittel = (pagew-10)/3 - 7 ;
	
	panel_w = pagew-24;
	panel_h = pageHeight()-10-headerHeight();
	
	$(".ap_ext_subtab").css("display", "none");
	    
	$("#ap_ext_panel").css("width", panel_w);
	$(".ap_ext_panel_header").css("width", panel_w);
	$("#ap_ext_panel").css("height", panel_h);
	
	$(".ap_ext_subtab").css("height", panel_h - (7*25)-20-1);
	
    ap_ext_subtab_autoexpand();
}

/**
 * Show one of the subtabs. All others are collapsed, so only their header
 * is visible.
 */
function ap_ext_subtab_expand(id) {
	$("#ap_ext_sub1").removeClass("active");
	$("#ap_ext_sub2").removeClass("active");
	$("#ap_ext_sub3").removeClass("active");
	$("#ap_ext_sub4").removeClass("active");
	$("#ap_ext_sub5").removeClass("active");
	$("#ap_ext_sub6").removeClass("active");
	$("#ap_ext_sub7").removeClass("active");
	$(".ap_ext_subtab").css("display", "none");	
	
	sub_id="#ap_ext_sub" +id;
	$(sub_id).addClass("active");
	
	subtab="#ap_ext_s"+id;
	$(subtab).css("display", "block");
	
	$.cookie('ap_ext_activePanel_'+usr_ID, id);
}

/**
 * Show the last subtab, the user has seen. This information is stored in a
 * cookie. If we're unable to read it show the first subtab.
 */
function ap_ext_subtab_autoexpand() {
	ap_ext_activePanel  = $.cookie('ap_ext_activePanel_'+usr_ID);
    if (ap_ext_activePanel) {
        ap_ext_subtab_expand(ap_ext_activePanel);
    } else {
        ap_ext_subtab_expand(1);
    }
}



// ------------------------------------------------------


function ap_ext_triggerchange() {
    if ($('.ap_ext').css('display') == "block") {
        ap_ext_refreshSubtab('knd');
        ap_ext_refreshSubtab('pct');
        ap_ext_refreshSubtab('evt');
    } else {
        tss_hook_flag++;
    }
    if (ap_chk_hook_flag) {
        ap_ext_triggerCHK();
    }
    if (ap_chp_hook_flag) {
        ap_ext_triggerCHP();
    }
    if (ap_che_hook_flag) {
        ap_ext_triggerCHE();
    }
    if (ap_usr_hook_flag) {
        ap_ext_triggerUSR();
    }
    
    ap_tss_hook_flag = 0;
    ap_rec_hook_flag = 0;
    ap_stp_hook_flag = 0;
    ap_chk_hook_flag = 0;
    ap_chp_hook_flag = 0;
    ap_che_hook_flag = 0;
    ap_usr_hook_flag = 0;
}

function ap_ext_triggerCHK() {
    if ($('.ap_ext').css('display') == "block") {
        ap_ext_refreshSubtab('knd');
        ap_ext_refreshSubtab('pct');
    } else {
        ap_chk_hook_flag++;
    }
}

function ap_ext_triggerCHP() {
    if ($('.ap_ext').css('display') == "block") {
        ap_ext_refreshSubtab('pct');
    } else {
        ap_chp_hook_flag++;
    }
}

function ap_ext_triggerCHE() {
    if ($('.ap_ext').css('display') == "block") {
        ap_ext_refreshSubtab('evt');
    } else {
        ap_che_hook_flag++;
    }
}

function ap_ext_triggerUSR() {
    if ($('.ap_ext').css('display') == "block") {
        ap_ext_refreshSubtab('usr');
    } else {
        ap_usr_hook_flag++;
    }
}

// ------------------------------------------------------




// ----------------------------------------------------------------------------------------
// graps the value of the newUser input field 
// and ajaxes it to the createUsr function of the processor
//
function ap_ext_newUser() {
    newuser = $("#newuser").val();
    if (newuser == "") {
        alert(lang_checkUsername);
        return false;
    }
    $.post(ap_ext_path + "processor.php", { axAction: "createUsr", axValue: newuser, id: 0 }, 
    function(data) {
        ap_ext_refreshSubtab('usr');
        ap_ext_editUser(data);
    });
}

function ap_ext_showDeletedUsers() {
    $.post(ap_ext_path + "processor.php", { axAction: "toggleDeletedUsers", axValue: 1, id: 0 }, 
    function(data) {
        ap_ext_refreshSubtab('usr');
    });
}

function ap_ext_hideDeletedUsers() {
    $.post(ap_ext_path + "processor.php", { axAction: "toggleDeletedUsers", axValue: 0, id: 0 }, 
    function(data) {
        ap_ext_refreshSubtab('usr');
    });
}


// ----------------------------------------------------------------------------------------
// graps the value of the newGroup input field 
// and ajaxes it to the createGrp function of the processor
//
function ap_ext_newGroup() {
    newgroup = $("#newgroup").val();
    if (newgroup == "") {
        alert(lang_checkGroupname);
        return false;
    }
    $.post(ap_ext_path + "processor.php", { axAction: "createGrp", axValue: newgroup, id: 0 }, 
    function(data) {
        ap_ext_refreshSubtab('grp');
    });
}



// ----------------------------------------------------------------------------------------
// by clicking on the edit button of a user the edit dialogue pops up
//
function ap_ext_editUser(id) {
    floaterShow(ap_ext_path + "floaters.php","editUsr",0,id,400,230);
}

// ----------------------------------------------------------------------------------------
// by clicking on the edit button of a group the edit dialogue pops up
//
function ap_ext_editGroup(id) {
    floaterShow(ap_ext_path + "floaters.php","editGrp",0,id,450,100);
}

// ----------------------------------------------------------------------------------------
// refreshes either user/group/advanced/DB subtab
//
function ap_ext_refreshSubtab(tab) {
    options = { axAction: "refreshSubtab", axValue: tab, id: 0 };
    if (tab == 'evt') {
      options.evt_filter = $('#evt_pct_filter').val();
    }
    $.post(ap_ext_path + "processor.php", options, 
    function(data) {
        switch(tab) {
            case "usr":  target = "#ap_ext_s1"; break
            case "grp":  target = "#ap_ext_s2"; break
            case "adv":  target = "#ap_ext_s3"; break
            case "db":   target = "#ap_ext_s4"; break
            case "knd":  target = "#ap_ext_s5"; break
            case "pct":  target = "#ap_ext_s6"; break
            case "evt":  target = "#ap_ext_s7"; break
        }
        $(target).html(data);
    });
}

// ----------------------------------------------------------------------------------------
// delete user
//
function ap_ext_deleteUser(id) {
    $.post(ap_ext_path + "processor.php", { axAction: "deleteUsr", axValue: 0, id: id }, 
        function(data) {
            if (confirm(data)) {
                $.post(ap_ext_path + "processor.php", {axAction: "deleteUsr", axValue: 1, id: id }, 
                    function() { 
                      ap_ext_refreshSubtab('usr');
                      ap_ext_refreshSubtab('grp');
                      hook_chgUsr(); }
                );
            }
        }
    );
}

// ----------------------------------------------------------------------------------------
// delete group
//
function ap_ext_deleteGroup(id) {
    $.post(ap_ext_path + "processor.php", { axAction: "deleteGrp", axValue: 0, id: id }, 
        function(data) {
            if (confirm(data)) {
                $.post(ap_ext_path + "processor.php", {axAction: "deleteGrp", axValue: 1, id: id }, 
                    function() { ap_ext_refreshSubtab('grp'); }
                );
            }
        }
    );
}

// ----------------------------------------------------------------------------------------
// delete project
//
function ap_ext_deleteProject(id) {
    $.post(ap_ext_path + "processor.php", { axAction: "deletePct", axValue: 0, id: id }, 
        function(data) {
            if (confirm(data)) {
                if (recstate!=1 && selected_pct == id) {
                  $('#buzzer').addClass('disabled');
                  selected_pct = false;
                  $("#sel_pct").html('');
                }
                $.post(ap_ext_path + "processor.php", {axAction: "deletePct", axValue: 1, id: id }, 
                    function() { ap_ext_refreshSubtab('pct');
                 hook_chgPct(); }
                );
            }
        }
    );
}

// ----------------------------------------------------------------------------------------
// delete customer
//
function ap_ext_deleteCustomer(id) {
    $.post(ap_ext_path + "processor.php", { axAction: "deleteKnd", axValue: 0, id: id }, 
        function(data) {
            if (confirm(data)) {
                if (recstate!=1 && selected_knd == id) {
                  $('#buzzer').addClass('disabled');
                  selected_knd = false;
                  $("#sel_knd").html('');
                }
                $.post(ap_ext_path + "processor.php", {axAction: "deleteKnd", axValue: 1, id: id }, 
                    function() { ap_ext_refreshSubtab('knd');
                 hook_chgKnd(); }
                );
            }
        }
    );
}

// ----------------------------------------------------------------------------------------
// delete event
//
function ap_ext_deleteEvent(id) {
    $.post(ap_ext_path + "processor.php", { axAction: "deleteEvt", axValue: 0, id: id }, 
        function(data) {
            if (confirm(data)) {
                if (recstate!=1 && selected_evt == id) {
                  $('#buzzer').addClass('disabled');
                  selected_evt = false;
                  $("#sel_evt").html('');
                }
                
                $.post(ap_ext_path + "processor.php", {axAction: "deleteEvt", axValue: 1, id: id }, 
                    function() { ap_ext_refreshSubtab('evt');
                 hook_chgEvt(); }
                );
            }
        }
    );
}

// ----------------------------------------------------------------------------------------
// activates user for login
//
function ap_ext_unbanUser(id) {
    $("#ban"+id).blur();
    $("#ban"+id).html("<img border='0' width='16' height='16' src='../skins/"+skin+"/grfx/loading13.gif'/>");
    $.post(ap_ext_path + "processor.php", { axAction: "unbanUsr", axValue: 0, id: id }, 
        function(data) {
            $("#ban"+id).html(data);
            $("#ban"+id).attr({ "ONCLICK": "ap_ext_banUser('"+id+"'); return false;" });
        }
    );
}

// ----------------------------------------------------------------------------------------
// toggle ban and unban of users in admin panel
//
function ap_ext_banUser(id) {
    $("#ban"+id).blur();
    $("#ban"+id).html("<img border='0' width='16' height='16' src='../skins/"+skin+"/grfx/loading13.gif'/>");
    $.post(ap_ext_path + "processor.php", { axAction: "banUsr", axValue: 0, id: id },
        function(data) {
            $("#ban"+id).html(data);
            $("#ban"+id).attr({ "ONCLICK": "ap_ext_unbanUser('"+id+"'); return false;" });
        }
    );
}

function ap_ext_checkupdate() {
    $.post("checkupdate.php",
        function(data) {
           $('#ap_ext_checkupdate').html(data);
        }
    );
    
}