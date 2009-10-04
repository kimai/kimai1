<?php
/*
********************************************************
TinyButStrong - Template Engine for Pro and Beginners
------------------------
Version  : 2.05.8 for PHP >= 4.0.6
Date     : 2006-05-02
Web site : www.tinybutstrong.com
Author   : skrol29@freesurf.fr
********************************************************
This library is free software.
You can redistribute and modify it even for commercial usage,
but you must accept and respect the LPGL License (v2.1 or later).
*/
// Check PHP version
if (PHP_VERSION<'4.0.6') {
	echo '<br><b>TinyButStrong Error</b> (PHP Version Check) : Your PHP version is '.PHP_VERSION.' while TinyButStrong needs PHP version 4.0.6 or higher.';
} elseif (PHP_VERSION<'4.1.0') {
	function array_key_exists (&$key,&$array) {
		return key_exists($key,$array);
	}
}

// Render flags
define('TBS_NOTHING', 0);
define('TBS_OUTPUT', 1);
define('TBS_EXIT', 2);

// Special cache actions
define('TBS_DELETE', -1);
define('TBS_CANCEL', -2);
define('TBS_CACHENOW', -3);
define('TBS_CACHEONSHOW', -4);
define('TBS_CACHELOAD', -5);
define('TBS_CACHEGETAGE', -6);
define('TBS_CACHEGETNAME', -7);
define('TBS_CACHEISONSHOW', -8);

// *********************************************

class clsTbsLocator {
	var $PosBeg = false;
	var $PosEnd = false;
	var $Enlarged = false;
	var $FullName = false;
	var $SubName = '';
	var $SubOk = false;
	var $SubLst = array();
	var $SubNbr = 0;
	var $PrmLst = array();
	var $MagnetId = false;
	var $BlockFound = false;
	var $FirstMerge = true;
	var $ConvProtect = true;
	var $ConvHtml = true;
	var $ConvBr = true;
	var $ConvSpe = false;
}

// *********************************************

class clsTbsDataSource {

var $Type = false;
var $SubType = 0;
var $SrcId = false;
var $Query = '';
var $RecSet = false;
var $RecKey = '';
var $RecNum = 0;
var $RecNumInit = 0;
var $RecSaving = false;
var $RecSaved = false;
var $RecBuffer = false;
var $CurrRec = false;
var $PrevRec = array();
var $oTBS = false;
var $BlockName = '';
var $OnDataOk = false;
var $OnDataSave = '';
var $OnDataInfo = false;
var $OnDataPrm = array();

function DataAlert($Msg) {
	return $this->oTBS->meth_Misc_Alert('MergeBlock '.$this->oTBS->ChrOpen.$this->BlockName.$this->oTBS->ChrClose,$Msg);
}

function DataPrepare(&$SrcId,&$oTBS) {

	$this->SrcId =& $SrcId;
	$this->oTBS =& $oTBS;

	if (is_array($SrcId)) {
		$this->Type = 0;
	} elseif (is_resource($SrcId)) {

		$Key = get_resource_type($SrcId);
		switch ($Key) {
		case 'mysql link'            : $this->Type = 1; break;
		case 'mysql link persistent' : $this->Type = 1; break;
		case 'mysql result'          : $this->Type = 1; $this->SubType = 1; break;
		case 'pgsql link'            : $this->Type = 8; break;
		case 'pgsql link persistent' : $this->Type = 8; break;
		case 'pgsql result'          : $this->Type = 8; $this->SubType = 1; break;
		case 'sqlite database'       : $this->Type = 9; break;
		case 'sqlite database (persistent)'	: $this->Type = 9; break;
		case 'sqlite result'         : $this->Type = 9; $this->SubType = 1; break;
		default :
			$SubKey = 'resource type';
			$this->Type = 7;
			$x = strtolower($Key);
			$x = str_replace('-','_',$x);
			$Function = '';
			$i = 0;
			$iMax = strlen($x);
			while ($i<$iMax) {
				if (($x[$i]==='_') or (($x[$i]>='a') and ($x[$i]<='z')) or (($x[$i]>='0') and ($x[$i]<='9'))) {
					$Function .= $x[$i];
					$i++;
				} else {
					$i = $iMax;
				}
			}
		}

	} elseif (is_string($SrcId)) {

		switch (strtolower($SrcId)) {
		case 'array' : $this->Type = 0; $this->SubType = 1; break;
		case 'clear' : $this->Type = 0; $this->SubType = 3; break;
		case 'mysql' : $this->Type = 1; $this->SubType = 2; break;
		case 'text'  : $this->Type = 4; break;
		case 'num'   : $this->Type = 6; break;
		default :
			if ($SrcId[0]==='~') {
				$ErrMsg = false;
				$this->FctOpen  = $SrcId.'_open';
				$this->FctFetch = $SrcId.'_fetch';
				$this->FctClose = $SrcId.'_close';
				$this->FctPrm = array(false,0);
				if ($oTBS->meth_Misc_UserFctCheck($this->FctOpen,$ErrMsg)) {
					if ($oTBS->meth_Misc_UserFctCheck($this->FctFetch,$ErrMsg)) {
						if ($oTBS->meth_Misc_UserFctCheck($this->FctClose,$ErrMsg)) {
							$this->Type = 11;
							$this->SrcId =& $oTBS->ObjectRef;
						}
					}
				}
				if ($ErrMsg!==false) $this->Type = $this->DataAlert($ErrMsg);
			} else {
				$Key = $SrcId;
				$SubKey = 'keyword';
				$this->Type = 7;
				$Function = $SrcId;
			}
		}

	} elseif (is_object($SrcId)) {
		if (method_exists($SrcId,'tbsdb_open')) {
			if (!method_exists($SrcId,'tbsdb_fetch')) {
				$this->Type = $this->DataAlert('The expected method \'tbsdb_fetch\' is not found for the class '.get_class($SrcId).'.');
			} elseif (!method_exists($SrcId,'tbsdb_close')) {
				$this->Type = $this->DataAlert('The expected method \'tbsdb_close\' is not found for the class '.get_class($SrcId).'.');
			} else {
				$this->Type = 10;
			}
		} else {
			$Key = get_class($SrcId);
			$SubKey = 'object type';
			$this->Type = 7;
			$Function = $Key;
		}
	} elseif ($SrcId===false) {
		$this->DataAlert('The specified source is set to FALSE. Maybe your connection has failed.');
	} else {
		$this->DataAlert('Unsupported variable type : \''.gettype($SrcId).'\'.');
	}

	if ($this->Type===7) {
		$this->FctOpen  = 'tbsdb_'.$Function.'_open';
		$Ok = function_exists($this->FctOpen);
		if (!$Ok) { // Some extended call can have a suffix in the class name, we check without the suffix
			$i = strpos($Function,'_');
			if ($i!==false) {
				$x = substr($Function,0,$i);
				$z  = 'tbsdb_'.$x.'_open';
				$Ok = function_exists($z);
				if ($Ok) {
					$Function = $x;
					$this->FctOpen = $z;
				}
			}
		}
		if ($Ok) {
			$this->FctFetch = 'tbsdb_'.$Function.'_fetch';
			$this->FctClose = 'tbsdb_'.$Function.'_close';
			if (function_exists($this->FctFetch)) {
				if (!function_exists($this->FctClose)) $this->Type = $this->DataAlert('The expected custom function \''.$this->FctClose.'\' is not found.');
			} else {
				$this->Type = $this->DataAlert('The expected custom function \''.$this->FctFetch.'\' is not found.');
			}
		} else {
			$this->Type = $this->DataAlert('The data source Id \''.$Key.'\' is an unsupported '.$SubKey.' because custom function \''.$this->FctOpen.'\' is not found.');
		}
	}

	return ($this->Type!==false);

}

function DataOpen(&$Query,&$PageSize,&$PageNum,&$RecStop) {

	// Init values
	unset($this->CurrRec); $this->CurrRec = true;
	if ($this->RecSaved) {
		$this->FirstRec = true;
		unset($this->RecKey); $this->RecKey = '';
		$this->RecNum = $this->RecNumInit;
		return true;
	}
	unset($this->RecSet); $this->RecSet = false;
	$this->RecNumInit = 0;
	$this->RecNum = 0;
	if ($this->OnDataInfo!==false) {
		$this->OnDataOk = true;
		$this->OnDataPrm[0] =& $this->BlockName;
		$this->OnDataPrm[1] =& $this->CurrRec;
		$this->OnDataPrm[2] =& $this->RecNum;
	}

	switch ($this->Type) {
	case 0: // Array
		if (($this->SubType===1) and (is_string($Query))) $this->SubType = 2;
		if ($this->SubType===0) {
			if (PHP_VERSION==='4.4.1') {$this->RecSet = $this->SrcId;} else {$this->RecSet =& $this->SrcId;} // bad bug in PHP 4.4.1
		} elseif ($this->SubType===1) {
			if (is_array($Query)) {
				if (PHP_VERSION==='4.4.1') {$this->RecSet = $Query;} else {$this->RecSet =& $Query;}
			} else {
				$this->DataAlert('Type \''.gettype($Query).'\' not supported for the Query Parameter going with \'array\' Source Type.');
			}
		} elseif ($this->SubType===2) {
			//Found the global variable name and the sub-keys
			$Pos = strpos($Query,'[');
			if ($Pos===false) {
				$VarName = $Query;
				$Keys = array();
			} else {
				$VarName = substr($Query,0,$Pos);
				$Keys = substr($Query,$Pos+1,strlen($Query)-$Pos-2);
				$Keys = explode('][',$Keys);
			}
			// Check variable and sub-keys
			if (isset($GLOBALS[$VarName])) {
				if (PHP_VERSION==='4.4.1') {$Var = $GLOBALS[$VarName];} else {$Var =& $GLOBALS[$VarName];}
				if (is_array($Var)) {
					$Ok = true;
					$KeyMax = count($Keys)-1;
					$KeyNum = 0;
					while ($Ok and ($KeyNum<=$KeyMax)) {
						if (isset($Var[$Keys[$KeyNum]])) {
							$Var =& $Var[$Keys[$KeyNum]];
							$KeyNum++;
							if (!is_array($Var)) $Ok = $this->DataAlert('Invalid query \''.$Query.'\' because item \''.$VarName.'['.implode('][',array_splice($Keys,0,$KeyNum)).']\' is not an array.');
						} else {
							$Ok = false; // Item not found => not an error, considered as a query with empty result.
							$this->RecSet = array();
						}
					}
					if ($Ok) $this->RecSet =& $Var;
				} else {
					$this->DataAlert('Invalid query \''.$Query.'\' because global variable \''.$VarName.'\' is not an array.');
				}
			} else {
				$this->DataAlert('Invalid query \''.$Query.'\' because global variable \''.$VarName.'\' is not found.');
			}
		} elseif ($this->SubType===3) { // Clear
			$this->RecSet = array();
		}
		// First record
		if ($this->RecSet!==false) {
			$this->RecNbr = $this->RecNumInit + count($this->RecSet);
			$this->FirstRec = true;
			$this->RecSaved = true;
			$this->RecSaving = false;
		}
		break;
	case 1: // MySQL
		switch ($this->SubType) {
		case 0: $this->RecSet = @mysql_query($Query,$this->SrcId); break;
		case 1: $this->RecSet = $this->SrcId; break;
		case 2: $this->RecSet = @mysql_query($Query); break;
		}
		if ($this->RecSet===false) $this->DataAlert('MySql error message when opening the query: '.mysql_error());
		break;
	case 4: // Text
		if (is_string($Query)) {
			$this->RecSet =& $Query;
		} else {
			$this->RecSet = ''.$Query;	
		}
		$PageSize = 0;
		break;
	case 6: // Num
		$this->RecSet = true;
		$this->NumMin = 1;
		$this->NumMax = 1;
		$this->NumStep = 1;
		if (is_array($Query)) {
			if (isset($Query['min'])) $this->NumMin = $Query['min'];
			if (isset($Query['step'])) $this->NumStep = $Query['step'];
			if (isset($Query['max'])) {
				$this->NumMax = $Query['max'];
			} else {
				$this->RecSet = $this->DataAlert('The \'num\' source is an array that has no value for the \'max\' key.');
			}
			if ($this->NumStep==0) $this->RecSet = $this->DataAlert('The \'num\' source is an array that has a step value set to zero.');
		} else {
			$this->NumMax = ceil($Query);
		}
		if ($this->RecSet) {
			if ($this->NumStep>0) {
				$this->NumVal = $this->NumMin;
			} else {
				$this->NumVal = $this->NumMax;
			}
		}
		break;
	case 7: // Custom function
		$FctOpen = $this->FctOpen;
		$this->RecSet = $FctOpen($this->SrcId,$Query);
		break;
	case 8: // PostgreSQL
		switch ($this->SubType) {
		case 0: $this->RecSet = @pg_query($this->SrcId,$Query); break;
		case 1: $this->RecSet = $this->SrcId; break;
		}
		if ($this->RecSet===false) $this->DataAlert('PostgreSQL error message when opening the query: '.pg_last_error($this->SrcId));
		break;
	case 9: // SQLite
		switch ($this->SubType) {
		case 0: $this->RecSet = @sqlite_query($this->SrcId,$Query); break;
		case 1: $this->RecSet = $this->SrcId; break;
		}
		if ($this->RecSet===false) $this->DataAlert('SQLite error message when opening the query:'.sqlite_error_string(sqlite_last_error($this->SrcId)));
		break;
	case 10: // Custom method
		$this->RecSet = $this->SrcId->tbsdb_open($this->SrcId,$Query);
		break;
	case 11: // ObjectRef
		$this->RecSet = call_user_func_array($this->FctOpen,array(&$this->SrcId,&$Query));
		break;
	}

	if ($this->Type===0) {
		unset($this->RecKey); $this->RecKey = '';
	} else {
		if ($this->RecSaving) {
			unset($this->RecBuffer); $this->RecBuffer = array();
		}
		$this->RecKey =& $this->RecNum; // Not array: RecKey = RecNum
	}

	//Goto the page
	if (($this->RecSet!==false) and ($PageSize>0)) {
		if ($PageNum==-1) { // Goto end of the recordset
			if ($this->RecSaved) { // Data source is array
				$PageNum = intval(ceil($this->RecNbr/$PageSize));
			} else {
				// Read records, saving the last page in $this->RecBuffer
				$i = 0;
				unset($this->RecBuffer); $this->RecBuffer = array();
				$this->RecSaving = true;
				$this->DataFetch();
				while ($this->CurrRec!==false) {
					if ($i===$PageSize) {
						$this->RecBuffer = array($this->RecKey => $this->CurrRec);
						$i = 0;
						$this->RecNumInit += $PageSize;
					}
					$i++;
					$this->DataFetch();
				}
				$this->DataClose(); // Close the real recordset source
				unset($this->RecNum); $this->RecNum = 0+$this->RecNumInit;
				$this->FirstRec = true;
			}
		}
		if ($PageNum>0) {
			// We pass all record until the asked page
			$RecStop = ($PageNum-1) * $PageSize;
			while ($this->RecNum<$RecStop) {
				$this->DataFetch();
				if ($this->CurrRec===false) $RecStop=$this->RecNum;	
			}
			if ($this->CurrRec!==false) $RecStop = $PageNum * $PageSize;
			$this->RecNumInit = $this->RecNum; // Useful if RecSaved
		} else {
			$RecStop = 1;
		}
	}

	return ($this->RecSet!==false);

}

function DataFetch() {

	if ($this->RecSaved) {
		if ($this->RecNum<$this->RecNbr) {
			if ($this->FirstRec) {
				if ($this->SubType===2) { // From string
					reset($this->RecSet);
					$this->RecKey = key($this->RecSet);
					$this->CurrRec =& $this->RecSet[$this->RecKey];
				} else {
					$this->CurrRec = reset($this->RecSet);
					$this->RecKey = key($this->RecSet);
				}
				$this->FirstRec = false;
			} else {
				if ($this->SubType===2) { // From string
					next($this->RecSet);
					$this->RecKey = key($this->RecSet);
					$this->CurrRec =& $this->RecSet[$this->RecKey];
				} else {
					$this->CurrRec = next($this->RecSet);
					$this->RecKey = key($this->RecSet);
				}
			}
			if (!is_array($this->CurrRec)) $this->CurrRec = array('key'=>$this->RecKey, 'val'=>$this->CurrRec);
			$this->RecNum++;
			if ($this->OnDataOk) {
				$this->OnDataPrm[1] =& $this->CurrRec; // Reference has changed if ($this->SubType===2)
				call_user_func_array($this->OnDataInfo,$this->OnDataPrm);
			}
		} else {
			unset($this->CurrRec); $this->CurrRec = false;
		}
		return;
	}

	switch ($this->Type) {
	case 1: // MySQL
		$this->CurrRec = mysql_fetch_assoc($this->RecSet);
		break;
	case 4: // Text
		if ($this->RecNum===0) {
			if ($this->RecSet==='') {
				$this->CurrRec = false;
			} else {
				$this->CurrRec =& $this->RecSet;
			}
		} else {
			$this->CurrRec = false;
		}
		break;
	case 6: // Num
		if (($this->NumVal>=$this->NumMin) and ($this->NumVal<=$this->NumMax)) {
			$this->CurrRec = array('val'=>$this->NumVal);
			$this->NumVal += $this->NumStep;
		} else {
			$this->CurrRec = false;
		}
		break;
	case 7: // Custom function
		$FctFetch = $this->FctFetch;
		$this->CurrRec = $FctFetch($this->RecSet,$this->RecNum+1);
		break;
	case 8: // PostgreSQL
		$this->CurrRec = @pg_fetch_array($this->RecSet,$this->RecNum,PGSQL_ASSOC); // warning comes when no record left.
		break;
	case 9: // SQLite
		$this->CurrRec = sqlite_fetch_array($this->RecSet,SQLITE_ASSOC);
		break;
	case 10: // Custom method
		$this->CurrRec = $this->SrcId->tbsdb_fetch($this->RecSet,$this->RecNum+1);
		break;
	case 11: // ObjectRef
		$this->FctPrm[0] =& $this->RecSet; $this->FctPrm[1] = $this->RecNum+1;
		$this->CurrRec = call_user_func_array($this->FctFetch,$this->FctPrm);
		break;
	}

	// Set the row count
	if ($this->CurrRec!==false) {
		$this->RecNum++;
		if ($this->OnDataOk) call_user_func_array($this->OnDataInfo,$this->OnDataPrm);
		if ($this->RecSaving) $this->RecBuffer[$this->RecKey] = $this->CurrRec;
	}

}

function DataClose() {
	if ($this->RecSaved) return;
	$this->OnDataOk = false;
	switch ($this->Type) {
	case 1: mysql_free_result($this->RecSet); break;
	case 7: $FctClose=$this->FctClose; $FctClose($this->RecSet); break;
	case 8: pg_free_result($this->RecSet); break;
	case 10: $this->SrcId->tbsdb_close($this->RecSet); break;
	case 11: call_user_func_array($this->FctClose,array(&$this->RecSet)); break;
	}
	if ($this->RecSaving) {
		$this->RecSet =& $this->RecBuffer;
		$this->RecNbr = $this->RecNumInit + count($this->RecSet);
		$this->RecSaving = false;
		$this->RecSaved = true;
	}
}

}

