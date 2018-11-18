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
$text = strtolower($text);
$reply = isset($message['reply_to_message']['text']) ? $message['reply_to_message']['text'] : "";

header("Content-Type: application/json");
$response = '';
$apiToken = "689487990:AAGhqhcsalt0mXYRnUqFro9ECNxPuOOVPZc";
$channel = '@centoPoGO';


$bot_Exeggutor = 158754689;
$group_PogoTube42 = -1001204753064;
$group_NordEstLegit = -1001119443518;
$authorizedChats = array( $group_PogoTube42, $group_NordEstLegit, $bot_Exeggutor );

if (in_array($chatId, $authorizedChats)) {


// Create connection
$conn = new mysqli("sql7.freemysqlhosting.net:3306/sql7243921", "sql7243921", "4ezgelH6xq", "sql7243921");
// Check connection
if ($conn->connect_error) {
	$error = "Connection failed: " . $conn->connect_error;
}

// CERCA POKÈSTOP NEL DATABASE
$query = "SELECT * FROM `sessions` WHERE `userID` = '$userId'";
$result = mysqli_query($conn,$query);
$row = mysqli_fetch_assoc($result);
$status = $row['status'];

/*
			// CERCA POKÈSTOP NEL DATABASE
			$query = "SELECT * FROM `pokestops` WHERE `pokestop` = 'Squid'";
			$result = mysqli_query($conn,$query);
			$row = mysqli_fetch_assoc($result);
			$lat = $row['lat'];
			$lng = $row['lng'];
*/

if($status == '')
{
	// 100%
	if(strpos($text, "/100") === 0 )
	{
		if(isset($message['reply_to_message']['text']))
		{
			$data = [
	    		'chat_id' => $userId,
	    		'text' => 'Mandami la posizione di*'.$reply.'*.',
	    		'parse_mode' => 'markdown',
			];
			/*
			$location = [
	    		'chat_id' => '@centoPoGO',
	    		'latitude' => $lat,
	    		'longitude' => $lng,
			];
			*/
			$response1 = file_get_contents("https://api.telegram.org/bot$apiToken/sendMessage?" . http_build_query($data) );
			mysqli_query($conn,"INSERT INTO `sessions` (userID, status, alert) VALUES ($userId, 1, '$reply')";
			//$response2 = file_get_contents("https://api.telegram.org/bot$apiToken/sendlocation?" . http_build_query($location) );
		}
		else
		{
			$data = [
	   	 	'chat_id' => $userId,
	   	 	'text' => 'Mandami la posizione di*'.str_replace('/100', '', $text).'*.',
	    		'parse_mode' => 'markdown',
	   	];
			/*
			$location = [
	    		'chat_id' => '@centoPoGO',
	    		'latitude' => $lat,
	    		'longitude' => $lng,
			];
			*/
			$response1 = file_get_contents("https://api.telegram.org/bot$apiToken/sendMessage?" . http_build_query($data) );
			mysqli_query($conn,"INSERT INTO `sessions` (userID, status, alert) VALUES ($userId, 1, '$text')";
			//$response2 = file_get_contents("https://api.telegram.org/bot$apiToken/sendlocation?" . http_build_query($location) );
		}
	}
}
elseif($status == 1)
{
	$data = [
	   'chat_id' => $userId,
	   'text' => 'da completare.',
	   'parse_mode' => 'markdown',
	  ];
	$response1 = file_get_contents("https://api.telegram.org/bot$apiToken/sendMessage?" . http_build_query($data) );
}


//close the mySQL connection
$conn->close();



} else { $response = "Gruppo non autorizzato. Contattare l'admin."; };

/*
// DEBUG - PRINT
$response = $lat . ", " . $lng;
$parameters = array('chat_id' => $chatId, "text" => $response, "parse_mode" => "markdown");
$parameters["method"] = "sendMessage";
echo json_encode($parameters);
*/


