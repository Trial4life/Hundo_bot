<?php
spl_autoload_register(
    function ($class) {
        $path = __DIR__ . "/classes/$class.php";
        if (file_exists($path)) require_once $path;
    }
);

date_default_timezone_set('Europe/Rome');
$content = file_get_contents("php://input");
$update = json_decode($content, true);

if(!$update)
{
  exit;
}

$message = isset($update['message']) ? $update['message'] : "";
$messageId = isset($message['message_id']) ? $message['message_id'] : "";
$chatId = isset($message['chat']['id']) ? $message['chat']['id'] : "";
$chatType = isset($message['chat']['type']) ? $message['chat']['type'] : "";
$userId = isset($message['from']['id']) ? $message['from']['id'] : "";
$firstname = isset($message['from']['first_name']) ? $message['from']['first_name'] : "";
$lastname = isset($message['chat']['last_name']) ? $message['chat']['last_name'] : "";
$username = isset($message['from']['username']) ? $message['from']['username'] : "";
$date = isset($message['date']) ? $message['date'] : "";
$text = isset($message['text']) ? $message['text'] : "";
$text = trim($text);
$URLs = isset($message['entities']) ? $message['entities'] : "";
$reply = isset($message['reply_to_message']['text']) ? $message['reply_to_message']['text'] : "";
$lat = isset($message['location']['latitude']) ? $message['location']['latitude'] : NULL;
$lng = isset($message['location']['longitude']) ? $message['location']['longitude'] : NULL;
$today = date('Y-m-d');
$today2 = date('d/m/y');

header("Content-Type: application/json");
$response = '';
$apiToken = "689487990:AAGhqhcsalt0mXYRnUqFro9ECNxPuOOVPZc";
$channel = '@PokeradarRoma';

// AUTORIZZAZIONI
include $_SERVER['DOCUMENT_ROOT'] . "/authorizations.php";
// FUNZIONI
include $_SERVER['DOCUMENT_ROOT'] . "/functions.php";
// EMOJIS
$EMO_100 = "\xF0\x9F\x92\xAF";
$EMO_PIN = "\xF0\x9F\x93\x8C";
$EMO_zZz = "\xF0\x9F\x92\xA4";
$EMO_GLO = "\xF0\x9F\x8C\x90";
$EMO_EXE = "\xF0\x9F\x8C\xB4";
$EMO_TRI = "\xE2\x96\xB6";
$EMO_ON = "\xF0\x9F\x94\x94";
$EMO_OFF = "\xF0\x9F\x94\x95";
$EMO_v = json_decode('"'."\u2705".'"');
$EMO_x = json_decode('"'."\u274c".'"');
$EMO_ALR = json_decode('"'."\u203c".'"');
$EMO_ERR = json_decode('"'."\u26d4".'"');

// MySQL -> Create connection
$conn = new mysqli("db4free.net", "trial4life", "16021993", "tradepkmn");
// $conn = new mysqli("sql7.freemysqlhosting.net:3306/sql7243921", "sql7243921", "4ezgelH6xq", "sql7243921");   [OLD freemysqlhosting account]
// Check connection

// CONTROLLA SESSIONE UTENTE
$query = "SELECT * FROM `sessions` WHERE `userID` = $userId";
$result = mysqli_query($conn,$query);
if(!$result) {
	$status = 0;
}
else {
	$row = mysqli_fetch_assoc($result);
	$status = $row['status'];
	$alert = $row['alert'];
}


//_________________________//
//________ COMANDI ________//
//_________________________//

/*
if(strpos($text, "/start") === 0 ) {
	$data = [
	   'chat_id' => $userId,
	   'text' => 'Bot usato per segnalare le quest e gli avvistamenti di IV alti, che verranno pubblicati nel canale @CentoPoGO.',
	];
	$response = file_get_contents("https://api.telegram.org/bot$apiToken/sendMessage?" . http_build_query($data) );
}
*/