// *********************************************

class clsTinyButStrong {

// Public properties
var $Source = ''; // Current result of the merged template
var $Render = 3;
var $HtmlCharSet = '';
var $TplVars = array();
var $VarPrefix = '';
var $ObjectRef = false;
var $Protect = true;
// Private properties
var $_LastFile = ''; // The last loaded template file
var $_CacheFile = false; // The name of the file to save the content in.
var $_HtmlCharFct = false;
var $_Mode = 0;
// Used to be globals
var $ChrOpen = '[';
var $ChrClose = ']';
var $ChrVal = '[val]';
var $ChrProtect = '&#91;';
var $CacheMask = 'cache_tbs_*.php';
var $TurboBlock = true;
var $MaxEnd = '...';

function clsTinyButStrong($Chrs='',$VarPrefix='') {
	if ($Chrs!=='') {
		$Ok = false;
		$Len = strlen($Chrs);
		if ($Len===2) { // For compatibility
			$this->ChrOpen = $Chrs[0];
			$this->ChrClose = $Chrs[1];
			$Ok = true;
		} else {
			$Pos = strpos($Chrs,',');
			if (($Pos!==false) and ($Pos>0) and ($Pos<$Len-1)) {
				$this->ChrOpen = substr($Chrs,0,$Pos);
				$this->ChrClose = substr($Chrs,$Pos+1);
				$Ok = true;
			}
		}
		if ($Ok) {
			$this->ChrVal = $this->ChrOpen.'val'.$this->ChrClose;
			$this->ChrProtect = '&#'.ord($this->ChrOpen[0]).';'.substr($this->ChrOpen,1);
		} else {
			$this->meth_Misc_Alert('Creating instance','Bad argument for tag delimitors \''.$Chrs.'\'.');
		}
	}
	$this->VarPrefix = $VarPrefix;
	//Cache for formats
	if (!isset($GLOBALS['_tbs_FrmMultiLst'])) {
		$GLOBALS['_tbs_FrmMultiLst'] = array();
		$GLOBALS['_tbs_FrmSimpleLst'] = array();
	}
}

// Public methods
function LoadTemplate($File,$HtmlCharSet='') {
	// Load the file
	$x = '';
	if (!tbs_Misc_GetFile($x,$File)) return $this->meth_Misc_Alert('LoadTemplate Method','Unable to read the file \''.$File.'\'.');
	// CharSet analysis
	if ($HtmlCharSet==='+') {
		$this->Source .= $x;
	} else {
		$this->Source = $x;
		if ($this->_Mode==0) {
			$this->_LastFile = $File;
			$this->_HtmlCharFct = false;
			$this->TplVars = array();
			if (is_string($HtmlCharSet)) {
				if (($HtmlCharSet!=='') and ($HtmlCharSet[0]==='=')) {
					$ErrMsg = false;
					$HtmlCharSet = substr($HtmlCharSet,1);
					if ($this->meth_Misc_UserFctCheck($HtmlCharSet,$ErrMsg)) {
						$this->_HtmlCharFct = true;
					} else {
						$this->meth_Misc_Alert('LoadTemplate Method',$ErrMsg);
						$HtmlCharSet = '';
					}
				}
			} elseif ($HtmlCharSet===false) {
				$this->Protect = false;
			} else {
				$this->meth_Misc_Alert('LoadTemplate Method','CharSet is not a string.');
				$HtmlCharSet = '';
			}
			$this->HtmlCharSet = $HtmlCharSet;
		}
	}
	// Automatic fields and blocks
	$this->meth_Merge_Auto($this->Source,'onload',true,true);
	return true;
}

function GetBlockSource($BlockName,$List=false) {
	$RetVal = array();
	$Nbr = 0;
	$Pos = 0;
	$FieldOutside = false;
	$P1 = false;
	while ($Loc = $this->meth_Locator_FindBlockNext($this->Source,$BlockName,$Pos,'.',false,$P1,$FieldOutside)) {
		$P1 = false;
		$Nbr++;
		$RetVal[$Nbr] = substr($this->Source,$Loc->PosBeg,$Loc->PosEnd-$Loc->PosBeg+1);
		if (!$List) return $RetVal[$Nbr];
		$Pos = $Loc->PosEnd;
	}
	if ($List) {
		return $RetVal;
	} else {
		return false;
	}
}

function MergeBlock($BlockName,$SrcId,$Query='',$PageSize=0,$PageNum=0,$RecKnown=0) {
	if ($SrcId==='cond') {
		$Nbr = 0;
		$BlockLst = explode(',',$BlockName);
		foreach ($BlockLst as $Block) {
			$Nbr += $this->meth_Merge_Auto($this->Source,$Block,false,false);
		}
		return $Nbr;
	} else {
		return $this->meth_Merge_Block($this->Source,$BlockName,$SrcId,$Query,$PageSize,$PageNum,$RecKnown);
	}
}

function MergeField($Name,$Value,$IsUserFct=false) {
	$PosBeg = 0;
	if ($IsUserFct) {
		$FctInfo = $Value;
		$ErrMsg = false;
		if ($this->meth_Misc_UserFctCheck($FctInfo,$ErrMsg)) {
			$FctPrm = array('','');
			while ($Loc = $this->meth_Locator_FindTbs($this->Source,$Name,$PosBeg,'.')) {
				$FctPrm[0] =& $Loc->SubName; $FctPrm[1] =& $Loc->PrmLst;
				$x = call_user_func_array($FctInfo,$FctPrm);
				$PosBeg = $this->meth_Locator_Replace($this->Source,$Loc,$x,false);
			}
		} else {
			$this->meth_Misc_Alert('MergeField Method',$ErrMsg);
		}
	} else {
		while ($Loc = $this->meth_Locator_FindTbs($this->Source,$Name,$PosBeg,'.')) {
			$PosBeg = $this->meth_Locator_Replace($this->Source,$Loc,$Value,true);
		}
	}
}

function MergeSpecial($Type) {
	$Type = strtolower($Type);
	$this->meth_Merge_Special($Type);
}

function MergeNavigationBar($BlockLst,$Options,$PageCurr,$RecCnt=-1,$PageSize=1) {
	$BlockLst = explode(',',$BlockLst);
	foreach ($BlockLst as $BlockName) {
		$BlockName = trim($BlockName);
		$this->meth_Merge_NavigationBar($this->Source,$BlockName,$Options,$PageCurr,$RecCnt,$PageSize);
	}
}

function Show($Render='') {
	if ($Render==='') $Render = $this->Render;
	if ($this->_CacheFile!==true) $this->meth_Merge_Special('onshow,var');
	if (is_string($this->_CacheFile)) $this->meth_Cache_Save($this->_CacheFile,$this->Source);
	if (($Render & TBS_OUTPUT)==TBS_OUTPUT) echo $this->Source;
	if (($this->_Mode==0) and (($Render & TBS_EXIT)==TBS_EXIT)) exit;
}

function CacheAction($CacheId,$Action=3600,$Dir='') {

	$CacheId = trim($CacheId);
	$Res = false;

	if ($Action===TBS_CANCEL) { // Cancel cache save if any
		$this->_CacheFile = false;
	} elseif ($CacheId === '*') {
		if ($Action===TBS_DELETE) $Res = tbs_Cache_DeleteAll($Dir,$this->CacheMask);
	} else {
		$CacheFile = tbs_Cache_File($Dir,$CacheId,$this->CacheMask);
		if ($Action===TBS_CACHENOW) {
			$this->meth_Cache_Save($CacheFile,$this->Source);
		} elseif ($Action===TBS_CACHEGETAGE) {
			if (file_exists($CacheFile)) $Res = time()-filemtime($CacheFile);
		} elseif ($Action===TBS_CACHEGETNAME) {
			$Res = $CacheFile;
		} elseif ($Action===TBS_CACHEISONSHOW) {
			$Res = ($this->_CacheFile!==false);
		} elseif ($Action===TBS_CACHELOAD) {
			if (file_exists($CacheFile)) {
				if (tbs_Misc_GetFile($this->Source,$CacheFile)) {
					$this->_CacheFile = $CacheFile;
					$Res = true;
				}
			}
			if ($Res===false)	$this->Source = '';
		} elseif ($Action===TBS_DELETE) {
			if (file_exists($CacheFile)) $Res = @unlink($CacheFile);
		} elseif ($Action===TBS_CACHEONSHOW) {
			$this->_CacheFile = $CacheFile;
			@touch($CacheFile);
		} elseif($Action>=0) {
			$Res = tbs_Cache_IsValide($CacheFile,$Action);
			if ($Res) { // Load the cache
				if (tbs_Misc_GetFile($this->Source,$CacheFile)) {
					$this->_CacheFile = true; // Special value
					$this->Show();
				} else {
					$this->meth_Misc_Alert('CacheAction Method','Unable to read the file \''.$CacheFile.'\'.');
					$Res==false;
				}
				$this->_CacheFile = false;
			} else {
				// The result will be saved in the cache when the Show() method is called
				$this->_CacheFile = $CacheFile;
				@touch($CacheFile);
			}
		}
	}

	return $Res;

}

// *-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-

function meth_Locator_FindTbs(&$Txt,$Name,$Pos,$ChrSub) {
// Find a TBS Locator

	$PosEnd = false;
	$PosMax = strlen($Txt) -1;
	$Start = $this->ChrOpen.$Name;

	do {
		// Search for the opening char
		if ($Pos>$PosMax) return false;
		$Pos = strpos($Txt,$Start,$Pos);

		// If found => next chars are analyzed
		if ($Pos===false) {
			return false;
		} else {
			$Loc =& new clsTbsLocator;
			$ReadPrm = false;
			$PosX = $Pos + strlen($Start);
			$x = $Txt[$PosX];

			if ($x===$this->ChrClose) {
				$PosEnd = $PosX;
			} elseif ($x===$ChrSub) {
				$Loc->SubOk = true; // it is no longer the false value
				$ReadPrm = true;
				$PosX++;
			} elseif (strpos(';',$x)!==false) {
				$ReadPrm = true;
				$PosX++;
			} else {
				$Pos++;
			}

			if ($ReadPrm) {
				tbs_Locator_PrmRead($Txt,$PosX,false,'\'',$this->ChrOpen,$this->ChrClose,$Loc,$PosEnd);
				if ($PosEnd===false) {
					$this->meth_Misc_Alert('Tag definition','Can\'t found the end of the tag \''.substr($Txt,$Pos,$PosX-$Pos+10).'...\'.');
					$Pos++;
				}
			}
			
		}

	} while ($PosEnd===false);

	$Loc->PosBeg = $Pos;
	$Loc->PosEnd = $PosEnd;
	if ($Loc->SubOk) {
		$Loc->FullName = $Name.'.'.$Loc->SubName;
		$Loc->SubLst = explode('.',$Loc->SubName);
		$Loc->SubNbr = count($Loc->SubLst);
	} else {
		$Loc->FullName = $Name;
	}
	if ($ReadPrm and isset($Loc->PrmLst['comm'])) {
		$Loc->PosBeg0 = $Loc->PosBeg;
		$Loc->PosEnd0 = $Loc->PosEnd;
		$Loc->Enlarged = tbs_Locator_EnlargeToStr($Txt,$Loc,'<!--' ,'-->');
	}

	return $Loc;

}

// Search and cache TBS locators founded in $Txt.
function meth_Locator_SectionCache(&$LocR,$Bid) {

	$LocR->BlockChk[$Bid] = false;

	$LocLst =& $LocR->BlockLoc[$Bid];
	$Txt =& $LocR->BlockSrc[$Bid];
	$BlockName =& $LocR->BlockName[$Bid];

	$Pos = 0;
	$PrevEnd = -1;
	$Nbr = 0;
	while ($Loc = $this->meth_Locator_FindTbs($Txt,$BlockName,$Pos,'.')) {
		if (($Loc->SubName==='#') or ($Loc->SubName==='$')) {
			$Loc->IsRecInfo = true;
			$Loc->RecInfo = $Loc->SubName;
			$Loc->SubName = '';
		} else {
			$Loc->IsRecInfo = false;
		}
		if ($Loc->PosBeg>$PrevEnd) {
			// The previous tag is not embeding => increment
			$Nbr++;
		} else {
			// The previous tag is embeding => no increment, then previous is over writed
			$LocR->BlockChk[$Bid] = true;
		}
		$PrevEnd = $Loc->PosEnd;
		if ($Loc->Enlarged) { // Parameter 'comm'
			$Pos = $Loc->PosBeg0+1;
			$Loc->Enlarged = false;
		} else {
			$Pos = $Loc->PosBeg+1;
		}
		$LocLst[$Nbr] = $Loc;
	}

	$LocLst[0] = $Nbr;

}

function meth_Locator_Replace(&$Txt,&$Loc,&$Value,$CheckSub) {
// This function enables to merge a locator with a text and returns the position just after the replaced block
// This position can be useful because we don't know in advance how $Value will be replaced.

	// Found the value if there is a subname
	if ($CheckSub and $Loc->SubOk) {
		$SubId = 0;
		while ($SubId<$Loc->SubNbr) {
			$x = $Loc->SubLst[$SubId]; // &$Loc... brings an error with Event Example, I don't know why.
			if (is_array($Value)) {
				if (isset($Value[$x])) {
					$Value =& $Value[$x];
				} elseif (array_key_exists($x,$Value)) {// can happens when value is NULL
					$Value =& $Value[$x];
				} else {
					unset($Value); $Value = '';
					if (!isset($Loc->PrmLst['noerr'])) $this->meth_Misc_Alert('Array value','Can\'t merge '.$this->ChrOpen.$Loc->FullName.$this->ChrClose.' because sub-item \''.$x.'\' is not an existing key in the array.',true);
				}
				$SubId++;
			} elseif (is_object($Value)) {
				if (method_exists($Value,$x)) {
					$x = call_user_func(array(&$Value,$x));
				} elseif (isset($Value->$x)) {
					$x = $Value->$x;
				} else {
					if (!isset($Loc->PrmLst['noerr'])) $this->meth_Misc_Alert('Object value','Can\'t merge '.$this->ChrOpen.$Loc->FullName.$this->ChrClose.' because \''.$x.'\' is neither a method nor a property in the class \''.get_class($Value).'\'.',true);
					unset($x); $x = '';
				}
				$Value =& $x;
				unset($x); $x = '';
				$SubId++;
			} else {
				if (isset($Loc->PrmLst['selected'])) {
					$SelArray =& $Value;
				} else {
					if (!isset($Loc->PrmLst['noerr'])) $this->meth_Misc_Alert('Object or Array value expected','Can\'t merge '.$this->ChrOpen.$Loc->FullName.$this->ChrClose.' because the item before \''.$x.'\' is neither an object nor an array. Its type is '.gettype($Value).'.',true);
				}
				unset($Value); $Value = '';
				$SubId = $Loc->SubNbr;
			}
		}
	}

	$CurrVal = $Value;

	// File inclusion
	if (isset($Loc->PrmLst['file'])) {
		$File = $Loc->PrmLst['file'];
		$this->meth_Merge_PhpVar($File,false);
		$File = str_replace($this->ChrVal,$CurrVal,$File);
		$OnlyBody = !(isset($Loc->PrmLst['htmlconv']) and (strtolower($Loc->PrmLst['htmlconv'])==='no')); // It's a text file, we don't get the BODY part
		if (tbs_Misc_GetFile($CurrVal,$File)) {
			if ($OnlyBody) $CurrVal = tbs_Html_GetPart($CurrVal,'BODY',false,true);
		} else {
			$CurrVal = '';
			if (!isset($Loc->PrmLst['noerr'])) $this->meth_Misc_Alert('Parameter \'file\'','Field '.$this->ChrOpen.$Loc->FullName.$this->ChrClose.' : unable to read the file \''.$File.'\'.',true);
		}
		$Loc->ConvHtml = false;
		$Loc->ConvProtect = false;
	}

	// OnFormat event
	if (isset($Loc->PrmLst['onformat'])) {
		if ($Loc->FirstMerge) {
			$Loc->OnFrmInfo = $Loc->PrmLst['onformat'];
			$Loc->OnFrmPrm = array(&$Loc->FullName,'',&$Loc->PrmLst,&$this);
			$ErrMsg = false;
			if (!$this->meth_Misc_UserFctCheck($Loc->OnFrmInfo,$ErrMsg)) {
				unset($Loc->PrmLst['onformat']);
				if (!isset($Loc->PrmLst['noerr'])) $this->meth_Misc_Alert('Parameter \'onformat\'',$ErrMsg);
				$Loc->OnFrmInfo = 'pi'; // Execute the function pi() just to avoid extra error messages 
			}
		}
		$Loc->OnFrmPrm[1] =& $CurrVal;
		if (isset($Loc->PrmLst['subtpl'])) {
			$this->meth_Misc_ChangeMode(true,$Loc,$CurrVal,true,true);
			call_user_func_array($Loc->OnFrmInfo,$Loc->OnFrmPrm);
			$this->meth_Misc_ChangeMode(false,$Loc,$CurrVal,true,true);
		} else {
			call_user_func_array($Loc->OnFrmInfo,$Loc->OnFrmPrm);
		}
	}

	// Select a value in a HTML option list
	$Select = isset($Loc->PrmLst['selected']);
	if ($Select) {
		if (is_array($CurrVal)) {
			$SelArray =& $CurrVal;
			unset($CurrVal); $CurrVal = ' ';
		} else {
			$SelArray = false;
		}
	}

	// Convert the value to a string, use format if specified
	if (isset($Loc->PrmLst['frm'])) {
		$CurrVal = tbs_Misc_Format($Loc,$CurrVal);
		$Loc->ConvHtml = false;
	} else {
		if (!is_string($CurrVal)) $CurrVal = @strval($CurrVal);
	}

	// case of an 'if' 'then' 'else' options
	$OVal =& $CurrVal; // Must be assigner after $SelArray, if any
	$Script = isset($Loc->PrmLst['script']);
	if (isset($Loc->PrmLst['if'])) {
		if ($Loc->FirstMerge) {
			$this->meth_Merge_PhpVar($Loc->PrmLst['if'],false);
			if (isset($Loc->PrmLst['then'])) $this->meth_Merge_PhpVar($Loc->PrmLst['then'],true);
			if (isset($Loc->PrmLst['else'])) $this->meth_Merge_PhpVar($Loc->PrmLst['else'],true);
		}
		$x = str_replace($this->ChrVal,$CurrVal,$Loc->PrmLst['if']);
		if (tbs_Misc_CheckCondition($x)) {
			if (isset($Loc->PrmLst['then'])) {
				unset($CurrVal); $CurrVal = ''.$Loc->PrmLst['then']; // Now $CurrVal and $OVal are different
			}
		} else {
			$Script = false;
			if (isset($Loc->PrmLst['else'])) {
				unset($CurrVal); $CurrVal = ''.$Loc->PrmLst['else']; // Now $CurrVal and $OVal are different
			} else {
				$CurrVal = '';
			}
		}
	}

	if ($Script) {// Include external PHP script
		$File = $Loc->PrmLst['script'];
		$this->meth_Merge_PhpVar($File,false);
		$File = str_replace($this->ChrVal,$CurrVal,$File);
		$Switch = isset($Loc->PrmLst['subtpl']);
		$GetOb = ($Switch or isset($Loc->PrmLst['getob']));
		$CurrPrm =& $Loc->PrmLst; // Local var for users
		$this->meth_Misc_ChangeMode(true,$Loc,$CurrVal,$Switch,$GetOb);
		if (isset($Loc->PrmLst['once'])) {$x = @include_once($File);} else {$x = @include($File);}
		if ($x===false) {
			if (!isset($Loc->PrmLst['noerr'])) $this->meth_Misc_Alert('Parameter \'script\'','Field '.$this->ChrOpen.$Loc->FullName.$this->ChrClose.' cannot be merged because file \''.$File.'\' is not found or not readable.',true);
		}
		$this->meth_Misc_ChangeMode(false,$Loc,$CurrVal,$Switch,$GetOb);
	}

	if ($Loc->FirstMerge) {
		$Loc->FirstMerge = false;
		// Check HtmlConv parameter
		if (isset($Loc->PrmLst['htmlconv'])) {
			$x = strtolower($Loc->PrmLst['htmlconv']);
			$x = '+'.str_replace(' ','',$x).'+';
			if (strpos($x,'+esc+')!==false)  {tbs_Misc_ConvSpe($Loc); $Loc->ConvHtml = false; $Loc->ConvEsc = true; }
			if (strpos($x,'+wsp+')!==false)  {tbs_Misc_ConvSpe($Loc); $Loc->ConvWS = true; }
			if (strpos($x,'+js+')!==false)   {tbs_Misc_ConvSpe($Loc); $Loc->ConvHtml = false; $Loc->ConvJS = true; }
			if (strpos($x,'+no+')!==false)   $Loc->ConvHtml = false;
			if (strpos($x,'+yes+')!==false)  $Loc->ConvHtml = true;
			if (strpos($x,'+nobr+')!==false) {$Loc->ConvHtml = true; $Loc->ConvBr = false; }
			if (strpos($x,'+look+')!==false) {tbs_Misc_ConvSpe($Loc); $Loc->ConvLook = true; }
		} else {
			if ($this->HtmlCharSet===false) $Loc->ConvHtml = false; // No HTML
		}
		// We protect the data that does not come from the source of the template
		if (isset($Loc->PrmLst['protect'])) {
			$x = strtolower($Loc->PrmLst['protect']);
			switch ($x) {
			case 'no' : $Loc->ConvProtect = false; break;
			case 'yes': $Loc->ConvProtect = true; break;
			}
		} else {
			if ($this->Protect===false) $Loc->ConvProtect = false;
		}
	}

	// MaxLength
	if (isset($Loc->PrmLst['max'])) {
		$x = intval($Loc->PrmLst['max']);
		if (strlen($CurrVal)>$x) {
			if ($Loc->ConvHtml or ($this->HtmlCharSet===false)) {
				$CurrVal = substr($CurrVal,0,$x-1).$this->MaxEnd;
			} else {
				tbs_Html_Max($CurrVal,$x);
			}
		}
	}

	// HTML conversion, and TBS protection
	if ($Loc->ConvSpe) { // Using special parameters
		if ($Loc->ConvLook) {
			$Loc->ConvHtml = !tbs_Html_IsHtml($OVal);
			if ($Loc->ConvHtml===false) $OVal = tbs_Html_GetPart($OVal,'BODY',false,true);
		}
		if ($Loc->ConvHtml) {
			$this->meth_Conv_Html($OVal);
			if ($Loc->ConvBr) $OVal = nl2br($OVal);
		}
		if ($Loc->ConvEsc) $OVal = str_replace('\'','\'\'',$OVal);
		if ($Loc->ConvWS) {
			$check = '  ';
			$nbsp = '&nbsp;';
			do {
				$pos = strpos($OVal,$check);
				if ($pos!==false) $OVal = substr_replace($OVal,$nbsp,$pos,1);
			} while ($pos!==false);
		}
		if ($Loc->ConvJS) {
			$OVal = addslashes($OVal); // apply to ('), ("), (\) and (null)
			$OVal = str_replace("\n",'\n',$OVal);
			$OVal = str_replace("\r",'\r',$OVal);
			$OVal = str_replace("\t",'\t',$OVal);
		}
	}	elseif ($Loc->ConvHtml) {
		$this->meth_Conv_Html($OVal);
		if ($Loc->ConvBr) $OVal = nl2br($OVal);
	}
	if ($Loc->ConvProtect) $OVal = str_replace($this->ChrOpen,$this->ChrProtect,$OVal);
	if ($CurrVal!==$OVal) $CurrVal = str_replace($this->ChrVal,$OVal,$CurrVal);

	// Case when it's an empty string
	if ($CurrVal==='') {

		if ($Loc->MagnetId===false) {
			if (isset($Loc->PrmLst['.'])) {
				$Loc->MagnetId = -1;
			} elseif (isset($Loc->PrmLst['ifempty'])) {
				$Loc->MagnetId = -2;
			} elseif (isset($Loc->PrmLst['magnet'])) {
				$Loc->MagnetId = 1;
				$Loc->PosBeg0 = $Loc->PosBeg;
				$Loc->PosEnd0 = $Loc->PosEnd;
				if (isset($Loc->PrmLst['mtype'])) {
					switch ($Loc->PrmLst['mtype']) {
					case 'm+m': $Loc->MagnetId = 2; break;
					case 'm*': $Loc->MagnetId = 3; break;
					case '*m': $Loc->MagnetId = 4; break;
					}
				}
			} else {
				$Loc->MagnetId = 0;
			}
		}

		switch ($Loc->MagnetId) {
		case 0: break;
		case -1: $CurrVal = '&nbsp;'; break; // Enables to avoid blanks in HTML tables
		case -2: $CurrVal = $Loc->PrmLst['ifempty']; break;
		case 1:
			$Loc->Enlarged = true;
			tbs_Locator_EnlargeToTag($Txt,$Loc,$Loc->PrmLst['magnet'],false,false);
			break;
		case 2:
			$Loc->Enlarged = true;
			$CurrVal = tbs_Locator_EnlargeToTag($Txt,$Loc,$Loc->PrmLst['magnet'],false,true);
			break;
		case 3:
			$Loc->Enlarged = true;
			$Loc2 = tbs_Html_FindTag($Txt,$Loc->PrmLst['magnet'],true,$Loc->PosBeg,false,1,false);
			if ($Loc2!==false) {
				$Loc->PosBeg = $Loc2->PosBeg;
				if ($Loc->PosEnd<$Loc2->PosEnd) $Loc->PosEnd = $Loc2->PosEnd;
			}
			break;
		case 4:
			$Loc->Enlarged = true;
			$Loc2 = tbs_Html_FindTag($Txt,$Loc->PrmLst['magnet'],true,$Loc->PosBeg,true,1,false);
			if ($Loc2!==false) $Loc->PosEnd = $Loc2->PosEnd;
			break;
		}
		$NewEnd = $Loc->PosBeg; // Useful when mtype='m+m'
	} else {
		$NewEnd = $Loc->PosBeg + strlen($CurrVal);
	}

	$Txt = substr_replace($Txt,$CurrVal,$Loc->PosBeg,$Loc->PosEnd-$Loc->PosBeg+1);

	if ($Select) tbs_Html_MergeItems($Txt,$Loc,$CurrVal,$SelArray,$NewEnd);

	return $NewEnd; // Returns the new end position of the field

}

function meth_Locator_FindBlockNext(&$Txt,$BlockName,$PosBeg,$ChrSub,$Special,&$P1,&$FieldBefore) {
// Return the first block locator object just after the PosBeg position
// If $Special==true => don't set ->BlockSrc and accept TBS Fields (used for automatic blocks)

	$SearchDef = true;
	$FirstField = false;

	// Search for the first tag with parameter "block"
	while ($SearchDef and ($Loc = $this->meth_Locator_FindTbs($Txt,$BlockName,$PosBeg,$ChrSub))) {
		if (isset($Loc->PrmLst['block'])) {
			$Block = $Loc->PrmLst['block'];
			if ($P1) {
				if (isset($Loc->PrmLst['p1'])) return false;
			} else {
				if (isset($Loc->PrmLst['p1'])) $P1 = true;
			}
			$SearchDef = false;
		} elseif ($Special) {
			return $Loc;
		} elseif ($FirstField===false) {
			$FirstField = $Loc;
		}
		$PosBeg = $Loc->PosEnd;
	}

	if ($SearchDef) {
		if ($FirstField!==false) $FieldBefore = true;
		return false;
	}

	if ($Block==='begin') { // Block definied using begin/end

		if (($FirstField!==false) and ($FirstField->PosEnd<$Loc->PosBeg)) $FieldBefore = true;

		$Opened = 1;
		while ($Loc2 = $this->meth_Locator_FindTbs($Txt,$BlockName,$PosBeg,$ChrSub)) {
			if (isset($Loc2->PrmLst['block'])) {
				switch ($Loc2->PrmLst['block']) {
				case 'end':   $Opened--; break;
				case 'begin': $Opened++; break;
				}
				if ($Opened==0) {
					if ($Special) {
						$Loc->PosBeg2 = $Loc2->PosBeg;
						$Loc->PosEnd2 = $Loc2->PosEnd;
					} else {
						$Loc->BlockSrc = substr($Txt,$Loc->PosEnd+1,$Loc2->PosBeg-$Loc->PosEnd-1);
						$Loc->PosEnd = $Loc2->PosEnd;
						$Loc->PosDef = 0;
					}
					$Loc->BlockFound = true;
					return $Loc;
				}
			}
			$PosBeg = $Loc2->PosEnd;
		}

		return $this->meth_Misc_Alert('Block definition','At least one block '.$this->ChrOpen.$Loc->FullName.$this->ChrClose.' with parameter \'block=end\' is missing.');

	}

	if ($Special) {
		$Loc->PosBeg2 = false;
	} else {

		$Loc->PosDef = $Loc->PosBeg;
		if (!$Loc->SubOk) {
			$PosBeg1 = $Loc->PosBeg;
			$PosEnd1 = $Loc->PosEnd;
		}
		if (tbs_Locator_EnlargeToTag($Txt,$Loc,$Block,true,false)===false) return $this->meth_Misc_Alert('Block definition',$this->ChrOpen.$Loc->FullName.$this->ChrClose.' can not be defined because tag <'.$Loc->PrmLst['block'].'> or </'.$Loc->PrmLst['block'].'> is not found.');
		$Loc->PosDef = $Loc->PosDef - $Loc->PosBeg;
		if ($Loc->SubOk) {
			$Loc->BlockSrc = substr($Txt,$Loc->PosBeg,$Loc->PosEnd-$Loc->PosBeg+1);
			$Loc->PosDef++;
		} else {
			$Loc->BlockSrc = substr($Txt,$Loc->PosBeg,$PosBeg1-$Loc->PosBeg).substr($Txt,$PosEnd1+1,$Loc->PosEnd-$PosEnd1);		
		}
	}

	$Loc->BlockFound = true;
	if (($FirstField!==false) and ($FirstField->PosEnd<$Loc->PosBeg)) $FieldBefore = true;
	return $Loc; // methods return by ref by default

}

function meth_Locator_FindBlockLst(&$Txt,$BlockName,$Pos) {
// Return a locator object covering all block definitions, even if there is no block definition found.

	$LocR =& new clsTbsLocator;
	$LocR->P1 = false;
	$LocR->FieldOutside = false;
	$LocR->BlockNbr = 0;
	$LocR->BlockSrc = array(); // 1 to BlockNbr
	$LocR->BlockLoc = array(); // 1 to BlockNbr
	$LocR->BlockChk = array(); // 1 to BlockNbr
	$LocR->BlockName = array(); // 1 to BlockNbr
	$LocR->NoDataBid = false;
	$LocR->SpecialBid = false;
	$LocR->HeaderFound = false;
	$LocR->FooterFound = false;
	$LocR->WhenFound = false;
	$LocR->WhenDefaultBid = false;
	$LocR->SectionNbr = 0;
	$LocR->SectionBid = array();       // 1 to SectionNbr
	$LocR->SectionIsSerial = array();  // 1 to SectionNbr
	$LocR->SectionSerialBid = array(); // 1 to SectionNbr
	$LocR->SectionSerialOrd = array(); // 1 to SectionNbr
	$LocR->SerialEmpty = false;

	$Bid =& $LocR->BlockNbr;
	$Sid =& $LocR->SectionNbr;
	$Pid = 0;

	do {

		$Loc = $this->meth_Locator_FindBlockNext($Txt,$BlockName,$Pos,'.',false,$LocR->P1,$LocR->FieldOutside);

		if ($Loc===false) {

			if ($Pid>0) { // parentgrp mode => disconnect $Txt from the source
				$Src = $Txt;
				$Txt =& $Parent[$Pid]['txt'];
				if ($LocR->BlockFound) {
					// Redefine the Header block
					$LocR->BlockSrc[$Parent[$Pid]['bid']] = substr($Src,0,$LocR->PosBeg);
					// Add a Footer block
					tbs_Locator_SectionAddBlk($LocR,$BlockName,substr($Src,$LocR->PosEnd+1));
					tbs_Locator_SectionAddGrp($LocR,$Bid,'F',$Parent[$Pid]['fld']);
				}
				// Now gowing down to previous level
				$Pos = $Parent[$Pid]['pos'];
				$LocR->PosBeg = $Parent[$Pid]['beg'];
				$LocR->PosEnd = $Parent[$Pid]['end'];
				$LocR->BlockFound = true;
				unset($Parent[$Pid]);
				$Pid--;
				$Loc = true;
			}

		} else {

			$Pos = $Loc->PosEnd;
		
			// Define the block limits
			if ($LocR->BlockFound) {
				if ( $LocR->PosBeg > $Loc->PosBeg ) $LocR->PosBeg = $Loc->PosBeg;
				if ( $LocR->PosEnd < $Loc->PosEnd ) $LocR->PosEnd = $Loc->PosEnd;
			} else {
				$LocR->BlockFound = true;
				$LocR->PosBeg = $Loc->PosBeg;
				$LocR->PosEnd = $Loc->PosEnd;
			}
	
			// Merge block parameters
			if (count($Loc->PrmLst)>0) $LocR->PrmLst = array_merge($LocR->PrmLst,$Loc->PrmLst);
	
			// Save the block and cache its tags (incrments $LocR->BlockNbr)
			tbs_Locator_SectionAddBlk($LocR,$BlockName,$Loc->BlockSrc);
	
			// Add the text in the list of blocks
			if (isset($Loc->PrmLst['nodata'])) { // Nodata section
				$LocR->NoDataBid = $Bid;
			} elseif (isset($Loc->PrmLst['currpage'])) { // Special section (used for navigation bar)
				$LocR->SpecialBid = $Bid;
			} elseif (isset($Loc->PrmLst['when'])) {
				if ($LocR->WhenFound===false) {
					$LocR->WhenFound = true;
					$LocR->WhenSeveral = false;
					$LocR->WhenNbr = 0;
					$LocR->WhenSectionBid[] = array(); // Bid of the section to display
					$LocR->WhenCondBid[] = array();    // Bid of the condition to check
					$LocR->WhenBeforeNS[] = array();   // True if the When section must be displayed before a 
				}
				$LocR->WhenNbr++;
				if (isset($Loc->PrmLst['several'])) $LocR->WhenSeveral = true;
				$LocR->WhenSectionBid[$LocR->WhenNbr] = $Bid;
				$this->meth_Merge_PhpVar($Loc->PrmLst['when'],false);
				tbs_Locator_SectionAddBlk($LocR,$BlockName,$Loc->PrmLst['when']);
				$LocR->WhenCondBid[$LocR->WhenNbr] = $Bid;
				$LocR->WhenBeforeNS[$LocR->WhenNbr] = ($Sid===0);
			} elseif (isset($Loc->PrmLst['default'])) {
				$LocR->WhenDefaultBid = $Bid;
				$LocR->WhenDefaultBeforeNS = ($Sid===0);
			} elseif (isset($Loc->PrmLst['headergrp'])) {
				tbs_Locator_SectionAddGrp($LocR,$Bid,'H',$Loc->PrmLst['headergrp']);
			} elseif (isset($Loc->PrmLst['footergrp'])) {
				tbs_Locator_SectionAddGrp($LocR,$Bid,'F',$Loc->PrmLst['footergrp']);
			} elseif (isset($Loc->PrmLst['splittergrp'])) {
				tbs_Locator_SectionAddGrp($LocR,$Bid,'S',$Loc->PrmLst['splittergrp']);
			} elseif (isset($Loc->PrmLst['parentgrp'])) {
				tbs_Locator_SectionAddGrp($LocR,$Bid,'H',$Loc->PrmLst['parentgrp']);
				$Pid++;
				$Parent[$Pid]['bid'] = $Bid;
				$Parent[$Pid]['fld'] = $Loc->PrmLst['parentgrp'];
				$Parent[$Pid]['txt'] =& $Txt;
				$Parent[$Pid]['pos'] = $Pos;
				$Parent[$Pid]['beg'] = $LocR->PosBeg;
				$Parent[$Pid]['end'] = $LocR->PosEnd;
				$Txt =& $LocR->BlockSrc[$Bid];
				$Pos = $Loc->PosDef + 1;
				$LocR->BlockFound = false;
				$LocR->PosBeg = false;
				$LocR->PosEnd = false;
			} elseif (isset($Loc->PrmLst['serial'])) {
				// Section	with Serial Sub-Sections
				$Src =& $LocR->BlockSrc[$Bid];
				$Loc0 = false;
				if ($LocR->SerialEmpty===false) {
					$NameSr = $BlockName.'_0';
					$x = false;
					$LocSr = $this->meth_Locator_FindBlockNext($Src,$NameSr,0,'.',false,$x,$x);
					if ($LocSr!==false) {
						$LocR->SerialEmpty = $LocSr->BlockSrc;
						$Src = substr_replace($Src,'',$LocSr->PosBeg,$LocSr->PosEnd-$LocSr->PosBeg+1);
					}
				}
				$NameSr = $BlockName.'_1';
				$x = false;
				$LocSr = $this->meth_Locator_FindBlockNext($Src,$NameSr,0,'.',false,$x,$x);
				if ($LocSr!==false) {
					$Sid++;
					$LocR->SectionBid[$Sid] = $Bid;
					$LocR->SectionIsSerial[$Sid] = true;
					$LocR->SectionSerialBid[$Sid] = array();
					$LocR->SectionSerialOrd[$Sid] = array();
					$SrBid =& $LocR->SectionSerialBid[$Sid];
					$SrOrd =& $LocR->SectionSerialOrd[$Sid];
					$BidParent = $Bid;
					$SrNum = 1;
					do {
						// Save previous sub-section
						$LocR->BlockLoc[$BidParent][$SrNum] = $LocSr;
						tbs_Locator_SectionAddBlk($LocR,$NameSr,$LocSr->BlockSrc);
						$SrBid[$SrNum] = $Bid;
						$SrOrd[$SrNum] = $SrNum;
						$i = $SrNum;
						while (($i>1) and ($LocSr->PosBeg<$LocR->BlockLoc[$BidParent][$SrOrd[$i-1]]->PosBeg)) {
							$SrOrd[$i] = $SrOrd[$i-1];
							$SrOrd[$i-1] = $SrNum;
							$i--;
						}
						// Search next section
						$SrNum++;
						$NameSr = $BlockName.'_'.$SrNum;
						$x = false;
						$LocSr = $this->meth_Locator_FindBlockNext($Src,$NameSr,0,'.',false,$x,$x);
					} while ($LocSr!==false);
					$SrBid[0] = $SrNum-1;
				}
			} else {
				// Normal section
				$Sid++;
				$LocR->SectionBid[$Sid] = $Bid;
				$LocR->SectionIsSerial[$Sid] = false;
			}
			
		}

	} while ($Loc!==false);

	if ($LocR->WhenFound and ($Sid===0)) {
		// Add a blank section if When is used without a normal section
		tbs_Locator_SectionAddBlk($LocR,$BlockName,'');
		$Sid++;
		$LocR->SectionBid[$Sid] = $Bid;
		$LocR->SectionIsSerial[$Sid] = false;
	}

	// Calculate Cache
	if ($this->TurboBlock) {
		for ($i=1;$i<=$LocR->BlockNbr;$i++) {
			$this->meth_Locator_SectionCache($LocR,$i);
		}
	}

	return $LocR; // methods return by ref by default

}

function meth_Merge_Block(&$Txt,&$BlockName,&$SrcId,&$Query,$PageSize,$PageNum,$RecKnown) {

	// Get source type and info
	$Src =& new clsTbsDataSource;
	$Src->BlockName = $BlockName;
	if (!$Src->DataPrepare($SrcId,$this)) return 0;

	$BlockId = 0;
	$BlockLst = explode(',',$BlockName);
	$BlockNbr = count($BlockLst);
	$WasP1 = false;
	$NbrRecTot = 0;
	$QueryZ =& $Query;

	while ($BlockId<$BlockNbr) {

		$RecStop = 0; // Stop the merge after this row
		$RecSpe = 0;  // Row with a special block's definition (used for the navigation bar)
		$QueryOk = true;
		$NoFct = true;
		$Src->BlockName = trim($BlockLst[$BlockId]);

		// Search the block
		$LocR = $this->meth_Locator_FindBlockLst($Txt,$Src->BlockName,0);

		if ($LocR->BlockFound) {
			if ($LocR->SpecialBid!==false) $RecSpe = $RecKnown;
			// OnSection
			if (isset($LocR->PrmLst['onsection'])) {
				$LocR->OnSecInfo = $LocR->PrmLst['onsection'];
				$ErrMsg = false;
				if ($this->meth_Misc_UserFctCheck($LocR->OnSecInfo,$ErrMsg)) {
					$LocR->OnSecPrm = array($BlockName,'','','');
					$NoFct = false;
				} else {
					$this->meth_Misc_Alert('Block definition \''.$BlockName.'\'',$ErrMsg);
				}
			}
			// OnData
			if (isset($LocR->PrmLst['ondata'])) {
				if ($LocR->PrmLst['ondata']!==$Src->OnDataSave) {
					$Src->OnDataSave = $LocR->PrmLst['ondata'];
					$Src->OnDataInfo = $Src->OnDataSave;
					$ErrMsg = false;
					if ($this->meth_Misc_UserFctCheck($Src->OnDataInfo,$ErrMsg)) {
						$Src->OnDataPrm = array($BlockName,'','');
					} else {
						$Src->OnDataInfo = false;
						$this->meth_Misc_Alert('Block definition \''.$BlockName.'\'',$ErrMsg);
					}
				}
			}
			// Dynamic query
			if ($LocR->P1) {
				if (is_string($Query)) {
					$Src->RecSaved = false;
					unset($QueryZ); $QueryZ = ''.$Query;
					$i = 1;
					do {
						$x = 'p'.$i;
						if (isset($LocR->PrmLst[$x])) {
							$QueryZ = str_replace('%p'.$i.'%',$LocR->PrmLst[$x],$QueryZ);
							$i++;
						} else {
							$i = false;
						}
					} while ($i!==false);
				}
				$WasP1 = true;
			} elseif (($Src->RecSaved===false) and ($BlockNbr-$BlockId>1)) {
				$Src->RecSaving = true;
			}
		} else {
			if ($WasP1) {
				$QueryOk = false;
				$WasP1 = false;
			} else {
				$RecStop = 1;
			}
		}

		// Open the recordset
		if ($QueryOk) {
			if ((!$LocR->BlockFound) and (!$LocR->FieldOutside)) {
				$QueryOk = false;
			}	else {
				$QueryOk = $Src->DataOpen($QueryZ,$PageSize,$PageNum,$RecStop);
			}
		}

		// Merge sections
		if ($QueryOk) {
			if ($Src->Type===4) { // Special for Text merge
				if ($LocR->BlockFound) {
					$Src->RecNum = 1;
					$Src->CurrRec = false;
					if ($NoFct===false) {
						$LocR->OnSecPrm[1] =& $Src->CurrRec ; $LocR->OnSecPrm[2] =& $Src->RecSet; $LocR->OnSecPrm[3] =& $Src->RecNum;
						call_user_func_array($LocR->OnSecInfo,$LocR->OnSecPrm);
					}
					$Txt = substr_replace($Txt,$Src->RecSet,$LocR->PosBeg,$LocR->PosEnd-$LocR->PosBeg+1);
				} else {
					$Src->DataAlert('Can\'t merge the block with a text value because the block definition is not found.');
				}
			} else { // Other data source type
				$Src->DataFetch();
				if ($LocR->BlockFound!==false) $this->meth_Merge_BlockSections($Txt,$LocR,$Src,$NoFct,$RecSpe,$RecStop);
				// Mode Page: Calculate the value to return
				if (($PageSize>0) and ($Src->RecNum>=$RecStop)) {
					if ($RecKnown<0) { // Pass pages in order to count all records
						do {
							$Src->DataFetch();
						} while ($Src->CurrRec!==false);
					} else { // We know that there is more records
						if ($RecKnown>$Src->RecNum) $Src->RecNum = $RecKnown;
					}
				}
			}
			$Src->DataClose(); // Close the resource
		}

		if (!$WasP1) {
			$NbrRecTot += $Src->RecNum;
			if ($LocR->FieldOutside and $QueryOk) {
				// Merge last record on the entire template
				$Pos = 0;
				$ChkSub = ($Src->CurrRec!==false);
				while ($Loc = $this->meth_Locator_FindTbs($Txt,$Src->BlockName,$Pos,'.')) {
					if ($Loc->SubName==='#') {
						$Pos = $this->meth_Locator_Replace($Txt,$Loc,$Src->RecNum,false);
					} else {
						$Pos = $this->meth_Locator_Replace($Txt,$Loc,$Src->CurrRec,$ChkSub);
					}
				}
			}
			$BlockId++;
		}

	} // -> while ($BlockId<$BlockNbr) {...

	// End of the merge
	unset($Src); unset($LocR); return $NbrRecTot;

}

function meth_Merge_BlockSections(&$Txt,&$LocR,&$Src,&$NoFct,&$RecSpe,&$RecStop) {

	// Initialise
	$SecId = 0;
	$SecOk = ($LocR->SectionNbr>0);
	$SecIncr = true;
	$BlockRes = ''; // The result of the chained merged blocks
	$SerialMode = false;
	$SerialNum = 0;
	$SerialMax = 0;
	$SerialTxt = array();
	$GrpFound = ($LocR->HeaderFound or $LocR->FooterFound);

	// Main loop
	//$Src->DataFetch();
	while($Src->CurrRec!==false) {

		// Headers and Footers
		if ($GrpFound) {
			$grp_change = false;
			$grp_src = '';
			if ($LocR->FooterFound) {
				$change = false;
				for ($i=$LocR->FooterNbr;$i>=1;$i--) {
					$x = $Src->CurrRec[$LocR->FooterField[$i]];
					if ($Src->RecNum===1) {
						$LocR->FooterPrevValue[$i] = $x;
					} else {
						if ($LocR->FooterIsFooter[$i]) {
							$change_i =& $change;
						} else {
							unset($change_i); $change_i = false;
						}
						if (!$change_i) $change_i = !($LocR->FooterPrevValue[$i]===$x);
						if ($change_i) {
							$grp_change = true;
							$grp_src = $this->meth_Merge_SectionNormal($LocR,$LocR->FooterBid[$i],$PrevRec,$PrevNum,$PrevKey,$NoFct).$grp_src;
							$LocR->FooterPrevValue[$i] = $x;
						}
					}
				}
				$PrevRec = $Src->CurrRec;
				$PrevNum = $Src->RecNum;
				$PrevKey = $Src->RecKey;
			}
			if ($LocR->HeaderFound) {
				$change = ($Src->RecNum===1);
				for ($i=1;$i<=$LocR->HeaderNbr;$i++) {
					$x = $Src->CurrRec[$LocR->HeaderField[$i]];
					if (!$change) $change = !($LocR->HeaderPrevValue[$i]===$x);
					if ($change) {
						$grp_src .= $this->meth_Merge_SectionNormal($LocR,$LocR->HeaderBid[$i],$Src->CurrRec,$Src->RecNum,$Src->RecKey,$NoFct);
						$LocR->HeaderPrevValue[$i] = $x;
					}
				}
				$grp_change = ($grp_change or $change);
			}
			if ($grp_change) {
				if ($SerialMode) {
					$BlockRes .= $this->meth_Merge_SectionSerial($LocR,$SecId,$SerialNum,$SerialMax,$SerialTxt);
					$SecIncr = true;
				}
				$BlockRes .= $grp_src;
			}
		} // end of header and footer

		// Increment Section
		if ($SecIncr and $SecOk) {
			$SecId++;
			if ($SecId>$LocR->SectionNbr) $SecId = 1;
			$SerialMode = $LocR->SectionIsSerial[$SecId];
			if ($SerialMode) {
				$SerialNum = 0;
				$SerialMax = $LocR->SectionSerialBid[$SecId][0];
				$SecIncr = false;
			}
		}

		// Serial Mode Activation
		if ($SerialMode) { // Serial Merge
			$SerialNum++;
			$Bid = $LocR->SectionSerialBid[$SecId][$SerialNum];
			$SerialTxt[$SerialNum] = $this->meth_Merge_SectionNormal($LocR,$Bid,$Src->CurrRec,$Src->RecNum,$Src->RecKey,$NoFct);
			if ($SerialNum>=$SerialMax) {
				$BlockRes .= $this->meth_Merge_SectionSerial($LocR,$SecId,$SerialNum,$SerialMax,$SerialTxt);
				$SecIncr = true;
			}
		} else { // Classic merge
			if ($Src->RecNum===$RecSpe) {
				$Bid = $LocR->SpecialBid;
			} else {
				$Bid = $LocR->SectionBid[$SecId];
			}
			if ($LocR->WhenFound) { // With conditional blocks
				$x = $this->meth_Merge_SectionNormal($LocR,$Bid,$Src->CurrRec,$Src->RecNum,$Src->RecKey,$NoFct);
				$found = false;
				$continue = true;
				$i = 1;
				do {
					$cond = $this->meth_Merge_SectionNormal($LocR,$LocR->WhenCondBid[$i],$Src->CurrRec,$Src->RecNum,$Src->RecKey,$NoFct);
					if (tbs_Misc_CheckCondition($cond)) {
						$x_when = $this->meth_Merge_SectionNormal($LocR,$LocR->WhenSectionBid[$i],$Src->CurrRec,$Src->RecNum,$Src->RecKey,$NoFct);
						if ($LocR->WhenBeforeNS[$i]) {$x = $x_when.$x;} else {$x = $x.$x_when;}
						$found = true;
						if ($LocR->WhenSeveral===false) $continue = false;
					}
					$i++;
					if ($i>$LocR->WhenNbr) $continue = false;
				} while ($continue);
				if (($found===false) and ($LocR->WhenDefaultBid!==false)) {
					$x_when = $this->meth_Merge_SectionNormal($LocR,$LocR->WhenDefaultBid,$Src->CurrRec,$Src->RecNum,$Src->RecKey,$NoFct);
					if ($LocR->WhenDefaultBeforeNS) {$x = $x_when.$x;} else {$x = $x.$x_when;}
				}
				$BlockRes .= $x;
			} else { // Without conditional blocks
				$BlockRes .= $this->meth_Merge_SectionNormal($LocR,$Bid,$Src->CurrRec,$Src->RecNum,$Src->RecKey,$NoFct);
			}
		}

		// Next row
		if ($Src->RecNum===$RecStop) {
			$Src->CurrRec = false;
		} else {
			// $CurrRec can be set to False by the OnSection event function.
			if ($Src->CurrRec!==false) $Src->DataFetch();
		}

	} //--> while($CurrRec!==false) {

	// Serial: merge the extra the sub-blocks
	if ($SerialMode and !$SecIncr) {
		$BlockRes .= $this->meth_Merge_SectionSerial($LocR,$SecId,$SerialNum,$SerialMax,$SerialTxt);
	}

	// Footer
	if ($LocR->FooterFound) {
		if ($Src->RecNum>0) {
			for ($i=1;$i<=$LocR->FooterNbr;$i++) {
				if ($LocR->FooterIsFooter[$i]) $BlockRes .= $this->meth_Merge_SectionNormal($LocR,$LocR->FooterBid[$i],$PrevRec,$PrevNum,$PrevKey,$NoFct);
			}
		}
	}

	// NoData
	if (($Src->RecNum===0) and ($LocR->NoDataBid!==false)) $BlockRes = $LocR->BlockSrc[$LocR->NoDataBid];
	
	// Merge the result
	$Txt = substr_replace($Txt,$BlockRes,$LocR->PosBeg,$LocR->PosEnd-$LocR->PosBeg+1);

}

function meth_Merge_PhpVar(&$Txt,$HtmlConv) {
// Merge the PHP global variables of the main script.

	$Pref =& $this->VarPrefix;
	$PrefL = strlen($Pref);
	$PrefOk = ($PrefL>0);

	if ($HtmlConv===false) {
		$HtmlCharSet = $this->HtmlCharSet;
		$this->HtmlCharSet = false;
	}

	// Then we scann all fields in the model
	$x = '';
	$Pos = 0;
	while ($Loc = $this->meth_Locator_FindTbs($Txt,'var',$Pos,'.')) {
		if ($Loc->SubNbr>0) {
			if ($Loc->SubLst[0]==='') {
				$Pos = $this->meth_Merge_System($Txt,$Loc);
			} elseif ($Loc->SubLst[0][0]==='~') {
				if (!isset($ObjOk)) $ObjOk = (is_object($this->ObjectRef) or is_array($this->ObjectRef));
				if ($ObjOk) {
					$Loc->SubLst[0] = substr($Loc->SubLst[0],1);
					$Pos = $this->meth_Locator_Replace($Txt,$Loc,$this->ObjectRef,true);
				} elseif (isset($Loc->PrmLst['noerr'])) {
					$Pos = $this->meth_Locator_Replace($Txt,$Loc,$x,false);
				} else {
					$this->meth_Misc_Alert('Merge ObjectRef sub item','Can\'t merge '.$this->ChrOpen.$Loc->FullName.$this->ChrClose.' because property ObjectRef is neither an object nor an array. Its type is \''.gettype($this->ObjectRef).'\'.',true);
					$Pos = $Loc->PosEnd + 1;
				}
			} elseif ($PrefOk and (substr($Loc->SubLst[0],0,$PrefL)!==$Pref)) {
				if (isset($Loc->PrmLst['noerr'])) {
					$Pos = $this->meth_Locator_Replace($Txt,$Loc,$x,false);
				} else {
					$this->meth_Misc_Alert('Merge PHP global variables','Can\'t merge '.$this->ChrOpen.$Loc->FullName.$this->ChrClose.' because allowed prefix is set to \''.$Pref.'\'.',true);
					$Pos = $Loc->PosEnd + 1;
				}
			} elseif (isset($GLOBALS[$Loc->SubLst[0]])) {
				$Pos = $this->meth_Locator_Replace($Txt,$Loc,$GLOBALS,true);
			} else {
				if (isset($Loc->PrmLst['noerr'])) {
					$Pos = $this->meth_Locator_Replace($Txt,$Loc,$x,false);
				} else {
					$Pos = $Loc->PosEnd + 1;
					$this->meth_Misc_Alert('Merge PHP global variables','Can\'t merge '.$this->ChrOpen.$Loc->FullName.$this->ChrClose.' because there is no PHP global variable named \''.$Loc->SubLst[0].'\'.',true);
				}
			}
		}
	}

	if ($HtmlConv===false) $this->HtmlCharSet = $HtmlCharSet;

}

function meth_Merge_System(&$Txt,&$Loc) {
// This function enables to merge TBS special fields

	if (isset($Loc->SubLst[1])) {
		switch ($Loc->SubLst[1]) {
		case 'now':
			$x = mktime();
			return $this->meth_Locator_Replace($Txt,$Loc,$x,false);
		case 'version':
			$x = '2.05.8';
			return $this->meth_Locator_Replace($Txt,$Loc,$x,false);
		case 'script_name':
			if (isset($_SERVER)) { // PHP<4.1.0 compatibilty
				$x = tbs_Misc_GetFilePart($_SERVER['PHP_SELF'],1);
			} else {
				global $HTTP_SERVER_VARS;
				$x = tbs_Misc_GetFilePart($HTTP_SERVER_VARS['PHP_SELF'],1);
			}
			return $this->meth_Locator_Replace($Txt,$Loc,$x,false);
		case 'template_name':
			return $this->meth_Locator_Replace($Txt,$Loc,$this->_LastFile,false);
		case 'template_date':
			$x = filemtime($this->_LastFile);
			return $this->meth_Locator_Replace($Txt,$Loc,$x,false);
		case 'template_path':
			$x = tbs_Misc_GetFilePart($this->_LastFile,0);
			return $this->meth_Locator_Replace($Txt,$Loc,$x,false);
		case 'name':
			$x = 'TinyButStrong';
			return $this->meth_Locator_Replace($Txt,$Loc,$x,false);
		case 'logo':
			$x = '**TinyButStrong**';
			return $this->meth_Locator_Replace($Txt,$Loc,$x,false);
		case 'charset':
			return $this->meth_Locator_Replace($Txt,$Loc,$this->HtmlCharSet,false);
		case 'tplvars':
			if ($Loc->SubNbr==2) {
				$x = implode(',',array_keys($this->TplVars));
				return $this->meth_Locator_Replace($Txt,$Loc,$x,false);
			} else {
				if (isset($this->TplVars[$Loc->SubLst[2]])) {
					array_shift($Loc->SubLst);
					array_shift($Loc->SubLst);
					$Loc->SubNbr = $Loc->SubNbr - 2;
					return $this->meth_Locator_Replace($Txt,$Loc,$this->TplVars,true);
				} else {
					$this->meth_Misc_Alert('System Fields','Can\'t merge ['.$Loc->FullName.'] because property TplVars doesn\'t have any item named \''.$Loc->SubLst[2].'\'.');
					return $Loc->PosBeg+1;
				}
			}
		case '':
			$this->meth_Misc_Alert('System Fields','Can\'t merge ['.$Loc->FullName.'] because it doesn\'t have any requested keyword.');
			return $Loc->PosBeg+1;
		default:
			$this->meth_Misc_Alert('System Fields','Can\'t merge ['.$Loc->FullName.'] because \''.$Loc->SubLst[1].'\' is an unknown keyword.');
			return $Loc->PosBeg+1;
		}
	} else {
		$this->meth_Misc_Alert('System Fields','Can\'t merge ['.$Loc->FullName.'] because it doesn\'t have any subname.');
		return $Loc->PosBeg+1;
	}

}

function meth_Merge_Special($Type) {
// Proceed to one of the special merge

	if ($Type==='*') $Type = 'onload,onshow,var';

	$TypeLst = explode(',',$Type);
	foreach ($TypeLst as $Type) {
		switch ($Type) {
		case 'var':	$this->meth_Merge_PhpVar($this->Source,true); break;
		case 'onload': $this->meth_Merge_Auto($this->Source,'onload',true,true); break;
		case 'onshow': $this->meth_Merge_Auto($this->Source,'onshow',false,true); break;
		}
	}

}

function meth_Merge_SectionNormal(&$LocR,&$BlockId,&$CurrRec,&$RecNum,&$RecKey,&$NoFct) {

	$Txt = $LocR->BlockSrc[$BlockId];

	if ($NoFct) {
		$LocLst =& $LocR->BlockLoc[$BlockId];
		$iMax = $LocLst[0];
		$PosMax = strlen($Txt);
		$DoUnCached =& $LocR->BlockChk[$BlockId];
	} else {
		$Txt0 = $Txt;
		$LocR->OnSecPrm[1] =& $CurrRec ; $LocR->OnSecPrm[2] =& $Txt; $LocR->OnSecPrm[3] =& $RecNum;
		call_user_func_array($LocR->OnSecInfo,$LocR->OnSecPrm);
		if ($Txt0===$Txt) {
			$LocLst =& $LocR->BlockLoc[$BlockId];
			$iMax = $LocLst[0];
			$PosMax = strlen($Txt);
			$DoUnCached =& $LocR->BlockChk[$BlockId];
		} else {
			$iMax = 0;
			$DoUnCached = true;
		}
	}

	if ($RecNum===false) { // Erase all fields

		$x = '';

		// Chached locators
		for ($i=$iMax;$i>0;$i--) {
			if ($LocLst[$i]->PosBeg<$PosMax) {
				$this->meth_Locator_Replace($Txt,$LocLst[$i],$x,false);
				if ($LocLst[$i]->Enlarged) {
					$PosMax = $LocLst[$i]->PosBeg;
					$LocLst[$i]->PosBeg = $LocLst[$i]->PosBeg0;
					$LocLst[$i]->PosEnd = $LocLst[$i]->PosEnd0;
					$LocLst[$i]->Enlarged = false;
				}
			}
		}

		// Unchached locators
		if ($DoUnCached) {
			$BlockName =& $LocR->BlockName[$BlockId];
			$Pos = 0;
			while ($Loc = $this->meth_Locator_FindTbs($Txt,$BlockName,$Pos,'.')) $Pos = $this->meth_Locator_Replace($Txt,$Loc,$x,false);
		}		

	} else {

		// Chached locators
		for ($i=$iMax;$i>0;$i--) {
			if ($LocLst[$i]->PosBeg<$PosMax) {
				if ($LocLst[$i]->IsRecInfo) {
					if ($LocLst[$i]->RecInfo==='#') {
						$this->meth_Locator_Replace($Txt,$LocLst[$i],$RecNum,false);
					} else {
						$this->meth_Locator_Replace($Txt,$LocLst[$i],$RecKey,false);
					}
				} else {
					$this->meth_Locator_Replace($Txt,$LocLst[$i],$CurrRec,true);
				}
				if ($LocLst[$i]->Enlarged) {
					$PosMax = $LocLst[$i]->PosBeg;
					$LocLst[$i]->PosBeg = $LocLst[$i]->PosBeg0;
					$LocLst[$i]->PosEnd = $LocLst[$i]->PosEnd0;
					$LocLst[$i]->Enlarged = false;
				}
			}
		}

		// Unchached locators
		if ($DoUnCached) {
			$BlockName =& $LocR->BlockName[$BlockId];
			foreach ($CurrRec as $key => $val) {
				$Pos = 0;
				$Name = $BlockName.'.'.$key;
				while ($Loc = $this->meth_Locator_FindTbs($Txt,$Name,$Pos,'.')) {
					$Pos = $this->meth_Locator_Replace($Txt,$Loc,$val,true);
				}
			}
			$Pos = 0;
			$Name = $BlockName.'.#';
			while ($Loc = $this->meth_Locator_FindTbs($Txt,$Name,$Pos,'.')) $Pos = $this->meth_Locator_Replace($Txt,$Loc,$RecNum,true);
			$Pos = 0;
			$Name = $BlockName.'.$';
			while ($Loc = $this->meth_Locator_FindTbs($Txt,$Name,$Pos,'.')) $Pos = $this->meth_Locator_Replace($Txt,$Loc,$RecKey,true);
		}

	}

	return $Txt;

}

function meth_Merge_SectionSerial(&$LocR,&$SecId,&$SerialNum,&$SerialMax,&$SerialTxt) {

	$Txt = $LocR->BlockSrc[$LocR->SectionBid[$SecId]];
	$LocLst =& $LocR->BlockLoc[$LocR->SectionBid[$SecId]];
	$OrdLst =& $LocR->SectionSerialOrd[$SecId];

	// Prepare the Empty Item
	if ($SerialNum<$SerialMax) {
		if ($LocR->SerialEmpty===false) {
			$F = false;
			$NoFct = true;
		} else {
			$EmptySrc =& $LocR->SerialEmpty;
		}
	}

	// All Items
	for ($i=$SerialMax;$i>0;$i--) {
		$Sr = $OrdLst[$i];
		if ($Sr>$SerialNum) {
			if ($LocR->SerialEmpty===false) {
				$k = $LocR->SectionSerialBid[$SecId][$Sr];
				$EmptySrc = $this->meth_Merge_SectionNormal($LocR,$k,$F,$F,$F,$NoFct);
			}
			$Txt = substr_replace($Txt,$EmptySrc,$LocLst[$Sr]->PosBeg,$LocLst[$Sr]->PosEnd-$LocLst[$Sr]->PosBeg+1);
		} else {
			$Txt = substr_replace($Txt,$SerialTxt[$Sr],$LocLst[$Sr]->PosBeg,$LocLst[$Sr]->PosEnd-$LocLst[$Sr]->PosBeg+1);
		}
	}

	// Update variables
	$SerialNum = 0;
	$SerialTxt = array();

	return $Txt;

}

function meth_Merge_Auto(&$Txt,$Name,$TplVar,$AcceptGrp) {
// onload - onshow

	$GrpDisplayed = array();
	$GrpExclusive = array();
	$P1 = false;
	$FieldBefore = false;
	$Pos = 0;

	if ($AcceptGrp) {
		$ChrSub = '_';
	} else {
		$ChrSub = '';
	}

	while ($LocA=$this->meth_Locator_FindBlockNext($Txt,$Name,$Pos,$ChrSub,true,$P1,$FieldBefore)) {

		if ($LocA->BlockFound) {

			if (!isset($GrpDisplayed[$LocA->SubName])) {
				$GrpDisplayed[$LocA->SubName] = false;
				$GrpExclusive[$LocA->SubName] = !($AcceptGrp and ($LocA->SubName===''));
			}
			$Displayed =& $GrpDisplayed[$LocA->SubName];
			$Exclusive =& $GrpExclusive[$LocA->SubName];

			$DelBlock = false;
			$DelField = false;
			if ($Displayed and $Exclusive) {
				$DelBlock = true;
			} else {
				if (isset($LocA->PrmLst['when'])) {
					if (isset($LocA->PrmLst['several'])) $Exclusive=false;
					$x = $LocA->PrmLst['when'];
					$this->meth_Merge_PhpVar($x,false);
					if (tbs_Misc_CheckCondition($x)) {
						$DelField = true;
						$Displayed = true;
					} else {
						$DelBlock = true;
					}
				} elseif(isset($LocA->PrmLst['default'])) {
					if ($Displayed) {
						$DelBlock = true;
					} else {
						$Displayed = true;
						$DelField = true;
					}
					$Exclusive = true; // No more block displayed for the group after VisElse
				}
			}
							
			// Del parts
			if ($DelField) {
				if ($LocA->PosBeg2!==false) $Txt = substr_replace($Txt,'',$LocA->PosBeg2,$LocA->PosEnd2-$LocA->PosBeg2+1);
				$Txt = substr_replace($Txt,'',$LocA->PosBeg,$LocA->PosEnd-$LocA->PosBeg+1);
				$Pos = $LocA->PosBeg;
			} else {
				if ($LocA->PosBeg2===false) {
					tbs_Locator_EnlargeToTag($Txt,$LocA,$LocA->PrmLst['block'],true,false);
				} else {
					$LocA->PosEnd = $LocA->PosEnd2;
				}
				if ($DelBlock) {
					$Txt = substr_replace($Txt,'',$LocA->PosBeg,$LocA->PosEnd-$LocA->PosBeg+1);
				} else {
					// Merge the block as if it was a field
					$x = '';
					$this->meth_Locator_Replace($Txt,$LocA,$x,false);
				}
				$Pos = $LocA->PosBeg;
			}

		} else { // Field

			// Check for Template Var
			if ($TplVar and	isset($LocA->PrmLst['tplvars'])) {
				$Ok = false;
				foreach ($LocA->PrmLst as $Key => $Val) {
					if ($Ok) {
						$this->TplVars[$Key] = $Val;
					} else {
						if ($Key==='tplvars') $Ok = true;
					}
				}
			}

			$x = '';
			$Pos = $this->meth_Locator_Replace($Txt,$LocA,$x,false);
			$Pos = $LocA->PosBeg;

		}

	}

	return count($GrpDisplayed);

}

function meth_Merge_NavigationBar(&$Txt,$BlockName,$Options,$PageCurr,$RecCnt,$PageSize) {

	// Get block parameters
	$PosBeg = 0;
	$PrmLst = array();
	while ($Loc = $this->meth_Locator_FindTbs($Txt,$BlockName,$PosBeg,'.')) {
		if (isset($Loc->PrmLst['block'])) $PrmLst = array_merge($PrmLst,$Loc->PrmLst);
		$PosBeg = $Loc->PosEnd;
	}

	// Prepare options
	if (!is_array($Options)) $Options = array('navsize'=>intval($Options));
	$Options = array_merge($Options,$PrmLst);

	// Default options
	if (!isset($Options['navsize'])) $Options['navsize'] = 10;
	if (!isset($Options['navpos'])) $Options['navpos'] = 'step';
	if (!isset($Options['navdel'])) $Options['navdel'] = '';
	if (!isset($Options['pagemin'])) $Options['pagemin'] = 1;

	// Check options
	if ($Options['navsize']<=0) $Options['navsize'] = 10;
	if ($PageSize<=0) $PageSize = 1;
	if ($PageCurr<$Options['pagemin']) $PageCurr = $Options['pagemin'];

	$CurrPos = 0;
	$CurrNav = array('curr'=>$PageCurr,'first'=>$Options['pagemin'],'last'=>-1,'bound'=>false);

	// Calculate displayed PageMin and PageMax
	if ($Options['navpos']=='centred') {
		$PageMin = $Options['pagemin']-1+$PageCurr - intval(floor($Options['navsize']/2));
	} else {
		// Display by block
		$PageMin = $Options['pagemin']-1+$PageCurr - ( ($PageCurr-1) % $Options['navsize']);
	}
	$PageMin = max($PageMin,$Options['pagemin']);
	$PageMax = $PageMin + $Options['navsize'] - 1;

	// Calculate previous and next pages
	$CurrNav['prev'] = $PageCurr - 1;
	if ($CurrNav['prev']<$Options['pagemin']) {
		$CurrNav['prev'] = $Options['pagemin'];
		$CurrNav['bound'] = $Options['pagemin'];
	}
	$CurrNav['next'] = $PageCurr + 1;
	if ($RecCnt>=0) {
		$PageCnt = $Options['pagemin']-1 + intval(ceil($RecCnt/$PageSize));
		$PageMax = min($PageMax,$PageCnt);
		$PageMin = max($Options['pagemin'],$PageMax-$Options['navsize']+1);
	} else {
		$PageCnt = $Options['pagemin']-1;
	}
	if ($PageCnt>=$Options['pagemin']) {
		if ($PageCurr>=$PageCnt) {
			$CurrNav['next'] = $PageCnt;
			$CurrNav['last'] = $PageCnt;
			$CurrNav['bound'] = $PageCnt;
		} else {
			$CurrNav['last'] = $PageCnt;
		}
	}	

	// Display or hide the bar
	if ($Options['navdel']=='') {
		$Display = true;
	} else {
		$Display = (($PageMax-$PageMin)>0);
	}

	// Merge general information
	$Pos = 0;
	while ($Loc = $this->meth_Locator_FindTbs($Txt,$BlockName,$Pos,'.')) {
		$Pos = $Loc->PosBeg + 1;
		$x = strtolower($Loc->SubName);
		if (isset($CurrNav[$x])) {
			$Val = $CurrNav[$x];
			if ($CurrNav[$x]==$CurrNav['bound']) {
				if (isset($Loc->PrmLst['endpoint'])) {
					$Val = '';
				}
			}
			$this->meth_Locator_Replace($Txt,$Loc,$Val,false);
		}
	}

	// Merge pages
	$Query = '';
	if ($Display) {
		$Data = array();
		$RecSpe = 0;
		$RecCurr = 0;
		for ($PageId=$PageMin;$PageId<=$PageMax;$PageId++) {
			$RecCurr++;
			if ($PageId==$PageCurr) $RecSpe = $RecCurr;
			$Data[] = array('page'=>$PageId);
		}
		$this->meth_Merge_Block($Txt,$BlockName,$Data,$Query,0,0,$RecSpe);
		if ($Options['navdel']!='') { // Delete the block definition tags
			$PosBeg = 0;
			while ($Loc = $this->meth_Locator_FindTbs($Txt,$Options['navdel'],$PosBeg,'.')) {
				$PosBeg = $Loc->PosBeg;
				$Txt = substr_replace($Txt,'',$Loc->PosBeg,$Loc->PosEnd-$Loc->PosBeg+1);
			}
		}
	} else {
		if ($Options['navdel']!='') {
			$SrcType = 'text';
			$this->meth_Merge_Block($Txt,$Options['navdel'],$SrcType,$Query,0,0,0);
		}
	}

}

// Convert a string to Html with several options
function meth_Conv_Html(&$Txt) {
	if ($this->HtmlCharSet==='') {
		$Txt = htmlspecialchars($Txt); // Faster
	} elseif ($this->_HtmlCharFct) {
		$Txt = call_user_func($this->HtmlCharSet,$Txt);
	} else {
		$Txt = htmlspecialchars($Txt,ENT_COMPAT,$this->HtmlCharSet);
	}
}

// Standard alert message provided by TinyButStrong, return False is the message is cancelled.
function meth_Misc_Alert($Source,$Message,$NoErrMsg=false) {
	$x = '<br /><b>TinyButStrong Error</b> ('.$Source.'): '.htmlentities($Message);
	if ($NoErrMsg) $x = $x.' <em>This message can be cancelled using parameter \'noerr\'.</em>';
	$x = $x."<br />\n";
	$x = str_replace($this->ChrOpen,$this->ChrProtect,$x);
	echo $x;
	return false;
}

function meth_Misc_ChangeMode($Init,&$Loc,&$CurrVal,$Switch,$GetOb) {
	if ($Init) {
		// Save contents configuration
		if ($Switch) {
			$Loc->SaveSrc =& $this->Source;
			$Loc->SaveRender = $this->Render;
			$Loc->SaveCache = $this->_CacheFile;
			$Loc->SaveMode = $this->_Mode;
			unset($this->Source); $this->Source = '';
			$this->Render = TBS_OUTPUT;
			$this->_CacheFile = false;
			$this->_Mode = 1;
			$File = $Loc->PrmLst['subtpl'];
			if (is_string($File) and (strlen($File)>0)) {
				$this->meth_Merge_PhpVar($File,false);
				$File = str_replace($this->ChrVal,$CurrVal,$File);
				if (tbs_Misc_GetFile($this->Source,$File)) {
					$this->meth_Merge_Auto($this->Source,'onload',true,true);
				} else {
					if (!isset($Loc->PrmLst['noerr'])) $this->meth_Misc_Alert('Parameter subtpl','Unable to read the file \''.$File.'\'.');
				}
			}
		}
		if ($GetOb) ob_start();
	} else {
		// Restore contents configuration
		if ($Switch) {
			$this->Source =& $Loc->SaveSrc;
			$this->Render = $Loc->SaveRender;
			$this->_CacheFile = $Loc->SaveCache;
			$this->_Mode = $Loc->SaveMode;
		}
		if ($GetOb) {
			$CurrVal = ob_get_contents();
			ob_end_clean();
		}
		$Loc->ConvHtml = false;
		$Loc->ConvProtect = false;
	}
}

function meth_Misc_UserFctCheck(&$FctInfo,&$ErrMsg) {
	if (substr($FctInfo,0,1)==='~') {
		$ObjRef =& $this->ObjectRef;
		$Lst = explode('.',substr($FctInfo,1));
		$iMax = count($Lst) - 1;
		for ($i=0;$i<=$iMax;$i++) {
			$x =& $Lst[$i];
			if (is_object($ObjRef)) {
				if (method_exists($ObjRef,$x)) {
					if ($i===$iMax) {
						$FctInfo = array(&$ObjRef,$x);
					} else {
						$ObjRef = call_user_func(array(&$ObjRef,$x));
					}
				} elseif ($i===$iMax) {
					$ErrMsg = 'Expression \''.$FctInfo.'\' is invalid because \''.$x.'\' is not a method in the class \''.get_class($ObjRef).'\'.';
					return false;
				} elseif (isset($ObjRef->$x)) {
					$ObjRef =& $ObjRef->$x;
				} else {
					$ErrMsg = 'Expression \''.$FctInfo.'\' is invalid because sub-item \''.$x.'\' is neither a method nor a property in the class \''.get_class($ObjRef).'\'.';
					return false;
				}
			} elseif (($i<$iMax) and is_array($ObjRef)) {
				if (isset($ObjRef[$x])) {
					$ObjRef =& $ObjRef[$x];
				} else {
					$ErrMsg = 'Expression \''.$FctInfo.'\' is invalid because sub-item \''.$x.'\' is not a existing key in the array.';
					return false;
				}
			} else {
				$ErrMsg = 'Expression \''.$FctInfo.'\' is invalid because '.(($i===0)?'property ObjectRef':'sub-item \''.$x.'\'').' is not an object'.(($i<$iMax)?' or an array.':'.');
				return false;
			}
		}
	} else {
		if (!function_exists($FctInfo)) {
			$ErrMsg = 'Custom function \''.$FctInfo.'\' is not found.';
			return false;
		}
	}
	return true;
}

function meth_Cache_Save($CacheFile,&$Txt) {
	$fid = @fopen($CacheFile, 'w');
	if ($fid===false) {
		$this->meth_Misc_Alert('Cache System','The cache file \''.$CacheFile.'\' can not be saved.');
		return false;
	} else {
		flock($fid,2); // acquire an exlusive lock
		fwrite($fid,$Txt);
		flock($fid,3); // release the lock
		fclose($fid);
		return true;
	}
}

} // class clsTinyButStrong

