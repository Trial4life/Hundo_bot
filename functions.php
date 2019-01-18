<?php
	function get_string_between($string, $start, $end){
	    $string = ' ' . $string;
	    $ini = strpos($string, $start);
	    if ($ini == 0) return '';
	    $ini += strlen($start);
	    $len = strpos($string, $end, $ini) - $ini;
	    return substr($string, $ini, $len);
	}

	function getPortalData($msg, $URL) {
		$pkst = str_replace("'","\'",get_string_between($msg, 'Portal: ', "\nAddress:"));
		$latlng = explode(',', get_string_between($URL,'ll=','&z'));
		$lat = $latlng[0];
		$lng = $latlng[1];
   	return array($pkst, $lat, $lng);
   }

   function getPortalZone($level, $lat, $lng, $conn) {
   	$s2cell = new S2Cell(S2LatLng::fromDegrees($lat,$lng));
		$lXcell = new S2Cell($s2cell->id()->parent($level));

		$idCella = $lXcell->id();
		$idCellaLong = $idCella->pos();
		$idCella64 = dechex($idCellaLong);

		$query = "SELECT * FROM `zones` WHERE `cellId64` = '$idCella64'";
		$result = mysqli_query($conn,$query);
		$row = mysqli_fetch_assoc($result);

		if ($row != NULL) {$zona = $row['name'];}

		return $zona;
   }

   function getCellData($ID, $add) {
 		$cellIdObj = new S2CellId($ID);
		$cellObj = new S2Cell($cellIdObj);
		$cellCenter = new S2LatLng($cellObj->getCenter());
		$zoom = $cellObj->level()+$add;
		$lat = $cellCenter->latDegrees();
		$lng = $cellCenter->lngDegrees();

		return array($lat, $lng, $zoom);
   }

   function computeDistance($latA,$lngA,$latB,$lngB) {
   	$latA = $latA*(M_PI/180);
		$lngA = $lngA*(M_PI/180);
		$latB = $latB*(M_PI/180);
		$lngB = $lngB*(M_PI/180);

		$subBA   = $lngB - $lngA;

		$cosLatA = cos($latA);
		$cosLatB = cos($latB);
		$sinLatA = sin($latA);
		$sinLatB = sin($latB);

		$distance = 6371*acos($cosLatA*$cosLatB*cos($subBA)+$sinLatA*$sinLatB);

		return $distance;
   }

   function getWeather($ind, $t) {
		global $wind_1, $wind_2, $gust_1, $gust_2, $GO1, $GO2, $GO1_EMO, $GO2_EMO, $AW1, $AW2, $sunsetHour, $sunriseHour;

		if ($ind == 1) {
			if ($AW1[$t] == 'x') {
				return "\xE2\x96\xAB";
			}
			elseif ($gust_1[$t] > 30.0 && $GO1[$t] != "Pioggia" && $GO1[$t] != "Neve" && strpos($AW1[$t], "rovesci") === false && strpos($AW1[$t], "temporali") === false) {
				return "\xF0\x9F\x92\xA8";
			}
			elseif ($GO1[$t]=="Sereno" && ($t <= $sunriseHour || $t > $sunsetHour)) {
				return "\xF0\x9F\x8C\x99";
			}
			else { return json_encode(str_replace("\\\\","\\",$GO1_EMO[$t])); }
		}
		elseif ($ind == 2) {
			if ($AW2[$t] == 'x') {
				return "\xE2\x96\xAB";
			}
			elseif ($gust_2[$t] > 30.0 && $GO2[$t] != "Pioggia" && $GO2[$t] != "Neve" && strpos($AW2[$t], "rovesci") === false && strpos($AW2[$t], "temporali") === false) {
				return "\xF0\x9F\x92\xA8";
			}
			elseif ($GO2[$t]=="Sereno" && ($t <= $sunriseHour || $t > $sunsetHour)) {
				return "\xF0\x9F\x8C\x99";
			}
			else { return json_encode(str_replace("\\\\","\\",$GO2_EMO[$t])); }
		}
	}

   /* FUNZIONE BETA PER COPIARE I DATABASE
   function copyDB() {
   	$conn = new mysqli("sql7.freemysqlhosting.net:3306/sql7243921", "sql7243921", "4ezgelH6xq", "sql7243921");
   	$connS1 = new mysqli("sql7.freemysqlhosting.net:3306/sql7243921", "sql7243921", "4ezgelH6xq", "sql7243921");
   	$connS2 = new mysqli("sql7.freemysqlhosting.net:3306/sql7243921", "sql7243921", "4ezgelH6xq", "sql7243921");

		$q1 = mysqli_query($conn, "
    		SELECT *
    		FROM   tasks"
    	);

		$tmp = array();
		while($val = mysqli_fetch_assoc($q1))
    		$tmp[] = $val['id'];

		mysqli_query($connS1, "
   	zona	INSERT INTO	tasks
   		VALUES (".implode(', ', $tmp).")"
   	);
   }
   */
?>