if(strpos($text, "/exeggutorhelp") === 0 ) {
	$data = [
	   'chat_id' => $chatId,
	   'text' => $EMO_TRI . " *Per segnalare un pokémon selvatico*, utilizzare il comando `/100 <segnalazione>` (sia in chat privata, sia nel gruppo) e, dopo aver inviato il comando, condivedere la posizione dell'avvistamento direttamente tramite telegram.\n\n_Esempio:_\n`/100 Dratini 100% appena spawnato al Pincio`\n\n" . $EMO_TRI . " *Per segnalare una quest*, utilizzare in chat privata con il bot il comando `/quest <inserire-la-ricompensa>` e, dopo aver inviato il comando, condividere la posizione del pokéstop tramite @ingressportalbot (da utilizzare in modalità inline: dopo aver digitato `@ingressportalbot`, iniziare a digitare il nome del portale e selezionarlo dal menu a tendina una volta comparso - si consiglia di condividere la posizione con il bot in modo da rilevare i portali più vicini).\n\n_Esempio:_\n`/quest Dratini`\n`/quest 1 Caramella Rara, vinci 3 sfide`\n\n". $EMO_TRI . " *Per attivare/disattivare le notifiche per una quest* utilizzare i comandi `/addalert <inserire-la-ricompensa>` e `/delalert <inserire-la-ricompensa>` \n\n_Esempio_:\n`/addalert Larvitar`\n`/delalert Dratini`\n\n" .$EMO_TRI ." *Per mostrare le notifiche attive*, utilizzare il comando `/alerts`.",
	   'parse_mode' => 'markdown',
	];
	$response = file_get_contents("https://api.telegram.org/bot$apiToken/sendMessage?" . http_build_query($data) );
}

/*
elseif(strpos($text, "/debug") === 0 ) {
	$response = PHP_INT_SIZE;
	$parameters = array('chat_id' => $chatId, "text" => $response, "parse_mode" => "markdown");
	$parameters["method"] = "sendMessage";
	echo json_encode($parameters);
}
*/

elseif(strpos($text, "/annulla") === 0 ) {
	if ($status == 0) {
		$data = [
		   'chat_id' => $chatId,
		   'text' => "Non c'è nessuna segnalazione in corso.",
		];
		$response = file_get_contents("https://api.telegram.org/bot$apiToken/sendMessage?" . http_build_query($data) );
	}
	else {
		mysqli_query($conn,"DELETE FROM `sessions` WHERE userID = $userId");
		$data = [
		   'chat_id' => $chatId,
		   'text' => $EMO_x.' Segnalazione annullata.',
		];
		$response = file_get_contents("https://api.telegram.org/bot$apiToken/sendMessage?" . http_build_query($data) );
	}
}

//////////////
//// 100% ////
//////////////
elseif($status == 0 and strpos($text, "/100 ") === 0 ) {
	if (!in_array($username, $bannedUsers)) {
		if(isset($message['reply_to_message']['text']))	{
			$data = [
   	 		'chat_id' => $chatId,
   	 		'text' => $EMO_PIN.' Mandami la posizione di *'.$reply.'*.',
   	 		'parse_mode' => 'markdown',
			];
			$response = file_get_contents("https://api.telegram.org/bot$apiToken/sendMessage?" . http_build_query($data) );
			mysqli_query($conn,"INSERT INTO `sessions` (userID, username, status, alert) VALUES ($userId, '$username', 1, '$reply')");
		}
		else {
			$text = str_replace('/100', '', $text);
			$data = [
   		 	'chat_id' => $chatId,
   		 	'text' => $EMO_PIN.' Mandami la posizione di*'.$text.'*.',
   	 		'parse_mode' => 'markdown',
   		];
			$response = file_get_contents("https://api.telegram.org/bot$apiToken/sendMessage?" . http_build_query($data) );
			mysqli_query($conn,"INSERT INTO `sessions` (userID, username, status, alert) VALUES ($userId, '$username', 1, '$text')");
		}
	}
	else {
		$data = [
   	 	'chat_id' => $chatId,
   	 	'text' => $EMO_ERR.' Non sei autorizzato alle segnalazioni. Contatta un admin. '.$EMO_ERR,
		];
		$response = file_get_contents("https://api.telegram.org/bot$apiToken/sendMessage?" . http_build_query($data) );
	}
}

