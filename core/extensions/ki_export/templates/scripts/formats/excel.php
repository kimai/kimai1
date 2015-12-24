<?php
/**
 * This file is part of
 * Kimai - Open Source Time Tracking // http://www.kimai.org
 * (c) 2006-2009 Kimai-Development-Team
 *
 * Kimai is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; Version 3, 29 June 2007
 *
 * Kimai is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Kimai; If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * View to render export excel
 * uses PHPExcel
 * @author Florian Lentsch <office@florian-lentsch.at>
 */

require_once 'PHPExcel/Classes/PHPExcel.php';

/**
 * Column configuration:
 * <field name as in $this->columns> => [
 * 		'fieldName' => <field name as in $this->exportData - defaults to the same field name as in $this->columns>
 * 		'type' => <determines field formatting - one of string (default), date, time, duration, floatDurationHours, boolean, percent, and text>
 * 		'langLabel' => <key for $this->kga['lang'] - defaults to the same as the field name in $this->columns>
 * 		'sum' => boolean - if true, sums will be generated for this field 
 * ]
 */
$COLUMN_CONFIG = array(
		'date' => array('fieldName' => 'time_in', 'type' => 'date', 'langLabel' => 'datum'),
		'from' => array('fieldName' => 'time_in', 'type' => 'time', 'langLabel' => 'in'),
		'to' => array('fieldName' => 'time_out', 'type' => 'time', 'langLabel' => 'out'),
		'time' => array('fieldName' => 'duration', 'type' => 'duration', 'sum' => true),
		'dec_time' => array('fieldName' => 'duration', 'type' => 'floatDurationHours', 'langLabel' => 'timelabel', 'sum' => true),
		'rate' => array('type' => 'float'),
		'wage' => array('type' => 'money', 'sum' => true),
		'budget' => array('type' => 'money', 'sum' => true),
		'approved' => array('type' => 'boolean'),
		'billable' => array('type' => 'percent'),
		'customer' => array('fieldName' => 'customerName'),
		'project' => array('fieldName' => 'projectName'),
		'activity' => array('fieldName' => 'activityName'),
		'description' => array('type' => 'text'),
		'comment' => array('type' => 'text'),
		'user' => array('fieldName' => 'username', 'langLabel' => 'username'),
		'cleared' => array('type' => 'boolean'),
);

// vertical offset for data rows:
$EXCEL_HEADER_OFFSET = 1;

/**
 * Utility functions - TODO: Maybe move them elsewhere:
 */

/**
 * get excel column letter by index (e.g. '2' becomes 'C')
 * @param integer $n column index
 * @return string excel column letter
 */
function excelColumnAddr($n) {
	$r = '';
	for ($i = 1; $n >= 0 && $i < 10; $i++) {
		$r = chr(0x41 + ($n % pow(26, $i) / pow(26, $i - 1))) . $r;
		$n -= pow(26, $i);
	}
	return $r;
}

/**
 * get excel field address by supplying coordinates (e.g. '1/1' becomes 'B2')
 * @param integer $x horizontal coordinate
 * @param integer $y vertical coordinate
 * @return string excel field address
 */
function excelAddr($x,$y) {
	return excelColumnAddr($x).($y+1);
}

/**
 * get excel field range by supplying coordinates
 * @param integer first corner's horizontal coordinate
 * @param integer first corner's vertical coordinate
 * @param integer second corner's horizontal coordinate
 * @param integer second corner's vertical coordinate
 * @return string excel field range
 */
function excelRange($x1,$y1,$x2,$y2) {
	return excelAddr($x1,$y1).':'.excelAddr($x2,$y2);
}


/**
 * Initialization:
 */
PHPExcel_Settings::setLocale($this->kga['language']);
$generator = new PHPExcel();
$sheet = $generator->setActiveSheetIndex(0);
$sheet->setTitle("Export");
// increase chances that page can be printed out without seperating a row's data over more than one page:
$sheet->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);


$columns = $this->columns;
unset($columns['cleared']); // <- cannot be toggled - so for now, don't show it at all
$activeColumns_arr = array_keys(array_filter($columns, function($active) {
	return $active;
}));


/**
 * Headers:
 */
foreach($activeColumns_arr as $x => $columnName) {
	if(array_key_exists($columnName, $COLUMN_CONFIG) && array_key_exists('langLabel', $COLUMN_CONFIG[$columnName])) {
		$columnName = $COLUMN_CONFIG[$columnName]['langLabel'];
	}
	
	$columnLabel = $this->kga['lang'][$columnName];
	$sheet->setCellValue(excelAddr($x, 0), $columnLabel);
}

/**
 * Data:
 */
