<?php

/*
	Function: xajaxCompressFile
	
	<xajax> will call this function internally to compress the javascript code for 
	more efficient delivery.
	
	$sFile - (stirng):  The file to be compressed.
*/
function xajaxCompressFile($sFile)
{
	//remove windows cariage returns
	$sFile = str_replace("\r",'',$sFile);
	
	//array to store replaced literal strings
	$literal_strings = array();
	
	//explode the string into lines
	$lines = explode("\n",$sFile);
	//loop through all the lines, building a new string at the same time as removing literal strings
	$clean = '';
	$inComment = false;
	$literal = '';
	$inQuote = false;
	$escaped = false;
	$quoteChar = '';
	
	$iLen = count($lines);
	for($i=0; $i<$iLen; ++$i)
	{
		$line = $lines[$i];
		$inNormalComment = false;
	
		//loop through line's characters and take out any literal strings, replace them with ___i___ where i is the index of this string
		$jLen = strlen($line);
		for($j=0; $j<$jLen; ++$j)
		{
			$c = substr($line,$j,1);
			$d = substr($line,$j,2);
	
			//look for start of quote
			if(!$inQuote && !$inComment)
			{
				//is this character a quote or a comment
				if(($c=='"' || $c=="'") && !$inComment && !$inNormalComment)
				{
					$inQuote = true;
					$inComment = false;
					$escaped = false;
					$quoteChar = $c;
					$literal = $c;
				}
				else if($d=="/*" && !$inNormalComment)
				{
					$inQuote = false;
					$inComment = true;
					$escaped = false;
					$quoteChar = $d;
					$literal = $d;	
					$j++;	
				}
				else if($d=="//") //ignore string markers that are found inside comments
				{
					$inNormalComment = true;
				}
				else
				{
					if (!$inNormalComment)
						$clean .= $c;
				}
			}
			else //allready in a string so find end quote
			{
				if($c == $quoteChar && !$escaped && !$inComment)
				{
					$inQuote = false;
					$literal .= $c;
	
					//subsitute in a marker for the string
					$clean .= "___" . count($literal_strings) . "___";
	
					//push the string onto our array
					array_push($literal_strings,$literal);
	
				}
				else if($inComment && $d=="*/")
				{
					$inComment = false;
					$literal .= $d;
					++$j;
				}
				else if($c == "\\" && !$escaped)
					$escaped = true;
				else
					$escaped = false;
	
				$literal .= $c;
			}
		}
		if($inComment) $literal .= "\n";
		$clean .= "\n";
	}
	//explode the clean string into lines again
	$lines = explode("\n",$clean);
	
	//now process each line at a time
	$iLen = count($lines);
	for($i=0; $i<$iLen; ++$i)
	{
		$line = $lines[$i];
	
		//remove comments
		$line = preg_replace("/\/\/(.*)/","",$line);
	
		//strip leading and trailing whitespace
		$line = trim($line);
	
		//remove all whitespace with a single space
		$line = preg_replace("/\s+/"," ",$line);
	
		//remove any whitespace that occurs after/before an operator
		$line = preg_replace("/\s*([!\}\{;,&=\|\-\+\*\/\)\(:])\s*/","\\1",$line);
	
		$lines[$i] = $line;
	}
	
	//implode the lines
	$sFile = implode("\n",$lines);
	
	//make sure there is a max of 1 \n after each line
	$sFile = preg_replace("/[\n]+/","\n",$sFile);
	
	//strip out line breaks that immediately follow a semi-colon
	$sFile = preg_replace("/;\n/",";",$sFile);
	
	//curly brackets aren't on their own
	$sFile = preg_replace("/[\n]*\{[\n]*/","{",$sFile);
	
	//finally loop through and replace all the literal strings:
	$iLen = count($literal_strings);
	for($i=0; $i<$iLen; ++$i)
		$sFile = str_replace('___'.$i.'___',$literal_strings[$i],$sFile);
	
	return $sFile;
}