elseif($status == 1) {
	if (!$lat or !$lng) {
		list($pkst, $lat, $lng) = getPortalData($text, $URLs[1]['url']);
	}

	if (!$lat or !$lng)
	{
		$data = [
	   	'chat_id' => $chatId,
	   	'text' => $EMO_PIN.' Ho bisogno della posizione per inoltrare la segnalazione.',
		];
		$response = file_get_contents("https://api.telegram.org/bot$apiToken/sendMessage?" . http_build_query($data) );
	}
	else {
		$data = [
	   	'chat_id' => $channel,
	   	'text' => $EMO_ALR."*".$alert."* ".$EMO_ALR,
	   	'parse_mode' => 'markdown',
		];
		$location = [
	    	'chat_id' => $channel,
	   	 'latitude' => $lat,
	   	 'longitude' => $lng,
		];
		$response1 = file_get_contents("https://api.telegram.org/bot$apiToken/sendMessage?" . http_build_query($data) );
		$response2 = file_get_contents("https://api.telegram.org/bot$apiToken/sendlocation?" . http_build_query($location) );
		mysqli_query($conn,"DELETE FROM `sessions` WHERE userID = $userId");
	}
}

// IN CASO DI ERRORE DI CONNESSIONE CON IL DATABASE

elseif ($conn->connect_error and (strpos($text, "/annulla") === 0 or strpos($text, "/cancella") === 0 or strpos($text, "/quests") === 0 or strpos($text, "/quest") === 0 or strpos($text, "/100") === 0 or strpos($text, "/mappaquest") === 0)) {
	$response = $EMO_zZz . " Database temporaneamente offline, riprova più tardi.";
	$parameters = array('chat_id' => $chatId, "text" => $response, "parse_mode" => "markdown");
	$parameters["method"] = "sendMessage";
	echo json_encode($parameters);
}

elseif(strpos($text, "/cancella") === 0 ) {
	if (in_array($username, $admins)) {
		$text = str_replace('/cancella ', '', str_replace("'","\'",$text));
		$query = "SELECT * FROM `quests` WHERE `pokestop` = '$text'";
		$result = mysqli_query($conn,$query);
		$row = mysqli_fetch_assoc($result);
		if(!$row) {
			$data = [
		   	'chat_id' => $chatId,
		   	'text' => $EMO_ERR . ' Pokéstop non trovato.',
			];
			$response = file_get_contents("https://api.telegram.org/bot$apiToken/sendMessage?" . http_build_query($data) );
		}
		else {
			mysqli_query($conn,"DELETE FROM `quests` WHERE pokestop = '$text'");
			$data = [
			   'chat_id' => $chatId,
			   'text' => $EMO_x.' Quest cancellata.',
			];
			$response = file_get_contents("https://api.telegram.org/bot$apiToken/sendMessage?" . http_build_query($data) );
		}
	}
	else {
		$data = [
		  	'chat_id' => $chatId,
		  	'text' => $EMO_ERR.'Solo gli admin possono utilizzare questo comando. '.$EMO_ERR,
		];
		$response = file_get_contents("https://api.telegram.org/bot$apiToken/sendMessage?" . http_build_query($data) );
	}
}

elseif(strpos($text, "/termina") === 0 ) {
	if (in_array($username, $admins)) {
		$user = str_replace("/termina ", "", $text);
		$query = "SELECT * FROM `sessions` WHERE username = '$user'";
		$result = mysqli_query($conn,$query);
		$row = mysqli_fetch_assoc($result);
		if(!$row) {
			$data = [
			   'chat_id' => $chatId,
			   'text' => "Non c'è nessuna segnalazione in corso.",
			];
			$response = file_get_contents("https://api.telegram.org/bot$apiToken/sendMessage?" . http_build_query($data) );
		}
		else {
			mysqli_query($conn,"DELETE FROM `sessions` WHERE username = '$user'");
			$data = [
			   'chat_id' => $chatId,
			   'text' => $EMO_x.' Segnalazione annullata.',
			];
			$response = file_get_contents("https://api.telegram.org/bot$apiToken/sendMessage?" . http_build_query($data) );
		}
	}
	else {
		$data = [
		  	'chat_id' => $chatId,
		  	'text' => $EMO_ERR.'Solo gli admin possono utilizzare questo comando. '.$EMO_ERR,
		];
		$response = file_get_contents("https://api.telegram.org/bot$apiToken/sendMessage?" . http_build_query($data) );
	}
}