// *********************************************

function tbs_Misc_ConvSpe(&$Loc) {
	if ($Loc->ConvSpe===false) {
		$Loc->ConvSpe = true;
		$Loc->ConvEsc = false;
		$Loc->ConvWS = false;
		$Loc->ConvJS = false;
		$Loc->ConvLook = false;
	}
}

function tbs_Misc_GetStrId($Txt) {
	$Txt = strtolower($Txt);
	$Txt = str_replace('-','_',$Txt);
	$x = '';
	$i = 0;
	$iMax = strlen($Txt2);
	while ($i<$iMax) {
		if (($Txt[$i]==='_') or (($Txt[$i]>='a') and ($Txt[$i]<='z')) or (($Txt[$i]>='0') and ($Txt[$i]<='9'))) {
			$x .= $Txt[$i];
			$i++;
		} else {
			$i = $iMax;
		}
	}
	return $x;
}

function tbs_Misc_CheckCondition($Str) {
// Check if an expression like "exrp1=expr2" is true or false.

	// Find operator and position
	$Ope = '=';
	$Len = 1;
	$Max = strlen($Str)-1;
	$Pos = strpos($Str,$Ope);
	if ($Pos===false) {
		$Ope = '+';
		$Pos = strpos($Str,$Ope);
		if ($Pos===false) return false;
		if (($Pos>0) and ($Str[$Pos-1]==='-')) {
			$Ope = '-+'; $Pos--; $Len=2;
		} elseif (($Pos<$Max) and ($Str[$Pos+1]==='-')) {
			$Ope = '+-'; $Len=2;
		} else {
			return false;
		}
	} else {
		if ($Pos>0) {
			$x = $Str[$Pos-1];
			if ($x==='!') {
				$Ope = '!='; $Pos--; $Len=2;
			} elseif ($Pos<$Max) {
				$y = $Str[$Pos+1];
				if ($y==='=') {
					$Len=2;
				} elseif (($x==='+') and ($y==='-')) {
					$Ope = '+=-'; $Pos--; $Len=3;
				} elseif (($x==='-') and ($y==='+')) {
					$Ope = '-=+'; $Pos--; $Len=3;
				}
			} else {
			}
		}
	}

	// Read values
	$Val1  = trim(substr($Str,0,$Pos));
	$Nude1 = tbs_Misc_DelDelimiter($Val1,'\'');
	$Val2  = trim(substr($Str,$Pos+$Len));
	$Nude2 = tbs_Misc_DelDelimiter($Val2,'\'');

	// Compare values
	if ($Ope==='=') {
		return (strcasecmp($Val1,$Val2)==0);
	} elseif ($Ope==='!=') {
		return (strcasecmp($Val1,$Val2)!=0);
	} else {
		if ($Nude1) $Val1 = (float) $Val1;
		if ($Nude2) $Val2 = (float) $Val2;
		if ($Ope==='+-') {
			return ($Val1>$Val2);
		} elseif ($Ope==='-+') {
			return ($Val1 < $Val2);
		} elseif ($Ope==='+=-') {
			return ($Val1 >= $Val2);
		} elseif ($Ope==='-=+') {
			return ($Val1<=$Val2);
		} else {
			return false;
		}
	}

}

