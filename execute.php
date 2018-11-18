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



$bot_Exeggutor = 158754689;
$group_PogoTube42 = -1001204753064;
$group_NordEstLegit = -1001119443518;
$authorizedChats = array( $group_PogoTube42, $group_NordEstLegit, $bot_Exeggutor );

if (in_array($chatId, $authorizedChats)) {


// Create connection
$conn = new mysqli("s336.altervista.org/phpmyadmin/db_structure.php?db=my_pogotube42&table=&server=1&target=&token=e01f170d7aa9cf2b35d903acd4750b49#PMAURL-1:sql.php?db=my_pogotube42&table=pokestop&server=1&target=&token=e01f170d7aa9cf2b35d903acd4750b49", "pogotube42", "", "pogotube42");
// Check connection
if ($conn->connect_error) {
	$error = "Connection failed: " . $conn->connect_error;
}
			// CERCA NEL DATABASE
			$query = "SELECT * FROM `pokestops` WHERE `pokestop` = 'Squid'";
			$result = mysqli_query($conn,$query);
			$row = mysqli_fetch_assoc($result);
			$coords = $row['coordinate'];


// 100%
if(strpos($text, "/100") === 0 )
{
	if(isset($message['reply_to_message']['text']))
	{
		$data = [
    		'chat_id' => '@centoPoGO',
    		'text' => $reply
		];
		$response = file_get_contents("https://api.telegram.org/bot$apiToken/sendMessage?" . http_build_query($coords) );
	}
	else
	{
		$data = [
   	 	'chat_id' => '@centoPoGO',
   	 	'text' => str_replace('/100', '', $text)
		];
		$response = file_get_contents("https://api.telegram.org/bot$apiToken/sendMessage?" . http_build_query($coords) );
	}
}


//close the mySQL connection
$conn->close();



} else { $response = "Gruppo non autorizzato. Contattare l'admin."; };

/*
// DEBUG - PRINT
$parameters = array('chat_id' => $chatId, "text" => $response, "parse_mode" => "markdown");
$parameters["method"] = "sendMessage";
echo json_encode($parameters);
*/