elseif(strpos($text, "/quests ") === 0 ) {
	$zona = ucfirst(str_replace('/quests ', '', $text));
	// ELENCO QUESTS
	mysqli_query($conn,"DELETE FROM `quests` WHERE giorno < '$today'");  // RIMUOVE LE QUEST DEL GIORNO PRECEDENTE
	$query = "SELECT * FROM `quests` WHERE INSTR(`zona`,'$zona')>0 ORDER BY quest ASC";
	$result_quest = mysqli_query($conn,$query);
	$quest = $pokestop = $lat = $lng = array();
	while ($row = mysqli_fetch_assoc($result_quest)) {
		array_push($quest, $row['quest']);
		array_push($pokestop, $row['pokestop']);
		//$curr_pkst = end($pokestop);
		//$query = "SELECT * FROM `pokestops` WHERE pokestop = '$curr_pkst'";
		//$result_pkst = mysqli_query($conn,$query);
		//$row2 = mysqli_fetch_assoc($result_pkst);
		array_push($lat, $row['lat']);
		array_push($lng, $row['lng']);
	}

	if (sizeof($quest)==0) {
		$response = 'Non è stata segnalata nessuna quest nella zona *'.$zona.'* per oggi.';
		$parameters = array('chat_id' => $chatId, "text" => $response, "parse_mode" => "markdown", "disable_web_page_preview" => TRUE);
		$parameters["method"] = "sendMessage";
		echo json_encode($parameters);
	}
	else {
		$response = 'Elenco delle quest nella zona *'.$zona.'*:';
		for ($i = 0; $i <= sizeof($quest)-1; $i++){
			$link = 'https://maps.google.com/?q='.$lat[$i].','.$lng[$i];
			$response = $response . "\n*" . ucfirst($quest[$i]) . "* − [" . $pokestop[$i] . "](" . $link . ")";
		}

		$parameters = array('chat_id' => $chatId, "text" => $response, "parse_mode" => "markdown", "disable_web_page_preview" => TRUE);
		$parameters["method"] = "sendMessage";
		echo json_encode($parameters);
	}
}

elseif(strpos($text, "/quests") === 0 ) {
	// ELENCO QUESTS
	mysqli_query($conn,"DELETE FROM `quests` WHERE giorno < '$today'");  // RIMUOVE LE QUEST DEL GIORNO PRECEDENTE
	$query = "SELECT * FROM `quests` ORDER BY quest ASC";
	$result_quest = mysqli_query($conn,$query);
	$quest = $pokestop = $lat = $lng = array();
	while ($row = mysqli_fetch_assoc($result_quest)) {
		array_push($quest, $row['quest']);
		array_push($pokestop, $row['pokestop']);
		//$curr_pkst = end($pokestop);
		//$query = "SELECT * FROM `pokestops` WHERE pokestop = '$curr_pkst'";
		//$result_pkst = mysqli_query($conn,$query);
		//$row2 = mysqli_fetch_assoc($result_pkst);
		array_push($lat, $row['lat']);
		array_push($lng, $row['lng']);
	}

	if (sizeof($quest)==0) {
		$response = 'Non è stata segnalata nessuna quest per oggi.';
		$parameters = array('chat_id' => $chatId, "text" => $response, "parse_mode" => "markdown", "disable_web_page_preview" => TRUE);
		$parameters["method"] = "sendMessage";
		echo json_encode($parameters);
	}
	else {
		$response = 'Elenco delle quest di oggi:';
		for ($i = 0; $i <= sizeof($quest)-1; $i++){
			$link = 'https://maps.google.com/?q='.$lat[$i].','.$lng[$i];
			$response = $response . "\n*" . ucfirst($quest[$i]) . "* − [" . $pokestop[$i] . "](" . $link . ")";
		}

		$parameters = array('chat_id' => $chatId, "text" => $response, "parse_mode" => "markdown", "disable_web_page_preview" => TRUE);
		$parameters["method"] = "sendMessage";
		echo json_encode($parameters);
	}
}

elseif(strpos($text, "/mappaquest") === 0 ) {
	$response = $EMO_GLO . ' Mappa delle quest ' . $EMO_GLO;
	$link = 'http://pogocasts.com/questmap/questmap.php';
	$response = "[" . $response . "](" . $link . ")";
	$parameters = array('chat_id' => $chatId, "text" => $response, "parse_mode" => "markdown", "disable_web_page_preview" => TRUE);
	$parameters["method"] = "sendMessage";
	echo json_encode($parameters);
}

