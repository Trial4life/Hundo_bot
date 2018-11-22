<?php
	function get_string_between($string, $start, $end){
	    $string = ' ' . $string;
	    $ini = strpos($string, $start);
	    if ($ini == 0) return '';
	    $ini += strlen($start);
	    $len = strpos($string, $end, $ini) - $ini;
	    return substr($string, $ini, $len);
	}

	function getPortalData($msg) {
		$pkst = get_string_between($msg, 'Portal: ', 'Address:');
		$lat = 0;
		$lng = 0;
   	return array($pkst, $lat, $lng);
   }
?>