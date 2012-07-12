<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="robots" value="noindex,nofollow" />

    <title>{$kga.user.name|escape:'html'} - Kimai</title>
    <link rel="shortcut icon" type="image/x-icon" href="../favicon.ico">

    <!-- Default Stylesheets -->
    <link rel="stylesheet" href="../skins/{$kga.conf.skin|escape:'html'}/styles.css" type="text/css" media="screen" title="no title" charset="utf-8" />
    <link rel="stylesheet" type="text/css" href="../skins/{$kga.conf.skin|escape:'html'}/jquery.jqplot.css" />
    <!-- /Default Stylesheets -->
    
    <!-- Extension Stylesheets -->
{foreach from=$css_extension_files item="object"}
    <link rel="stylesheet" href="{$object|escape:'html'}" type="text/css" media="screen" title="no title" charset="utf-8" />
{/foreach}
    <!-- /Extension Stylesheets -->

    <!-- Libs -->
    <script src="../libraries/jQuery/jquery-1.4.2.min.js" type="text/javascript" charset="utf-8"></script>
    <script src="../libraries/jQuery/jquery.hoverIntent.minified.js" type="text/javascript" charset="utf-8"></script>
    <script src="../libraries/jQuery/jquery.form.js" type="text/javascript" charset="utf-8"></script>
    <script src="../libraries/jQuery/jquery.newsticker.pack.js" type="text/javascript" charset="utf-8"></script>
    <script src="../libraries/jQuery/jquery.cookie.js" type="text/javascript" charset="utf-8"></script>
    <script src="../libraries/jQuery/jquery.selectboxes.min.js" type="text/javascript" charset="utf-8"></script>
    <script src="../libraries/jQuery/jquery-ui-1.8.14.custom.min.js" type="text/javascript" charset="utf-8"></script>
    <!--[if IE]><script src="../libraries/jQuery/excanvas.js" type="text/javascript"></script><![endif]-->
    <script src="../libraries/jQuery/jquery.jqplot.js" type="text/javascript"></script>
    <script src="../libraries/jQuery/jqplot.pieRenderer.js" type="text/javascript" ></script>