elseif($status == 0) {
	///////////////
	//// QUEST ////
	///////////////
	if(strpos($text, "/quest ") === 0 )	{
		//if (in_array($chatId, $authorizedChats)) {
		if ($chatType == 'group' or $chatType == 'supergroup') {
			$response = $EMO_EXE." Per segnalare le quest, utilizza il comando /quest in chat privata con il bot.";
			$parameters = array('chat_id' => $chatId, "text" => $response, "parse_mode" => "markdown",);
			$parameters["method"] = "sendMessage";
			echo json_encode($parameters);
		}
		else {
			if (!in_array($username, $bannedUsers)) {
				$quest = ucfirst(str_replace('/quest ', '', $text));
				$data = [
		   	 	'chat_id' => $chatId,
		   	 	'text' => $EMO_PIN.' @'.$username.', mandami la posizione della quest *'.$quest.'* tramite @ingressportalbot.',
		   	 	'parse_mode' => 'markdown',
				];
				$response = file_get_contents("https://api.telegram.org/bot$apiToken/sendMessage?" . http_build_query($data) );
				mysqli_query($conn,"INSERT INTO `sessions` (userID, username, status, alert) VALUES ($userId, '$username', 2, '$quest')");
			}
			else {
				$data = [
		   	 	'chat_id' => $chatId,
		   	 	'text' => $EMO_ERR.' Non sei autorizzato alle segnalazioni. Contatta un admin. '.$EMO_ERR,
				];
				$response = file_get_contents("https://api.telegram.org/bot$apiToken/sendMessage?" . http_build_query($data) );
			}
		}
	}

	/////////////////
	/// NEW QUEST ///
	/////////////////
	elseif(strpos($text, "/newquest") === 0 )	{
		if (in_array($username, $admins)) {
			$str = explode(', ', str_replace('/newquest ', '', $text));
			$reward = ucfirst($str[0]);
			$task = ucfirst($str[1]);
			$flag = $str[2];
			mysqli_query($conn,"INSERT INTO `tasks` (reward, task, flag) VALUES ('$reward', '$task', 1)");

			$response = $EMO_v.' Quest aggiunta.';
			$parameters = array('chat_id' => $chatId, "text" => $response, "parse_mode" => "markdown", "disable_web_page_preview" => TRUE);
			$parameters["method"] = "sendMessage";
			echo json_encode($parameters);


		}
		else {
			$data = [
		  		'chat_id' => $chatId,
		  		'text' => $EMO_ERR.'Solo gli admin possono utilizzare questo comando. '.$EMO_ERR,
			];
			$response = file_get_contents("https://api.telegram.org/bot$apiToken/sendMessage?" . http_build_query($data) );
		}
	}

	/////////////////
	/// DEL QUEST ///
	/////////////////
	elseif(strpos($text, "/delquest") === 0 )	{
		if (in_array($username, $admins)) {
			$reward = str_replace('/delquest ', '', $text);
			mysqli_query($conn,"DELETE FROM `tasks` WHERE reward = '$reward'");

			$response = $EMO_x.' Quest eliminata.';
			$parameters = array('chat_id' => $chatId, "text" => $response, "parse_mode" => "markdown", "disable_web_page_preview" => TRUE);
			$parameters["method"] = "sendMessage";
			echo json_encode($parameters);
		}
		else {
			$data = [
		  		'chat_id' => $chatId,
		  		'text' => $EMO_ERR.'Solo gli admin possono utilizzare questo comando. '.$EMO_ERR,
			];
			$response = file_get_contents("https://api.telegram.org/bot$apiToken/sendMessage?" . http_build_query($data) );
		}
	}

	/////////////////
	/// QUESTLIST ///
	/////////////////
	elseif(strpos($text, "/listquests") === 0 )	{
		if (in_array($username, $admins)) {
			$query = "SELECT * FROM `tasks`";
			$result = mysqli_query($conn,$query);
			$reward = $task = array();
			while ($row = mysqli_fetch_assoc($result)) {
				array_push($reward, $row['reward']);
				array_push($task, $row['task']);
			}

			$response = "Lista delle quest con notifica:";
			for ($i = 0; $i <= sizeof($reward)-1; $i++){
				$response = $response."\n*".$reward[$i]."* − ".$task[$i];
			}

			$parameters = array('chat_id' => $chatId, "text" => $response, "parse_mode" => "markdown", "disable_web_page_preview" => TRUE);
			$parameters["method"] = "sendMessage";
			echo json_encode($parameters);
		}
		else {
			$data = [
		  		'chat_id' => $chatId,
		  		'text' => $EMO_ERR.'Solo gli admin possono utilizzare questo comando. '.$EMO_ERR,
			];
			$response = file_get_contents("https://api.telegram.org/bot$apiToken/sendMessage?" . http_build_query($data) );
		}
	}

	/////////////////
	/// ADD ALERT ///
	/////////////////
	elseif(strpos($text, "/addalert") === 0 ) {
		$quest = ucfirst(str_replace('/addalert ', '', $text));
		$query = "SELECT * FROM `pokeid` WHERE pokemon = '$quest'";
		$result = mysqli_query($conn,$query);
		$row = mysqli_fetch_assoc($result);
		$currUserAlerts = $row['userAlerts'];
		if (!$row) {
			$response = $EMO_ERR.' Quest *'.$quest.'* non trovata.';
		}
		else {
			if (stristr($currUserAlerts,strval($userId))) {
				$response = 'Le notifiche per le quest *'.$quest.'* sono già attive.';
			}
			else {
				$response = $EMO_ON.' Notifiche per le quest *'.$quest.'* attivate.';
				mysqli_query($conn,"UPDATE `pokeid` SET userAlerts = concat('$currUserAlerts', '$userId', ',') WHERE pokemon = '$quest'");
			}
		}
		$parameters = array('chat_id' => $chatId, "text" => $response, "parse_mode" => "markdown", "disable_web_page_preview" => TRUE);
		$parameters["method"] = "sendMessage";
		echo json_encode($parameters);
	}

	/////////////////
	/// DEL ALERT ///
	/////////////////
	elseif(strpos($text, "/delalert") === 0 ) {
		$quest = ucfirst(str_replace('/delalert ', '', $text));
		$query = "SELECT * FROM `pokeid` WHERE pokemon = '$quest'";
		$result = mysqli_query($conn,$query);
		$row = mysqli_fetch_assoc($result);
		$currUserAlerts = $row['userAlerts'];
		if (!$row) {
			$response = $EMO_ERR.' Quest *'.$quest.'* non trovata.';
		}
		else {
			$response = $EMO_OFF.' Notifiche per le quest *'.$quest.'* disattivate.';
		}
		$parameters = array('chat_id' => $chatId, "text" => $response, "parse_mode" => "markdown", "disable_web_page_preview" => TRUE);
		$parameters["method"] = "sendMessage";
		echo json_encode($parameters);
		mysqli_query($conn,"UPDATE `pokeid` SET userAlerts = replace('$currUserAlerts',concat('$userId', ','), '') WHERE pokemon = '$quest'");
	}

	/////////////////
	/// DEL ALERT ///
	/////////////////
	elseif(strpos($text, "/alerts") === 0 ) {
		// CERCA ALERTS NEL DATABASE
		$query = "SELECT * FROM `pokeid`";
		$result = mysqli_query($conn,$query);
		$alertsFound = array();
		$counter = 0;
		while ($row = mysqli_fetch_assoc($result)) {
			$curr_PkMn = $row['pokemon'];
			$usersFound = $row['userAlerts'];
			if (stristr($usersFound, strval($userId))) {
				array_push($alertsFound,$curr_PkMn);
			}
			$counter++;
			if ($counter >= 1000) {
        		break;
    		}
		}

		// INVIA MESSAGGIO
		if (!empty($alertsFound)) {
			$response = $EMO_ON." Notifiche quest attive ".$EMO_ON."\n";
			$alertsFound_num = sizeof($alertsFound);
			for ($i = 0; $i <= $alertsFound_num-1; $i++) {
				$response = $response."*".$alertsFound[$i]."*\n";
			}
		}
		else {
			$response = "Al momento non è attiva nessuna notifica per le quest.";
		}
		$parameters = array('chat_id' => $chatId, "text" => $response, "parse_mode" => "markdown");
		$parameters["method"] = "sendMessage";
		echo json_encode($parameters);
	}

	//////////////////
	/// ADD REGION ///
	//////////////////
	elseif(strpos($text, "/addregion") === 0 ) {
		$data = explode(', ', str_replace('/addregion ', '', $text));
		$cellId = $data[0];
		$name = $data[1];
		$cellId64 = $cellId . str_repeat("0",16-strlen($cellId));

		$query = "SELECT * FROM `zones` WHERE cellId = '$cellId'";
		$result = mysqli_query($conn,$query);
		$row = mysqli_fetch_assoc($result);
		if (!$row) {
			$response = $EMO_v." La cella *".$cellId."* è stata registrata come *\"".$name."\"*.";
			mysqli_query($conn,"INSERT INTO `zones` (cellId, cellId64, name) VALUES ('$cellId', '$cellId64', '$name')");
			//$response = mysqli_error($conn);
		}
		else {
			$response = $EMO_v.' La cella *'.$cellId.'* è già registrata.';
		}
		$parameters = array('chat_id' => $chatId, "text" => $response, "parse_mode" => "markdown", "disable_web_page_preview" => TRUE);
		$parameters["method"] = "sendMessage";
		echo json_encode($parameters);
	}

	//////////////////
	/// DEL REGION ///
	//////////////////
	elseif(strpos($text, "/delregion") === 0) {
		$name = str_replace('/delregion ', '', $text);

		$query = "SELECT * FROM `zones` WHERE name = '$name'";
		$result = mysqli_query($conn,$query);
		$row = mysqli_fetch_assoc($result);
		if (!$row) {
			$response = $EMO_ERR.' Cella *'.$name.'* non trovata.';
		}
		else {
			$response = $EMO_x.' La cella *'.$name.'* è stata rimossa.';
			mysqli_query($conn,"DELETE FROM `zones` WHERE name = '$name'");
		}
		$parameters = array('chat_id' => $chatId, "text" => $response, "parse_mode" => "markdown", "disable_web_page_preview" => TRUE);
		$parameters["method"] = "sendMessage";
		echo json_encode($parameters);
	}

	///////////////
	/// REGIONS ///
	///////////////
	elseif(strpos($text, "/regions") === 0) {
		$query = "SELECT * FROM `zones` ORDER BY name ASC";
		$result = mysqli_query($conn,$query);
		$cell = $name = $lat = $lng = $zoom = array();
		while ($row = mysqli_fetch_assoc($result)) {
			array_push($cell, $row['cellId']);
			array_push($name, $row['name']);
			$cellIdObj = new S2CellId(hexdec($row['cellId64']));
			$cellObj = new S2Cell($cellIdObj);
			//array_push($zoom, $cellObj->level()+2);		// scommentare quando si fixa $lat e $lng
			array_push($zoom, 12);
			array_push($lat, 41.891165  );
			array_push($lng, 12.492826  );
		}

		$link_all = "https://s2.sidewalklabs.com/regioncoverer/?center=41.891165%2C12.492826&zoom=12&cells=";
		for ($i = 0; $i <= sizeof($cell)-1; $i++){
			$link_all = $link_all . "%2C" . $cell[$i];
		}
		$response = $EMO_GLO." Lista delle [celle attive](".$link_all."): ".$EMO_GLO;
		for ($i = 0; $i <= sizeof($cell)-1; $i++){
			$link = "https://s2.sidewalklabs.com/regioncoverer/?center=". $lat[$i] ."%2C". $lng[$i] . "&zoom=" . $zoom[$i] . "&cells=" . $cell[$i];
			$response = $response."\n*".$name[$i]."* − [".$cell[$i]."](".$link.")";
		}
		$parameters = array('chat_id' => $chatId, "text" => $response, "parse_mode" => "markdown", "disable_web_page_preview" => TRUE);
		$parameters["method"] = "sendMessage";
		echo json_encode($parameters);
	}
}

