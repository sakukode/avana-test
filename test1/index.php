<?php 

function findClosingBracketPosition($str, $position) {
	if ($str[$position] !== '(') {
	    echo 'Can\'t find open bracket';
	    return false;
	}

	$depth = 1;
	for ($i=$position+1; $i < strlen($str) ; $i++) { 
		if($str[$i] === '(') {
			++$depth;
		} else if($str[$i] === ')') {
			--$depth;
		} 

		if($depth === 0) {
			return $i;
		}
	}

	return -1;
}

$str = "a (b c (d e (f) g) h) i (j k)";
$position = 2;

echo findClosingBracketPosition($str, $position);