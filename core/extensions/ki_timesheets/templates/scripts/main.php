    <script type="text/javascript"> 
        $(document).ready(function() {
            ts_ext_onload();
        }); 
    </script>

<div id="timeSheet_head">
    <div class="left">
    <?php if (isset($this->kga['user'])): ?>
        <a href="#" onClick="floaterShow('../extensions/ki_timesheets/floaters.php','add_edit_timeSheetEntry',selected_project+'|'+selected_activity,0,650); $(this).blur(); return false;"><?php echo $this->kga['lang']['add']?></a>
    <?php endif; ?>
    </div>
    <table>
        <colgroup>
<?php
if($this->visibleColumn['option']) echo "            <col class=\"option\" />\n";
if($this->visibleColumn['date']) echo "            <col class=\"date\" />\n";
if($this->visibleColumn['from']) echo "            <col class=\"from\" />\n";
if($this->visibleColumn['to']) echo "            <col class=\"to\" />\n";
if($this->visibleColumn['time']) echo "            <col class=\"time\" />\n";
if($this->visibleColumn['wage']) echo "            <col class=\"wage\" />\n";
if($this->visibleColumn['customer']) echo "            <col class=\"customer\" />\n";
if($this->visibleColumn['project']) echo "            <col class=\"project\" />\n";
if($this->visibleColumn['activity']) echo "            <col class=\"activity\" />\n";
if($this->visibleColumn['trackingnumber']) echo "            <col class=\"trackingnumber\" />\n";
if($this->visibleColumn['username']) echo "            <col class=\"username\" />\n";
?>
        </colgroup>
        <tbody>
            <tr>
<?php
if($this->visibleColumn['option']) echo "                <td class=\"option\">&nbsp;</td>\n";
if($this->visibleColumn['date']) echo "                <td class=\"date\">" . $this->kga['lang']['datum'] . "</td>\n";
if($this->visibleColumn['from']) echo "                <td class=\"from\">" . $this->kga['lang']['in'] . "</td>\n";
if($this->visibleColumn['to']) echo "                <td class=\"to\">" . $this->kga['lang']['out'] . "</td>\n";
if($this->visibleColumn['time']) echo "                <td class=\"time\">" . $this->kga['lang']['time'] . "</td>\n";
if($this->visibleColumn['wage']) echo "                <td class=\"wage\">" . $this->kga['lang']['wage'] . "</td>\n";
if($this->visibleColumn['customer']) echo "                <td class=\"customer\">" . $this->kga['lang']['customer'] . "</td>\n";
if($this->visibleColumn['project']) echo "                <td class=\"project\">" . $this->kga['lang']['project'] . "</td>\n";
if($this->visibleColumn['activity']) echo "                <td class=\"activity\">" . $this->kga['lang']['activity'] . "</td>\n";
if($this->visibleColumn['trackingnumber']) echo "                <td class=\"trackingnumber\">" . $this->kga['lang']['trackingNumber'] . "</td>\n";
if($this->visibleColumn['username']) echo "                <td class=\"username\">" . $this->kga['lang']['username'] . "</td>\n";
?>
            </tr>
        </tbody>
    </table>
</div>

<div id="timeSheet"><?php echo $this->timeSheet_display?> </div>
