<?php

require_once "../tools/generic_includes.php";


class myAdditions 
{
	function checkblanks($record,$scores,$s,$fieldname)
	{
		//$record2 = $record;
		unset($s['notes']);
		if ((array_search('', $s)!==false) or (array_search('missing', array_map('strtolower',$s))!==false)) {
			if (strpos($record['notes'], " AUTOMATED MESSAGE: some field(s) not entered") === FALSE) {
				$scores['notes'] = $record['notes'] . " AUTOMATED MESSAGE: some field(s) not entered";	
			} 

			if($fieldname !== '') {
				$scores[$fieldname] = NULL;
			}
			$scores['warnings'] = "Warning:  Blank entries found!";
		} else {
		

			$scores['notes'] = str_replace("AUTOMATED MESSAGE: some field(s) not entered"," ",$record['notes']);
			$scores['warnings'] = NULL;
		}

		return $scores;
	}

}


