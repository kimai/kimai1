<?php
/*
 * PHP cli script to merge arrays from two language files
 * and write resulting array to new file
 * 
 * Developed for Kimai time tracker
 * http://www.kimai.org/
 * 
 * The purpouse is to merge array structure and new values from
 * a source language file with an existing but outdated language file
 * into a new language file to retain previous translations where suitable.
 * 
 * Only array keys present in first file will be used.
 * Array value from the second file will be used if the array key are present in both files.
 * 
 * This script should be executed with 3 filepaths as arguments
 * The first filepath should contain a complete up-to-date language array, to be used as the source for array structure and all new key-value pairs.
 * The second filepath should be an (old) translation file. All values where corresponding key exist in the first file will be reused.
 * The third filepath will be created as the target file for the resulting array.
 * 
 * Command could be something like this:
 * php find_missing_translations.php ../language/en.php ../language/sv.php ../language/sv_new.php
 * 
 * Default values will be used if no arguments are given, like:
 * php find_missing_translations.php
 * 
 * The above command examples assumes that you are in the same working directory as this script
 * and that the php executable are installed in the system path.
 * 
 * In Windows it might be executed in this format:
 * C:\PHP5\php.exe -f "C:\PHP Scripts\script.php" -- -arg1 -arg2 -arg3
 * 
 * The target file need some manual review afterwards:
 *  -Enter language
 * 	-Enter name of translator
 * 	-Enter kimai version
 *	-Translate lines marked with // REVIEW (remove mark afterwards)
 * 	-You might need to add missing backslashes (if you get a parse error at the end), 
 * 		like change You'll to You\'ll 
 * 		or simply rephrase to You will
 * 	-You might also need to remove excessive backslashes, like You\\'ll 
 * 
 * Status: Beta, more testing should be done
 * 
 * TODO: 
 * 	-Improve escape of special characters
 * 
 * Development notes:
 * array_diff_key() are only one-dimensional.
 * 
 * array_merge() do not handle int keys in a way suitable for this task
 * 		and would add deprecated keys to target file.
 * 
 * All strings are enclosed with single quotes since they don't contain php variables
 * 		and therefore don't need to be parsed for php variables.
 * 
 * addslashes() will escape double quotes even if string are enclosed in single quotes.
 * 
 * And yes, I know functions should not be at the end of the code, but it makes sense (to me) in this case
 * And yes, it's procedural, it should be all OO. I can hear you but I don't listen.
 * And yes, it can certainly be improved in many ways, I will happily discuss, review and incorporate your improvements.
 * Or you could just fork it and fix it yourself.
*/

// Manage PHP error reporting
// Give all php notice and error messages
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check that we are executed from command line
if (PHP_SAPI !== 'cli') {
	echo 'This file are intended to be executed from terminal as a PHP CLI script.'."\n";
	// Exit with an error code
	exit(1);
}

// Get command line arguments or set default values
$keyPath = !empty($argv[1]) ? trim($argv[1]) : '../language/de.php';
$valuePath = !empty($argv[2]) ? trim($argv[2]) : '../language/en.php';
$targetPath = !empty($argv[3]) ? trim($argv[3]) : '../language/en_'.date('Ymd\THis').'.php';

// Define counters
$countReused = 0;
$countReview = 0;

