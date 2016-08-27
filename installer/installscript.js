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

$(window).load(function() {
    $('#wrapper').fadeIn(1000);
    $('#footer').fadeIn(1000);
    $('#jswarn').hide();
    $('#installsteps').fadeIn('slow');
    $('input').attr("checked",false);
});

function step_ahead() {
    $('#progressbar>span').removeClass('step_yap');
    $('#progressbar>span').addClass('step_nope');
    
    for (i=1;i<step+1;i++) {
        $evalstring="$('#progressbar>span:eq("+(i-1)+")').addClass('step_yap');"
        eval($evalstring);
    }
    step++;
}

function step_back() {
    
    switch(current) {
        case 25: target = "20_gpl"; break;
        case 28: target = "25_system_requirements"; break;
        case 30: target = "28_timezone"; break;
        case 40: target = "28_timezone"; break;
        case 45: target = "40_permissions"; break;
        case 50: target = "40_permissions"; break;
        case 60: target = "50_enter_access_data"; break;
        case 70: target = "60_db_select_mysql"; break;
    }

    step=step-2;
    step_ahead();        
    $('#installsteps').slideUp(500,function() {
        $.post("steps/"+target+".php", { hostname:hostname, username:username, password:password, lang:language, prefix:prefix, database:database },
            function(data) {
                $('#installsteps').html(data);
                $('#installsteps').slideDown(500);
            }
        );
    });
}

// -------------------------------------------------
// Language selection

function lang_selected(lang) {
    step_ahead();
    language = lang;
    $('#installsteps').slideUp(500,function() {
        target = "20_gpl";
        $.post("steps/"+target+".php", {lang:language},
            function(data) {
                $('#installsteps').html(data);
                $('#installsteps').slideDown(500);
            }
        );
    });
}

// -------------------------------------------------
// Agree to GPL

function gpl_agreed(checkbox) {
    if($(checkbox).prop('checked')) {
        $('#installsteps button.proceed').fadeIn('slow');
    } else {
        $('#installsteps button.proceed').fadeOut();
    }
}

function gpl_proceed() {
    step_ahead();
    $('#installsteps').slideUp(500,function() {
        target = "25_system_requirements";
        $.post("steps/"+target+".php", {lang:language},
            function(data) {
                $('#installsteps').html(data);
                $('#installsteps').slideDown(500);
            }
        );
    });
}

// -------------------------------------------------
// Check system requirements

function check_system_requirements() {
    $.post("processor.php", { axAction: 'checkRequirements' },
        function(data) {
            eval(data);
        }
    );
}

function resetRequirementsIndicators() {
    $('div.sp_phpversion').removeClass("fail");
    $('div.sp_mysql').removeClass("fail");
    $('div.sp_iconv').removeClass("fail");
    $('div.sp_memory').removeClass("fail");
    $('div.sp_dom').removeClass("fail");

    $('div.sp_phpversion').addClass("ok");
    $('div.sp_mysql').addClass("ok");
    $('div.sp_iconv').addClass("ok");
    $('div.sp_memory').addClass("ok");
    $('div.sp_dom').addClass("ok");
}

function system_requirements_proceed() {
    step_ahead();
    $('#installsteps').slideUp(500,function() {
        target = "28_timezone";
        $.post("steps/"+target+".php", {lang:language},
            function(data) {
                $('#installsteps').html(data);
                $('#installsteps').slideDown(500);
            }
        );
    });
}

// -------------------------------------------------
// Timezone selection


function timezone_proceed() {
    step_ahead();
    timezone = $('#timezone').val();
    $('#installsteps').slideUp(500,function() {
        target = "40_permissions";
        $.post("steps/"+target+".php", {lang:language},
            function(data) {
                $('#installsteps').html(data);
                $('#installsteps').slideDown(500);
            }
        );
    });
}

// -------------------------------------------------
// Check write-permissions

function check_permissions() {
    $.post("processor.php", { axAction: 'checkRights' },
        function(data) {
            resetPermissionIndicators();
            eval(data);
        }
    );
}