function tbs_Misc_DelDelimiter(&$Txt,$Delim) {
// Delete the string delimiters
	$len = strlen($Txt);
	if (($len>1) and ($Txt[0]===$Delim)) {
		if ($Txt[$len-1]===$Delim) $Txt = substr($Txt,1,$len-2);
		return false;
	} else {
		return true;
	}
}

function tbs_Misc_GetFile(&$Txt,$File) {
// Load the content of a file into the text variable.
	$Txt = '';
	$fd = @fopen($File, 'r'); // 'rb' if binary for some OS
	if ($fd===false) return false;
	$fs = @filesize($File); // return False for an URL
	if ($fs===false) {
		while (!feof($fd)) $Txt .= fread($fd,4096);
	} else {
		if ($fs>0) $Txt = fread($fd,$fs);
	}	
	fclose($fd);
	return true;
}

function tbs_Misc_GetFilePart($File,$Part) {
	$Pos = strrpos($File,'/');
	if ($Part===0) { // Path
		if ($Pos===false) {
			return '';
		} else {
			return substr($File,0,$Pos+1);
		}
	} else { // File
		if ($Pos===false) {
			return $File;
		} else {
			return substr($File,$Pos+1);
		}
	}
}

function tbs_Misc_Format(&$Loc,&$Value) {
// This function return the formated representation of a Date/Time or numeric variable using a 'VB like' format syntax instead of the PHP syntax.

	global $_tbs_FrmSimpleLst;

	$FrmStr = $Loc->PrmLst['frm'];
	$CheckNumeric = true;
	if (is_string($Value)) $Value = trim($Value);

	// Manage Multi format strings
	if (strpos($FrmStr,'|')!==false) {

		global $_tbs_FrmMultiLst;

		// Save the format if it doesn't exist
		if (isset($_tbs_FrmMultiLst[$FrmStr])) {
			$FrmLst =& $_tbs_FrmMultiLst[$FrmStr];
		} else {
			$FrmLst = explode('|',$FrmStr); // syntax : PostiveFrm|NegativeFrm|ZeroFrm|NullFrm
			$FrmNbr = count($FrmLst);
			if (($FrmNbr<=1) or ($FrmLst[1]==='')) {
				$FrmLst[1] =& $FrmLst[0]; // negativ
				$FrmLst['abs'] = false;
			} else {
				$FrmLst['abs'] = true;
			}
			if (($FrmNbr<=2) or ($FrmLst[2]==='')) $FrmLst[2] =& $FrmLst[0]; // zero
			if (($FrmNbr<=3) or ($FrmLst[3]==='')) $FrmLst[3] = ''; // null
			$_tbs_FrmMultiLst[$FrmStr] = $FrmLst;
		}

		// Select the format
		if (is_numeric($Value)) {
			if (is_string($Value)) $Value = 0.0 + $Value;
			if ($Value>0) {
				$FrmStr =& $FrmLst[0];
			} elseif ($Value<0) {
				$FrmStr =& $FrmLst[1];
				if ($FrmLst['abs']) $Value = abs($Value);
			} else { // zero
				$FrmStr =& $FrmLst[2];
				$Minus = '';
			}
			$CheckNumeric = false;
		} else {
			$Value = ''.$Value;
			if ($Value==='') {
				return $FrmLst[3]; // Null value
			} else {
				$t = strtotime($Value); // We look if it's a date
				if ($t===-1) { // Date not recognized
					return $FrmLst[1];
				} elseif ($t===943916400) { // Date to zero
					return $FrmLst[2];
				} else { // It's a date
					$Value = $t;
					$FrmStr =& $FrmLst[0];
				}
			}
		}

	}

	if ($FrmStr==='') return ''.$Value;

	// Retrieve the correct simple format
	if (!isset($_tbs_FrmSimpleLst[$FrmStr])) tbs_Misc_FormatSave($FrmStr);

	$Frm =& $_tbs_FrmSimpleLst[$FrmStr];

	switch ($Frm['type']) {
	case 'num' :
		// NUMERIC
		if ($CheckNumeric) {
			if (is_numeric($Value)) {
				if (is_string($Value)) $Value = 0.0 + $Value;
			} else {
				return ''.$Value;
			}
		}
		if ($Frm['PerCent']) $Value = $Value * 100;
		$Value = number_format($Value,$Frm['DecNbr'],$Frm['DecSep'],$Frm['ThsSep']);
		return substr_replace($FrmStr,$Value,$Frm['Pos'],$Frm['Len']);
		break;
	case 'date' :
		// DATE
		if (is_string($Value)) {
			if ($Value==='') return '';
			$x = strtotime($Value);
			if ($x===-1) {
				if (!is_numeric($Value)) $Value = 0;
			} else {
				$Value =& $x;
			}
		} else {
			if (!is_numeric($Value)) return ''.$Value;
		}
		if (isset($Loc->PrmLst['locale'])) {
			return strftime($Frm['str_loc'],$Value);
		} else {
			return date($Frm['str_us'],$Value);
		}
		break;
	default:
		return $Frm['string'];
		break;
	}

}

