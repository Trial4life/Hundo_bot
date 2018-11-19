<?php
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
//$text = strtolower($text);
$reply = isset($message['reply_to_message']['text']) ? $message['reply_to_message']['text'] : "";
$lat = isset($message['location']['latitude']) ? $message['location']['latitude'] : NULL;
$lng = isset($message['location']['longitude']) ? $message['location']['longitude'] : NULL;
$today = date('Y-m-d');

header("Content-Type: application/json");
$response = '';
$apiToken = "689487990:AAGhqhcsalt0mXYRnUqFro9ECNxPuOOVPZc";
$channel = '@centoPoGO';


$bot_Exeggutor = 158754689;
$group_PogoTube42 = -1001204753064;
$group_NordEstLegit = -1001119443518;
$group_admin = -1001205498567;
$authorizedChats = array( $group_PogoTube42, $group_NordEstLegit, $bot_Exeggutor, $group_admin );
$authorizedUsers = array( 'Trial4life', 'DadyGC', 'medix93');



// Create connection
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

/*
			BETA// CERCA POKÈSTOP NEL DATABASE
			$query = "SELECT * FROM `pokestops` WHERE `pokestop` = 'Squid'";
			$result = mysqli_query($conn,$query);
			$row = mysqli_fetch_assoc($result);
			$lat = $row['lat'];
			$lng = $row['lng'];
*/

if(strpos($text, "/annulla") === 0 ) {
	mysqli_query($conn,"DELETE FROM `sessions` WHERE userID = $userId");
	$data = [
	   'chat_id' => $userId,
	   'text' => 'Segnalazione annullata.',
	];
	$response = file_get_contents("https://api.telegram.org/bot$apiToken/sendMessage?" . http_build_query($data) );
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
		$curr_pkst = end($pokestop);
		$query = "SELECT * FROM `pokestops` WHERE pokestop = '$curr_pkst'";
		$result_pkst = mysqli_query($conn,$query);
		$row2 = mysqli_fetch_assoc($result_pkst);
		array_push($lat, $row2['lat']);
		array_push($lng, $row2['lng']);
	}

	$response = 'Elenco delle quests di oggi:';
	for ($i = 0; $i <= sizeof($quest)-1; $i++){
		$link = 'https://maps.google.com/?q='.$lat[$i].','.$lng[$i];
		$response = $response . "\n*" . ucfirst($quest[$i]) . "* − [" . $pokestop[$i] . "](" . $link . ")";
	}

	$parameters = array('chat_id' => $chatId, "text" => $response, "parse_mode" => "markdown", "disable_web_page_preview" => TRUE);
	$parameters["method"] = "sendMessage";
	echo json_encode($parameters);

}

elseif($status == 0)
{
	if (in_array($chatId, $authorizedChats)) {
		//////////////
		//// 100% ////
		//////////////
		if(strpos($text, "/100") === 0 )
		{
			if (in_array($username, $authorizedUsers)) {
				if(isset($message['reply_to_message']['text']))
				{
					$data = [
		   	 		'chat_id' => $userId,
		   	 		'text' => 'Mandami la posizione di *'.$reply.'*.',
		   	 		'parse_mode' => 'markdown',
					];
					$response = file_get_contents("https://api.telegram.org/bot$apiToken/sendMessage?" . http_build_query($data) );
					mysqli_query($conn,"INSERT INTO `sessions` (userID, status, alert) VALUES ($userId, 1, '$reply')");

				}
				else
				{
					$text = str_replace('/100', '', $text);
					$data = [
		   		 	'chat_id' => $userId,
		   		 	'text' => 'Mandami la posizione di*'.str_replace('/100', '', $text).'*.',
		   	 		'parse_mode' => 'markdown',
		   		];
					$response = file_get_contents("https://api.telegram.org/bot$apiToken/sendMessage?" . http_build_query($data) );
					mysqli_query($conn,"INSERT INTO `sessions` (userID, status, alert) VALUES ($userId, 1, '$text')");
				}
			}
			else {
				$data = [
		   	 	'chat_id' => $chatId,
		   	 	'text' => 'Non sei autorizzato alle segnalazioni. Contatta un admin.',
				];
				$response = file_get_contents("https://api.telegram.org/bot$apiToken/sendMessage?" . http_build_query($data) );
			}
		}

		////////////////
		//// QUESTS ////
		////////////////



	}
	else {
		$data = [
		   'chat_id' => $chatId,
		   'text' => "Gruppo non autorizzato. Contattare l'admin",
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
	   	'text' => 'Ho bisogno della posizione per inoltrare la segnalazione.',
		];
		$response = file_get_contents("https://api.telegram.org/bot$apiToken/sendMessage?" . http_build_query($data) );
	}
	else {
		$data = [
	   	'chat_id' => $channel,
	   	'text' => '*'.$alert.'*',
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

elseif($status == 2 and $chatId == $userId)
{
	// CODICE QUEST
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


