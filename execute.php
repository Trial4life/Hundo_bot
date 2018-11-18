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
$apiToken = "721221790:AAFvEHkZQUVU3S9SeNaYoKIHPvvVojnCq6Q";


// Create connection
$conn = new mysqli("sql7.freemysqlhosting.net:3306/sql7243921", "sql7243921", "4ezgelH6xq", "sql7243921");
// Check connection
if ($conn->connect_error) {
	$error = "Connection failed: " . $conn->connect_error;
}

$group_TestBot = -267586313;
$group_NordEstLegit = -1001187994497;
$authorizedChats = array( $group_TestBot, $group_NordEstLegit );

//if($chatId === $group_TestBot or $chatId === $group_NordEstLegit) {
if (in_array($chatId, $authorizedChats)) {

if(strpos($text, "/100") === 0 )
{
	if(isset($message['reply_to_message']['text']))
	{
		$data = [
    		'chat_id' => '@centoPoGO',
    		'text' => $reply
		];
		$response = file_get_contents("https://api.telegram.org/bot$apiToken/sendMessage?" . http_build_query($data) );
	}
	else
	{
		$data = [
   	 	'chat_id' => '@centoPoGO',
   	 	'text' => str_replace('/100', '', $text)
		];
		$response = file_get_contents("https://api.telegram.org/bot$apiToken/sendMessage?" . http_build_query($data) );
	}
}

} else { $response = "Gruppo non autorizzato. Contattare l'admin."; };

$response = $chatId;




// DEBUG - PRINT
$parameters = array('chat_id' => $chatId, "text" => $response, "parse_mode" => "markdown");
$parameters["method"] = "sendMessage";
echo json_encode($parameters);