<!--    <script src="../libraries/jQuery/jqplot.dateAxisRenderer.min.js" type="text/javascript" ></script>-->
<!--    <script src="../libraries/jQuery/jqplot.highlighter.min.js" type="text/javascript" ></script>-->
<!--    <script src="../libraries/jQuery/jqplot.cursor.min.js" type="text/javascript" ></script>-->
    <script src="../libraries/jQuery/jquery.timePicker.js" type="text/javascript" ></script>
    <script src="../libraries/jQuery/jquery.uniform.min.js" type="text/javascript" ></script>
    <script src="../libraries/jQuery/ui.sexyselect.0.6.js" type="text/javascript" ></script>
    <script src="../libraries/phpjs/strftime.min.js" type="text/javascript" ></script>
    <!-- /Libs -->

    <!-- Default JavaScripts -->
    <script src="../js/main.js" type="text/javascript" charset="utf-8"></script>
    <script src="../js/init.js" type="text/javascript" charset="utf-8"></script>
    <!-- /Default JavaScripts -->

    <!-- Extension JavaScripts -->
{foreach from=$js_extension_files item="object"}
    <script src="{$object|escape:'html'}" type="text/javascript" charset="utf-8"></script>
{/foreach}
    <!-- /Extension JavaScripts -->


    <script type="text/javascript"> 
    
        var skin ="{$kga.conf.skin|escape:'html'}";

        var lang_checkUsername    = "{$kga.lang.checkUsername}";
        var lang_checkGroupname   = "{$kga.lang.checkGroupname}";
        var lang_checkStatusname   = "{$kga.lang.checkStatusname}";
        var lang_passwordsDontMatch   = "{$kga.lang.passwordsDontMatch}";
        var lang_passwordTooShort = "{$kga.lang.passwordTooShort}";

        var currentRecording      = {$currentRecording};

        {if $kga.conf.quickdelete == 2}
        var confirmText           = "{$kga.lang.sure}";
        {else}
        var confirmText           = undefined;
        {/if}
        
        {if (isset($kga.user))}
        var userID                = {$kga.user.userID};
        {else}
        var userID                = null;
        {/if}


        {if ($kga.conf.noFading)}
        fading_enabled = false;
        {/if}
       
        var timeoutTicktack       = 0;
        
        var hour                  = {$current_timer_hour};
        var min                   = {$current_timer_min};
        var sec                   = {$current_timer_sec};
        var startsec              = {$current_timer_start};
        var now                   = {$current_time};
        var offset                = Math.floor(((new Date()).getTime())/1000)-now;
        

        var default_title         = "{$kga.user.name|escape:'html'} - Kimai";
        var revision              = {$kga.revision};
        var timeframeDateFormat   = "{$kga.date_format.2|escape:'html'}";

        var selected_customer  = '{$customerData.customerID}';
        var selected_project  = '{$projectData.projectID}';
        var selected_activity  = '{$activityData.activityID}';

        var pickerClicked = '';
        
        var weekdayNames = {$weekdays_short_array};

        $.datepicker.setDefaults(
          {literal}{{/literal} showOtherMonths :true,
            selectOtherMonths : true,
            nextText: '',
            prevText: '',
            {if ($kga.conf.noFading)}
              showAnim: '',
            {/if}
            dateFormat : 'dd.mm.yy', // TODO use correct format depending on admin panel setting
            dayNames: {$weekdays_array},
            dayNamesMin:{$weekdays_short_array},
            dayNamesShort: {$weekdays_short_array},
            monthNames: {$months_array},
            monthNamesShort: {$months_short_array},
            firstDay:1 //TODO should also be depending on user setting
          {literal}}{/literal}
          );

        
        // HOOKS
        {literal}function hook_timeframe_changed(){{/literal}{$hook_timeframe_changed}{literal}}{/literal}
        {literal}function hook_buzzer_record(){{/literal}{$hook_buzzer_record}{literal}}{/literal}
        {literal}function hook_buzzer_stopped(){{/literal}{$hook_buzzer_stopped}{literal}}{/literal}
        {literal}function hook_users_changed(){lists_reload("user");{/literal}{$hook_users_changed}{literal}}{/literal}
        {literal}function hook_customers_changed(){lists_reload("customer");lists_reload("project");{/literal}{$hook_customers_changed}{literal}}{/literal}
        {literal}function hook_projects_changed(){lists_reload("project");{/literal}{$hook_projects_changed}{literal}}{/literal}
        {literal}function hook_activities_changed(){lists_reload("activity");{/literal}{$hook_activities_changed}{literal}}{/literal}
        {literal}function hook_filter(){{/literal}{$hook_filter}{literal}}{/literal}
        {literal}function hook_resize(){{/literal}{$hook_resize}{literal}}{/literal}
        {literal}function kill_reg_timeouts(){{/literal}{$timeoutlist}{literal}}{/literal}

        {literal}function kimai_onload() {
    $('#extensionShrink').hover(lists_extensionShrinkShow,lists_extensionShrinkHide);
    $('#extensionShrink').click(lists_shrinkExtToggle);
    $('#customersShrink').hover(lists_customerShrinkShow,lists_customerShrinkHide);
    $('#customersShrink').click(lists_shrinkCustomerToggle);
  {/literal}{if !$kga.user || $kga.user.status < 2}
    $('#usersShrink').hover(lists_userShrinkShow,lists_userShrinkHide);
    $('#usersShrink').click(lists_shrinkUserToggle);
  {else}
    $('#usersShrink').hide();
  {/if}

  {if $kga.conf.user_list_hidden || $kga.user.status == 2}
    lists_shrinkUserToggle();
  {/if}
  {literal}
    $('#projects>table>tbody>tr>td>a.preselect#ps'+selected_project+'>img').attr('src','../skins/'+skin+'/grfx/preselect_on.png');
    $('#activities>table>tbody>tr>td>a.preselect#ps'+selected_activity+'>img').attr('src','../skins/'+skin+'/grfx/preselect_on.png');

    $('#floater').draggable({  
        zIndex:20,
        ghosting:false,
        opacity:0.7,
        cursor:'move',
        handle: '#floater_handle'
      });   

    $('#n_date').html(weekdayNames[Jetzt.getDay()] + " " +strftime(timeframeDateFormat,new Date()));
    
    // give browser time to render page. afterwards make sure lists are resized correctly
    setTimeout(lists_resize,500);
    clearTimeout(lists_resize);


        if ($('#row_activity'+selected_activity).length == 0) {
          $('#buzzer').addClass('disabled');
        }

	resize_menu();

        {/literal}{if $showInstallWarning}
        floaterShow("floaters.php","securityWarning","installer",0,450,200);
        {/if}{literal}
        }{/literal}

    </script>

    <link href="../favicon.ico" rel="shortcut icon" />
    
  </head>