foreach($this->exportData as $y => $data) {
	foreach($activeColumns_arr as $x => $columnName) {
		$curAddr = excelAddr($x, $y + $EXCEL_HEADER_OFFSET);
		$fieldConf = array_key_exists($columnName, $COLUMN_CONFIG) ? $COLUMN_CONFIG[$columnName] : array();
		$key = array_key_exists('fieldName', $fieldConf) ? $fieldConf['fieldName'] : $columnName;

		$type = array_key_exists('type', $fieldConf) ? $fieldConf['type'] : 'string';
		switch ($type) {
			case 'date':
				$formattedValue =  PHPExcel_Shared_Date::PHPToExcel($data[$key]);
				break;
			case 'time':
				$formattedValue =  PHPExcel_Shared_Date::PHPToExcel($data[$key]);
				break;
			case 'duration':
				$formattedValue = (((date('H', $data[$key]) - 1) * 3600) + (date('i', $data[$key]) * 60) + date('s', $data[$key])) / 86400;
				break;
			case 'float':
			case 'percent':
			case 'money':
				// once PHP < 5.3 support is dropped, we could use numfmt_parse instead: 
				$formattedValue = floatval($data[$key]);
				break;
			case 'floatDurationHours':
				$formattedValue = floatval($data[$key]) / 3600;
				break;
			case 'boolean':
				$formattedValue = $data[$key] ? $this->kga['lang']['yes'] : $this->kga['lang']['no'];
				break;
			default: 
				$formattedValue = trim($data[$key]);
				break;
		}
		
		$sheet->setCellValue($curAddr, $formattedValue);
	}
}

/**
 * Sums:
 */
$anySumsWereAdded = false;
foreach($activeColumns_arr as $x => $columnName) {
	$fieldConf = array_key_exists($columnName, $COLUMN_CONFIG) ? $COLUMN_CONFIG[$columnName] : array();
	$doSum = array_key_exists('sum', $fieldConf) ? $fieldConf['sum'] : false;
	$curAddr = excelAddr($x, $EXCEL_HEADER_OFFSET + count($this->exportData));
	
	if (!$doSum) {
		if ($x == 0) {
			$sheet->setCellValue($curAddr, $this->kga['lang']['total']);
		}
		continue;
	}
	
	$anySumsWereAdded = true;
	$sheet->setCellValue($curAddr, "=SUM(" . excelRange($x, $EXCEL_HEADER_OFFSET, $x, $EXCEL_HEADER_OFFSET + count($this->exportData) - 1) . ")");
}

/**
 * Layout:
 */

// convert strftime format to Excel date/time formats: 
$dateFormat = str_replace('%', '', $this->kga['conf']['date_format_1']); // preferring the configurable value over $this->dateformat (hardcoded) 
$dateFormat = str_replace('y', 'yy', $dateFormat);
$dateFormat = str_replace('Y', 'yyyy', $dateFormat);
$dateFormat = str_replace('d', 'dd', $dateFormat);
$dateFormat = str_replace('a', 'ddd', $dateFormat);
$dateFormat = str_replace('w', 'dddd', $dateFormat);
$dateFormat = str_replace('m', 'mm', $dateFormat);

$timeFormat = str_replace('%', '', $this->custom_timeformat); // $this->custom_timeformat is currently hardcoded - but it's better than nothing
$timeFormat = str_replace('H', 'hh', $timeFormat);
$timeFormat = str_replace('M', 'mm', $timeFormat);
$timeFormat = str_replace('S', 'ss', $timeFormat);
$timeFormat = str_replace('I', 'hh', $timeFormat);
$timeFormat = str_replace('p', 'AM/PM', $timeFormat);

// Type depending formats:
foreach($activeColumns_arr as $x => $columnName) {
	$fieldConf = array_key_exists($columnName, $COLUMN_CONFIG) ? $COLUMN_CONFIG[$columnName] : array();
	$key = array_key_exists('fieldName', $fieldConf) ? $fieldConf['fieldName'] : $columnName;
	$doSum = array_key_exists('sum', $fieldConf) ? $fieldConf['sum'] : false;	
	$curRange = excelRange($x, $EXCEL_HEADER_OFFSET, $x, $EXCEL_HEADER_OFFSET + count($this->exportData) - ($doSum ? 0 : 1));
	
	$type = array_key_exists('type', $fieldConf) ? $fieldConf['type'] : 'string';
	switch($type) {
		case 'date':
			$sheet->getStyle($curRange)
				->getNumberFormat()
				->setFormatCode($dateFormat);
			break;
		case 'time':
			$sheet->getStyle($curRange)
				->getNumberFormat()
				->setFormatCode($timeFormat);
			break;
		case 'duration':
			$sheet->getStyle($curRange)
				->getNumberFormat()
				->setFormatCode('[h]:mm');
			break;
		case 'floatDurationHours':
		case 'float':
			$sheet->getStyle($curRange)
				->getNumberFormat()
				->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_00);
			break;
		case 'percent':
			$sheet->getStyle($curRange)
				->getNumberFormat()
				->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_00 . " %");
			break;
		case 'money':
			$sheet->getStyle($curRange)
				->getNumberFormat()
				->setFormatCode("{$this->kga['conf']['currency_sign']} " . PHPExcel_Style_NumberFormat::FORMAT_NUMBER_00);
			break;
		case 'text':
			$sheet->getStyle($curRange)
				->getAlignment()->setWrapText(true);
			break;
	}
}

