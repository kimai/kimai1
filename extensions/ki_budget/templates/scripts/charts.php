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
    if (array_search($project['projectID'], $this->projects_selected) === false) {
        continue;
    }

    $temp = $project['projectID'];

    // do not render projects that have only empty values
    if ($this->arr_plotdata[$temp]['total'] == 0 &&
        $this->arr_plotdata[$temp]['budget'] == 0 &&
        (!isset($this->arr_plotdata[$temp][0]['expenses']) || $this->arr_plotdata[$temp][0]['expenses'] == 0)) {
        continue;
    }
    ?>
    <div class="budget_project">
        <div class="project_head project_overview">
            <?php echo $this->escape($project['customerName']) ?>
            <br>
            <?php echo $this->escape($project['name']) ?>
        </div>
        <div id="budget_chartdiv_<?php echo $project['projectID'] ?>" class="budget_plot_area"
             style="height:140px;width:200px;"></div>
        <table class="data">
            <tr>
                <td class="total"><?php echo $this->translate('total'); ?>:</td>
                <td><?php echo sprintf("%.2f", $this->arr_plotdata[$temp]['total']) ?></td>
            </tr>
            <tr>
                <td class="budget"><?php echo $this->translate('budget'); ?>:</td>
                <td><?php echo sprintf("%.2f", $this->arr_plotdata[$temp]['budget']) ?></td>
            </tr>
            <tr>
                <td class="billable"><?php echo $this->translate('billable'); ?>:</td>
                <td><?php echo sprintf("%.2f", $this->arr_plotdata[$temp]['billable_total']) ?></td>
            </tr>
            <tr>
                <td class="approved"><?php echo $this->translate('approved'); ?>:</td>
                <td><?php echo sprintf("%.2f", $this->arr_plotdata[$temp]['approved']) ?></td>
            </tr>
            <?php
            if ($this->arr_plotdata[$temp]['budget'] - $this->arr_plotdata[$temp]['budget'] < 0) {
                ?>
                <tr>
                    <td class="budgetminus"><?php echo $this->kga['lang']['budget_minus'] ?>:</td>
                    <td><?php
                        $budget = $this->arr_plotdata[$temp]['budget'];
                        $total = $this->arr_plotdata[$temp]['total'];
                        $makePlus = 1;
                        echo round(($budget - $total) * -$makePlus, 2) ?></td>
                </tr>
                <?php
            }
            ?>
        </table>
    </div>

    <?php
    foreach ($this->arr_plotdata[$temp] as $id => $activity)
    {
        if (array_search($id, $this->activities_selected) === false) {
            continue;
        }

        if ($activity['total'] == 0 &&
            $activity['budget'] == 0 && $activity['budget_total'] == 0 &&
            $activity['approved'] == 0 && $activity['approved_total'] == 0
        ) {
                continue;
        }

        ?>
        <div class="budget_project">
            <div class="project_head">
                <?php echo $this->escape($project['name']); ?>
                <br>
                <?php echo $this->escape($activity['name']); ?>
            </div>
            <div id="budget_chartdiv_<?php echo $project['projectID'] ?>_activity_<?php echo $id ?>"
                 class="budget_plot_area" style="height:140px;width:200px; "></div>
            <table class="data">
                <tr>
                    <td class="total"><?php echo $this->translate('total'); ?>:</td>
                    <td><?php echo sprintf("%.2f", $activity['total']) ?></td>
                </tr>
                <tr>
                    <td class="budget"><?php echo $this->translate('budget'); ?>:</td>
                    <td><?php echo sprintf("%.2f", $activity['budget_total']) ?></td>
                </tr>
                <tr>
                    <td class="approved"><?php echo $this->translate('approved'); ?>:</td>
                    <td><?php echo sprintf("%.2f", $activity['approved_total']) ?></td>
                </tr>
                <?php
                if ($activity['budget'] <= 0)
                {
                    ?>
                    <tr>
                        <td class="budgetminus"><?php echo $this->kga['lang']['budget_minus'] ?>:</td>
                        <td><?php
                            $budget = $activity['budget_total'];
                            $total = $activity['total'];
                            $makePlus = 1;
                            echo round(($budget - $total) * -$makePlus, 2) ?></td>
                    </tr>
                    <?php
                }
                ?>
            </table>
        </div>
        <?php
    }
    ?>
    <br>
    <?php
}
?>
<div class="budget_project_end"/>
</div>