<body onload="kimai_onload();">

    <div id="top">
        
        <div id="logo">
            <img src="../skins/{$kga.conf.skin|escape:'html'}/grfx/g3_logo.png" width="151" height="52" alt="Logo" />
        </div>
        
        <div id="menu">
            <a id="main_logout_button" href="../index.php?a=logout"><img src="../skins/{$kga.conf.skin|escape:'html'}/grfx/g3_menu_logout.png" width="36" height="27" alt="Logout" /></a>
            <a id="main_tools_button" href="#" ><img src="../skins/{$kga.conf.skin|escape:'html'}/grfx/g3_menu_dropdown.png" width="44" height="27" alt="Menu Dropdown" /></a>
            <br/>{$kga.lang.logged_in_as} <b>{$kga.user.name|escape:'html'}</b>
        </div>
        
        <div id="main_tools_menu">
            <div class="slider">
                <a href="#" id="main_credits_button">{$kga.lang.about} Kimai</a> |
                <a href="#" id="main_prefs_button">{$kga.lang.preferences}</a>
            </div>
            <div class="end"></div>
        </div>
        
        <div id="display">
            {include file="core/display.tpl"}
        </div> 
        
        {if $kga.user}
        
        <div id="selector">
            {include file="core/selector.tpl"}
        </div> 
   
        <div id="stopwatch">
            <span class="watch"><span id="h">00</span>:<span id="m">00</span>:<span id="s">00</span></span>
        </div>
        
        <div id="stopwatch_ticker">
            <ul id="ticker"><li id="ticker_customer">&nbsp;</li><li id="ticker_project">&nbsp;</li><li id="ticker_activity">&nbsp;</li></ul>
        </div>
        
        <div id="buzzer" class="disabled">
            <div>&nbsp;</div>
        </div>
        {/if}        
        
    </div>

    <div id="fliptabs" class="menuBackground">
        <ul class="menu">
            
           	<li class="tab act" id="exttab_0">
           	    <a href="javascript:void(0);" onclick="changeTab(0,'ki_timesheets/init.php'); timesheet_extension_tab_changed();">
           	        <span class="aa">&nbsp;</span>
           	        <span class="bb">
                    {if isset($kga.lang.extensions.ki_timesheet)}
                    {$kga.lang.extensions.ki_timesheet}
                    {else}
                    Timesheet
                    {/if}</span>
           	        <span class="cc">&nbsp;</span>
           	    </a>
           	</li>
           	        
{foreach name="tabloop" from=$extensions item="extension"}
{if $extension.name AND $extension.key != "ki_timesheet"}
            <li class="tab norm" id="exttab_{$smarty.foreach.tabloop.iteration}">
                <a href="javascript:void(0);" onclick="changeTab({$smarty.foreach.tabloop.iteration}, '{$extension.initFile}'); {$extension.tabChangeTrigger};">
                    <span class="aa">&nbsp;</span>
                    <span class="bb">
                    {if isset($kga.lang.extensions[$extension.key])}
                    {$kga.lang.extensions[$extension.key]}
                    {else}
                    {$extension.name|escape:'html'}
                    {/if}</span>
                    <span class="cc">&nbsp;</span>
                </a>
            </li>
{/if}
{/foreach}

        </ul>
    </div>
    
    <div id="gui">
    	<div id="extdiv_0" class="ext ki_timesheet"></div>
    	
