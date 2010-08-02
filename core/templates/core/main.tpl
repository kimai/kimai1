<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="robots" value="noindex,nofollow" />

    <title>{$kga.usr.usr_name} - Kimai</title>

    <!-- Default Stylesheets -->
    <link rel="stylesheet" href="../skins/{$kga.conf.skin}/styles.css" type="text/css" media="screen" title="no title" charset="utf-8" />
    <link rel="stylesheet" type="text/css" href="../skins/{$kga.conf.skin}/jquery.jqplot.css" />
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
    <script src="../libraries/jQuery/jquery.selectboxes.min.js" type="text/javascript" charset="utf-8"></script>
    <script src="../libraries/jQuery/ui.core.min.js" type="text/javascript" charset="utf-8"></script>
    <script src="../libraries/jQuery/ui.draggable.min.js" type="text/javascript" charset="utf-8"></script>
    <script src="../libraries/jQuery/ui.datepicker.min.js" type="text/javascript" charset="utf-8"></script>
    <!--[if IE]><script src="../libraries/jQuery/excanvas.js" type="text/javascript"></script><![endif]-->
    <script src="../libraries/jQuery/jquery.jqplot.min.js" type="text/javascript"></script>
    <script src="../libraries/jQuery/jqplot.pieRenderer.min.js" type="text/javascript" ></script>
    <script src="../libraries/phpjs/strftime.min.js" type="text/javascript" ></script>
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
        
        {if (isset($kga.usr))}
        var usr_ID                = {$kga.usr.usr_ID};
        {else}
        var usr_ID                = null;
        {/if}


        {if ($kga.conf.noFading)}
        fading_enabled = false;
        {/if}
       
        var timeoutTicktack       = 0;
        
        var hour                  = {$current_timer_hour};
        var min                   = {$current_timer_min };
        var sec                   = {$current_timer_sec };
        var startsec              = {$current_timer_start};
        var now                   = {$current_time};
        var offset                = Math.floor(((new Date()).getTime())/1000)-now;
        
        var nextday               = "{$nextday}";
        var default_title         = "{$kga.uer.usr_name} - Kimai";
        var revision              = {$kga.revision};
        var timespaceDateFormat   = "{$kga.date_format.2}";

        var selected_knd  = '{$knd_data.knd_ID}';
        var selected_pct  = '{$pct_data.pct_ID}';
        var selected_evt  = '{$evt_data.evt_ID}';

        var pickerClicked = '';

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
        {literal}function hook_tss(){{/literal}{$hook_tss}{literal}}{/literal}
        {literal}function hook_bzzRec(){{/literal}{$hook_bzzRec}{literal}}{/literal}
        {literal}function hook_bzzStp(){{/literal}{$hook_bzzStp}{literal}}{/literal}
        {literal}function hook_chgUsr(){lists_reload("usr");{/literal}{$hook_chgUsr}{literal}}{/literal}
        {literal}function hook_chgKnd(){lists_reload("knd");lists_reload("pct");{/literal}{$hook_chgKnd}{literal}}{/literal}
        {literal}function hook_chgPct(){lists_reload("pct");{/literal}{$hook_chgPct}{literal}}{/literal}
        {literal}function hook_chgEvt(){lists_reload("evt");{/literal}{$hook_chgEvt}{literal}}{/literal}
        {literal}function hook_filter(){{/literal}{$hook_filter}{literal}}{/literal}
        {literal}function hook_resize(){{/literal}{$hook_resize}{literal}}{/literal}
        {literal}function kill_reg_timeouts(){{/literal}{$timeoutlist}{literal}}{/literal}

        {literal}function kimai_onload() {
    $('#extShrink').hover(lists_extShrinkShow,lists_extShrinkHide);
    $('#extShrink').click(lists_shrinkExtToggle);
    $('#kndShrink').hover(lists_kndShrinkShow,lists_kndShrinkHide);
    $('#kndShrink').click(lists_shrinkKndToggle);
  {/literal}{if !$kga.usr || $kga.usr.usr_sts < 2}
    $('#usrShrink').hover(lists_usrShrinkShow,lists_usrShrinkHide);
    $('#usrShrink').click(lists_shrinkUsrToggle);
  {else}
    $('#usrShrink').hide();
  {/if}

  {if $kga.conf.user_list_hidden || $kga.usr.usr_sts == 2}
    lists_shrinkUsrToggle();
  {/if}
  {literal}
    $('#pct>table>tbody>tr>td>a.preselect#ps'+selected_pct+'>img').attr('src','../skins/'+skin+'/grfx/preselect_on.png');
    $('#evt>table>tbody>tr>td>a.preselect#ps'+selected_evt+'>img').attr('src','../skins/'+skin+'/grfx/preselect_on.png');
    
    //$('#gui').html('');
    
    // give browser time to render page. afterwards make sure lists are resized correctly
    setTimeout(lists_resize,500);
    clearTimeout(lists_resize);


        if ($('#row_evt'+selected_evt).length == 0) {
          $('#buzzer').addClass('disabled');
        }

	resize_menu();
    
        }{/literal}

    </script>

    <link href="../favicon.ico" rel="shortcut icon" />
    
  </head>

