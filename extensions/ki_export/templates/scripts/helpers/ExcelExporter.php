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
 * Helper class to export time sheets as Excel file
 * @author Florian Lentsch <office@florian-lentsch.at>
 *
 */
class Kimai_Export_ExcelExporter extends PHPExcel {

	/**
	 * Column configuration:
	 * <field name as in $this->columns_dict> => [
	 * 		'fieldName' => <field name as in $this->exportData_arr - defaults to the same field name as in $this->columns>
	 * 		'type' => <determines field formatting - one of string (default), date, time, duration, floatDurationHours, boolean, percent, and text>
	 * 		'langLabel' => <key for $this->kga['lang'] - defaults to the same as the field name in $this->columns>
	 * 		'sum' => boolean - if true, sums will be generated for this field
	 * ]
	 * @var array
	 */
	protected static $COLUMN_CONFIG = array(
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

	/**
	 * vertical offset for data rows
	 * @var integer
	 */
	const EXCEL_HEADER_OFFSET = 1;

	/**
	 * Kimai Global Array
	 * @var array
	 */
	public $kga;

	/**
	 * the data rows to be exported
	 * @var array
	 */
	public $exportData_arr;

	/**
	 * dictionary
	 * 	value: column name as in extensions/ki_export/templates/scripts/main.php
	 * 	key: true if active, else false
	 * @var array
	 */
	public $columns_dict;

	/**
	 * strftime format string for fields of type time
	 * @var string
	 */
	public $customTimeformat;

	/**
	 * contains names (as in $this->columns_dict) of active columns
	 * @var array
	 */
	protected $activeColumns_arr;

	/**
	 * the first (and only) worksheet
	 * @var PHPExcel_Worksheet
	 */
	protected $sheet;

	/**
	 * excel format string for fields of type date
	 * @var string
	 */
	protected $dateFormat;

	/**
	 * excel format string for fields of type time
	 * @var string
	 */
	protected $timeFormat;

	/**
	 * true, if there are any active columns with sums, else false
	 * @var boolean
	 */
	protected $anySumsWereAdded = false;

	/**
	 * generates an .xlsx file containing time sheets to be exported
	 * the file will be sent to the browser for download/viewing
	 * @param array $kga Kimai Global Array
	 * @param array $exportData_arr the data rows to be exported
	 * @param array $columns_dict dictionary of columns: column name => active (true/false)
	 * @param string $customTimeformat strftime format string for fields of type time (for dates $this->kga['conf']['date_format_1'] will be used)
	 */
	public function render(array $kga, array $exportData_arr, array $columns_dict, $customTimeformat) {
		$this->kga = $kga;
		$this->exportData_arr = $exportData_arr;
		$this->columns_dict = $columns_dict;
		$this->customTimeformat = $customTimeformat;

		$this->initialize();
		$this->writeHeaders();
		$this->writeDataRows();
		$this->writeSums();

		$this->doLayouting();

		$this->renderExcel();
	}

	/**
	 * renders the excel and sends it to the browser
	 */
	protected function renderExcel() {
		header('Content-Disposition: attachment;filename="export.xlsx"');
		header('Cache-Control: max-age=0');
		header("Content-Type: application/vnd.ms-excel");
		$writer = PHPExcel_IOFactory::createWriter($this, 'Excel2007');
		$writer->setPreCalculateFormulas(true);
		$writer->save('php://output');
	}

	/**
	 * initial PHPExcel settings; build $this->activeColumns_arr
	 */
	protected function initialize() {
		$this->sheet = $this->setActiveSheetIndex(0);
		$this->sheet->setTitle("Export");

		// build $this->activeColumns_arr:
		$columns_dict = $this->columns_dict;
		unset($columns_dict['cleared']); // <- cannot be toggled - so for now, don't show it at all
		$this->activeColumns_arr = array_keys(array_filter($columns_dict, function($active) {
			return $active;
		}));

		// convert strftime format to Excel date/time formats:
		$this->dateFormat = str_replace('%', '', $this->kga['conf']['date_format_1']); // preferring the configurable value over $this->dateformat (hardcoded)
		$this->dateFormat = str_replace('y', 'yy', $this->dateFormat);
		$this->dateFormat = str_replace('Y', 'yyyy', $this->dateFormat);
		$this->dateFormat = str_replace('d', 'dd', $this->dateFormat);
		$this->dateFormat = str_replace('a', 'ddd', $this->dateFormat);
		$this->dateFormat = str_replace('w', 'dddd', $this->dateFormat);
		$this->dateFormat = str_replace('m', 'mm', $this->dateFormat);

		$this->timeFormat = str_replace('%', '', $this->customTimeformat); // $this->custom_timeformat is currently hardcoded - but it's better than nothing
		$this->timeFormat = str_replace('H', 'hh', $this->timeFormat);
		$this->timeFormat = str_replace('M', 'mm', $this->timeFormat);
		$this->timeFormat = str_replace('S', 'ss', $this->timeFormat);
		$this->timeFormat = str_replace('I', 'hh', $this->timeFormat);
		$this->timeFormat = str_replace('p', 'AM/PM', $this->timeFormat);
	}

	/**
	 * write the headers for all active columns
	 */
	protected function writeHeaders() {
		foreach($this->activeColumns_arr as $x => $columnName) {
			if(array_key_exists($columnName, self::$COLUMN_CONFIG) && array_key_exists('langLabel', self::$COLUMN_CONFIG[$columnName])) {
				$columnName = self::$COLUMN_CONFIG[$columnName]['langLabel'];
			}

			$columnLabel = $this->kga['lang'][$columnName];
			$this->sheet->setCellValue(self::excelAddr($x, 0), $columnLabel);
		}
	}

	/**
	 * write the data rows for all active columns
	 */
	protected function writeDataRows() {
		foreach($this->exportData_arr as $y => $data) {
			foreach($this->activeColumns_arr as $x => $columnName) {
				$curAddr = self::excelAddr($x, $y + self::EXCEL_HEADER_OFFSET);
				$fieldConf = array_key_exists($columnName, self::$COLUMN_CONFIG) ? self::$COLUMN_CONFIG[$columnName] : array();
				$key = array_key_exists('fieldName', $fieldConf) ? $fieldConf['fieldName'] : $columnName;

				$type = array_key_exists('type', $fieldConf) ? $fieldConf['type'] : 'string';
				switch ($type) {
					case 'date':
						$date = date('Y-m-d H:i:s +0000', $data[$key]);
						$tstamp = date('U', strtotime($date));
						$formattedValue =  PHPExcel_Shared_Date::PHPToExcel($tstamp);
						break;
					case 'time':
						$date = date('Y-m-d H:i:s +0000', $data[$key]);
						$tstamp = date('U', strtotime($date));
						$formattedValue =  PHPExcel_Shared_Date::PHPToExcel($tstamp);
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

				$this->sheet->setCellValue($curAddr, $formattedValue);
			}
		}
	}

	/**
	 * write sums for active columns
	 * only if configured in self::$COLUMN_CONFIG
	 */
	protected function writeSums() {
		for($x = count($this->activeColumns_arr) - 1; $x >= 0; $x--) {
			$columnName = $this->activeColumns_arr[$x];
			$fieldConf = array_key_exists($columnName, self::$COLUMN_CONFIG) ? self::$COLUMN_CONFIG[$columnName] : array();
			$doSum = array_key_exists('sum', $fieldConf) ? $fieldConf['sum'] : false;
			$curAddr = self::excelAddr($x, self::EXCEL_HEADER_OFFSET + count($this->exportData_arr));

			if (!$doSum) {
				if ($x == 0 && $this->anySumsWereAdded) {
					$this->sheet->setCellValue($curAddr, $this->kga['lang']['total']);
				}

				continue;
			}

			$this->anySumsWereAdded = true;
			$this->sheet->setCellValue($curAddr, "=SUM(" . self::excelRange($x, self::EXCEL_HEADER_OFFSET, $x, self::EXCEL_HEADER_OFFSET + count($this->exportData_arr) - 1) . ")");
		}
	}

	/**
	 * do layouting (cell borders, font styles, background colors,
	 * cell sizing, page setup)
	 */
	protected function doLayouting() {
		$this->formatDataRowsByType();
		$this->addCellBorders();
		$this->formatHeaders();
		$this->formatSums();

		// auto-width columns:
		for ($x = 0; $x < count($this->activeColumns_arr); $x++) {
			$this->sheet->getColumnDimension(self::excelColumnAddr($x))->setAutoSize(true);
		}

		// auto-height rows:
		foreach($this->sheet->getRowDimensions() as $rd) {
			$rd->setRowHeight(-1);
		}

		// fixed header when scrolling down in Excel:
		$this->sheet->freezePane(self::excelAddr(0, self::EXCEL_HEADER_OFFSET));


		$pageSetup = $this->sheet->getPageSetup();

		// when printing, show header on every page:
		$pageSetup->setRowsToRepeatAtTopByStartAndEnd(1, self::EXCEL_HEADER_OFFSET);

		// increase chances that page can be printed out without seperating a row's data over more than one page:
		$pageSetup->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
	}

	/**
	 * cell formatting (number/text/date ...) as
	 * configured in self::$COLUMN_CONFIG
	 */
	protected function formatDataRowsByType() {
		foreach($this->activeColumns_arr as $x => $columnName) {
			$fieldConf = array_key_exists($columnName, self::$COLUMN_CONFIG) ? self::$COLUMN_CONFIG[$columnName] : array();
			$key = array_key_exists('fieldName', $fieldConf) ? $fieldConf['fieldName'] : $columnName;
			$doSum = array_key_exists('sum', $fieldConf) ? $fieldConf['sum'] : false;
			$curRange = self::excelRange($x, self::EXCEL_HEADER_OFFSET, $x, self::EXCEL_HEADER_OFFSET + count($this->exportData_arr) - ($doSum ? 0 : 1));

			$type = array_key_exists('type', $fieldConf) ? $fieldConf['type'] : 'string';
			switch($type) {
				case 'date':
					$this->sheet->getStyle($curRange)
								->getNumberFormat()
								->setFormatCode($this->dateFormat);
					break;
				case 'time':
					$this->sheet->getStyle($curRange)
								->getNumberFormat()
								->setFormatCode($this->timeFormat);
					break;
				case 'duration':
					$this->sheet->getStyle($curRange)
								->getNumberFormat()
								->setFormatCode('[h]:mm');
					break;
				case 'floatDurationHours':
				case 'float':
					$this->sheet->getStyle($curRange)
								->getNumberFormat()
								->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_00);
					break;
				case 'percent':
					$this->sheet->getStyle($curRange)
								->getNumberFormat()
								->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_00 . " %");
					break;
				case 'money':
					$this->sheet->getStyle($curRange)
								->getNumberFormat()
								->setFormatCode("{$this->kga['conf']['currency_sign']} " . PHPExcel_Style_NumberFormat::FORMAT_NUMBER_00);
					break;
				case 'text':
					$this->sheet->getStyle($curRange)
								->getAlignment()->setWrapText(true);
					break;
			}
		}
	}

