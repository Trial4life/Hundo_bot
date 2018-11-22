<?php
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
$userId = isset($message['from']['id']) ? $message['from']['id'] : "";
$firstname = isset($message['from']['first_name']) ? $message['from']['first_name'] : "";
$lastname = isset($message['chat']['last_name']) ? $message['chat']['last_name'] : "";
$username = isset($message['from']['username']) ? $message['from']['username'] : "";
$date = isset($message['date']) ? $message['date'] : "";
$text = isset($message['text']) ? $message['text'] : "";
$text = trim($text);
$reply = isset($message['reply_to_message']['text']) ? $message['reply_to_message']['text'] : "";
$lat = isset($message['location']['latitude']) ? $message['location']['latitude'] : NULL;
$lng = isset($message['location']['longitude']) ? $message['location']['longitude'] : NULL;
$today = date('Y-m-d');

header("Content-Type: application/json");
$response = '';
$apiToken = "689487990:AAGhqhcsalt0mXYRnUqFro9ECNxPuOOVPZc";
$channel = '@centoPoGO';

// AUTORIZZAZIONI
include $_SERVER['DOCUMENT_ROOT'] . "/authorizations.php";
// FUNZIONI
include $_SERVER['DOCUMENT_ROOT'] . "/functions.php";
// EMOJIS
$EMO_100 = json_decode('"'."\u1f4af".'"');
$EMO_PIN = json_decode('"'."\u1f4cc".'"');
$EMO_v = json_decode('"'."\u2705".'"');
$EMO_x = json_decode('"'."\u274c".'"');
$EMO_ALR = json_decode('"'."\u203c".'"');
$EMO_ERR = json_decode('"'."\u26d4".'"');

// MySQL -> Create connection
$conn = new mysqli("sql7.freemysqlhosting.net:3306/sql7243921", "sql7243921", "4ezgelH6xq", "sql7243921");
// Check connection
if ($conn->connect_error) {
	$error = "Connection failed: " . $conn->connect_error;
}

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

if(strpos($text, "/annulla") === 0 ) {
	mysqli_query($conn,"DELETE FROM `sessions` WHERE userID = $userId");
	$data = [
	   'chat_id' => $userId,
	   'text' => $EMO_x.' Segnalazione annullata.',
	];
	$response = file_get_contents("https://api.telegram.org/bot$apiToken/sendMessage?" . http_build_query($data) );
}

elseif(strpos($text, "/cancella") === 0 ) {
	$text = str_replace('/cancella ', '', $text);
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

elseif(strpos($text, "/quests") === 0 ) {
	// ELENCO QUESTS
	mysqli_query($conn,"DELETE FROM `quests` WHERE giorno < '$today'");  // RIMUOVE LE QUEST DEL GIORNO PRECEDENTE
	$query = "SELECT * FROM `quests`";
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
		$response = 'Elenco delle quests di oggi:';
		for ($i = 0; $i <= sizeof($quest)-1; $i++){
			$link = 'https://maps.google.com/?q='.$lat[$i].','.$lng[$i];
			$response = $response . "\n*" . ucfirst($quest[$i]) . "* − [" . $pokestop[$i] . "](" . $link . ")";
		}

		$parameters = array('chat_id' => $chatId, "text" => $response, "parse_mode" => "markdown", "disable_web_page_preview" => TRUE);
		$parameters["method"] = "sendMessage";
		echo json_encode($parameters);
	}

}

