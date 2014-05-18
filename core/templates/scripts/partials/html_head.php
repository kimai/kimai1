<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<meta name="robots" value="noindex,nofollow" />

<title><?php echo $this->username(); ?> - Kimai</title>
<link rel="shortcut icon" type="image/x-icon" href="../favicon.ico">

<!-- Default Stylesheets -->
<link rel="stylesheet" href="../skins/<?php echo $this->escape($this->kga['conf']['skin']); ?>/styles.css" type="text/css" media="screen" title="no title" charset="utf-8" />
<link rel="stylesheet" type="text/css" href="../skins/<?php echo $this->escape($this->kga['conf']['skin']); ?>/jquery.jqplot.css" />
<!-- /Default Stylesheets -->

<!-- Extension Stylesheets -->
<?php
    foreach ($this->extensions as $extension) {
        foreach ($extension->getStylesheets() as $css) {
            ?><link rel="stylesheet" href="../extensions/<?php echo $this->escape($extension->getDirectory())?>/<?php echo $this->escape($css)?>" type="text/css" media="screen" title="no title" charset="utf-8" /><?php
            echo "\n";
        }
    }
?>
<!-- /Extension Stylesheets -->

<!-- Libs -->
<script src="../libraries/jQuery/jquery-1.9.1.min.js" type="text/javascript" charset="utf-8"></script>
<script src="../libraries/jQuery/jquery.hoverIntent.minified.js" type="text/javascript" charset="utf-8"></script>
<script src="../libraries/jQuery/jquery.form.js" type="text/javascript" charset="utf-8"></script>
<script src="../libraries/jQuery/jquery.newsticker.pack.js" type="text/javascript" charset="utf-8"></script>
<script src="../libraries/jQuery/jquery.cookie.js" type="text/javascript" charset="utf-8"></script>
<script src="../libraries/jQuery/jquery-ui-1.10.2.min.js" type="text/javascript" charset="utf-8"></script>
<!--[if IE]><script src="../libraries/jQuery/excanvas.min.js" type="text/javascript"></script><![endif]-->
<script src="../libraries/jQuery/jquery.jqplot.min.js" type="text/javascript"></script>
<script src="../libraries/jQuery/jqplot.pieRenderer.min.js" type="text/javascript" ></script>
<script src="../libraries/jQuery/jquery-ui-timepicker/jquery.ui.timepicker.js" type="text/javascript" ></script>
<script src="../libraries/phpjs/strftime.min.js" type="text/javascript" ></script>
<script src="../libraries/jQuery/jquery.selectboxes.min.js" type="text/javascript" charset="utf-8"></script>
<script src="../libraries/jQuery/pubsub.js" type="text/javascript" charset="utf-8"></script>
<!-- /Libs -->

<!-- Default JavaScripts -->
<script src="../js/main.js" type="text/javascript" charset="utf-8"></script>
<script src="../js/init.js" type="text/javascript" charset="utf-8"></script>
<!-- /Default JavaScripts -->

<!-- Extension JavaScripts -->
<?php
    foreach ($this->extensions as $extension) {
        foreach ($extension->getJavascripts() as $js) {
            ?><script src="../extensions/<?php echo $this->escape($extension->getDirectory())?>/<?php echo $this->escape($js)?>" type="text/javascript" charset="utf-8"></script><?php
            echo "\n";
        }
    }
?>
<!-- /Extension JavaScripts -->