	/**
	 * add cell borders
	 */
	protected function addCellBorders() {
		// thin lines on the outline, hair lines for all the interior cell borders:
		$this->sheet->getStyle(self::excelRange(0, 0, count($this->activeColumns_arr) - 1, self::EXCEL_HEADER_OFFSET + count($this->exportData_arr) - ($this->anySumsWereAdded ? 0 : 1)))
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
		foreach($this->exportData_arr as $y => $data) {
			$date = date('Y-m-d', $data['time_in']);
			if ($date !== $curDate) {
				if ($curDate !== NULL) {
					$this->sheet->getStyle(self::excelRange(0, $y + self::EXCEL_HEADER_OFFSET, count($this->activeColumns_arr) - 1, $y + self::EXCEL_HEADER_OFFSET))
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
	}

	/**
	 * format the headers (additional cell borders,
	 * background color, alignment)
	 */
	protected function formatHeaders() {
		// bold & thin cell border outline:		
		$this->sheet->getStyle(self::excelRange(0, 0, count($this->activeColumns_arr) - 1, 0))
					->applyFromArray(array(
						'font' => array(
							'bold' => 'true'
						),
						'borders' => array(
							'outline' => array(
								'style'	=> PHPExcel_Style_Border::BORDER_THIN,
								'color' => array('argb' => 'FF000000'),
							),
						),
					));

		// gray background color:
		$this->sheet->getStyle(self::excelRange(0, 0, count($this->activeColumns_arr) - 1, 0))
					->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
					->getStartColor()->setARGB('FFEEEEEE');

		// align headers for numeric fields right:
		foreach($this->activeColumns_arr as $x => $columnName) {
			$fieldConf = array_key_exists($columnName, self::$COLUMN_CONFIG) ? self::$COLUMN_CONFIG[$columnName] : array();
			$type = array_key_exists('type', $fieldConf) ? $fieldConf['type'] : 'string';
			$curAddr = self::excelAddr($x, 0);

			switch($type) {
				case 'date':
				case 'time':
				case 'duration':
				case 'float':
				case 'floatDurationHours':
				case 'money':
					$this->sheet->getStyle($curAddr)
								->applyFromArray(array(
									'alignment'	 => array(
										'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
									),
								));
					break;
				default:
					$this->sheet->getStyle($curAddr)
								->applyFromArray(array(
									'alignment'	 => array(
										'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
									),
								));
					break;
			}
		}
	}

	/**
	 * format the sums row if there are any sums
	 * (borders and background color)
	 */
	protected function formatSums() {
		if (!$this->anySumsWereAdded) {
			return;
		}

		$this->sheet->getStyle(self::excelRange(0, self::EXCEL_HEADER_OFFSET + count($this->exportData_arr), count($this->activeColumns_arr) - 1, self::EXCEL_HEADER_OFFSET + count($this->exportData_arr)))
					->applyFromArray(array(
						'font' => array(
							'bold' => 'true'
						),
						'borders' => array(
							'outline' => array(
								'style'	=> PHPExcel_Style_Border::BORDER_THIN,
								'color' => array('argb' => 'FF000000'),
							),
						),
					));

		$this->sheet->getStyle(self::excelRange(0, self::EXCEL_HEADER_OFFSET + count($this->exportData_arr), count($this->activeColumns_arr) - 1, self::EXCEL_HEADER_OFFSET + count($this->exportData_arr)))
					->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
					->getStartColor()->setARGB('FFEEEEEE');

	}


	/**
	 * get excel column letter by index (e.g. '2' becomes 'C')
	 * using this over PHPExcels built-in methods, because
	 * they are partly 1-based instead of 0-based
	 * @param integer $n column index
	 * @return string excel column letter
	 */
	protected static function excelColumnAddr($n) {
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
	protected static function excelAddr($x,$y) {
		return self::excelColumnAddr($x).($y+1);
	}

	/**
	 * get excel field range by supplying coordinates
	 * @param integer first corner's horizontal coordinate
	 * @param integer first corner's vertical coordinate
	 * @param integer second corner's horizontal coordinate
	 * @param integer second corner's vertical coordinate
	 * @return string excel field range
	 */
	protected static function excelRange($x1,$y1,$x2,$y2) {
		return self::excelAddr($x1,$y1).':'.self::excelAddr($x2,$y2);
	}
}
