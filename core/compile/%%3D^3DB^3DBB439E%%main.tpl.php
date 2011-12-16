<?php /* Smarty version 2.6.20, created on 2011-12-07 17:39:07
         compiled from core/main.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'escape', 'core/main.tpl', 7, false),)), $this); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="robots" value="noindex,nofollow" />

    <title><?php echo ((is_array($_tmp=$this->_tpl_vars['kga']['usr']['usr_name'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
 - Kimai</title>

    <!-- Default Stylesheets -->
    <link rel="stylesheet" href="../skins/<?php echo ((is_array($_tmp=$this->_tpl_vars['kga']['conf']['skin'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
/styles.css" type="text/css" media="screen" title="no title" charset="utf-8" />
    <link rel="stylesheet" type="text/css" href="../skins/<?php echo ((is_array($_tmp=$this->_tpl_vars['kga']['conf']['skin'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
/jquery.jqplot.css" />
    <!-- /Default Stylesheets -->
    
    <!-- Extension Stylesheets -->
<?php $_from = $this->_tpl_vars['css_extension_files']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['object']):
?>
    <link rel="stylesheet" href="<?php echo ((is_array($_tmp=$this->_tpl_vars['object'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
" type="text/css" media="screen" title="no title" charset="utf-8" />
<?php endforeach; endif; unset($_from); ?>
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
<?php $_from = $this->_tpl_vars['js_extension_files']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['object']):
?>
    <script src="<?php echo ((is_array($_tmp=$this->_tpl_vars['object'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
" type="text/javascript" charset="utf-8"></script>
<?php endforeach; endif; unset($_from); ?>
    <!-- /Extension JavaScripts -->


    <script type="text/javascript"> 
    
        var skin ="<?php echo ((is_array($_tmp=$this->_tpl_vars['kga']['conf']['skin'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
";

        var lang_checkUsername    = "<?php echo $this->_tpl_vars['kga']['lang']['checkUsername']; ?>
";
        var lang_checkGroupname   = "<?php echo $this->_tpl_vars['kga']['lang']['checkGroupname']; ?>
";
        var lang_checkStatusname   = "<?php echo $this->_tpl_vars['kga']['lang']['checkStatusname']; ?>
";
        var lang_passwordsDontMatch   = "<?php echo $this->_tpl_vars['kga']['lang']['passwordsDontMatch']; ?>
";
        var lang_passwordTooShort = "<?php echo $this->_tpl_vars['kga']['lang']['passwordTooShort']; ?>
";

        var recstate              = <?php echo $this->_tpl_vars['recstate']; ?>
;

        <?php if ($this->_tpl_vars['kga']['conf']['quickdelete'] == 2): ?>
        var confirmText           = "<?php echo $this->_tpl_vars['kga']['lang']['sure']; ?>
";
        <?php else: ?>
        var confirmText           = undefined;
        <?php endif; ?>
        
        <?php if (( isset ( $this->_tpl_vars['kga']['usr'] ) )): ?>
        var usr_ID                = <?php echo $this->_tpl_vars['kga']['usr']['usr_ID']; ?>
;
        <?php else: ?>
        var usr_ID                = null;
        <?php endif; ?>


        <?php if (( $this->_tpl_vars['kga']['conf']['noFading'] )): ?>
        fading_enabled = false;
        <?php endif; ?>
       
        var timeoutTicktack       = 0;
        
        var hour                  = <?php echo $this->_tpl_vars['current_timer_hour']; ?>
;
        var min                   = <?php echo $this->_tpl_vars['current_timer_min']; ?>
;
        var sec                   = <?php echo $this->_tpl_vars['current_timer_sec']; ?>
;
        var startsec              = <?php echo $this->_tpl_vars['current_timer_start']; ?>
;
        var now                   = <?php echo $this->_tpl_vars['current_time']; ?>
;
        var offset                = Math.floor(((new Date()).getTime())/1000)-now;
        

        var default_title         = "<?php echo ((is_array($_tmp=$this->_tpl_vars['kga']['usr']['usr_name'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
 - Kimai";
        var revision              = <?php echo $this->_tpl_vars['kga']['revision']; ?>
;
        var timespaceDateFormat   = "<?php echo ((is_array($_tmp=$this->_tpl_vars['kga']['date_format']['2'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
";

        var selected_knd  = '<?php echo $this->_tpl_vars['knd_data']['knd_ID']; ?>
';
        var selected_pct  = '<?php echo $this->_tpl_vars['pct_data']['pct_ID']; ?>
';
        var selected_evt  = '<?php echo $this->_tpl_vars['evt_data']['evt_ID']; ?>
';

        var pickerClicked = '';
        
        var weekdayNames = <?php echo $this->_tpl_vars['weekdays_short_array']; ?>
;

        $.datepicker.setDefaults(
          <?php echo '{'; ?>
 showOtherMonths :true,
            selectOtherMonths : true,
            nextText: '',
            prevText: '',
            <?php if (( $this->_tpl_vars['kga']['conf']['noFading'] )): ?>
              showAnim: '',
            <?php endif; ?>
            dateFormat : 'dd.mm.yy', // TODO use correct format depending on admin panel setting
            dayNames: <?php echo $this->_tpl_vars['weekdays_array']; ?>
,
            dayNamesMin:<?php echo $this->_tpl_vars['weekdays_short_array']; ?>
,
            dayNamesShort: <?php echo $this->_tpl_vars['weekdays_short_array']; ?>
,
            monthNames: <?php echo $this->_tpl_vars['months_array']; ?>
,
            monthNamesShort: <?php echo $this->_tpl_vars['months_short_array']; ?>
,
            firstDay:1 //TODO should also be depending on user setting
          <?php echo '}'; ?>

          );

        
        // HOOKS
        <?php echo 'function hook_tss(){'; ?>
<?php echo $this->_tpl_vars['hook_tss']; ?>
<?php echo '}'; ?>

        <?php echo 'function hook_bzzRec(){'; ?>
<?php echo $this->_tpl_vars['hook_bzzRec']; ?>
<?php echo '}'; ?>

        <?php echo 'function hook_bzzStp(){'; ?>
<?php echo $this->_tpl_vars['hook_bzzStp']; ?>
<?php echo '}'; ?>

        <?php echo 'function hook_chgUsr(){lists_reload("usr");'; ?>
<?php echo $this->_tpl_vars['hook_chgUsr']; ?>
<?php echo '}'; ?>

        <?php echo 'function hook_chgKnd(){lists_reload("knd");lists_reload("pct");'; ?>
<?php echo $this->_tpl_vars['hook_chgKnd']; ?>
<?php echo '}'; ?>

        <?php echo 'function hook_chgPct(){lists_reload("pct");'; ?>
<?php echo $this->_tpl_vars['hook_chgPct']; ?>
<?php echo '}'; ?>

        <?php echo 'function hook_chgEvt(){lists_reload("evt");'; ?>
<?php echo $this->_tpl_vars['hook_chgEvt']; ?>
<?php echo '}'; ?>

        <?php echo 'function hook_filter(){'; ?>
<?php echo $this->_tpl_vars['hook_filter']; ?>
<?php echo '}'; ?>

        <?php echo 'function hook_resize(){'; ?>
<?php echo $this->_tpl_vars['hook_resize']; ?>
<?php echo '}'; ?>

        <?php echo 'function kill_reg_timeouts(){'; ?>
<?php echo $this->_tpl_vars['timeoutlist']; ?>
<?php echo '}'; ?>


        <?php echo 'function kimai_onload() {
    $(\'#extShrink\').hover(lists_extShrinkShow,lists_extShrinkHide);
    $(\'#extShrink\').click(lists_shrinkExtToggle);
    $(\'#kndShrink\').hover(lists_kndShrinkShow,lists_kndShrinkHide);
    $(\'#kndShrink\').click(lists_shrinkKndToggle);
  '; ?>
<?php if (! $this->_tpl_vars['kga']['usr'] || $this->_tpl_vars['kga']['usr']['usr_sts'] < 2): ?>
    $('#usrShrink').hover(lists_usrShrinkShow,lists_usrShrinkHide);
    $('#usrShrink').click(lists_shrinkUsrToggle);
  <?php else: ?>
    $('#usrShrink').hide();
  <?php endif; ?>

  <?php if ($this->_tpl_vars['kga']['conf']['user_list_hidden'] || $this->_tpl_vars['kga']['usr']['usr_sts'] == 2): ?>
    lists_shrinkUsrToggle();
  <?php endif; ?>
  <?php echo '
    $(\'#pct>table>tbody>tr>td>a.preselect#ps\'+selected_pct+\'>img\').attr(\'src\',\'../skins/\'+skin+\'/grfx/preselect_on.png\');
    $(\'#evt>table>tbody>tr>td>a.preselect#ps\'+selected_evt+\'>img\').attr(\'src\',\'../skins/\'+skin+\'/grfx/preselect_on.png\');

    $(\'#floater\').draggable({  
        zIndex:20,
        ghosting:false,
        opacity:0.7,
        cursor:\'move\',
        handle: \'#floater_handle\'
      });   

    $(\'#n_date\').html(weekdayNames[Jetzt.getDay()] + " " +strftime(timespaceDateFormat,new Date()));
    
    // give browser time to render page. afterwards make sure lists are resized correctly
    setTimeout(lists_resize,500);
    clearTimeout(lists_resize);


        if ($(\'#row_evt\'+selected_evt).length == 0) {
          $(\'#buzzer\').addClass(\'disabled\');
        }

	resize_menu();

        '; ?>
<?php if ($this->_tpl_vars['showInstallWarning']): ?>
        floaterShow("floaters.php","securityWarning","installer",0,450,200);
        <?php endif; ?><?php echo '
        }'; ?>


    </script>

    <link href="../favicon.ico" rel="shortcut icon" />
    
  </head>

<body onload="kimai_onload();">

    <div id="top">
        
        <div id="logo">
            <img src="../skins/<?php echo ((is_array($_tmp=$this->_tpl_vars['kga']['conf']['skin'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
/grfx/g3_logo.png" width="151" height="52" alt="Logo" />
        </div>
        
        <div id="menu">
            <a id="main_logout_button" href="../index.php?a=logout"><img src="../skins/<?php echo ((is_array($_tmp=$this->_tpl_vars['kga']['conf']['skin'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
/grfx/g3_menu_logout.png" width="36" height="27" alt="Logout" /></a>
            <a id="main_tools_button" href="#" ><img src="../skins/<?php echo ((is_array($_tmp=$this->_tpl_vars['kga']['conf']['skin'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
/grfx/g3_menu_dropdown.png" width="44" height="27" alt="Menu Dropdown" /></a>
            <br/><?php echo $this->_tpl_vars['kga']['lang']['logged_in_as']; ?>
 <b><?php echo ((is_array($_tmp=$this->_tpl_vars['kga']['usr']['usr_name'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
</b>
        </div>
        
        <div id="main_tools_menu">
            <div class="slider">
                <a href="#" id="main_credits_button"><?php echo $this->_tpl_vars['kga']['lang']['about']; ?>
 Kimai</a> |
                <a href="#" id="main_prefs_button"><?php echo $this->_tpl_vars['kga']['lang']['preferences']; ?>
</a>
            </div>
            <div class="end"></div>
        </div>
        
        <div id="display">
            <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "core/display.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
        </div> 
        
        <?php if ($this->_tpl_vars['kga']['usr']): ?>
        
        <div id="selector">
            <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "core/selector.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
        </div> 
   
        <div id="stopwatch">
            <span class="watch"><span id="h">00</span>:<span id="m">00</span>:<span id="s">00</span></span>
        </div>

        <div id="stopwatch_edit_starttime">
            <a href="#" onclick="edit_running_starttime();$(this).blur();return false;"><img src="../skins/<?php echo ((is_array($_tmp=$this->_tpl_vars['kga']['conf']['skin'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
/grfx/uhr.gif"/></a>
        </div>

        <div id="stopwatch_edit_comment">
            <a href="#" onclick="edit_running_comment();$(this).blur();return false;"><img src="../skins/<?php echo ((is_array($_tmp=$this->_tpl_vars['kga']['conf']['skin'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
/grfx/blase.gif"/></a>
        </div>
        
        <div id="stopwatch_ticker">
            <ul id="ticker"><li id="ticker_knd">&nbsp;</li><li id="ticker_pct">&nbsp;</li><li id="ticker_evt">&nbsp;</li></ul>
        </div>
        
        <div id="buzzer" class="disabled">
            <div>&nbsp;</div>
        </div>
        <?php endif; ?>        
        
    </div>

    <div id="fliptabs" class="menuBackground">
        <ul class="menu">
            
           	<li class="tab act" id="exttab_0">
           	    <a href="javascript:void(0);" onclick="changeTab(0,'ki_timesheets/init.php'); ts_ext_triggerchange();">
           	        <span class="aa">&nbsp;</span>
           	        <span class="bb">
                    <?php if (isset ( $this->_tpl_vars['kga']['lang']['extensions']['ki_timesheet'] )): ?>
                    <?php echo $this->_tpl_vars['kga']['lang']['extensions']['ki_timesheet']; ?>

                    <?php else: ?>
                    Timesheet
                    <?php endif; ?></span>
           	        <span class="cc">&nbsp;</span>
           	    </a>
           	</li>
           	        
<?php $_from = $this->_tpl_vars['extensions']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }$this->_foreach['tabloop'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['tabloop']['total'] > 0):
    foreach ($_from as $this->_tpl_vars['extension']):
        $this->_foreach['tabloop']['iteration']++;
?>
<?php if ($this->_tpl_vars['extension']['name'] && $this->_tpl_vars['extension']['key'] != 'ki_timesheet'): ?>
            <li class="tab norm" id="exttab_<?php echo $this->_foreach['tabloop']['iteration']; ?>
">
                <a href="javascript:void(0);" onclick="changeTab(<?php echo $this->_foreach['tabloop']['iteration']; ?>
, '<?php echo $this->_tpl_vars['extension']['initFile']; ?>
'); <?php echo $this->_tpl_vars['extension']['tabChangeTrigger']; ?>
;">
                    <span class="aa">&nbsp;</span>
                    <span class="bb">
                    <?php if (isset ( $this->_tpl_vars['kga']['lang']['extensions'][$this->_tpl_vars['extension']['key']] )): ?>
                    <?php echo $this->_tpl_vars['kga']['lang']['extensions'][$this->_tpl_vars['extension']['key']]; ?>

                    <?php else: ?>
                    <?php echo ((is_array($_tmp=$this->_tpl_vars['extension']['name'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>

                    <?php endif; ?></span>
                    <span class="cc">&nbsp;</span>
                </a>
            </li>
<?php endif; ?>
<?php endforeach; endif; unset($_from); ?>

        </ul>
    </div>
    
    <div id="gui">
    	<div id="extdiv_0" class="ext ki_timesheet"></div>
    	
<?php $_from = $this->_tpl_vars['extensions']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }$this->_foreach['extensionloop'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['extensionloop']['total'] > 0):
    foreach ($_from as $this->_tpl_vars['extension']):
        $this->_foreach['extensionloop']['iteration']++;
?>
<?php if ($this->_tpl_vars['extension'] != 'ki_timesheet'): ?>
		<div id="extdiv_<?php echo $this->_foreach['extensionloop']['iteration']; ?>
" class="ext <?php echo $this->_tpl_vars['extension']['key']; ?>
" style="display:none;"></div>
<?php endif; ?>
<?php endforeach; endif; unset($_from); ?>

    </div>

<div class="lists" style="display:none">
<div id="usr_head">
        <input class="livefilterfield" onkeyup="lists_live_filter('usr', this.value);" type="text" id="filt_usr" name="filt_usr"/>
    <?php echo $this->_tpl_vars['kga']['lang']['users']; ?>
 
</div>

<div id="knd_head">
        <input class="livefilterfield" onkeyup="lists_live_filter('knd', this.value);" type="text" id="filt_knd" name="filt_knd"/>
    <?php echo $this->_tpl_vars['kga']['lang']['knds']; ?>
 

</div>

<div id="pct_head">
        <input class="livefilterfield" onkeyup="lists_live_filter('pct', this.value);" type="text" id="filt_pct" name="filt_pct"/>
    <?php echo $this->_tpl_vars['kga']['lang']['pcts']; ?>

    
    
</div>

<div id="evt_head">
        <input class="livefilterfield" onkeyup="lists_live_filter('evt', this.value);" type="text" id="filt_evt" name="filt_evt"/>
    <?php echo $this->_tpl_vars['kga']['lang']['evts']; ?>

    
    
</div>

<div id="usr"><?php echo $this->_tpl_vars['usr_display']; ?>
</div>
<div id="knd"><?php echo $this->_tpl_vars['knd_display']; ?>
</div>
<div id="pct"><?php echo $this->_tpl_vars['pct_display']; ?>
</div>
<div id="evt"><?php echo $this->_tpl_vars['evt_display']; ?>
</div>

<div id="usr_foot">
<a href="#" class="selectAllLink" onClick="lists_filter_select_all('usr'); $(this).blur(); return false;"></a>
<a href="#" class="deselectAllLink" onClick="lists_filter_deselect_all('usr'); $(this).blur(); return false;"></a>
<a href="#" class="selectInvertLink" onClick="lists_filter_select_invert('usr'); $(this).blur(); return false;"></a>
<div style="clear:both"></div>
</div>

<div id="knd_foot">    
<?php if ($this->_tpl_vars['kga']['usr'] && $this->_tpl_vars['kga']['usr']['usr_sts'] != 2): ?>    
        <a href="#" class="addLink" onClick="floaterShow('floaters.php','add_edit_knd',0,0,450,200); $(this).blur(); return false;"></a>
<?php endif; ?>
<a href="#" class="selectAllLink" onClick="lists_filter_select_all('knd'); $(this).blur(); return false;"></a>
<a href="#" class="deselectAllLink" onClick="lists_filter_deselect_all('knd'); $(this).blur(); return false;"></a>
<a href="#" class="selectInvertLink" onClick="lists_filter_select_invert('knd'); $(this).blur(); return false;"></a>
<div style="clear:both"></div>
</div>

<div id="pct_foot">
<?php if ($this->_tpl_vars['kga']['usr'] && $this->_tpl_vars['kga']['usr']['usr_sts'] != 2): ?>  
        <a href="#" class="addLink" onClick="floaterShow('floaters.php','add_edit_pct',0,0,650,200); $(this).blur(); return false;"></a>
<?php endif; ?>
<a href="#" class="selectAllLink" onClick="lists_filter_select_all('pct'); $(this).blur(); return false;"></a>
<a href="#" class="deselectAllLink" onClick="lists_filter_deselect_all('pct'); $(this).blur(); return false;"></a>
<a href="#" class="selectInvertLink" onClick="lists_filter_select_invert('pct'); $(this).blur(); return false;"></a>
<div style="clear:both"></div>
</div>

<div id="evt_foot">
<?php if ($this->_tpl_vars['kga']['usr'] && $this->_tpl_vars['kga']['usr']['usr_sts'] != 2): ?> 
        <a href="#" class="addLink" onClick="floaterShow('floaters.php','add_edit_evt',0,0,450,200); $(this).blur(); return false;"></a>
<?php endif; ?>
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