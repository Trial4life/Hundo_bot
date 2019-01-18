<?php
   date_default_timezone_set('Europe/Rome');

	// Cells //
	$cellCode = array("roma_c","roma_e","roma_ne","roma_n","roma_no","roma_o","roma_so","roma_s","roma_se",);
	$cellTitle = array("Centro","Est","Nord-Est","Nord","Nord-Ovest","Ovest","Sud-Ovest","Sud","Sud-Est",);

/* È GIÀ CONNESSO DAL MAIN
	// Create connection
   $conn = new mysqli("db4free.net", "trial4life", "16021993", "tradepkmn");
   // Check connection
   if ($conn->connect_error) {
       die("Connection failed: " . $conn->connect_error);
   }
*/
	$AW1 = $AW2 = $wind_1 = $wind_2 = $gust_1 = $gust_2 = $GO1 = $GO2 = $GO1_EMO[] = $GO2_EMO[] = array();

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
		$GO1_EMO[] = $row2['EMO'];

		$query3 = "SELECT * FROM `conversion` WHERE `AW` = '$AW2_value'";
		$result3 = mysqli_query($conn,$query3);
		$row3 = mysqli_fetch_array($result3);
		$GO2[] = str_replace(" ", "_", $row3['GO']);
		$GO2_EMO[] = $row3['EMO'];
	}

	$updateDateResult = mysqli_query($conn,"SELECT update_time
														FROM   update_times
														WHERE  cell = '$cellCode[$cell]'");
	$updateDate = mysqli_fetch_array($updateDateResult);
/*
	//close the mySQL connection
	$conn->close();
*/

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
	$sunriseHour = intval(substr(date_sunrise(time(), SUNFUNCS_RET_STRING, 41.893056, 12.482778, 90, 2),0,-2));
	$sunsetHour = intval(substr(date_sunset(time(), SUNFUNCS_RET_STRING, 41.893056, 12.482778, 90, 2),0,-2));

	$response = $cellTitle[$cell];

	for ($i = 0; $i <= 23; $i++) {
		$response = $response. "\n".$i. getWeather(1,$i). " | " .getWeather(2,$i);
	};

?>