{foreach name="extensionloop" from=$extensions item="extension"}
{if $extension != "ki_timesheet"}
		<div id="extdiv_{$smarty.foreach.extensionloop.iteration}" class="ext {$extension.key}" style="display:none;"></div>
{/if}
{/foreach}

    </div>

<div class="lists" style="display:none">
<div id="users_head">
        <input class="livefilterfield" onkeyup="lists_live_filter('user', this.value);" type="text" id="filt_user" name="filt_user"/>
    {$kga.lang.users} 
</div>

<div id="customers_head">
        <input class="livefilterfield" onkeyup="lists_live_filter('customer', this.value);" type="text" id="filter_customer" name="filter_customer"/>
    {$kga.lang.customers} 

</div>

<div id="projects_head">
        <input class="livefilterfield" onkeyup="lists_live_filter('project', this.value);" type="text" id="filter_project" name="filter_project"/>
    {$kga.lang.projects}
    
    
</div>

<div id="activities_head">
        <input class="livefilterfield" onkeyup="lists_live_filter('activity', this.value);" type="text" id="filter_activity" name="filter_activity"/>
    {$kga.lang.activities}
    
    
</div>

<div id="users">{$user_display}</div>
<div id="customers">{$customer_display}</div>
<div id="projects">{$project_display}</div>
<div id="activities">{$activity_display}</div>

<div id="users_foot">
<a href="#" class="selectAllLink" onClick="lists_filter_select_all('user'); $(this).blur(); return false;"></a>
<a href="#" class="deselectAllLink" onClick="lists_filter_deselect_all('user'); $(this).blur(); return false;"></a>
<a href="#" class="selectInvertLink" onClick="lists_filter_select_invert('user'); $(this).blur(); return false;"></a>
<div style="clear:both"></div>
</div>

<div id="customers_foot">    
{if $kga.user && $kga.user.status != 2 }    
        <a href="#" class="addLink" onClick="floaterShow('floaters.php','add_edit_customer',0,0,450,200); $(this).blur(); return false;"></a>
{/if}
<a href="#" class="selectAllLink" onClick="lists_filter_select_all('customer'); $(this).blur(); return false;"></a>
<a href="#" class="deselectAllLink" onClick="lists_filter_deselect_all('customer'); $(this).blur(); return false;"></a>
<a href="#" class="selectInvertLink" onClick="lists_filter_select_invert('customer'); $(this).blur(); return false;"></a>
<div style="clear:both"></div>
</div>

<div id="projects_foot">
{if $kga.user && $kga.user.status != 2 }  
        <a href="#" class="addLink" onClick="floaterShow('floaters.php','add_edit_project',0,0,650,200); $(this).blur(); return false;"></a>
{/if}
<a href="#" class="selectAllLink" onClick="lists_filter_select_all('project'); $(this).blur(); return false;"></a>
<a href="#" class="deselectAllLink" onClick="lists_filter_deselect_all('project'); $(this).blur(); return false;"></a>
<a href="#" class="selectInvertLink" onClick="lists_filter_select_invert('project'); $(this).blur(); return false;"></a>
<div style="clear:both"></div>
</div>

<div id="activities_foot">
{if $kga.user && $kga.user.status != 2 } 
        <a href="#" class="addLink" onClick="floaterShow('floaters.php','add_edit_activity',0,0,450,200); $(this).blur(); return false;"></a>
{/if}
<a href="#" class="selectAllLink" onClick="lists_filter_select_all('activity'); $(this).blur(); return false;"></a>
<a href="#" class="deselectAllLink" onClick="lists_filter_deselect_all('activity'); $(this).blur(); return false;"></a>
<a href="#" class="selectInvertLink" onClick="lists_filter_select_invert('activity'); $(this).blur(); return false;"></a>
<div style="clear:both"></div>
</div>

<div id="extensionShrink">&nbsp;</div>
<div id="usersShrink">&nbsp;</div>
<div id="customersShrink">&nbsp;</div>
</div>
    
    <div id="loader">&nbsp;</div>
    
	<div id="floater">floater</div>

</body>
</html>
