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
 * along with Kimai; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 * 
 */ 

/**
 *  This hack was originally coded by Martin Klemkow, Mandarin Medien, Schwerin, Germany
 *  Still needs to be turned into a real extension...
 *
 *  Version 0.3 (25. Sep. 2009)
 */
	
	$standard_Location = "";
	# TODO: Insert standard location field for each user in table 'usr',
	#       maybe also an overriding global standard location...
	
	// DB Conection etc
	include('../../includes/basics.php');
	
	$p = $kga['server_prefix'];

	if ($_REQUEST['submit']=="Excell-Export")
	{
		$formvalues['select_kunde']                  = $_REQUEST['select_kunde']             ;
		$formvalues['select_projekt']                = $_REQUEST['select_projekt']           ;
		$formvalues['select_events']                 = $_REQUEST['select_events']            ;

		$formvalues['startdatum']['Date_Month']      = $_REQUEST['startdatum']['Date_Month'] ;
		$formvalues['startdatum']['Date_Day']        = $_REQUEST['startdatum']['Date_Day']   ;
		$formvalues['startdatum']['Date_Year']       = $_REQUEST['startdatum']['Date_Year']  ;
		$formvalues['enddatum']['Date_Month']        = $_REQUEST['enddatum']['Date_Month']   ;
		$formvalues['enddatum']['Date_Day']          = $_REQUEST['enddatum']['Date_Day']     ;
		$formvalues['enddatum']['Date_Year']         = $_REQUEST['enddatum']['Date_Year']    ;

		$formvalues['select_filter']                 = $_REQUEST['select_filter']            ;
		$formvalues['select_user']                   = $_REQUEST['select_user']              ;
		$formvalues['options_timeformat']            = $_REQUEST['options_timeformat']       ;
		
	}
	else
	{
		// Xajax
		require_once("../../libraries/xajax/xajax_core/xajax.inc.php");
		$xajax = new xajax();
		$xajax->configure("javascript URI","../../libraries/xajax/");
	
		// Smarty
		require('../../libraries/smarty/Smarty.class.php');
		$smarty = new Smarty();
		$smarty->template_dir = 'smarty_templates';
		$smarty->compile_dir = '../../compile';
	
		// xajax register functions	
		$xajax->register(XAJAX_FUNCTION,'selectProjects');
		$xajax->register(XAJAX_FUNCTION,'selectEvents');
		$xajax->register(XAJAX_FUNCTION,'selectUser');
		$xajax->register(XAJAX_FUNCTION,'showJobs');
		$xajax->register(XAJAX_FUNCTION,'markCleared');
	}
	
	function selectProjects($formvalues) {
		global $p;
		$objResponse = new xajaxResponse();
		$formvalues['select_projekt'] = 0;
		#if($formvalues['select_kunde'] == "0"){
		#	$where = "";
		#} else {
			$where = "AND pct_kndID = '".$formvalues['select_kunde']."'";
		#}
		$query = "
		SELECT * FROM ${p}pct 
		STRAIGHT_JOIN ${p}knd ON (knd_ID = pct_kndID)
		WHERE 1 ".$where."
		ORDER BY pct_name
		";
		$res = mysql_query($query);
		print mysql_error();
		$return = "";
		$return .="<select name=\"select_projekt\" style=\"width: 220px;\" onchange=\"form_showJobs();return false;\">";
		$return .="<option value=\"0\">alle Projekte</option>";
		while($row = mysql_fetch_assoc($res)){
			$return .= "<option value=\"".$row['pct_ID']."\">".$row['pct_name']." - ".$row['knd_name']."</option>";
		}
		$return .= "</select>";	
		$objResponse->assign("div_selectProjekte","innerHTML",$return);
		$objResponse->loadcommands(showJobs($formvalues));
		return $objResponse;
	}
	
	function selectEvents($formvalues) {
		global $p;
		
		$objResponse = new xajaxResponse();
		
		$query = "
		SELECT * FROM ${p}evt 
		ORDER BY evt_name
		";
		$res = mysql_query($query);
		print mysql_error();
		$return = "";
		$return .="<select name=\"select_events\" size=\"20\" multiple=\"multiple\" style=\"width: 220px;\" onchange=\"form_showJobs();return false;\">";
		while($row = mysql_fetch_assoc($res)){
			$return .= "<option value=\"".$row['evt_ID']."\" >".$row['evt_name']."</option>";
		}
		$return .= "</select>";	
		$objResponse->assign("div_selectEvents","innerHTML",$return);
		$objResponse->loadcommands(showJobs($formvalues));
		
		return $objResponse;
	}
	
	function selectUser($formvalues) {
		global $p;
		
		$objResponse = new xajaxResponse();
		
		$query = "
		SELECT * FROM ${p}usr 
		WHERE usr_active='1'
		ORDER BY usr_name
		";
		$res = mysql_query($query);
		print mysql_error();
		$return = "";
		$return .="<select name=\"select_user\" size=\"6\" multiple=\"multiple\" style=\"width: 220px;\" onchange=\"form_showJobs();return false;\">";
		while($row = mysql_fetch_assoc($res)){
			$return .= "<option value=\"".$row['usr_ID']."\" >".$row['usr_name']."</option>";
		}
		$return .= "</select>";	
		$objResponse->assign("div_selectUser","innerHTML",$return);
		$objResponse->loadcommands(showJobs($formvalues));
		
		return $objResponse;
	}
	
	function markCleared($arg){
		global $p;
		
		#$objResponse = new xajaxResponse();
		
		$query = "
		SELECT * FROM ${p}zef 
		WHERE zef_ID ='".$arg."'
		";
		$res = mysql_query($query);
		print mysql_error();
		$row = mysql_fetch_assoc($res);
		if($row['zef_cleared'] == 0){
			mysql_query("UPDATE ${p}zef SET zef_cleared = '1' WHERE zef_ID='".$arg."'");
		} else {
			mysql_query("UPDATE ${p}zef SET zef_cleared = '0' WHERE zef_ID='".$arg."'");
		}
		
		#$objResponse->loadcommands(showJobs($formvalues));
		
		#return $objResponse;
	}



	function showJobs($formvalues) {
	
		$objResponse = new xajaxResponse();
		
		$return = generateTable($formvalues);
		
		$objResponse->assign("div_liste","innerHTML",$return);
		return $objResponse;
	}





	function generateTable($formvalues,$screenmode=1) 
	{
		global $p, $standard_Location;
			
		$xwhere = "AND knd_ID = '".$formvalues['select_kunde']."'";

		$query_projektinfos = "
			SELECT * 
			FROM ${p}knd 
			WHERE 1 ".$xwhere." 
		";
		
		#print $query_projektinfos;
		$res_projektinfos = mysql_query($query_projektinfos);
		print mysql_error();
		$row_pi = mysql_fetch_assoc($res_projektinfos);
		
		if(($formvalues['select_projekt'] == 0 OR $formvalues['select_projekt'] == "") AND $formvalues['select_kunde'] == "0"){
			$where = "";
		} elseif (($formvalues['select_projekt'] == 0 OR $formvalues['select_projekt'] == "") AND $formvalues['select_kunde'] != "0"){
			$where = "WHERE pct_kndID = '".$formvalues['select_kunde']."'";
		} else {
			$where = "WHERE zef_pctID = '".$formvalues['select_projekt']."' AND pct_kndID = '".$formvalues['select_kunde']."'";
		}
		
		$startdatum = mktime(0, 0, 0, $formvalues['startdatum']['Date_Month'], $formvalues['startdatum']['Date_Day'], $formvalues['startdatum']['Date_Year']);
		$enddatum = mktime(23, 59, 59, $formvalues['enddatum']['Date_Month'], $formvalues['enddatum']['Date_Day'], $formvalues['enddatum']['Date_Year']);
		
		$where .= " AND zef_in >= ".$startdatum;
		$where .= " AND zef_in <= ".$enddatum;
		
		switch ($formvalues['select_filter']) {
			case "-1":
				$where .= " AND zef_cleared >= 0";
				break;
			case "1":
				$where .= " AND zef_cleared = 1";
				break;			
			case "0":
				$where .= " AND zef_cleared = 0";
				break;			
		}
		
		if(count($formvalues['select_events']) > 0){
			$where .= " AND (";
			$counter = 0;
			foreach($formvalues['select_events'] as $v){
				$where .= " zef_evtID = '".$v."'";
				$counter++;
				if($counter < count($formvalues['select_events'])){
					$where .= " OR";
				}
				
			}
			$where .= ")";	
		}
		
		if(count($formvalues['select_user']) > 0){
			$where .= " AND (";
			$counter = 0;
			foreach($formvalues['select_user'] as $v){
				$where .= " zef_usrID = '".$v."'";
				$counter++;
				if($counter < count($formvalues['select_user'])){
					$where .= " OR";
				}
				
			}
			$where .= ")";	
		}
		
		$query_gesamtdauer = "
		SELECT 
			SEC_TO_TIME(SUM(zef_time)) as gesamtdauer
		FROM ${p}zef
		STRAIGHT_JOIN ${p}evt ON (evt_ID = zef_evtID)
		STRAIGHT_JOIN ${p}pct ON (pct_ID = zef_pctID)
		STRAIGHT_JOIN ${p}knd ON (knd_ID = pct_kndID)
		".$where."
		ORDER BY zef_in
		";
		$res_gesamtdauer = mysql_query($query_gesamtdauer);
		print mysql_error();
		
		$row_gd = mysql_fetch_assoc($res_gesamtdauer);
		
		$query = "
		SELECT 
			*, SEC_TO_TIME(zef_time) as dauer
		FROM ${p}zef
		STRAIGHT_JOIN ${p}evt ON (evt_ID = zef_evtID)
		STRAIGHT_JOIN ${p}pct ON (pct_ID = zef_pctID)
		STRAIGHT_JOIN ${p}knd ON (knd_ID = pct_kndID)
		STRAIGHT_JOIN ${p}usr ON (usr_ID = zef_usrID)
		".$where."
		ORDER BY zef_in
		";
	
		/* Ausgabe als HTML ----------------------------------------------------------------------------- */
		$res = mysql_query($query);
		print mysql_error();
		$return = "";
		if($formvalues['select_kunde'] != "0"){
			if ($screenmode) $return .= "<strong>Kunde: ".$row_pi['knd_name']."</strong><br />";
		}
		
		// $return .= "<input type=\"button\" name=\"reload\" value=\"Reload\" onclick=\"form_showJobs();return false;\" />";
				
		if ($screenmode) $return .= "<h1>vom ".date("d.m.Y", $startdatum)." bis ".date("d.m.Y", $enddatum)."</h1>";
		if ($screenmode) $return .="<form name=\"jobanzeige\" method=\"get\" action=\"\">";
		$return .= "<table>";
		
		$return .= "<tr>";
		$return .= "<th>Tag</th>";
		$return .= "<th>Start</th>";
		$return .= "<th>Ende</th>";
		$return .= "<th>Dauer</th>";
		$return .= "<th>Stunden</th>";
		$return .= "<th>Kunde</th>";
		$return .= "<th>Projekt</th>";
		$return .= "<th>Tätigkeit</th>";
		$return .= "<th>Beschreibung</th>";
		$return .= "<th>Ort</th>";
		$return .= "<th>ausgeführt&nbsp;von</th>";
		if ($screenmode) $return .= "<th class=\"invertclm\">abgerechnet</th>";
		$return .= "</tr>";
		
		$zeit_summe = 0;
		
		while($row = mysql_fetch_assoc($res)){
			$return .= "<tr>";
			$return .= "<td>".date("d.m.Y", $row['zef_in'])."</td>";
			$return .= "<td>".date($formvalues['options_timeformat'], $row['zef_in'])."</td>";
			$return .= "<td>".date($formvalues['options_timeformat'], $row['zef_out'])."</td>";
			
			$zeit = round($row['zef_time']/3600, 2);
			$zeit_summe += $zeit;
			$zeit = number_format($zeit,2, ',', '.');
			
			$ort = $standard_Location;
			if ($row['zef_location'] != "") { 
				$ort = $row['zef_location'];
			}
			
			$return .= "<td>".$row['dauer']."</td>";
			$return .= "<td>".$zeit."</td>";
			$return .= "<td>".$row['knd_name']."</td>";
			$return .= "<td>".$row['pct_name']."</td>";
			$return .= "<td>".$row['evt_name']."</td>";
			$return .= "<td>".$row['zef_comment']."</td>";
			$return .= "<td>".$ort."</td>";
			
			if ($row['usr_alias'] != "") {
				$ausgefuehrt_von = $row['usr_alias'];
			} else {
				$ausgefuehrt_von = $row['usr_name'];
			}
			
			$return .= "<td>".$ausgefuehrt_von."</td>";
			
			if($row['zef_cleared'] == 1){$selected="checked='checked'";} else {$selected="";}
			if ($screenmode) $return .= "<td class=\"invertclm\"><input type=\"checkbox\" name=\"".$row['zef_ID']."\" class=\"clear_setter\"  id=\"".$row['zef_ID']."\" ".$selected." onchange=\"xajax_markCleared(".$row['zef_ID'].")\" /></td>";
			$return .= "</tr>";
		}
		
		// $gesamt = number_format(round($row_gd['gesamtdauer']/3600, 2),2, ',', '.');
		$gesamt = number_format($zeit_summe,2, ',', '.');
		
		$return .= "<tr>";
			$return .= "<td>&nbsp;</td>";
			$return .= "<td>&nbsp;</td>";
			$return .= "<td>&nbsp;</td>";
			$return .= "<td><strong>".$row_gd['gesamtdauer']."</strong></td>";
			$return .= "<td><strong>".$gesamt."</strong></td>";
			$return .= "<td>&nbsp;</td>";
			$return .= "<td>&nbsp;</td>";
			$return .= "<td>&nbsp;</td>";
			$return .= "<td>&nbsp;</td>";
			$return .= "<td>&nbsp;</td>";
			$return .= "<td>&nbsp;</td>";
			if ($screenmode) $return .= "<td class=\"invertclm\"><input id=\"invertbtn\" type=\"button\" name=\"invertButton\" value=\"Invertieren\" onclick=\"$('.clear_setter').each(function(index){ this.click(); });\" /></td>";
			$return .= "</tr>";
		$return .= "</table>";
		if ($screenmode) $return .= "</form>";
		
		return $return;
	}

	if ($_REQUEST['submit']=="Excell-Export")
	{
		header("Content-type: application/vnd-ms-excel"); 
	 	header("Content-Disposition: attachment; filename=export.xls");
        echo generateTable($_REQUEST,0);
	}
	else
	{
		$xajax->processRequest();
		$js = $xajax->getJavascript();
		$smarty->assign('xajax_js',$js);
	
		if (isset($formvalues['options_timeformat'])) {
			$smarty->assign('options_timeformat',$formvalues['options_timeformat']);
		}

		// grab customers ---------------------------------------------------------------------------------------------------------------
		$query = "SELECT * FROM ${p}knd ORDER BY knd_name";
		$res = mysql_query($query);
		$kunden = array();
		while($row = mysql_fetch_assoc($res)){
			$kunden[] = $row;
		}
		$smarty->assign('kunden', $kunden);
		// $smarty->assign('kga', $kga);
		$smarty->assign('kga', $p);
		$smarty->display('index.tpl');
	}
	
?>