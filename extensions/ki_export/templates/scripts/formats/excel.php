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

require_once dirname(__FILE__) . '/../helpers/ExcelExporter.php';

$excel = new Kimai_Export_ExcelExporter();
$excel->render($this->kga, $this->exportData, $this->columns, $this->custom_timeformat);