function resetPermissionIndicators() {
    $('span.ch_autoconf').removeClass("fail");
    $('span.ch_logfile').removeClass("fail");
    $('span.ch_temporary').removeClass("fail");
    
    $('span.ch_autoconf').addClass("ok");
    $('span.ch_logfile').addClass("ok");
    $('span.ch_temporary').addClass("ok");

    $('span.ch_correctit').fadeOut(500); 
}

function cp_proceed() {
    step_ahead();
    $('#installsteps').slideUp(500,function(){
        target = "50_enter_access_data";

        $.post("steps/"+target+".php", {lang:language},
            function(data) {
                $('#installsteps').html(data);
                $('#installsteps').slideDown(500);
            }
        );
    });
}

// -------------------------------------------------
// Enter DB HostUserPass

function host_proceed() {

    hostname = $('#host').val();
    username = $('#user').val();
    password = $('#pass').val();
    
    if (username == "") {
        
        if (language == "en") {
            caution = "You must enter a user name!";
        } else {
            caution = "Sie müssen einen Benutzer-Namen eingeben!";
        }
        
        $('#caution').html(caution);
    } else {
    
        step_ahead();
        $('#installsteps').slideUp(500,function(){
        
            target = "60_db_select_mysql";
        
            $.post("steps/"+target+".php", { hostname:hostname, username:username, password:password, lang:language},
                function(data) {
                    $('#installsteps').html(data);
                    $('#installsteps').slideDown(500);
                }
            );
        });
    }
}

// -------------------------------------------------
// Database selection

function db_check() {
    database        = $('#db_names').val();
    create_database = $('#db_create').val();
    prefix          = $('#prefix').val();
    
    databaseGiven = $('#db_names').is('select')?database != "0":database != "";
    
    if (!databaseGiven && create_database == "") {
        if (language =="en") {
            $('#db_select_label').html("You have to choose one of these databases!");
            $('#db_select_label').html("You have to choose either one of these ...");
            $('#db_create_label').html("... or create a new one!");
        } else {
            $('#db_select_label').html("Sie müssen hier eine Datenbank auswählen!");
            $('#db_select_label').html("Sie müssen entweder hier eine Datenbank auswählen ...");
            $('#db_create_label').html("... oder hier eine neue erstellen!");
        }
        $('#db_select_label').addClass("arrow");
        $('#db_create_label').addClass("arrow");
    }
    else {
      $('#installsteps').slideUp(500,function(){
        $.post("steps/"+target+".php", { hostname:hostname, username:username, password:password, lang:language, database:database, create_database:create_database, prefix:prefix, redirect:true},
              function(data) {
                $('#installsteps').html(data);
                $('#installsteps').slideDown(500);
              }
        );
      });
    }
}

function db_proceed() {
  database        = $('#db_names').val();
  create_database = $('#db_create').val();
  prefix          = $('#prefix').val();
  
  if (create_database != "") {
    database=create_database;
    new_database = 1;
  }
  
  target = "70_write_conf";
  
  step_ahead();
  
  $.post("steps/"+target+".php", { hostname:hostname, username:username, password:password, lang:language, database:database, prefix:prefix},
    function(data) {
      $('#installsteps').html(data);
      $('td.use_db').html(database);
      $('td.use_host').html(hostname);
      $('td.use_prefix').html(prefix);
      $('#installsteps').slideDown(500);
    }
  );
}

// -------------------------------------------------
// Execute Install

function install() {
    if (new_database == 1) {
        create_db();
    } else {
        write_config();
    }
}

function create_db() {

    $.post("processor.php", { axAction: 'make_database', hostname:hostname, username:username, password:password, lang:language, prefix:prefix, database:database },
        function(data) {
            
            if (data == "1") {
                write_config();
                
            } else {
                
                target = "db_error";
                
                $.post("steps/"+target+".php", {lang:language},
                    function(data) {
                        $('#installsteps').html(data);
                        $('#installsteps').slideDown(500);
                    }
                );
            }
        }
    );
}

function write_config() {
  
    step_ahead();

    $.post("processor.php", { axAction: 'write_config', hostname:hostname, username:username, password:password, lang:language, prefix:prefix, database:database, timezone:timezone },
        function(data) {
            $('#wrapper').fadeOut(2000);
            $('#footer').fadeOut(2000, function() {
                window.location.href='install.php?accept=1&timezone='+timezone;
            });
        }
    );
}

