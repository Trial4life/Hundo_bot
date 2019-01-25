<?php
   date_default_timezone_set('Europe/Rome');		// Nel main è dichiarato "London", qui serve "Rome" per gli orari di alba e tramonto
	$sunriseHour = intval(substr(date_sunrise(time(), SUNFUNCS_RET_STRING, 41.893056, 12.482778, 90, 2),0,-2));
	$sunsetHour = intval(substr(date_sunset(time(), SUNFUNCS_RET_STRING, 41.893056, 12.482778, 90, 2),0,-2));
	$now = date('G');

	// Cells //
	$cell = $callbackData;
	$cellCode = array("roma_c","roma_e","roma_ne","roma_n","roma_no","roma_o","roma_so","roma_s","roma_se",);
	$cellTitle = array("Centro","Est","Nord-Est","Nord","Nord-Ovest","Ovest","Sud-Ovest","Sud","Sud-Est",);
	$cellId = array('132f61','132f63','132f65','132f67','132f5d','132f5f','1325f5','13258b','132589');

	$AW1 = $AW2 = $wind_1 = $wind_2 = $gust_1 = $gust_2 = $GO1 = $GO2 = array();

	$query = "SELECT * FROM `$cellCode[$cell]`";
	$result = mysqli_query($conn,$query);

	if (!$result) {
	   die(mysqli_error());
	}

	while($row =  mysqli_fetch_assoc($result)) {
   	$AW1[] = str_replace(" ", "_", $row['AW1']);
		$AW2[] = str_replace(" ", "_", $row['AW2']);
		$wind_1[] = $row['wind_1'];
		$wind_2[] = $row['wind_2'];
		$gust_1[] = $row['gust_1'];
		$gust_2[] = $row['gust_2'];
		$AW1_value = $row['AW1'];
		$AW2_value = $row['AW2'];

		$query2 = "SELECT * FROM `conversion` WHERE `AW` = '$AW1_value'";
		$result2 = mysqli_query($conn,$query2);
		$row2 = mysqli_fetch_array($result2);
		$GO1[] = str_replace(" ", "_", $row2['GO']);

		$query3 = "SELECT * FROM `conversion` WHERE `AW` = '$AW2_value'";
		$result3 = mysqli_query($conn,$query3);
		$row3 = mysqli_fetch_array($result3);
		$GO2[] = str_replace(" ", "_", $row3['GO']);
	}

	$updateDateResult = mysqli_query($conn,"SELECT update_time
														 FROM   update_times
														 WHERE  cell = '$cellCode[$cell]'");
	$updateDate = mysqli_fetch_array($updateDateResult);

	$newFCresult = mysqli_query($conn,"SELECT *
												  FROM   update_times
												  WHERE  cell = 'NEW_FC'");
	$newFC = mysqli_fetch_array($newFCresult);
	$newFC['update_time'] == '0' ? $FCstatus = " | " : $FCstatus = " ¦ ";

// Current time
/*
	$currentTime = date("H:i");
	$cT = intval(substr($currentTime, 0, -3));

	if ($cT == 23) {
		if ($GO1[$cT] == "x" and $GO2[$cT] != "x") {
   	  	$curW_1 = "x";
			$curW_2 = getWeather(2,$cT);
   	  	$nextW_1 = "x";
			$nextW_2 = "x";
		}
		elseif ($GO1[$cT] != "x" and $GO2[$cT] == "x") {
   	  	$curW_2 = "x";
			$curW_1 = getWeather(1,$cT);
   	  	$nextW_1 = "x";
			$nextW_2 = "x";
		}
		else {
			$curW_1 = getWeather(1,$cT);
			$curW_2 = getWeather(2,$cT);
			$nextW_1 = "x";
			$nextW_2 = "x";
		}
	}
	else {
		if ($GO1[$cT] == "x" and $GO2[$cT] != "x") {
   	  	$curW_1 = "x";
			$curW_2 = getWeather(2,$cT);
   	  	$nextW_1 = getWeather(1,$cT+1);
			$nextW_2 = getWeather(2,$cT+1);
		}
		elseif ($GO1[$cT] != "x" and $GO2[$cT] == "x") {
   	  	$curW_2 = "x";
			$curW_1 = getWeather(1,$cT);
   	  	$nextW_1 = getWeather(1,$cT+1);
			$nextW_2 = getWeather(2,$cT+1);
		}
		else {
			$curW_1 = getWeather(1,$cT);
			$curW_2 = getWeather(2,$cT);
			$nextW_1 = getWeather(1,$cT+1);
			$nextW_2 = getWeather(2,$cT+1);
		}
	}
*/

	$link = "https://s2.sidewalklabs.com/regioncoverer/?center=41.891165%2C12.492826&zoom=12&cells=".$cellId[$cell];
	$response = "Previsioni meteo per la cella \n<a href='".$link."'>Roma ".$cellTitle[$cell]."</a>:";
	for ($i = $now; $i <= $now+12; $i++) {
		$i < 24 ? $n = $i : $n = $i % 24;
		$h = strval(sprintf('%02d',$n));
		$response = $response. "\n<code>".$h.":00</code> − ". getWeather(1,$n). $FCstatus .getWeather(2,$n).substr(date_sunrise(time(), SUNFUNCS_RET_STRING, 41.893056, 12.482778, 90, 2),0,-2).substr(date_sunset(time(), SUNFUNCS_RET_STRING, 41.893056, 12.482778, 90, 2),0,-2);
	};
?>