// Check arguments
if (!file_exists($keyPath)) {
	echo 'First file could not be found: '.$keyPath."\n";
	// Exit with an error code
	exit(2);
}
elseif (!is_readable($keyPath)) {
	echo 'First file not readable: '.$keyPath."\n";
	exit(3);
}
elseif (!file_exists($valuePath)) {
	echo 'Second file could not be found: '.$valuePath."\n";
	exit(4);
}
elseif (!is_readable($valuePath)) {
	echo 'Second file not readable: '.$valuePath."\n";
	exit(5);
}
elseif (file_exists($targetPath)) {
	echo 'Third file exist already: '.$targetPath."\n";
	exit(6);
}
else {
	// Include array from first file
	$keyArr = include $keyPath;
	// Count array items recursively
	$keyCount = count($keyArr, COUNT_RECURSIVE);
	
	// Second file
	$valueArr = include $valuePath;
	$valueCount = count($valueArr, COUNT_RECURSIVE);
	
	// 'w' Open for writing only; place the file pointer at the beginning of the file and truncate the file to zero length.
	// If the file does not exist, attempt to create it. 
	if (!($fopen = fopen($targetPath, 'w'))) {
		echo 'Third file could not be opened for writing: '.$targetPath."\n";
		exit(8);
	}
	else {
		// Build initial string
		$str = '<?php'."\n".'/**
 * This file is part of
 * Kimai - Open Source Time Tracking - http://www.kimai.org
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
 */';

		// Add translation info
		$str .= "\n\n".'/* Language: ';
		$str .= "\n".' * Translated by: ';
		$str .= "\n".' * Updated by: ';
		$str .= "\n".' * Kimai version: ';
		$str .= "\n".' *';

		// Add process info
		$str .= "\n".' * Generator: '.basename(__FILE__);
		$str .= "\n".' * Generated: '.date('Y-m-d H:i:s');
		$str .= "\n".' * First file (array structure and new values): '.basename($keyPath);
		$str .= "\n".' * Second file (translated values): '.basename($valuePath);
		$str .= "\n".' * Third file (resulting array): '.basename($targetPath);
		$str .= "\n".' */';
		
		// Initialize the target array
		$str .= "\n\n".'return array(';
		
		// Build and add target array to string
		mergeArr($keyArr, $valueArr);
		
		// Add trailing string to file
		$str .= "\n".')'."\n".'?>'."\n";
		
		// Write string to file
		fwrite($fopen, $str);
		
		// Close file
		fclose($fopen);
		
		// Include resulting array from new file
		// This also serves as a simple check if the resulting array are valid
		$targetArr = include $targetPath;
		
		// Count resulting array items recursively
		$targetCount = count($targetArr, COUNT_RECURSIVE);
		
		// Compare counts and give feedback if different
		if ($keyCount != $targetCount) {
			echo 'The resulting target array in the third file should have had the same amount of items as the source array from the first file.'."\n";
			echo 'Something probably failed. Debug info:'."\n";
			echo 'Array from first file have '.$keyCount.' items'."\n";
			echo 'Array from second file have '.$valueCount.' items'."\n";
			echo 'Array from third file have '.$targetCount.' items'."\n";
		}
		
		// Feedback on translation
		echo $countReused.' old values are reused, '.$countReview.' new values need to be reviewed in file: '.$targetPath."\n";
	}
}

// Traverse and process arrays
function mergeArr($arrKeys, $arrValues) {
	
	// Use global vars
	global $str;
	global $countReused;
	global $countReview;
	
	// Check if arr is array
	if (!is_array($arrKeys)) {
		echo '$arrKeys are no array: '.$arrKeys."\n";
		// Exit with an error code
		exit(9);
	}
	else {
		// Loop source array
		foreach ($arrKeys as $key => $value) {
			
			// Check array key
			if (!is_string($key)){
				// Add key as is
				$writeKey = $key;
			}
			elseif (ctype_digit($key)) {
				// Convert int string to int
				$writeKey = (int)$key;
			}
			else {
				// Quote strings
				// Assuming there's no single quotes in the array keys
				$writeKey = '\''.$key.'\'';
			}
			
			// Check if value is subarray
			if (is_array($value)) {
				
				// Check if translated value subarray exist
				if (!isset($arrValues[$key])) {
					// Set empty array as value subarray
					$arrValues[$key] = array();
				}
				
				// Add leading string for subarray
				$str .= "\n".$writeKey.' => array(';
				
				// Resend subarray to this function
				mergeArr($value, $arrValues[$key]);
				
				// We have finished this round
				// Remove last comma (if any)
				$str = rtrim($str, ',');
				// Add closing string for subarray
				$str .= "\n".'),'."\n";
			}
			elseif (isset($arrValues[$key])) {
				// This key and value pair are already translated
				// Add key with translated value to string
				// Escape single quotes
				$str .= "\n".$writeKey.' => '.'\''.str_replace('\'', '\\\'', $arrValues[$key]).'\',';
				$countReused++;
			}
			else {
				// This value should be reviewed (translated)
				// Add original key => value pair and review message to string
				// Escape single quotes
				$str .= "\n".$writeKey.' => '.'\''.str_replace('\'', '\\\'', $value).'\', // REVIEW';
				$countReview++;
			}
		}
		// Remove last comma (if any)
		$str = rtrim($str, ',');
	}
}






