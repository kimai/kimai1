<?php
	// DB Conection etc
	include('../../includes/basics.php');
	
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
	
	function selectProjects($formvalues) {
		$objResponse = new xajaxResponse();
		$formvalues['select_projekt'] = 0;
		#if($formvalues['select_kunde'] == "0"){
		#	$where = "";
		#} else {
			$where = "AND pct_kndID = '".$formvalues['select_kunde']."'";
		#}
		$query = "
		SELECT * FROM kimai_pct 
		STRAIGHT_JOIN kimai_knd ON (knd_ID = pct_kndID)
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
		$objResponse = new xajaxResponse();
		
		$query = "
		SELECT * FROM kimai_evt 
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
		$objResponse = new xajaxResponse();
		
		$query = "
		SELECT * FROM kimai_usr 
		WHERE usr_active='1'
		ORDER BY usr_name
		";
		$res = mysql_query($query);
		print mysql_error();
		$return = "";
		$return .="<select name=\"select_user\" size=\"10\" multiple=\"multiple\" style=\"width: 220px;\" onchange=\"form_showJobs();return false;\">";
		while($row = mysql_fetch_assoc($res)){
			$return .= "<option value=\"".$row['usr_ID']."\" >".$row['usr_name']."</option>";
		}
		$return .= "</select>";	
		$objResponse->assign("div_selectUser","innerHTML",$return);
		$objResponse->loadcommands(showJobs($formvalues));
		
		return $objResponse;
	}
	
	function markCleared($arg){
		
		#$objResponse = new xajaxResponse();
		
		$query = "
		SELECT * FROM kimai_zef 
		WHERE zef_ID ='".$arg."'
		";
		$res = mysql_query($query);
		print mysql_error();
		$row = mysql_fetch_assoc($res);
		if($row['zef_cleared'] == 0){
			mysql_query("UPDATE kimai_zef SET zef_cleared = '1' WHERE zef_ID='".$arg."'");
		} else {
			mysql_query("UPDATE kimai_zef SET zef_cleared = '0' WHERE zef_ID='".$arg."'");
		}
		
		
		#$objResponse->loadcommands(showJobs($formvalues));
		
		#return $objResponse;
	}
	
	function showJobs($formvalues) {
	
		#if($formvalues['select_kunde'] == "0"){
		#	$where = "";
		#} else {
			$xwhere = "AND knd_ID = '".$formvalues['select_kunde']."'";
		#}
		$query_projektinfos = "
			SELECT * 
			FROM kimai_knd 
			WHERE 1 ".$xwhere." 
		";
		#print $query_projektinfos;
		$res_projektinfos = mysql_query($query_projektinfos);
		print mysql_error();
		$row_pi = mysql_fetch_assoc($res_projektinfos);
		
		$objResponse = new xajaxResponse();
		
		if(($formvalues['select_projekt'] == 0 OR $formvalues['select_projekt'] == "") AND $formvalues['select_kunde'] == "0"){
			$where = "";
		} elseif (($formvalues['select_projekt'] == 0 OR $formvalues['select_projekt'] == "") AND $formvalues['select_kunde'] != "0"){
			$where = "WHERE pct_kndID = '".$formvalues['select_kunde']."'";
		} else {
			$where = "WHERE zef_pctID = '".$formvalues['select_projekt']."' AND pct_kndID = '".$formvalues['select_kunde']."'";
		}
		
		$startdatum = strtotime($formvalues['startdatum']['Date_Year']."-".$formvalues['startdatum']['Date_Month']."-".$formvalues['startdatum']['Date_Day']);
		$enddatum = strtotime($formvalues['enddatum']['Date_Year']."-".$formvalues['enddatum']['Date_Month']."-".$formvalues['enddatum']['Date_Day']);
		
		$where .= " AND zef_in > ".$startdatum;
		$where .= " AND zef_in < ".$enddatum;
		
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
		FROM kimai_zef
		STRAIGHT_JOIN kimai_evt ON (evt_ID = zef_evtID)
		STRAIGHT_JOIN kimai_pct ON (pct_ID = zef_pctID)
		STRAIGHT_JOIN kimai_knd ON (knd_ID = pct_kndID)
		".$where."
		ORDER BY zef_in
		";
		$res_gesamtdauer = mysql_query($query_gesamtdauer);
		print mysql_error();
		
		$row_gd = mysql_fetch_assoc($res_gesamtdauer);
		
		$query = "
		SELECT 
			*, 
			SEC_TO_TIME(zef_time) as dauer
		FROM kimai_zef
		STRAIGHT_JOIN kimai_evt ON (evt_ID = zef_evtID)
		STRAIGHT_JOIN kimai_pct ON (pct_ID = zef_pctID)
		STRAIGHT_JOIN kimai_knd ON (knd_ID = pct_kndID)
		STRAIGHT_JOIN kimai_usr ON (usr_ID = zef_usrID)
		".$where."
		ORDER BY zef_in
		";
	
		/* Ausgabe als HTML ----------------------------------------------------------------------------- */
		$res = mysql_query($query);
		print mysql_error();
		$return = "";
		if($formvalues['select_kunde'] != "0"){
			$return .= "<strong>Kunde: ".$row_pi['knd_name']."</strong><br />";
		}
		$return .= "vom: ".date("d.m.Y", $startdatum)." bis ".date("d.m.Y", $enddatum)."<br /><br />";
		$return .="<form name=\"jobanzeige\" method=\"get\" action=\"\">";
		$return .= "<table width=\"100%\" cellpadding=\"4\" cellspacing=\"0\">";
		$return .= "<tr>";
		$return .= "<th style=\"border-left: 1px solid #999;\">Kunde / Projekt</th>";
		$return .= "<th style=\"border-left: 1px solid #999;\">Start</th>";
		$return .= "<th style=\"border-left: 1px solid #999;\">Ende</th>";
		$return .= "<th style=\"border-left: 1px solid #999;\">Tätigkeit</th>";
		$return .= "<th style=\"border-left: 1px solid #999;\">Beschreibung</th>";
		$return .= "<th style=\"border-left: 1px solid #999;\">ausgeführt&nbsp;von</th>";
		$return .= "<th style=\"border-left: 1px solid #999;\">Dauer</th>";
		$return .= "<th style=\"border-left: 1px solid #999;border-right: 1px solid #999;\">abgerechnet</th>";
		$return .= "</tr>";
		while($row = mysql_fetch_assoc($res)){
			$return .= "<tr>";
			$return .= "<td style=\"border-left: 1px solid #999;\"><strong>".$row['knd_name']."</strong><br />".$row['pct_name']."</td>";
			$return .= "<td style=\"border-left: 1px solid #999;\">".date("d.m.Y-H:i", $row['zef_in'])."</td>";
			$return .= "<td style=\"border-left: 1px solid #999;\">".date("d.m.Y-H:i", $row['zef_out'])."</td>";
			$return .= "<td style=\"border-left: 1px solid #999;\">".$row['evt_name']."</td>";
			$return .= "<td style=\"border-left: 1px solid #999;\">".$row['zef_comment']."&nbsp;</td>";
			$return .= "<td style=\"border-left: 1px solid #999;\">".$row['usr_name']."</td>";
			$return .= "<td style=\"border-left: 1px solid #999;\">".$row['dauer']."</td>";
			if($row['zef_cleared'] == 1){$selected="checked='checked'";} else {$selected="";}
			$return .= "<td style=\"border-left: 1px solid #999;border-right: 1px solid #999;\"><input type=\"checkbox\" name=\"".$row['zef_ID']."\" id=\"".$row['zef_ID']."\" ".$selected." onchange=\"xajax_markCleared(".$row['zef_ID'].")\" /></td>";
			$return .= "</tr>";
		}
		$return .= "<tr>";
			$return .= "<td style=\"border-left: 1px solid #999;\">&nbsp;</td>";
			$return .= "<td style=\"border-left: 1px solid #999;\">&nbsp;</td>";
			$return .= "<td style=\"border-left: 1px solid #999;\">&nbsp;</td>";
			$return .= "<td style=\"border-left: 1px solid #999;\">&nbsp;</td>";
			$return .= "<td style=\"border-left: 1px solid #999;\">&nbsp;</td>";
			$return .= "<td style=\"border-left: 1px solid #999;\">Gesamt:</td>";
			$return .= "<td style=\"border-left: 1px solid #999;\">&nbsp;</td>";
			$return .= "<td style=\"border-left: 1px solid #999;border-right: 1px solid #999;\">".$row_gd['gesamtdauer']."</td>";
			$return .= "</tr>";
		$return .= "</table>";
		$return .= "</form>";
		$objResponse->assign("div_liste","innerHTML",$return);
		return $objResponse;
	}
	
	$xajax->processRequest();
	$js = $xajax->getJavascript();
	$smarty->assign('xajax_js',$js);
	
	// grab customers ---------------------------------------------------------------------------------------------------------------
	$query = "SELECT * FROM kimai_knd ORDER BY knd_name";
	$res = mysql_query($query);
	$kunden = array();
	while($row = mysql_fetch_assoc($res)){
		$kunden[] = $row;
	}
	$smarty->assign('kunden', $kunden);
	$smarty->display('index.tpl');
?>