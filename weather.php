<?php
    date_default_timezone_set('Europe/Rome');

	// Cells //
	$cellCode = array("roma_c","roma_e","roma_ne","roma_n","roma_no","roma_o","roma_so","roma_s","roma_se",);
	$cellTitle = array("Centro","Est","Nord-Est","Nord","Nord-Ovest","Ovest","Sud-Ovest","Sud","Sud-Est",);

	// Create connection
    $conn = new mysqli("db4free.net", "trial4life", "16021993", "tradepkmn");
    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

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

	//close the mySQL connection
	$conn->close();

	$currentTime = date("H:i");
	$cT = intval(substr($currentTime, 0, -3));
	$sunriseHour = intval(substr(date_sunrise(time(), SUNFUNCS_RET_STRING, 41.893056, 12.482778, 90, 2),0,-2));
	$sunsetHour = intval(substr(date_sunset(time(), SUNFUNCS_RET_STRING, 41.893056, 12.482778, 90, 2),0,-2));

	include_once $_SERVER['DOCUMENT_ROOT'] . "/getWeather.php";

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
?>

<!DOCTYPE html>

<html prefix="og: http://ogp.me/ns#">
<head>
	<title> Roma - <?php echo $cellTitle[$cell] ?> </title>
  	<link href="styles.css" media="screen and (min-device-width: 1012px)" rel="stylesheet" type="text/css">
  	<link href="styles_mobile.css" media="screen and (max-device-width: 1011px)" rel="stylesheet" type="text/css">
  	<link rel="icon" href="img/icon.ico">
  	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

  	<meta property="og:url" content="http://pogocasts.com/roma.php?cell=<?php echo $cell ?>"/>
    <meta property="og:title" content="pogocasts Roma <?php echo $cellTitle[$cell] ?>"/>
    <meta property="og:image" content="http://pogocasts.com/GO/<?php echo $curW_1 ?>.png"/>
    <meta property="og:description" content="Sito web dedicato alle previsioni meteo di Pokémon GO. Cella: Roma <?php echo $cellTitle[$cell] ?>" />
    <meta property="og:type" content="article" />

  	<!--<meta name="viewport" content="width=device-width, initial-scale=1.0">-->

<!-- Global site tag (gtag.js) - Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=UA-80287193-2"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'UA-80287193-2');
</script>

</head>

<body>
	<?php include_once $_SERVER['DOCUMENT_ROOT'] . "/nav.html"; ?>
	<center>
		<a href="index.php" title="Home"> <img src=<?php echo "Cells/".$cellCode[$cell].".png" ?> style="display: inline-block;margin-top:30px;"> </a>  <br>
	    <!--<a class="nav" href="index.php" title="Home"> « Mappa </a> <br>-->
		<h1> Roma − <?php echo $cellTitle[$cell] ?> </h1>
		<p> <?php
				$today = getdate();
				print_r($today['mday'] . "/" . sprintf("%02d", $today['mon']) . "/" . $today['year']);
				if ($updateDate[0] != date("Y-m-d")) {
			    	echo "<br>";
			    	echo "last update: ".date("d\/m\/Y",strtotime($updateDate[0]));
				}
				?>
		</p>


		<p class="centerY" style="font-weight: bold;"> Adesso: <?php
			if ($curW_1 == $curW_2) { echo "<img src='GO/".$curW_1.".png'>"; }
			elseif ($curW_1 != $curW_2 and $curW_2 == "x") { echo "<img src='GO/".$curW_1.".png'>"; }
			elseif ($curW_1 != $curW_2 and $curW_1 == "x") { echo "<img src='GO/".$curW_2.".png'>"; }
			elseif ($curW_1 != $curW_2) { echo "<img id='FC1' src='GO/".$curW_1.".png'><img id='FC2' src='GO/".$curW_2.".png'>"; };
		?>
		<img id="arrow" src="img/arrow.png"><img id="arrow" src="img/arrow.png">
		Dopo: <?php
			if ($nextW_1 == $nextW_2) { echo "<img src='GO/".$nextW_1.".png'>"; }
			elseif ($nextW_1 != $nextW_2 and $nextW_2 == "x") { echo "<img src='GO/".$nextW_1.".png'>"; }
			elseif ($nextW_1 != $nextW_2 and $nextW_1 == "x") { echo "<img src='GO/".$nextW_2.".png'>"; }
			elseif ($nextW_1 != $nextW_2) { echo "<img id='FC1' src='GO/".$nextW_1.".png'><img id='FC2' src='GO/".$nextW_2.".png'>"; };
		?>
		</p>

		<table class="tab">
			<tr>
				<th> Orario </th>
				<th> OLD </th>
				<th> NEW </th>
			</tr>
			<?php
				for ($i = 0; $i <= 23; $i++) {
					$num = sprintf("%02d", $i);

						echo "			<tr>
						<td>" . $num . ":00</td>
						<td><img src='GO/".getWeather(1,$i).".png'></td>
						<td><img src='GO/".getWeather(2,$i).".png'></td>
						</tr>";

				}; ?>
			</tr>
		</table>
		<br><br>

		<?php include_once $_SERVER['DOCUMENT_ROOT'] . "/contacts.html"; ?>
	</center>

</body>
</html>