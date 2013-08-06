<div id="users_head">
    <input class="livefilterfield" onkeyup="lists_live_filter('users', this.value);" type="text" id="filt_user" name="filt_user"/>
    <?php echo $this->kga['lang']['users'] ?>
</div>

<div id="customers_head">
    <input class="livefilterfield" onkeyup="lists_live_filter('customers', this.value);" type="text" id="filter_customer" name="filter_customer"/>
    <?php echo $this->kga['lang']['customers'] ?>
</div>

<div id="projects_head">
    <input class="livefilterfield" onkeyup="lists_live_filter('projects', this.value);" type="text" id="filter_project" name="filter_project"/>
    <?php echo $this->kga['lang']['projects'] ?>
</div>

<div id="activities_head">
    <input class="livefilterfield" onkeyup="lists_live_filter('activities', this.value);" type="text" id="filter_activity" name="filter_activity"/>
    <?php echo $this->kga['lang']['activities']?>
</div>

<div id="users"><?php echo $this->user_display?></div>
<div id="customers"><?php echo $this->customer_display?></div>
<div id="projects"><?php echo $this->project_display?></div>
<div id="activities"><?php echo $this->activity_display?></div>

<div id="users_foot">
    <a href="#" class="selectAllLink" onClick="lists_filter_select_all('users'); $(this).blur(); return false;"></a>
    <a href="#" class="deselectAllLink" onClick="lists_filter_deselect_all('users'); $(this).blur(); return false;"></a>
    <a href="#" class="selectInvertLink" onClick="lists_filter_select_invert('users'); $(this).blur(); return false;"></a>
    <div style="clear:both"></div>
</div>

<div id="customers_foot">
    <?php if ($this->show_customer_add_button): ?>
        <a href="#" class="addLink" onClick="floaterShow('floaters.php','add_edit_customer',0,0,450); $(this).blur(); return false;"></a>
    <?php endif; ?>
    <a href="#" class="selectAllLink" onClick="lists_filter_select_all('customers'); $(this).blur(); return false;"></a>
    <a href="#" class="deselectAllLink" onClick="lists_filter_deselect_all('customers'); $(this).blur(); return false;"></a>
    <a href="#" class="selectInvertLink" onClick="lists_filter_select_invert('customers'); $(this).blur(); return false;"></a>
    <div style="clear:both"></div>
</div>

<div id="projects_foot">
    <?php if ($this->show_project_add_button): ?>
        <a href="#" class="addLink" onClick="floaterShow('floaters.php','add_edit_project',0,0,650); $(this).blur(); return false;"></a>
    <?php endif; ?>
    <a href="#" class="selectAllLink" onClick="lists_filter_select_all('projects'); $(this).blur(); return false;"></a>
    <a href="#" class="deselectAllLink" onClick="lists_filter_deselect_all('projects'); $(this).blur(); return false;"></a>
    <a href="#" class="selectInvertLink" onClick="lists_filter_select_invert('projects'); $(this).blur(); return false;"></a>
    <div style="clear:both"></div>
</div>

<div id="activities_foot">
    <?php if ($this->show_activity_add_button): ?>
        <a href="#" class="addLink" onClick="floaterShow('floaters.php','add_edit_activity',0,0,450); $(this).blur(); return false;"></a>
    <?php endif; ?>
    <a href="#" class="selectAllLink" onClick="lists_filter_select_all('activities'); $(this).blur(); return false;"></a>
    <a href="#" class="deselectAllLink" onClick="lists_filter_deselect_all('activities'); $(this).blur(); return false;"></a>
    <a href="#" class="selectInvertLink" onClick="lists_filter_select_invert('activities'); $(this).blur(); return false;"></a>
    <div style="clear:both"></div>
</div>