<?php
if ($this->expenses) {
    $showGabBreaks = $this->kga->isShowGabBreaks();
    $showDaySeperatorLines = $this->kga->isShowDaySeperatorLines();
    ?>
    <div id="exptable">
	    <table>
		    <colgroup>
			    <col class="option"/>
			    <col class="date"/>
			    <col class="time"/>
			    <col class="value"/>
			    <col class="refundable"/>
			    <col class="client"/>
			    <col class="project"/>
			    <col class="designation"/>
			    <col class="username"/>
		    </colgroup>
	    <tbody>
	    <?php
	    $day_buffer = 0;
	    $timestamp_buffer = 0;

	    foreach ($this->expenses as $row) {
            $cur_day_buffer = strftime('%d', $row['timestamp']);
            $cur_timestamp_buffer = strftime('%H%M', $row['timestamp']);
            $tdClass = '';
            if ($cur_day_buffer != $day_buffer && $showDaySeperatorLines) {
                $tdClass = 'break_day';
            } elseif ($cur_timestamp_buffer != $timestamp_buffer && $showGabBreaks) {
                $tdClass = 'break_gap';
            }
	        ?>
	        <tr id="expEntry<?php echo $row['expenseID']?>" class="<?php echo $this->cycle(['odd', 'even'])->next()?>">
	            <td nowrap class="option <?php echo $tdClass ?>">
	            <?php if (isset($this->kga['user']) && (!$this->kga->isEditLimit() || time() - $row['timestamp'] <= $this->kga->getEditLimit())): ?>
	                <a href="#" onclick="expense_editRecord(<?php echo $row['expenseID']?>); $(this).blur(); return false;" title='<?php echo $this->translate('edit')?>'>
	                <img src="<?php echo $this->skin('grfx/edit2.gif'); ?>" width="13" height="13" alt="<?php echo $this->translate('edit')?>" title="<?php echo $this->translate('edit')?>" border="0" /></a>

	                <?php if ($this->kga->getSettings()->isShowQuickDelete()): ?>
	                    <a href="#" class="quickdelete" onclick="expense_quickdelete(<?php echo $row['expenseID']?>); return false;">
	                        <img src="<?php echo $this->skin('grfx/button_trashcan.png'); ?>" width="13" height="13" alt="<?php echo $this->translate('quickdelete')?>" title="<?php echo $this->translate('quickdelete')?>" border="0" />
	                    </a>
	                <?php endif; ?>
	            <?php endif; ?>
	            </td>
	            <td class="date <?php echo $tdClass ?>">
	                <?php echo $this->escape(strftime($this->kga->getDateFormat(1), $row['timestamp'])) ?>
	            </td>
	            <td class="time <?php echo $tdClass ?>">
	                <?php echo $this->escape(strftime("%H:%M", $row['timestamp'])) ?>
	            </td>
	            <td class="value <?php echo $tdClass ?>">
	                <?php echo $this->escape( number_format($row['value']*$row['multiplier'], 2, $this->kga['conf']['decimalSeparator'],'') ) ?>
	            </td>
	            <td class="refundable <?php echo $tdClass ?>">
		            <?php echo $row['refundable'] ? $this->translate('yes') : $this->translate('no') ?>
	            </td>
	            <td class="customer <?php echo $tdClass ?>">
	                <?php echo $this->escape($row['customerName'])?>
	            </td>
	            <td class="project <?php echo $tdClass ?>">
	                <?php echo $this->escape($row['projectName'])?>
	            </td>
	            <td class="designation <?php echo $tdClass ?>">
	                <?php echo $this->escape($row['designation'])?>
	                <?php if ($row['comment']): ?>
	                    <?php if ($row['commentType'] == '0'): ?>
	                        <a href="#" onclick="comment(<?php echo $row['expenseID']?>); $(this).blur(); return false;"><img src="<?php echo $this->skin('grfx/blase.gif'); ?>" width="12" height="13" title='<?php echo $this->escape($row['comment']);?>' border="0" /></a>
	                    <?php elseif ($row['commentType'] == '1'): ?>
	                        <a href="#" onclick="comment(<?php echo $row['expenseID']?>); $(this).blur(); return false;"><img src="<?php echo $this->skin('grfx/blase_sys.gif'); ?>" width="12" height="13" title='<?php echo $this->escape($row['comment']);?>' border="0" /></a>
	                    <?php elseif ($row['commentType'] == '2'): ?>
	                        <a href="#" onclick="comment(<?php echo $row['expenseID']?>); $(this).blur(); return false;"><img src="<?php echo $this->skin('grfx/blase_caution.gif'); ?>" width="12" height="13" title='<?php echo $this->escape($row['comment']);?>' border="0" /></a>
	                    <?php endif; ?>
	                <?php endif; ?>
	            </td>
	            <td class="username <?php echo $tdClass ?>">
	                <?php echo $this->escape($row['userName'])?>
	            </td>
	        </tr>
	        <?php
	        if ($row['comment']) {
	            ?>
	            <tr id="expense_c<?php echo $row['expenseID']?>" class="comm<?php echo $row['commentType']?>" <?php if ($this->hideComments): ?> style="display:none;"<?php endif; ?>>
	                <td colspan="8"><?php echo nl2br($this->escape($row['comment']));?></td>
	            </tr>
	            <?php
	        }
	        $day_buffer = $cur_day_buffer;
	        $timestamp_buffer = $cur_timestamp_buffer;
	    }
	    ?>
	    </tbody>
    </table>
    </div>
    <?php
} else {
    echo $this->error();
}
?>
<script type="text/javascript">
    expense_user_annotations = <?php echo json_encode($this->user_annotations); ?>;
    expense_customer_annotations = <?php echo json_encode($this->customer_annotations) ?>;
    expense_project_annotations = <?php echo json_encode($this->project_annotations) ?>;
    expense_activity_annotations = <?php echo json_encode($this->activity_annotations) ?>;
    expenses_total = '<?php echo $this->total?>';

    lists_update_annotations(parseInt($('#gui div.ki_expenses').attr('id').substring(7)),expense_user_annotations,expense_customer_annotations,expense_project_annotations,expense_activity_annotations);
    $('#display_total').html(expenses_total);
</script>
