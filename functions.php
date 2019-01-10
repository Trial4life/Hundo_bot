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
											$data = [
		  										'chat_id' => $chatId,
		  										'text' => $EMO_ERR.' Entrata \'computeDistance\' '.$EMO_ERR . "\nError: ". json_encode(error_get_last()),
											];
											$response = file_get_contents("https://api.telegram.org/bot$apiToken/sendMessage?" . http_build_query($data) );

   	$latA = $latA*(3.14/180);
		$lngA = $lngA*(3.14/180);
		$latB = $latB*(3.14/180);
		$lngB = $lngB*(3.14/180);

											$data = [
		  										'chat_id' => $chatId,
		  										'text' => $EMO_ERR.' Prima di `bcsub` '.$EMO_ERR . "\nError: ". json_encode(error_get_last()),
											];
											$response = file_get_contents("https://api.telegram.org/bot$apiToken/sendMessage?" . http_build_query($data) );

		$subBA   = bcsub ($lngB, $lngA, 20);

											$data = [
		  										'chat_id' => $chatId,
		  										'text' => $EMO_ERR.' Dopo di `bcsub`, prima di `cos`/`sin` '.$EMO_ERR . "\nError: ". json_encode(error_get_last()),
											];
											$response = file_get_contents("https://api.telegram.org/bot$apiToken/sendMessage?" . http_build_query($data) );
		$cosLatA = cos($latA);
		$cosLatB = cos($latB);
		$sinLatA = sin($latA);
		$sinLatB = sin($latB);

											$data = [
		  										'chat_id' => $chatId,
		  										'text' => $EMO_ERR.' Dopo di `cos`/`sin`, prima di `distance` '.$EMO_ERR . "\nError: ". json_encode(error_get_last()),
											];
											$response = file_get_contents("https://api.telegram.org/bot$apiToken/sendMessage?" . http_build_query($data) );

		$distance = 6371*acos($cosLatA*$cosLatB*cos($subBA)+$sinLatA*$sinLatB);

											$data = [
		  										'chat_id' => $chatId,
		  										'text' => $EMO_ERR.' Uscita funzione '.$EMO_ERR . "\nError: ". json_encode(error_get_last()),
											];
											$response = file_get_contents("https://api.telegram.org/bot$apiToken/sendMessage?" . http_build_query($data) );
		return $distance;
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