elseif($status == 2) {
	$quest = $alert;
	//$level = 10;

	list($pkst, $lat, $lng) = getPortalData($text, $URLs[1]['url']);
	$zone = ', ';
	for ($i = 10; $i ==13; $i++) {
		$tmp = getPortalZone($i, $lat, $lng, $conn);
		if ($tmp) {
			$zone = $zone . $tmp . ', ';
		}
	}

	if (!$lat or !$lng)	{
		$data = [
	   	'chat_id' => $userId,
	   	'text' => $EMO_PIN.' Ho bisogno della posizione per inoltrare la segnalazione. Inviami il pokéstop tramite @ingressportalbot.',
		];
		$response = file_get_contents("https://api.telegram.org/bot$apiToken/sendMessage?" . http_build_query($data) );
	}
	else {
		// CODICE QUEST
		$query = "SELECT * FROM `quests` WHERE `pokestop` = '$pkst'";
		$result = mysqli_query($conn,$query);
		$row = mysqli_fetch_assoc($result);
		$om_pkst = $row['pokestop'];
		$om_lat = $row['lat'];
		$om_lng = $row['lng'];
		$link = 'https://maps.google.com/?q='.$lat.','.$lng;
		if ($om_pkst == str_replace("\'","'",$pkst) and $om_lat == $lat and $om_lng == $lng) {				// IN REALTÀ BISOGNA FAR EIL CONFRONTO CON TUTTI GLI OMONINI! CI VUOLE while

			// AVVISO DI QUEST GIÀ SEGNALATA
			$response = $EMO_v.' La quest di questo pokéstop è stata già segnalata per oggi.';
			$parameters = array('chat_id' => $userId, "text" => $response, "parse_mode" => "markdown");
			$parameters["method"] = "sendMessage";
			echo json_encode($parameters);
			mysqli_query($conn,"DELETE FROM `sessions` WHERE userID = $userId");
		}
		else {
			$query = "SELECT * FROM `tasks` WHERE `reward` = '$quest'";
			$result = mysqli_query($conn,$query);
			$row2 = mysqli_fetch_assoc($result);
			$flag = $row2['flag'];
			$task = $row2['task'];

			// NOTIFICA UTENTI CON NOTIFICHE ATTIVE NELLA CHAT DEL BOT
			$query = "SELECT * FROM `pokeid` WHERE `pokemon` = '$quest'";
			$result = mysqli_query($conn,$query);
			$row3 = mysqli_fetch_assoc($result);
			$userAlerts = $row3['userAlerts'];
			$userAlertsIDs = explode(',', $userAlerts);
			foreach ($userAlertsIDs as $userAlertsID) {
				if ($userAlertsID != $userId) {
					$data = [
			  			'chat_id' => $userAlertsID,
			  			'text' => "`Quest:   ` *". $quest . "*\n`Pokéstop:` [" . $pkst . "](" . $link . ")\n`Giorno:  ` ".$today2."\n`Task:    ` ". $task,
			  			//'text' => "`Quest:   ` *". $quest . "*\n`Pokéstop:` [" . $pkst . "](" . $link . ")\n`Giorno:  ` ".$today2,
			  			'parse_mode' => 'markdown',
			  			'disable_web_page_preview' => TRUE,
					];
					$response = file_get_contents("https://api.telegram.org/bot$apiToken/sendMessage?" . http_build_query($data) );
				}
			}

			// SEGNALA LA QUEST NEL CANALE - CONTROLLO FLAG MISSIONI RARE
			if ($flag == 1) {
				$data = [
			  		'chat_id' => $channel,
			  		'text' => "`Quest:   ` *". $quest . "*\n`Pokéstop:` [" . $pkst . "](" . $link . ")\n`Giorno:  ` ".$today2."\n`Task:    ` ". $task,
			  		//'text' => "`Quest:   ` *". $quest . "*\n`Pokéstop:` [" . $pkst . "](" . $link . ")\n`Giorno:  ` ".$today2,
			  		'parse_mode' => 'markdown',
			  		'disable_web_page_preview' => TRUE,
				];
				$response = file_get_contents("https://api.telegram.org/bot$apiToken/sendMessage?" . http_build_query($data) );
			}

			$response = $EMO_v.' La quest è stata registrata.'.$zone;
			$parameters = array('chat_id' => $userId, "text" => $response, "parse_mode" => "markdown");
			$parameters["method"] = "sendMessage";
			echo json_encode($parameters);

/*			// INVIA MESSAGGIO NEL GRUPPO - DA AUTOMATIZZARE+SELEZIONARE GRUPPI IN BASE ALLE CELLE ASSOCIATE
			$data = [
			  	'chat_id' => $group_NordEstLegit,
			  	'text' => $firstname . " ha segnalato una quest *" . $quest . "* presso [" . $pkst . "](" . $link . ")",
			  	'parse_mode' => 'markdown',
			  	'disable_web_page_preview' => TRUE,
			];
			$response = file_get_contents("https://api.telegram.org/bot$apiToken/sendMessage?" . http_build_query($data) );
*/
			// REGISTRA LA QUEST NEL DATABASE E RESETTA LA SESSIONE DELL'UTENTE
			mysqli_query($conn,"INSERT INTO `quests` (quest, pokestop, lat, lng, zona, giorno) VALUES ('$quest', '$pkst', '$lat', '$lng', '$zone', '$today')");
			mysqli_query($conn,"DELETE FROM `sessions` WHERE userID = $userId");
		}
	}
}

//close the mySQL connection
$conn->close();

/*ì
// DEBUG - PRINT
$response = $today;
$parameters = array('chat_id' => $chatId, "text" => $response, "parse_mode" => "markdown");
$parameters["method"] = "sendMessage";
echo json_encode($parameters);
*/