// borders:
$sheet->getStyle(excelRange(0, 0, count($activeColumns_arr) - 1, $EXCEL_HEADER_OFFSET + count($this->exportData) - ($anySumsWereAdded ? 0 : 1)))
	->applyFromArray(array(
			'borders'	 => array(
					'allborders' => array(
							'style'	 => PHPExcel_Style_Border::BORDER_HAIR,
							'color' => array('argb' => 'FFAAAAAA'),
					),
					'outline' => array(
							'style'	 => PHPExcel_Style_Border::BORDER_THIN,
							'color' => array('argb' => 'FF000000'),
					),
			),
	));
	
// date seperating borders:
$curDate = NULL;
foreach($this->exportData as $y => $data) {
	$date = date('Y-m-d', $data['time_in']);
	if ($date !== $curDate) {
		if ($curDate !== NULL) {
			$sheet->getStyle(excelRange(0, $y + $EXCEL_HEADER_OFFSET, count($activeColumns_arr) - 1, $y + $EXCEL_HEADER_OFFSET))
				->applyFromArray(array(
						'borders'	 => array(
								'top' => array(
										'color' => array('argb' => 'FF000000'),
								),
						),
				));
		}
		
		$curDate = $date;
	}
}

// header:
$sheet->getStyle(excelRange(0, 0, count($activeColumns_arr) - 1, 0))
	->applyFromArray(array(
			'font' => array(
					'bold' => 'true'
			),
			'borders'	 => array(
					'outline' => array(
							'style'	 => PHPExcel_Style_Border::BORDER_THIN,
							'color' => array('argb' => 'FF000000'),
					),
			),
	));
	
$sheet->getStyle(excelRange(0, 0, count($activeColumns_arr) - 1, 0))
	->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
	->getStartColor()->setARGB('FFEEEEEE');
	
foreach($activeColumns_arr as $x => $columnName) {
	$fieldConf = array_key_exists($columnName, $COLUMN_CONFIG) ? $COLUMN_CONFIG[$columnName] : array();
	$type = array_key_exists('type', $fieldConf) ? $fieldConf['type'] : 'string';
	$curAddr = excelAddr($x, 0);
	
	switch($type) {
		case 'date':
		case 'time':
		case 'duration':
		case 'float':
		case 'floatDurationHours':
		case 'money':
			$sheet->getStyle($curAddr)
				->applyFromArray(array(
						'alignment'	 => array(
							'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
						),
				));
			break;
		default:
			$sheet->getStyle($curAddr)
				->applyFromArray(array(
						'alignment'	 => array(
								'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
						),
				));
			break;
	}
}
	
// sums:
if ($anySumsWereAdded) {
	$sheet->getStyle(excelRange(0, $EXCEL_HEADER_OFFSET + count($this->exportData), count($activeColumns_arr) - 1, $EXCEL_HEADER_OFFSET + count($this->exportData)))
		->applyFromArray(array(
				'font' => array(
						'bold' => 'true'
				),
				'borders'	 => array(
						'outline' => array(
								'style'	 => PHPExcel_Style_Border::BORDER_THIN,
								'color' => array('argb' => 'FF000000'),
						),
				),
		));
		
	$sheet->getStyle(excelRange(0, $EXCEL_HEADER_OFFSET + count($this->exportData), count($activeColumns_arr) - 1, $EXCEL_HEADER_OFFSET + count($this->exportData)))
		->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
		->getStartColor()->setARGB('FFEEEEEE');
}

// auto-width columns:
for ($x = 0; $x < count($activeColumns_arr); $x++) {
	$sheet->getColumnDimension(excelColumnAddr($x))->setAutoSize(true);
}

// auto-height rows:
foreach($sheet->getRowDimensions() as $rd) { 
	$rd->setRowHeight(-1); 
}

/**
 * Write excel:
 */
header('Content-Disposition: attachment;filename="export.xlsx"');
header('Cache-Control: max-age=0');
header("Content-Type: application/vnd.ms-excel");
$writer = PHPExcel_IOFactory::createWriter($generator, 'Excel2007');
$writer->setPreCalculateFormulas(true);
$writer->save('php://output');
?>