<script type="text/javascript">
    var skin ="<?php echo $this->escape($this->kga['conf']['skin']); ?>";
    var resizeTimer           = null;
    var lang_checkUsername    = "<?php echo $this->escape($this->kga['lang']['checkUsername']); ?>";
    var lang_checkGroupname   = "<?php echo $this->escape($this->kga['lang']['checkGroupname']); ?>";
    var lang_checkStatusname  = "<?php echo $this->escape($this->kga['lang']['checkStatusname']); ?>";
    var lang_passwordsDontMatch = "<?php echo $this->escape($this->kga['lang']['passwordsDontMatch']); ?>";
    var lang_passwordTooShort = "<?php echo $this->escape($this->kga['lang']['passwordTooShort']); ?>";
    var lang_sure             = "<?php echo $this->escape($this->kga['lang']['sure']); ?>";
    var currentRecording      = <?php echo $this->currentRecording?>;
    var openAfterRecorded     = <?php echo json_encode($this->openAfterRecorded) ?>;

    <?php if ($this->kga['conf']['quickdelete'] == 2): ?>
    var confirmText           = "<?php echo $this->escape($this->kga['lang']['sure']) ?>";
    <?php else: ?>
    var confirmText           = undefined;
    <?php endif; ?>

    <?php if (isset($this->kga['user'])): ?>
    var userID                = <?php echo $this->kga['user']['userID']; ?>;
    <?php else: ?>
    var userID                = null;
    <?php endif; ?>

    <?php if ($this->kga['conf']['noFading']): ?>
    fading_enabled = false;
    <?php endif; ?>

    var timeoutTicktack       = 0;

    var hour                  = <?php echo $this->current_timer_hour ?>;
    var min                   = <?php echo $this->current_timer_min ?>;
    var sec                   = <?php echo $this->current_timer_sec ?>;
    var startsec              = <?php echo $this->current_timer_start ?>;
    var now                   = <?php echo $this->current_time ?>;
    var offset                = Math.floor(((new Date()).getTime())/1000)-now;


    var default_title         = "<?php echo isset($this->kga['user']) ? $this->escape($this->kga['user']['name']) : $this->escape($this->kga['customer']['name'])?> - Kimai";
    var revision              = <?php echo $this->kga['revision'] ?>;
    var timeframeDateFormat   = "<?php echo $this->escape($this->kga['date_format'][2]) ?>";

    var selected_customer  = '<?php echo $this->customerData['customerID']?>';
    var selected_project   = '<?php echo $this->projectData['projectID']?>';
    var selected_activity  = '<?php echo $this->activityData['activityID']?>';

    var pickerClicked = '';

    var weekdayNames = <?php echo $this->weekdays_short_array?>;

    $.datepicker.setDefaults({
        showOtherMonths :true,
        selectOtherMonths : true,
        nextText: '',
        prevText: '',
        <?php if ($this->kga['conf']['noFading']): ?>
        showAnim: '',
        <?php endif; ?>
        dateFormat : 'dd.mm.yy', // TODO use correct format depending on admin panel setting
        dayNames: <?php echo $this->weekdays_array ?>,
        dayNamesMin: <?php echo $this->weekdays_short_array ?>,
        dayNamesShort: <?php echo $this->weekdays_short_array ?>,
        monthNames: <?php echo $this->months_array ?>,
        monthNamesShort: <?php echo $this->months_short_array ?>,
        firstDay:1 //TODO should also be depending on user setting
    });

    // internal function, do NOT rely on it in your in code
    function tabIdToExtensionId(tabId) {
        return $('#exttab_'+tabId).data('id');
    }

    function getActiveTabId() {
        return $('#fliptabs li.act').data('id');
    }

    // HOOKS
    function hook_timeframe_changed(timeframe) { $.publish('timeframe', [timeframe]); }
    function hook_buzzer_record(){ $.publish('buzzer-record'); }
    function hook_buzzer_stopped(){ $.publish('buzzer-stopped'); }
    function hook_users_changed(){ lists_reload("user"); $.publish('users'); }
    function hook_customers_changed(){ lists_reload("customer"); lists_reload("project"); $.publish('customers'); }
    function hook_projects_changed(){ lists_reload("project"); $.publish('projects'); }
    function hook_activities_changed(){ lists_reload("activity"); $.publish('activities'); }
    function hook_filter(){ $.publish('filter'); }
    function hook_resize(){
        // using timeout prevents the resizing being triggered to often
        if (resizeTimer) clearTimeout(resizeTimer);
        resizeTimer = setTimeout(function(){ $.publish('resize', [getActiveTabId()]); }, 100);
    }
    function kimai_onload() {
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

        if ($('#row_activity[data-id="'+selected_activity+'"]').length == 0) {
            $('#buzzer').addClass('disabled');
        }

        $.publish('onload');
        skin_onload();
    }
</script>