function tbs_Misc_FormatSave(&$FrmStr) {

	global $_tbs_FrmSimpleLst;

	$nPosEnd = strrpos($FrmStr,'0');

	if ($nPosEnd!==false) {

		// Numeric format
		$nDecSep = '.';
		$nDecNbr = 0;
		$nDecOk = true;

		if (substr($FrmStr,$nPosEnd+1,1)==='.') {
			$nPosEnd++;
			$nPosCurr = $nPosEnd;
		} else {
			$nPosCurr = $nPosEnd - 1;
			while (($nPosCurr>=0) and ($FrmStr[$nPosCurr]==='0')) {
				$nPosCurr--;
			}
			if (($nPosCurr>=1) and ($FrmStr[$nPosCurr-1]==='0')) {
				$nDecSep = $FrmStr[$nPosCurr];
				$nDecNbr = $nPosEnd - $nPosCurr;
			} else {
				$nDecOk = false;
			}
		}

		// Thousand separator
		$nThsSep = '';
		if (($nDecOk) and ($nPosCurr>=5)) {
			if ((substr($FrmStr,$nPosCurr-3,3)==='000') and ($FrmStr[$nPosCurr-4]!=='') and ($FrmStr[$nPosCurr-5]==='0')) {
				$nPosCurr = $nPosCurr-4;
				$nThsSep = $FrmStr[$nPosCurr];
			}
		}

		// Pass next zero
		if ($nDecOk) $nPosCurr--;
		while (($nPosCurr>=0) and ($FrmStr[$nPosCurr]==='0')) {
			$nPosCurr--;
		}

		// Percent
		$nPerCent = (strpos($FrmStr,'%')===false) ? false : true;

		$_tbs_FrmSimpleLst[$FrmStr] = array('type'=>'num','Pos'=>($nPosCurr+1),'Len'=>($nPosEnd-$nPosCurr),'ThsSep'=>$nThsSep,'DecSep'=>$nDecSep,'DecNbr'=>$nDecNbr,'PerCent'=>$nPerCent);

	} else { // if ($nPosEnd!==false)

		// Date format
		$FrmPHP = '';
		$FrmLOC = '';
		$Local = false;
		$StrIn = false;
		$iMax = strlen($FrmStr);
		$Cnt = 0;

		for ($i=0;$i<$iMax;$i++) {

			if ($StrIn) {
				// We are in a string part
				if ($FrmStr[$i]===$StrChr) {
					if (substr($FrmStr,$i+1,1)===$StrChr) {
						$FrmPHP .= '\\'.$FrmStr[$i]; // protected char
						$FrmLOC .= $FrmStr[$i];
						$i++;
					} else {
						$StrIn = false;
					}
				} else {
					$FrmPHP .= '\\'.$FrmStr[$i]; // protected char
					$FrmLOC .= $FrmStr[$i];
				}
			} else {
				if (($FrmStr[$i]==='"') or ($FrmStr[$i]==='\'')) {
					// Check if we have the opening string char
					$StrIn = true;
					$StrChr = $FrmStr[$i];
				} else {
					$Cnt++;
					if     (strcasecmp(substr($FrmStr,$i,4),'yyyy')===0) { $FrmPHP .= 'Y'; $FrmLOC .= '%Y'; $i += 3; }
					elseif (strcasecmp(substr($FrmStr,$i,2),'yy'  )===0) { $FrmPHP .= 'y'; $FrmLOC .= '%y'; $i += 1; }
					elseif (strcasecmp(substr($FrmStr,$i,4),'mmmm')===0) { $FrmPHP .= 'F'; $FrmLOC .= '%B'; $i += 3; }
					elseif (strcasecmp(substr($FrmStr,$i,3),'mmm' )===0) { $FrmPHP .= 'M'; $FrmLOC .= '%b'; $i += 2; }
					elseif (strcasecmp(substr($FrmStr,$i,2),'mm'  )===0) { $FrmPHP .= 'm'; $FrmLOC .= '%m'; $i += 1; }
					elseif (strcasecmp(substr($FrmStr,$i,1),'m'   )===0) { $FrmPHP .= 'n'; $FrmLOC .= '%m'; }
					elseif (strcasecmp(substr($FrmStr,$i,4),'wwww')===0) { $FrmPHP .= 'l'; $FrmLOC .= '%A'; $i += 3; }
					elseif (strcasecmp(substr($FrmStr,$i,3),'www' )===0) { $FrmPHP .= 'D'; $FrmLOC .= '%a'; $i += 2; }
					elseif (strcasecmp(substr($FrmStr,$i,1),'w'   )===0) { $FrmPHP .= 'w'; $FrmLOC .= '%u'; }
					elseif (strcasecmp(substr($FrmStr,$i,4),'dddd')===0) { $FrmPHP .= 'l'; $FrmLOC .= '%A'; $i += 3; }
					elseif (strcasecmp(substr($FrmStr,$i,3),'ddd' )===0) { $FrmPHP .= 'D'; $FrmLOC .= '%a'; $i += 2; }
					elseif (strcasecmp(substr($FrmStr,$i,2),'dd'  )===0) { $FrmPHP .= 'd'; $FrmLOC .= '%d'; $i += 1; }
					elseif (strcasecmp(substr($FrmStr,$i,1),'d'   )===0) { $FrmPHP .= 'j'; $FrmLOC .= '%d'; }
					elseif (strcasecmp(substr($FrmStr,$i,2),'hh'  )===0) { $FrmPHP .= 'H'; $FrmLOC .= '%H'; $i += 1; }
					elseif (strcasecmp(substr($FrmStr,$i,2),'nn'  )===0) { $FrmPHP .= 'i'; $FrmLOC .= '%M'; $i += 1; }
					elseif (strcasecmp(substr($FrmStr,$i,2),'ss'  )===0) { $FrmPHP .= 's'; $FrmLOC .= '%S'; $i += 1; }
					elseif (strcasecmp(substr($FrmStr,$i,2),'xx'  )===0) { $FrmPHP .= 'S'; $FrmLOC .= ''  ; $i += 1; }
					else {
						$FrmPHP .= '\\'.$FrmStr[$i]; // protected char
						$FrmLOC .= $FrmStr[$i]; // protected char
						$Cnt--;
					}
				}
			} //-> if ($StrIn) {...} else

		} //-> for ($i=0;$i<$iMax;$i++)

		if ($Cnt>0) {
			$_tbs_FrmSimpleLst[$FrmStr] = array('type'=>'date','str_us'=>$FrmPHP,'str_loc'=>$FrmLOC);
		} else {
			$_tbs_FrmSimpleLst[$FrmStr] = array('type'=>'else','string'=>$FrmStr);
		}

	} // if ($nPosEnd!==false) {...} else

}

