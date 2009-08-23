<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset={$kga.conf.charset}"/>

    <title>{$kga.usr.usr_name} - Kimai</title>

    <!-- Default Stylesheets -->
    <link rel="stylesheet" href="../skins/{$kga.conf.skin}/styles.css" type="text/css" media="screen" title="no title" charset="utf-8" />
    <link rel="stylesheet" href="../extensions/ki_timesheets/css/setup.css" type="text/css" media="screen" title="no title" charset="utf-8" />
    <!-- /Default Stylesheets -->
    
    <!-- Extension Stylesheets -->
{foreach from=$css_extension_files item="object"}
    <link rel="stylesheet" href="{$object}" type="text/css" media="screen" title="no title" charset="utf-8" />
{/foreach}
    <!-- /Extension Stylesheets -->

    <!-- Libs -->
    <script src="../libraries/jQuery/jquery-1.3.2.min.js" type="text/javascript" charset="utf-8"></script>
    <script src="../libraries/jQuery/jquery.hoverIntent.minified.js" type="text/javascript" charset="utf-8"></script>
    <script src="../libraries/jQuery/jquery.form.js" type="text/javascript" charset="utf-8"></script>
    <script src="../libraries/jQuery/jquery.newsticker.pack.js" type="text/javascript" charset="utf-8"></script>
    <script src="../libraries/jQuery/jquery.cookie.js" type="text/javascript" charset="utf-8"></script>
    <script src="../libraries/jQuery/jquery.datePicker.js" type="text/javascript" charset="utf-8"></script>
    <script src="../libraries/jQuery/jquery.selectboxes.min.js" type="text/javascript" charset="utf-8"></script>
    <script src="../libraries/jQuery/idrag.js" type="text/javascript" charset="utf-8"></script>
    <script src="../libraries/jQuery/iutil.js" type="text/javascript" charset="utf-8"></script>
    <script src="../libraries/jQuery/date.js" type="text/javascript" charset="utf-8"></script>
    <!-- /Libs -->

    <!-- Default JavaScripts -->
    <script src="../js/main.js" type="text/javascript" charset="utf-8"></script>
    <script src="../js/init.js" type="text/javascript" charset="utf-8"></script>
    <!-- /Default JavaScripts -->

    <!-- Extension JavaScripts -->
{foreach from=$js_extension_files item="object"}
    <script src="{$object}" type="text/javascript" charset="utf-8"></script>
{/foreach}
    <!-- /Extension JavaScripts -->

    <!-- xajax -->
        {$xajax_js}
    <!-- // xajax -->

    <script type="text/javascript"> 
    
        var skin ="{$kga.conf.skin}";

        var lang_checkUsername    = "{$kga.lang.checkUsername}";

        var recstate              = {$recstate};
        
        var usr_ID                = {$kga.usr.usr_ID}
       
        var timeoutTicktack       = 0;
        
        var hour                  = {$current_timer_hour};
        var min                   = {$current_timer_min };
        var sec                   = {$current_timer_sec };
        var startsec              = {$current_timer_start};
        var now                   = {$current_time};
        var offset                = Math.floor(((new Date()).getTime())/1000)-now;
        
        var nextday               = "{$nextday}";
        var default_title         = "{$kga.usr.usr_name} - Kimai";
        var revision              = {$kga.revision};

        var selected_knd  = '{$knd_data.knd_ID}';
        var selected_pct  = '{$pct_data.pct_ID}';
        var selected_evt  = '{$evt_data.evt_ID}';

        var pickerClicked = '';
        
        // HOOKS
        {literal}function hook_tss(){{/literal}{$hook_tss}{literal}}{/literal}
        {literal}function hook_bzzRec(){{/literal}{$hook_bzzRec}{literal}}{/literal}
        {literal}function hook_bzzStp(){{/literal}{$hook_bzzStp}{literal}}{/literal}
        {literal}function hook_chgKnd(){{/literal}{$hook_chgKnd}{literal}}{/literal}
        {literal}function hook_chgPct(){{/literal}{$hook_chgPct}{literal}}{/literal}
        {literal}function hook_chgEvt(){{/literal}{$hook_chgEvt}{literal}}{/literal}
        {literal}function kill_reg_timeouts(){{/literal}{$timeoutlist}{literal}}{/literal}

    </script>

    <link href="../favicon.ico" rel="shortcut icon" />
    
  </head>

<body>

    <div id="top">
        
        <div id="logo">
            <img src="../skins/{$kga.conf.skin}/grfx/g3_logo.png" width="151" height="52" alt="Logo" />
        </div>
        
        <div id="menu">
            <a id="main_logout_button" href="../index.php?a=logout"><img src="../skins/{$kga.conf.skin}/grfx/g3_menu_logout.png" width="36" height="27" alt="Logout" /></a>
            <a id="main_tools_button" href="#" ><img src="../skins/{$kga.conf.skin}/grfx/g3_menu_dropdown.png" width="44" height="27" alt="Menu Dropdown" /></a>
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
        
        <div id="selector">
            {include file="core/selector.tpl"}
        </div> 
   
        <div id="stopwatch">
            <span class="watch"><span id="h">00</span>:<span id="m">00</span>:<span id="s">00</span></span>
        </div>
        
        <div id="stopwatch_ticker">
            <ul id="ticker"><li id="ticker_knd">&nbsp;</li><li id="ticker_pct">&nbsp;</li><li id="ticker_evt">&nbsp;</li></ul>
        </div>
        
        <div id="buzzer">
            <a href="#">Start</a>
        </div>
        
    </div>

    <div id="fliptabs">
        <dl class="menu">
            
           	<dd class="tab act" id="exttab_0">
           	    <a href="javascript:void(0);" onclick="changeTab(0,'ki_timesheets/init.php'); ts_ext_triggerchange();">
           	        <span class="aa">&nbsp;</span>
           	        <span class="bb">Timesheet</span>
           	        <span class="cc">&nbsp;</span>
           	    </a>
           	</dd>
           	        
{foreach name="tabloop" from=$extensions item="object"}
{if $object.EXTENSION_NAME AND $object.EXTENSION_KEY != "ki_timesheet"}
            <dd class="tab norm" id="exttab_{$smarty.foreach.tabloop.iteration}">
                <a href="javascript:void(0);" onclick="changeTab({$smarty.foreach.tabloop.iteration}, '{$object.EXTENSION_INIT_FILE}'); {$object.TAB_CHANGE_TRIGGER};">
                    <span class="aa">&nbsp;</span>
                    <span class="bb">{$object.EXTENSION_NAME}</span>
                    <span class="cc">&nbsp;</span>
                </a>
            </dd>
{/if}
{/foreach}

        </dl>
    </div>
    
    <div id="gui">
    	<div id="extdiv_0" class="ext ki_timesheet"></div>
    	
{foreach name="extensionloop" from=$extensions item="object"}
{if $object.EXTENSION_KEY != "ki_timesheet"}
		<div id="extdiv_{$smarty.foreach.extensionloop.iteration}" class="ext {$object.EXTENSION_KEY}" style="display:none;"></div>
{/if}
{/foreach}

    </div>
    
    <div id="loader">&nbsp;</div>
    
	<div id="floater">floater</div>

</body>
</html>