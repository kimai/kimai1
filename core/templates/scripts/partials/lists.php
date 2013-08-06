<?php
// render the box header
foreach($this->list_entries as $listEntry)
{
    ?>
    <div id="<?php echo $listEntry['id']; ?>_head">
        <input class="livefilterfield" onkeyup="lists_live_filter('<?php echo $listEntry['id']; ?>', this.value);" type="text" id="<?php echo $listEntry['filter']; ?>" name="<?php echo $listEntry['filter']; ?>"/>
        <?php echo $listEntry['title']; ?>
    </div>
    <?php
}

// render the main body
foreach($this->list_entries as $listEntry)
{
    ?>
    <div id="<?php echo $listEntry['id']; ?>"><?php echo $listEntry['content']; ?></div>
    <?php
}

// render the box footer
foreach($this->list_entries as $listEntry)
{
    ?>
    <div id="<?php echo $listEntry['id']; ?>_foot">
        <a href="#" class="selectAllLink" onClick="lists_filter_select_all('<?php echo $listEntry['id']; ?>'); $(this).blur(); return false;"></a>
        <a href="#" class="deselectAllLink" onClick="lists_filter_deselect_all('<?php echo $listEntry['id']; ?>'); $(this).blur(); return false;"></a>
        <a href="#" class="selectInvertLink" onClick="lists_filter_select_invert('<?php echo $listEntry['id']; ?>'); $(this).blur(); return false;"></a>
        <div style="clear:both"></div>
    </div>
<?php
}