function tbs_Locator_SectionAddBlk(&$LocR,$BlockName,$Txt) {
	$LocR->BlockNbr++;
	$LocR->BlockName[$LocR->BlockNbr] = $BlockName;
	$LocR->BlockSrc[$LocR->BlockNbr] = $Txt;
	$LocR->BlockLoc[$LocR->BlockNbr] = array(0=>0);
	$LocR->BlockChk[$LocR->BlockNbr] = true;
	return $LocR->BlockNbr;
}

function tbs_Locator_SectionAddGrp(&$LocR,$Bid,$Type,$Field) {

	if ($Type==='H') {
		if ($LocR->HeaderFound===false) {
			$LocR->HeaderFound = true;
			$LocR->HeaderNbr = 0;
			$LocR->HeaderBid = array();       // 1 to HeaderNbr
			$LocR->HeaderPrevValue = array(); // 1 to HeaderNbr
			$LocR->HeaderField = array();     // 1 to HeaderNbr
		}
		$LocR->HeaderNbr++;
		$LocR->HeaderBid[$LocR->HeaderNbr] = $Bid;
		$LocR->HeaderPrevValue[$LocR->HeaderNbr] = false;
		$LocR->HeaderField[$LocR->HeaderNbr] = $Field;
	} else {
		if ($LocR->FooterFound===false) {
			$LocR->FooterFound = true;
			$LocR->FooterNbr = 0;
			$LocR->FooterBid = array();       // 1 to FooterNbr
			$LocR->FooterPrevValue = array(); // 1 to FooterNbr
			$LocR->FooterField = array();     // 1 to FooterNbr
			$LocR->FooterIsFooter = array();  // 1 to FooterNbr
		}
		$LocR->FooterNbr++;
		$LocR->FooterBid[$LocR->FooterNbr] = $Bid;
		$LocR->FooterPrevValue[$LocR->FooterNbr] = false;
		if ($Type==='F') {
			$LocR->FooterField[$LocR->FooterNbr] = $Field;
			$LocR->FooterIsFooter[$LocR->FooterNbr] = true;
		} else {
			$LocR->FooterField[$LocR->FooterNbr] = $Field;
			$LocR->FooterIsFooter[$LocR->FooterNbr] = false;
		}
	}
	
}