<body onload="kimai_onload();">

    <div id="top">
        
        <div id="logo">
            <img src="../skins/{$kga.conf.skin}/grfx/g3_logo.png" width="151" height="52" alt="Logo" />
        </div>
        
        <div id="menu">
            <a id="main_logout_button" href="../index.php?a=logout"><img src="../skins/{$kga.conf.skin}/grfx/g3_menu_logout.png" width="36" height="27" alt="Logout" /></a>
            <a id="main_tools_button" href="#" ><img src="../skins/{$kga.conf.skin}/grfx/g3_menu_dropdown.png" width="44" height="27" alt="Menu Dropdown" /></a>
            <br/>{$kga.lang.logged_in_as} <b>{$kga.usr.usr_name}</b>
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
        
        {if $kga.usr}
        
        <div id="selector">
            {include file="core/selector.tpl"}
        </div> 
   
        <div id="stopwatch">
            <span class="watch"><span id="h">00</span>:<span id="m">00</span>:<span id="s">00</span></span>
        </div>

        <div id="stopwatch_edit_comment">
            <a href="#" onclick="edit_running_comment();$(this).blur();return false;"><img src="../skins/{$kga.conf.skin}/grfx/blase.gif"/></a>
        </div>
        
        <div id="stopwatch_ticker">
            <ul id="ticker"><li id="ticker_knd">&nbsp;</li><li id="ticker_pct">&nbsp;</li><li id="ticker_evt">&nbsp;</li></ul>
        </div>
        
        <div id="buzzer" class="disabled">
            <div>&nbsp;</div>
        </div>
        {/if}        
        
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

<div class="lists" style="display:none">
<div id="usr_head">
        <input class="livefilterfield" onkeyup="lists_live_filter('usr', this.value);" type="text" id="filt_usr" name="filt_usr"/>
    {$kga.lang.users} 
</div>

<div id="knd_head">
        <input class="livefilterfield" onkeyup="lists_live_filter('knd', this.value);" type="text" id="filt_knd" name="filt_knd"/>
    {$kga.lang.knds} 

</div>

<div id="pct_head">
        <input class="livefilterfield" onkeyup="lists_live_filter('pct', this.value);" type="text" id="filt_pct" name="filt_pct"/>
    {$kga.lang.pcts}
    
    
</div>

<div id="evt_head">
        <input class="livefilterfield" onkeyup="lists_live_filter('evt', this.value);" type="text" id="filt_evt" name="filt_evt"/>
    {$kga.lang.evts}
    
    
</div>

<div id="usr">{$usr_display}</div>
<div id="knd">{$knd_display}</div>
<div id="pct">{$pct_display}</div>
<div id="evt">{$evt_display}</div>

<div id="usr_foot">
<a href="#" class="selectAllLink" onClick="lists_filter_select_all('usr'); $(this).blur(); return false;"></a>
<a href="#" class="deselectAllLink" onClick="lists_filter_deselect_all('usr'); $(this).blur(); return false;"></a>
<a href="#" class="selectInvertLink" onClick="lists_filter_select_invert('usr'); $(this).blur(); return false;"></a>
<div style="clear:both"></div>
</div>

<div id="knd_foot">    
{if $kga.usr && $kga.usr.usr_sts != 2 }    
        <a href="#" class="addLink" onClick="floaterShow('floaters.php','add_edit_knd',0,0,450,200); $(this).blur(); return false;"></a>
{/if}
<a href="#" class="selectAllLink" onClick="lists_filter_select_all('knd'); $(this).blur(); return false;"></a>
<a href="#" class="deselectAllLink" onClick="lists_filter_deselect_all('knd'); $(this).blur(); return false;"></a>
<a href="#" class="selectInvertLink" onClick="lists_filter_select_invert('knd'); $(this).blur(); return false;"></a>
<div style="clear:both"></div>
</div>

<div id="pct_foot">
{if $kga.usr && $kga.usr.usr_sts != 2 }  
        <a href="#" class="addLink" onClick="floaterShow('floaters.php','add_edit_pct',0,0,450,200); $(this).blur(); return false;"></a>
{/if}
<a href="#" class="selectAllLink" onClick="lists_filter_select_all('pct'); $(this).blur(); return false;"></a>
<a href="#" class="deselectAllLink" onClick="lists_filter_deselect_all('pct'); $(this).blur(); return false;"></a>
<a href="#" class="selectInvertLink" onClick="lists_filter_select_invert('pct'); $(this).blur(); return false;"></a>
<div style="clear:both"></div>
</div>

<div id="evt_foot">
{if $kga.usr && $kga.usr.usr_sts != 2 } 
        <a href="#" class="addLink" onClick="floaterShow('floaters.php','add_edit_evt',0,0,450,200); $(this).blur(); return false;"></a>
{/if}
<a href="#" class="selectAllLink" onClick="lists_filter_select_all('evt'); $(this).blur(); return false;"></a>
<a href="#" class="deselectAllLink" onClick="lists_filter_deselect_all('evt'); $(this).blur(); return false;"></a>
<a href="#" class="selectInvertLink" onClick="lists_filter_select_invert('evt'); $(this).blur(); return false;"></a>
<div style="clear:both"></div>
</div>

<div id="extShrink">&nbsp;</div>
<div id="usrShrink">&nbsp;</div>
<div id="kndShrink">&nbsp;</div>
</div>
    
    <div id="loader">&nbsp;</div>
    
	<div id="floater">floater</div>

</body>
</html>