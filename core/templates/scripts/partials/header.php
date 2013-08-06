
    <div id="logo">
        <img src="../skins/<?php echo $this->escape($this->kga['conf']['skin'])?>/grfx/g3_logo.png" width="151" height="52" alt="Logo" />
    </div>

    <div id="menu">
        <a id="main_logout_button" href="../index.php?a=logout"><img src="../skins/<?php echo $this->escape($this->kga['conf']['skin'])?>/grfx/g3_menu_logout.png" width="36" height="27" alt="Logout" /></a>
        <a id="main_tools_button" href="#" ><img src="../skins/<?php echo $this->escape($this->kga['conf']['skin'])?>/grfx/g3_menu_dropdown.png" width="44" height="27" alt="Menu Dropdown" /></a>
        <br/><?php echo $this->kga['lang']['logged_in_as']?> <b><?php echo isset($this->kga['user']) ? $this->escape($this->kga['user']['name']) : $this->escape($this->kga['customer']['name'])?></b>
    </div>

    <div id="main_tools_menu">
        <div class="slider">
            <a href="#" id="main_credits_button"><?php echo $this->kga['lang']['about'] ?> Kimai</a> |
            <a href="#" id="main_prefs_button"><?php echo $this->kga['lang']['preferences'] ?></a>
        </div>
        <div class="end"></div>
    </div>

    <div id="display">
        <script type="text/javascript" charset="utf-8">
            $(function()
            {
                $('.date-pick').datepicker(
                    {dateFormat:'mm/dd/yy',
                        onSelect: function(dateText, instance) {
                            if (this == $('#pick_in')[0]) {
                                setTimeframe(new Date(dateText),undefined);
                            }
                            if (this == $('#pick_out')[0]) {
                                setTimeframe(undefined,new Date(dateText));
                            }
                        }
                    });

                setTimeframeStart(new Date(<?php echo $this->timeframe_in*1000?>));
                setTimeframeEnd(new Date(<?php echo $this->timeframe_out*1000?>));
                updateTimeframeWarning();

            });
        </script>


        <div id="dates">
            <input type="hidden" id="pick_in" class="date-pick"/>
            <a href="#" id="ts_in" onClick="$('#pick_in').datepicker('show');return false"></a> -
            <input type="hidden" id="pick_out" class="date-pick"/>
            <a href="#" id="ts_out" onClick="$('#pick_out').datepicker('show');return false"></a>
        </div>


        <div id="infos">
            <span id="n_date"></span> &nbsp;
            <img src="../skins/<?php echo $this->escape($this->kga['conf']['skin'])?>/grfx/g3_display_smallclock.png" width="13" height="13" alt="Display Smallclock" />
            <span id="n_uhr">00:00</span> &nbsp;
            <img src="../skins/<?php echo $this->escape($this->kga['conf']['skin'])?>/grfx/g3_display_eye.png" width="15" height="12" alt="Display Eye" />
            <strong id="display_total"><?php echo $this->total?></strong>
        </div>
    </div>

    <?php if (isset($this->kga['user'])): ?>
    <div id="selector">
        <div class="preselection">

            <strong><?php echo $this->kga['lang']['selectedForRecording']?></strong><br />

            <strong class="short"><?php echo $this->kga['lang']['selectedCustomerLabel']?></strong><span class="selection" id="selected_customer"><?php echo $this->escape($this->customerData['name'])?></span><br/>
            <strong class="short"><?php echo $this->kga['lang']['selectedProjectLabel']?></strong><span class="selection" id="selected_project"><?php echo $this->escape($this->projectData['name'])?></span><br/>
            <strong class="short"><?php echo $this->kga['lang']['selectedActivityLabel']?></strong><span class="selection" id="selected_activity"><?php echo $this->escape($this->activityData['name'])?></span><br/>
        </div>
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
    <?php endif; ?>