function tbs_Locator_PrmRead(&$Txt,$Pos,$HtmlTag,$DelimChrs,$BegStr,$EndStr,&$Loc,&$PosEnd) {

	// À mettre dans la classe TBS
	$BegLen = strlen($BegStr);
	$BegChr = $BegStr[0];
	$BegIs1 = ($BegLen===1);

	$DelimIdx = false;
	$DelimCnt = 0;
	$DelimChr = '';
	$BegCnt = 0;
	$SubName = $Loc->SubOk;
	
	$Status = 0; // 0: name not started, 1: name started, 2: name ended, 3: equal found, 4: value started
	$PosName = 0;
	$PosNend = 0;
	$PosVal = 0;
	
	// Paramètres de vérif de la boucle
	$PosEnd = strpos($Txt,$EndStr,$Pos);
	if ($PosEnd===false) return;
	$Continue = ($Pos<$PosEnd);
	
	while ($Continue) {
		
		$Chr = $Txt[$Pos];

		if ($DelimIdx) { // Lecture dans une chaîne

			if ($Chr===$DelimChr) { // Quote rencontré
				if ($Chr===$Txt[$Pos+1]) { // Double quote => la chaîne continue en dédoublant le quote
					$Pos++;
				} else { // Simple quote => fin de la chaîne
					$DelimIdx = false;
				}
			}

		} else { // Lecture hors chaîne
			
			if ($BegCnt===0) {
				
				// Analyse des paramètre
				$CheckChr = false;
				if ($Chr===' ') {
					if ($Status===1) {
						$Status = 2;
						$PosNend = $Pos;
					} elseif ($HtmlTag and ($Status===4)) {
						tbs_Locator_PrmCompute($Txt,$Loc,$SubName,$Status,$HtmlTag,$DelimChr,$DelimCnt,$PosName,$PosNend,$PosVal,$Pos);
						$Status = 0;
					}
				} elseif (($HtmlTag===false) and ($Chr===';')) {
					tbs_Locator_PrmCompute($Txt,$Loc,$SubName,$Status,$HtmlTag,$DelimChr,$DelimCnt,$PosName,$PosNend,$PosVal,$Pos);
					$Status = 0;
				} elseif ($Status===4) {
					$CheckChr = true;
				} elseif ($Status===3) {
					$Status = 4;
					$DelimCnt = 0;
					$PosVal = $Pos;
					$CheckChr = true;
				} elseif ($Status===2) {
					if ($Chr==='=') {
						$Status = 3;
					} elseif ($HtmlTag) {
						tbs_Locator_PrmCompute($Txt,$Loc,$SubName,$Status,$HtmlTag,$DelimChr,$DelimCnt,$PosName,$PosNend,$PosVal,$Pos);
						$Status = 1;
						$PosName = $Pos;
						$CheckChr = true;
					} else {
						$Status = 4;
						$DelimCnt = 0;
						$PosVal = $Pos;
						$CheckChr = true;
					}
				} elseif ($Status===1) {
					if ($Chr==='=') {
						$Status = 3;
						$PosNend = $Pos;
					} else {
						$CheckChr = true;
					}
				} else {
					$Status = 1;
					$PosName = $Pos;
					$CheckChr = true;
				}
				
				if ($CheckChr) {
					$DelimIdx = strpos($DelimChrs,$Chr);
					if ($DelimIdx===false) {
						if ($Chr===$BegChr) {
							if ($BegIs1) {
								$BegCnt++;
							} elseif(substr($Txt,$Pos,$BegLen)===$BegStr) {
								$BegCnt++;
							}
						}
					} else {
						$DelimChr = $DelimChrs[$DelimIdx];
						$DelimCnt++;
						$DelimIdx = true;
					}
				}
				
			} else {
				if ($Chr===$BegChr) {
					if ($BegIs1) {
						$BegCnt++;
					} elseif(substr($Txt,$Pos,$BegLen)===$BegStr) {
						$BegCnt++;
					}
				}
			}
		
		}
		
		// Charactère suivant
		$Pos++;

		// On vérifie si c'est la fin
		if ($Pos===$PosEnd) {
			if ($DelimIdx===false) {
				if ($BegCnt>0) {
					$BegCnt--;
				} else {
					$Continue = false;
				}
			}
			if ($Continue) {
				$PosEnd = strpos($Txt,$EndStr,$PosEnd+1);
				if ($PosEnd===false) return;
			} else {
				tbs_Locator_PrmCompute($Txt,$Loc,$SubName,$Status,$HtmlTag,$DelimChr,$DelimCnt,$PosName,$PosNend,$PosVal,$Pos);
			}
		}
	
	}
	
	$PosEnd = $PosEnd + (strlen($EndStr)-1);

}

function tbs_Locator_PrmCompute(&$Txt,&$Loc,&$SubName,$Status,$HtmlTag,$DelimChr,$DelimCnt,$PosName,$PosNend,$PosVal,$Pos) {
	
	if ($Status===0) {
		$SubName = false;
	} else {
		if ($Status===1) {
			$x = substr($Txt,$PosName,$Pos-$PosName);
		} else {
			$x = substr($Txt,$PosName,$PosNend-$PosName);
		}
		if ($HtmlTag) $x = strtolower($x);
		if ($SubName) {
			$Loc->SubName = $x;
			$SubName = false;
		} elseif ($Status===4) {
			$v = trim(substr($Txt,$PosVal,$Pos-$PosVal));
			if ($DelimCnt===1) { // Delete quotes inside the value
				if ($v[0]===$DelimChr) {
					$len = strlen($v);
					if ($v[$len-1]===$DelimChr) {
						$v = substr($v,1,$len-2);
						$v = str_replace($DelimChr.$DelimChr,$DelimChr,$v);
					}
				}
			}
			$Loc->PrmLst[$x] = $v;
		} else {
			$Loc->PrmLst[$x] = true;
		}
	}

}

function tbs_Locator_EnlargeToStr(&$Txt,&$Loc,$StrBeg,$StrEnd) {
/*
This function enables to enlarge the pos limits of the Locator.
If the search result is not correct, $PosBeg must not change its value, and $PosEnd must be False.
This is because of the calling function.
*/

	// Search for the begining string
	$Pos = $Loc->PosBeg;
	$Ok = false;
	do {
		$Pos = strrpos(substr($Txt,0,$Pos),$StrBeg[0]);
		if ($Pos!==false) {
			if (substr($Txt,$Pos,strlen($StrBeg))===$StrBeg) $Ok = true;
		}
	} while ( (!$Ok) and ($Pos!==false) );

	if ($Ok) {
		$PosEnd = strpos($Txt,$StrEnd,$Loc->PosEnd + 1);
		if ($PosEnd===false) {
			$Ok = false;
		} else {
			$Loc->PosBeg = $Pos;
			$Loc->PosEnd = $PosEnd + strlen($StrEnd) - 1;
		}
	}

	return $Ok;

}

