<?php

foreach ($this->projects as $project)
{
    $projectId = $project['projectID'];
    $projectPlotData = $this->plotdata[$projectId];
    ?>
    <div class="budget_project">
        <div class="project_head table_header project_overview">
            <?php echo $this->escape($this->truncate($project['customerName'], 28, '...')) ?>
            <br>
            <?php echo $this->escape($this->truncate($project['name'], 28, '...')) ?>
        </div>
        <div id="budget_chartdiv_<?php echo $project['projectID'] ?>" class="budget_plot_area" style="height:140px;width:200px;"></div>
        <table class="data">
            <tr>
                <td class="total"><?php echo $this->translate('total'); ?>:</td>
                <td><?php echo sprintf('%.2f', $projectPlotData['total']) ?></td>
            </tr>
            <tr>
                <td class="budget"><?php echo $this->translate('budget'); ?>:</td>
                <td><?php echo sprintf('%.2f', $projectPlotData['budget']) ?></td>
            </tr>
            <tr>
                <td class="billable"><?php echo $this->translate('billable'); ?>:</td>
                <td><?php echo sprintf('%.2f', $projectPlotData['billable_total']) ?></td>
            </tr>
            <tr>
                <td class="approved"><?php echo $this->translate('approved'); ?>:</td>
                <td><?php echo sprintf('%.2f', $projectPlotData['approved']) ?></td>
            </tr>
            <?php
            if ($projectPlotData['budget'] - $projectPlotData['budget'] < 0) {
                ?>
                <tr>
                    <td class="budgetminus"><?php echo $this->kga['lang']['budget_minus'] ?>:</td>
                    <td><?php
                        $budget = $projectPlotData['budget'];
                        $total = $projectPlotData['total'];
                        $makePlus = 1;
                        echo round(($budget - $total) * -$makePlus, 2) ?></td>
                </tr>
                <?php
            }
            ?>
        </table>
    </div>
    <?php
    foreach ($projectPlotData as $id => $activity)
    {
        if (!is_array($activity) || !isset($activity['name'])) {
            continue;
        }
        ?>
        <div class="budget_project">
            <div class="project_head table_header">
                <?php echo $this->escape($this->truncate($project['name'], 28, '...')); ?>
                <br>
                <?php echo $this->escape($this->truncate($activity['name'], 28, '...')); ?>
            </div>
            <div id="budget_chartdiv_<?php echo $project['projectID'] ?>_activity_<?php echo $id ?>" class="budget_plot_area" style="height:140px;width:200px; "></div>
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
<div class="budget_project_end"></div>
<script type="text/javascript">
    $(document).ready(function() {
        chartColors = <?php echo $this->chartColors ?>;
        budget_extension_plot(<?php echo json_encode($this->plotdata) ?>);
        recalculate_budget_window();
    });
</script>