elseif($status == 0)
{
	if (in_array($chatId, $authorizedChats)) {
		//////////////
		//// 100% ////
		//////////////
		if(strpos($text, "/100") === 0 )	{
			if (in_array($username, $authorizedUsers)) {
				if(isset($message['reply_to_message']['text']))	{
					$data = [
		   	 		'chat_id' => $userId,
		   	 		'text' => $EMO_PIN.' Mandami la posizione di *'.$reply.'*.',
		   	 		'parse_mode' => 'markdown',
					];
					$response = file_get_contents("https://api.telegram.org/bot$apiToken/sendMessage?" . http_build_query($data) );
					mysqli_query($conn,"INSERT INTO `sessions` (userID, status, alert) VALUES ($userId, 1, '$reply')");

				}
				else {
					$text = str_replace('/100', '', $text);
					$data = [
		   		 	'chat_id' => $userId,
		   		 	'text' => $EMO_PIN.' Mandami la posizione di*'.$text.'*.',
		   	 		'parse_mode' => 'markdown',
		   		];
					$response = file_get_contents("https://api.telegram.org/bot$apiToken/sendMessage?" . http_build_query($data) );
					mysqli_query($conn,"INSERT INTO `sessions` (userID, status, alert) VALUES ($userId, 1, '$text')");
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

		////////////////
		//// QUESTS ////
		////////////////

		if(strpos($text, "/quest") === 0 )	{
			if (in_array($username, $authorizedUsers)) {
				$quest = explode(", ", str_replace('/quest ', '', $text));
				$data = [
		   	 	'chat_id' => $chatId,
		   	 	'text' => $EMO_PIN.' '.$username.', mandami la posizione della quest *'.$quest.'* tramite @ingressportalbot.',
		   	 	'parse_mode' => 'markdown',
				];
				$response = file_get_contents("https://api.telegram.org/bot$apiToken/sendMessage?" . http_build_query($data) );
				mysqli_query($conn,"INSERT INTO `sessions` (userID, status, alert) VALUES ($userId, 2, '$quest')");
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
	else {
		$data = [
		   'chat_id' => $chatId,
		   'text' => $EMO_ERR." Gruppo non autorizzato. Contattare l'admin. ".$EMO_ERR,
		];
		$response = file_get_contents("https://api.telegram.org/bot$apiToken/sendMessage?" . http_build_query($data) );
	}
}


elseif($status == 1 and $chatId == $userId)
{
	if (!$lat or !$lng)
	{
		$data = [
	   	'chat_id' => $userId,
	   	'text' => $EMO_PIN.' Ho bisogno della posizione per inoltrare la segnalazione.',
		];
		$response = file_get_contents("https://api.telegram.org/bot$apiToken/sendMessage?" . http_build_query($data) );
	}
	else {
		$data = [
	   	'chat_id' => $channel,
	   	'text' => $EMO_ALR." ".$EMO_100." ".$EMO_ALR.' *'.$alert.'* '.$EMO_ALR." ".$EMO_100." ".$EMO_ALR,
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

elseif($status == 2 /*and $chatId == $userId*/)
{
	if (!$lat or !$lng)	{
		$data = [
	   	'chat_id' => $userId,
	   	'text' => $EMO_PIN.' Ho bisogno della posizione per inoltrare la segnalazione.',
		];
		$response = file_get_contents("https://api.telegram.org/bot$apiToken/sendMessage?" . http_build_query($data) );
	}
	else {
		// CODICE QUEST
		$quest = $alert;
		list($pkst, $lat, $lng) = getPortalData($text);

		$query = "SELECT * FROM `quests` WHERE `pokestop` = '$pkst'";
		$result = mysqli_query($conn,$query);
		$row = mysqli_fetch_assoc($result);
		if(!$row) { // !!! ATTENZIONE, RAFFINARE IL CHECK PER L'EVENTUALITÀ DI POKÈSTOP OMONIMI!!!
			$query = "SELECT * FROM `tasks` WHERE `quest` = '$quest'";
			$result = mysqli_query($conn,$query);
			$row2 = mysqli_fetch_assoc($result);
			$flag = $row2['flag'];
			$task = $row2['task'];
			// SEGNALA LA QUEST NEL CANALE - CONTROLLO FLAG MISSIONI RARE
			if ($flag == 1) {
				$link = 'https://maps.google.com/?q='.$lat.','.$lng;
				$data = [
			  		'chat_id' => $channel,
			  		'text' => "`Quest:   ` *". $quest . "*\n`Pokéstop:` [" . $pkst . "](" . $link . ")",
			  		'parse_mode' => 'markdown',
			  		'disable_web_page_preview' => TRUE,
				];
				$response = file_get_contents("https://api.telegram.org/bot$apiToken/sendMessage?" . http_build_query($data) );

				// REGISTRA LA QUEST NEL DATABASE
				mysqli_query($conn,"INSERT INTO `quests` (quest, pokestop, lat, lng, giorno) VALUES ('$quest', '$pkst', '$lat', '$lng', '$today')");
			}
			else {
				mysqli_query($conn,"INSERT INTO `quests` (quest, pokestop, lat, lng, giorno) VALUES ('$quest', '$pkst', '$lat', '$lng', '$today')");
			}
			$response = $EMO_v.' La quest è stata registrata.';
			$parameters = array('chat_id' => $chatId, "text" => $response, "parse_mode" => "markdown");
			$parameters["method"] = "sendMessage";
			echo json_encode($parameters);
		}
	}
	else {
		// AVVISO DI QUEST GIÀ SEGNALATA
		$response = $EMO_v.' La quest di questo pokéstop è stata già segnalata per oggi.';
		$parameters = array('chat_id' => $chatId, "text" => $response, "parse_mode" => "markdown");
		$parameters["method"] = "sendMessage";
		echo json_encode($parameters);
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