function tbs_Locator_EnlargeToTag(&$Txt,&$Loc,$Tag,$IsBlock,$ReturnSrc) {
//Modify $Loc, return false if tags not found, returns the source of the locator if $ReturnSrc=true

	if ($Tag==='') { return false; }
	elseif ($Tag==='row') {$Tag = 'tr'; }
	elseif ($Tag==='opt') {$Tag = 'option'; }

	$RetVal = true;
	$Encaps = 1;
	if ($IsBlock and isset($Loc->PrmLst['encaps'])) $Encaps = abs(intval($Loc->PrmLst['encaps']));

	$TagO = tbs_Html_FindTag($Txt,$Tag,true,$Loc->PosBeg-1,false,$Encaps,false);
	if ($TagO===false) return false;
	$TagC = tbs_Html_FindTag($Txt,$Tag,false,$Loc->PosEnd+1,true,$Encaps,false);
	if ($TagC==false) return false;
	$PosBeg = $TagO->PosBeg;
	$PosEnd = $TagC->PosEnd;

	if ($IsBlock) {
		
		$ExtendFw = false;
		$ExtendBw = false;
		if (isset($Loc->PrmLst['extend'])) {
			$s = ',';
			$x = str_replace(' ','',''.$Loc->PrmLst['extend']);
			if (is_numeric($x)) {
				$x = intval($Loc->PrmLst['extend']);
				if ($x>0) {
					$lst =& $ExtendFw;
				} else {
					$lst =& $ExtendBw;
				}
				$x = str_repeat($Tag.$s,abs($x));
			} else {
				$lst =& $ExtendFw;
			}
			$lst = explode($s,$x);
		}		
		
		if ($ExtendFw!==false) { // Forward
			$TagC = true;
			foreach ($ExtendFw as $Tag) {
				if (($Tag!=='') and ($TagC!==false)) {
					$TagO = tbs_Html_FindTag($Txt,$Tag,true,$PosEnd+1,true,1,false);
					if ($TagO!==false) {
						$TagC = tbs_Html_FindTag($Txt,$Tag,false,$TagO->PosEnd+1,true,0,false);
						if ($TagC!==false) {
							$PosEnd = $TagC->PosEnd;
						}
					}
				}
			}
		}
		
		if ($ExtendBw!==false) { // Backward
			$TagO = true;
			for ($i=count($ExtendBw)-1;$i>=0;$i--) {
				$Tag = $ExtendBw[$i];
				if (($Tag!=='') and ($TagO!==false)) {
					$TagC = tbs_Html_FindTag($Txt,$Tag,false,$PosBeg-1,false,1,false);
					if ($TagC!==false) {
						$TagO = tbs_Html_FindTag($Txt,$Tag,true,$TagC->PosBeg-1,false,0,false);
						if ($TagO!==false) {
							$PosBeg = $TagO->PosBeg;
						}
					}
				}
			}
		}
		
	} elseif ($ReturnSrc) {
		
		$RetVal = '';
		if ($Loc->PosBeg>$TagO->PosEnd) $RetVal .= substr($Txt,$TagO->PosEnd+1,min($Loc->PosBeg,$TagC->PosBeg)-$TagO->PosEnd-1);
		if ($Loc->PosEnd<$TagC->PosBeg) $RetVal .= substr($Txt,max($Loc->PosEnd,$TagO->PosEnd)+1,$TagC->PosBeg-max($Loc->PosEnd,$TagO->PosEnd)-1);
		
	}

	$Loc->PosBeg = $PosBeg;
	$Loc->PosEnd = $PosEnd;
	return $RetVal;

}

function tbs_Html_Max(&$Txt,&$Nbr) {
// Limit the number of HTML chars

	$pMax = strlen($Txt)-1;
	$p=0;
	$n=0;
	$in = false;
	$ok = true;

	while ($ok) {
		if ($in) {
			if ($Txt[$p]===';') {
				$in = false;
				$n++;
			}
		} else {
			if ($Txt[$p]==='&') {
				$in = true;
			} else {
				$n++;
			}
		}
		if (($n>=$Nbr) or ($p>=$pMax)) {
			$ok = false;
		} else {
			$p++;
		}
	}

	if (($n>=$Nbr) and ($p<$pMax)) $Txt = substr($Txt,0,$p).'...';

}

function tbs_Html_IsHtml(&$Txt) {
// This function returns True if the text seems to have some HTML tags.

	// Search for opening and closing tags
	$pos = strpos($Txt,'<');
	if ( ($pos!==false) and ($pos<strlen($Txt)-1) ) {
		$pos = strpos($Txt,'>',$pos + 1);
		if ( ($pos!==false) and ($pos<strlen($Txt)-1) ) {
			$pos = strpos($Txt,'</',$pos + 1);
			if ( ($pos!==false)and ($pos<strlen($Txt)-1) ) {
				$pos = strpos($Txt,'>',$pos + 1);
				if ($pos!==false) return true;
			}
		}
	}

	// Search for special char
	$pos = strpos($Txt,'&');
	if ( ($pos!==false) and ($pos<strlen($Txt)-1) ) {
		$pos2 = strpos($Txt,';',$pos+1);
		if ($pos2!==false) {
			$x = substr($Txt,$pos+1,$pos2-$pos-1); // We extract the found text between the couple of tags
			if (strlen($x)<=10) {
				if (strpos($x,' ')===false) return true;
			}
		}
	}

	// Look for a simple tag
	$Loc1 = tbs_Html_FindTag($Txt,'BR',true,0,true,0,false); // line break
	if ($Loc1!==false) return true;
	$Loc1 = tbs_Html_FindTag($Txt,'HR',true,0,true,0,false); // horizontal line
	if ($Loc1!==false) return true;

	return false;

}

function tbs_Html_GetPart(&$Txt,$Tag,$WithTags=false,$CancelIfEmpty=false) {
// This function returns a part of the HTML document (HEAD or BODY)
// The $CancelIfEmpty parameter enables to cancel the extraction when the part is not found.

	$x = false;

	$LocOpen = tbs_Html_FindTag($Txt,$Tag,true,0,true,0,false);
	if ($LocOpen!==false) {
		$LocClose = tbs_Html_FindTag($Txt,$Tag,false,$LocOpen->PosEnd+1,true,0,false);
		if ($LocClose!==false) {
			if ($WithTags) {
				$x = substr($Txt,$LocOpen->PosBeg,$LocClose->PosEnd - $LocOpen->PosBeg + 1);
			} else {
				$x = substr($Txt,$LocOpen->PosEnd+1,$LocClose->PosBeg - $LocOpen->PosEnd - 1);
			}
		}
	}

	if ($x===false) {
		if ($CancelIfEmpty) {
			$x = $Txt;
		} else {
			$x = '';
		}
	}

	return $x;

}

function tbs_Html_InsertAttribute(&$Txt,&$Attr,$Pos) {
	// Check for XHTML end characters
	if ($Txt[$Pos-1]==='/') {
		$Pos--;
		if ($Txt[$Pos-1]===' ') $Pos--;
	}
	// Insert the parameter
	$Txt = substr_replace($Txt,$Attr,$Pos,0);
}

function tbs_Html_FindTag(&$Txt,$Tag,$Opening,$PosBeg,$Forward,$Encaps,$WithPrm) {
/* This function is a smarter issue to find an HTML tag.
It enables to ignore full opening/closing couple of tag that could be inserted before the searched tag.
It also enables to pass a number of encapsulations.
To ignore encapsulation and opengin/closing just set $Encaps=0.
*/
	if ($Forward) {
		$Pos = $PosBeg - 1;
	} else {
		$Pos = $PosBeg + 1;
	}
	$TagIsOpening = false;
	$TagClosing = '/'.$Tag;
	if ($Opening) {
		$EncapsEnd = $Encaps;
	} else {
		$EncapsEnd = - $Encaps;
	}
	$EncapsCnt = 0;
	$TagOk = false;

	do {

		// Look for the next tag def
		if ($Forward) {
			$Pos = strpos($Txt,'<',$Pos+1);
		} else {
			if ($Pos<=0) {
				$Pos = false;
			} else {
				$Pos = strrpos(substr($Txt,0,$Pos - 1),'<');
			}
		}

		if ($Pos!==false) {
			// Check the name of the tag
			if (strcasecmp(substr($Txt,$Pos+1,strlen($Tag)),$Tag)==0) {
				$PosX = $Pos + 1 + strlen($Tag); // The next char
				$TagOk = true;
				$TagIsOpening = true;
			} elseif (strcasecmp(substr($Txt,$Pos+1,strlen($TagClosing)),$TagClosing)==0) {
				$PosX = $Pos + 1 + strlen($TagClosing); // The next char
				$TagOk = true;
				$TagIsOpening = false;
			}

			if ($TagOk) {
				// Check the next char
				if (($Txt[$PosX]===' ') or ($Txt[$PosX]==='>')) {
					// Check the encapsulation count
					if ($EncapsEnd==0) {
						// No encaplusation check
						if ($TagIsOpening!==$Opening) $TagOk = false;
					} else {
						// Count the number of encapsulation
						if ($TagIsOpening) {
							$EncapsCnt++;
						} else {
							$EncapsCnt--;
						}
						// Check if it's the expected count
						if ($EncapsCnt!=$EncapsEnd) $TagOk = false;
					}
				} else {
					$TagOk = false;
				}
			} //--> if ($TagOk)

		}
	} while (($Pos!==false) and ($TagOk===false));

	// Search for the end of the tag
	if ($TagOk) {
		$Loc =& new clsTbsLocator;
		if ($WithPrm) {
			$PosEnd = 0;
			tbs_Locator_PrmRead($Txt,$PosX,true,'\'"','<','>',$Loc,$PosEnd);
		} else {
			$PosEnd = strpos($Txt,'>',$PosX);
			if ($PosEnd===false) {
				$TagOk = false;
			}
		}
	}

	// Result
	if ($TagOk) {
		$Loc->PosBeg = $Pos;
		$Loc->PosEnd = $PosEnd;
		return $Loc;
	} else {
		return false;
	}

}

function tbs_Html_MergeItems(&$Txt,&$Loc,&$SelValue,&$SelArray,&$NewEnd) {
// Merge items of a list, or radio or check buttons.
// At this point, the Locator is already merged with $SelValue.

	if ($Loc->PrmLst['selected']===true) {
		$IsList = true;
		$MainTag = 'SELECT';
		$ItemTag = 'OPTION';
		$ItemPrm = 'selected';
	} else {
		$IsList = false;
		$MainTag = 'FORM';
		$ItemTag = 'INPUT';
		$ItemPrm = 'checked';
	}
	if (isset($Loc->PrmLst['selbounds'])) $MainTag = $Loc->PrmLst['selbounds'];
	$ItemPrmZ = ' '.$ItemPrm.'="'.$ItemPrm.'"';

	$TagO = tbs_Html_FindTag($Txt,$MainTag,true,$Loc->PosBeg-1,false,0,false);

	if ($TagO!==false) {

		$TagC = tbs_Html_FindTag($Txt,$MainTag,false,$Loc->PosBeg,true,0,false);
		if ($TagC!==false) {

			// We get the main block without the main tags
			$MainSrc = substr($Txt,$TagO->PosEnd+1,$TagC->PosBeg - $TagO->PosEnd -1);

			if ($IsList) {
				// Information about the item that was used for the TBS field
				$Item0Beg = $Loc->PosBeg - ($TagO->PosEnd+1);
				$Item0Src = '';
				$Item0Ok = false;
			} else {
				// We delete the merged value
				$MainSrc = substr_replace($MainSrc,'',$Loc->PosBeg - ($TagO->PosEnd+1), strlen($SelValue));
			}

			// Now, we going to scan all of the item tags
			$Pos = 0;
			$SelNbr = 0;
			while ($ItemLoc = tbs_Html_FindTag($MainSrc,$ItemTag,true,$Pos,true,0,true)) {

				// we get the value of the item
				$ItemValue = false;

				if ($IsList) {
					// Look for the end of the item
					$OptCPos = strpos($MainSrc,'<',$ItemLoc->PosEnd+1);
					if ($OptCPos===false) $OptCPos = strlen($MainSrc);
					if (($Item0Ok===false) and ($ItemLoc->PosBeg<$Item0Beg) and ($Item0Beg<=$OptCPos)) {
						// If it's the original item, we save it and delete it.
						if (($OptCPos+1<strlen($MainSrc)) and ($MainSrc[$OptCPos+1]==='/')) {
							$OptCPos = strpos($MainSrc,'>',$OptCPos);
							if ($OptCPos===false) {
								$OptCPos = strlen($MainSrc);
							} else {
								$OptCPos++;
							}
						}
						$Item0Src = substr($MainSrc,$ItemLoc->PosBeg,$OptCPos-$ItemLoc->PosBeg);
						$MainSrc = substr_replace($MainSrc,'',$ItemLoc->PosBeg,strlen($Item0Src));
						if (!isset($ItemLoc->PrmLst[$ItemPrm])) tbs_Html_InsertAttribute($Item0Src,$ItemPrmZ,$ItemLoc->PosEnd-$ItemLoc->PosBeg);
						$OptCPos = min($ItemLoc->PosBeg,strlen($MainSrc)-1);
						$Select = false;
						$Item0Ok = true;
					} else {
						if (isset($ItemLoc->PrmLst['value'])) {
							$ItemValue = $ItemLoc->PrmLst['value'];
						} else { // The value of the option is its caption.
							$ItemValue = substr($MainSrc,$ItemLoc->PosEnd+1,$OptCPos - $ItemLoc->PosEnd - 1);
							$ItemValue = str_replace(chr(9),' ',$ItemValue);
							$ItemValue = str_replace(chr(10),' ',$ItemValue);
							$ItemValue = str_replace(chr(13),' ',$ItemValue);
							$ItemValue = trim($ItemValue);
						}
					}
					$Pos = $OptCPos;
				} else {
					if ((isset($ItemLoc->PrmLst['name'])) and (isset($ItemLoc->PrmLst['value']))) {
						if (strcasecmp($Loc->PrmLst['selected'],$ItemLoc->PrmLst['name'])==0) {
							$ItemValue = $ItemLoc->PrmLst['value'];
						}
					}
					$Pos = $ItemLoc->PosEnd;
				}

				if ($ItemValue!==false) {
					// we look if we select the item
					$Select = false;
					if ($SelArray===false) {
						if (strcasecmp($ItemValue,$SelValue)==0) {
							if ($SelNbr==0) $Select = true;
						}
					} else {
						if (array_search($ItemValue,$SelArray,false)!==false) $Select = true;
					}
					// Select the item
					if ($Select) {
						if (!isset($ItemLoc->PrmLst[$ItemPrm])) {
							tbs_Html_InsertAttribute($MainSrc,$ItemPrmZ,$ItemLoc->PosEnd);
							$Pos = $Pos + strlen($ItemPrmZ);
							if ($IsList and ($ItemLoc->PosBeg<$Item0Beg)) $Item0Beg = $Item0Beg + strlen($ItemPrmZ);
						}
						$SelNbr++;
					}
				}

			} //--> while ($ItemLoc = ... ) {

			if ($IsList) {
				// Add the original item if it's not found
				if (($SelArray===false) and ($SelNbr==0)) $MainSrc = $MainSrc.$Item0Src;
				$NewEnd = $TagO->PosEnd;
			} else {
				$NewEnd = $Loc->PosBeg;
			}

			$Txt = substr_replace($Txt,$MainSrc,$TagO->PosEnd+1,$TagC->PosBeg-$TagO->PosEnd-1);

		} //--> if ($TagC!==false) {
	} //--> if ($TagO!==false) {


}

function tbs_Cache_IsValide($CacheFile,$TimeOut) {
// Return True if there is a existing valid cache for the given file id.
	if (file_exists($CacheFile)) {
		if (time()-filemtime($CacheFile)>$TimeOut) {
			return false;
		} else {
			return true;
		}
	} else {
		return false;
	}
}

function tbs_Cache_File($Dir,$CacheId,$Mask) {
// Return the cache file path for a given Id.
	if (strlen($Dir)>0) {
		if ($Dir[strlen($Dir)-1]<>'/') {
			$Dir .= '/';
		}
	}
	return $Dir.str_replace('*',$CacheId,$Mask);
}

function tbs_Cache_DeleteAll($Dir,$Mask) {

	if (strlen($Dir)==0) {
		$Dir = '.';
	}
	if ($Dir[strlen($Dir)-1]<>'/') {
		$Dir .= '/';
	}
	$DirObj = dir($Dir);
	$Nbr = 0;
	$PosL = strpos($Mask,'*');
	$PosR = strlen($Mask) - $PosL - 1;

	// Get the list of cache files
	$FileLst = array();
	while ($FileName = $DirObj->read()) {
		$FullPath = $Dir.$FileName;
		if (strtolower(filetype($FullPath))==='file') {
			if (strlen($FileName)>=strlen($Mask)) {
				if ((substr($FileName,0,$PosL)===substr($Mask,0,$PosL)) and (substr($FileName,-$PosR)===substr($Mask,-$PosR))) {
					$FileLst[] = $FullPath;
				}
			}
		}
	}
	// Delete all listed files
	foreach ($FileLst as $FullPath) {
		if (@unlink($FullPath)) $Nbr++;
	}

	return $Nbr;

}

?>