<script type="text/javascript">
$(document).ready(function() {
    chartColors = <?php echo $this->chartColors ?>;
    budget_extension_plot(<?php echo $this->javascript_arr_plotdata ?>);
    recalculateWindow();
 });
</script>

<?php
foreach ($this->projects as $project)
{
    if (array_search($project['projectID'],$this->projects_selected) === false) {
        continue;
    }
    ?>

    <div class="budget_project">
        <div class="project_head project_overview">
            <?php echo $this->escape($project['name']) ?>
        </div>
        <div id="budget_chartdiv_<?php echo $project['projectID']?>" class="budget_plot_area" style="height:140px;width:200px;"></div>
        <?php $temp = $project['projectID']?>
        <span class="total">Total: <?php echo sprintf("%.2f", $this->arr_plotdata[$temp]['total']) ?></span><br/>
        <span class="budget">Budget: <?php echo sprintf("%.2f", $this->arr_plotdata[$temp]['budget']) ?></span> <br/>
        <span class="approved">Billable: <?php echo sprintf("%.2f", $this->arr_plotdata[$temp]['billable_total']) ?></span><br/>
        <span class="approved">Approved: <?php echo sprintf("%.2f", $this->arr_plotdata[$temp]['approved']) ?></span>
        <?php if ($this->arr_plotdata[$temp]['budget']-$this->arr_plotdata[$temp]['budget'] < 0) { ?>
            <br>
            <span style="text-color: red;" class="budgetminus"><?php echo $this->kga['lang']['budget_minus']?>: <?php
            $budget = $this->arr_plotdata[$temp]['budget'];
            $total = $this->arr_plotdata[$temp]['total'];
            $makePlus = 1;
            echo round(($budget-$total)*-$makePlus,2)?> </span>
            <br/>
        <?php } ?>
    </div>

    <?php
    foreach ($this->arr_plotdata[$temp] as $id => $activity)
    {
        if (array_search($id,$this->activities_selected)  === false) {
            continue;
        }

        ?>
        <div class="budget_project">
            <div class="project_head">
                <?php echo $this->escape($project['name']), '&nbsp', $this->escape($activity['name'])?>
            </div>
            <div id="budget_chartdiv_<?php echo $project['projectID']?>_activity_<?php echo $id?>" class="budget_plot_area" style="height:140px;width:200px; "></div>
            <span class="total">Total: <?php echo sprintf("%.2f", $activity['total'])?></span><br>
            <span class="budget">Budget: <?php echo sprintf("%.2f", $activity['budget_total'])?></span> <br>
            <span class="approved">Approved: <?php sprintf("%.2f", $activity['approved_total'])?></span>
            <?php if ($activity['budget'] <= 0) { ?>
                <br>
                <span style="text-color: red;" class="budgetminus"><?php echo $this->kga['lang']['budget_minus']?>: <?php
                $budget = $activity['budget_total'];
                $total = $activity['total'];
                $makePlus = 1;
                echo round(($budget-$total)*-$makePlus,2) ?></span>
                <br/>
            <?php } ?>
        </div>
        <?php /*
        {counter start=0 skip=1 assign="count"}

        {if $count is div by 7 && $count > 1}
            <br style="line-height: 250px;"/>
        {/if}
        {counter}
        */ ?>
        <?php
    }
    ?>

    <br>
    <?php /*
    {assign var=numberOfEvents value=$arr_plotdata[$temp]|@count}
    <br style="line-height: {math equation="ceil(number / max) * height" number=$numberOfEvents max=7 height=270}px;"/>
    <br style="line-height: 250px;"/>
    */ ?>

    <?php
}
?>
<div class="budget_project_end"> </div>