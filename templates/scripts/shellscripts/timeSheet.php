<?php
if ($this->timeSheetEntries) :
	$headerStyleLogo = 'padding: 12px; background: #545454;';
	$headerStyle = 'padding: 6px 8px 8px 6px; font-weight: bold; color: white; background: #484848;';

	$colspan = 8;
	if ($this->showRates) {
		$colspan ++;
	}
	if ($this->showTrackingNumber) {
		$colspan ++;
	}
?>
<html>
<head>
	<meta http-equiv="content-type" content="text/html; charset=utf-8">
</head>
<body>
<table style="border-collapse: collapse; font-size: 11px; color: rgb(54, 54, 54); border-bottom-width: 1px; border-bottom-style: solid; border-bottom-color: rgb(136, 136, 136); font-family: verdana, 'MS Trebuchet', sans-serif; background-color: #eeeeee;">
	<tbody>
	<tr>
		<td style="<?php echo $headerStyleLogo; ?>" colspan="<?php echo $colspan?>"><img src="<?php echo $this->embed['logo']?>" alt="Kimai Logo"/></td>
	</tr>
	<tr>
		<td style="<?php echo $headerStyle; ?>"><?php echo $this->translate('datum') ?></td>
		<td style="<?php echo $headerStyle; ?>"><?php echo $this->translate('in') ?></td>
		<td style="<?php echo $headerStyle; ?>"><?php echo $this->translate('out') ?></td>
		<td style="<?php echo $headerStyle; ?>"><?php echo $this->translate('time') ?></td>
		<?php if ($this->showRates): ?>
			<td style="<?php echo $headerStyle; ?>"><?php echo $this->translate('wage') ?></td>
		<?php endif; ?>
		<td style="<?php echo $headerStyle; ?>"><?php echo $this->translate('customer') ?></td>
		<td style="<?php echo $headerStyle; ?>"><?php echo $this->translate('project') ?></td>
		<td style="<?php echo $headerStyle; ?>"><?php echo $this->translate('activity') ?></td>
		<?php if ($this->showTrackingNumber) { ?>
			<td style="<?php echo $headerStyle; ?>"><?php echo $this->translate('trackingNumber') ?></td>
		<?php } ?>
		<td style="<?php echo $headerStyle; ?>"><?php echo $this->translate('username') ?></td>
	</tr>
<?php
	$tdBaseStyle = 'border-bottom: none; border-left: none; border-right: 1px dotted #CCC; padding: 6px;';
	$tdStyleOdd =  $tdBaseStyle . 'background-color: #EEE;';
	$tdStyleTimeOdd =  $tdBaseStyle . 'background-color: #64BF61;';
	$tdStyleEven = $tdBaseStyle . 'background-color: #FFF;';
	$tdStyleTimeEven = $tdBaseStyle . 'background-color: #A4E7A5;';

	foreach ($this->timeSheetEntries as $row) {
		$tdStyle = $this->cycle([$tdStyleOdd, $tdStyleEven], 'tdStyle')->next()->toString();
		$tdStyleTime = $this->cycle([$tdStyleTimeOdd, $tdStyleTimeEven], 'tdStyleTime')->next()->toString();
?>
		<tr>
			<td style="<?php echo $tdStyle; ?>"><?php echo $this->escape(strftime($this->kga['date_format_1'], $row['start']));?></td>
			<td style="<?php echo $tdStyle; ?>"><?php echo $this->escape(strftime("%H:%M", $row['start']));?></td>
			<td style="<?php echo $tdStyle; ?>"><?php
				if ($row['end']) {
					echo $this->escape(strftime("%H:%M", $row['end']));
				} else {
					echo "&ndash;&ndash;:&ndash;&ndash;";
				}
			?></td>
			<td style="<?php echo $tdStyleTime; ?>"><?php
				if (isset($row['duration'])) {
					echo $row['formattedDuration'];
				} else {
					echo "&ndash;:&ndash;&ndash;";
				}
			?></td>
			<?php if ($this->showRates): ?>
				<td style="<?php echo $tdStyle; ?> "><?php
				if (isset($row['wage'])) {
					echo $this->escape(str_replace('.',$this->kga['conf']['decimalSeparator'], $row['wage']));
				} else {
					echo "&ndash;";
				}
				?></td>
			<?php endif; ?>
			<td style="<?php echo $tdStyle; ?>"><?php echo $this->escape($row['customerName']) ?></td>
			<td style="<?php echo $tdStyle; ?>"><?php echo $this->escape($row['projectName']) ?></td>
			<td style="<?php echo $tdStyle; ?>"><?php echo $this->escape($row['activityName']);
				if ($row['description']) {
					echo '<br><br><em>'.nl2br($this->escape($row['description'])).'</em>';
				}
				?></td>
			<?php if ($this->showTrackingNumber) : ?>
				<td style="<?php echo $tdStyle; ?>">
					<?php echo $this->escape($row['trackingNumber']) ?>
				</td>
			<?php endif ?>
			<td style="<?php echo $tdStyle; ?>"><?php echo $this->escape($row['userName']) ?></td>
		</tr>
<?php } ?>
		</tbody>
	</table>
</body>
</html>
<?php endif ?>