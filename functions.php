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
		$latlng = explode(',', get_string_between($URLs[1]['url'],'ll=','&z'));
		$lat = $latlng[0];
		$lng = $latlng[1];
   	return array($pkst, $lat, $lng);
   }
?>