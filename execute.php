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
header("Content-Type: application/json");
$response = '';

$apiToken = "721221790:AAFvEHkZQUVU3S9SeNaYoKIHPvvVojnCq6Q";

$data = [
    'chat_id' => '@cento42',
    'text' => $text
];

if(strpos($text, "/hundo") === 0 )
{
	$response = file_get_contents("https://api.telegram.org/bot$apiToken/sendMessage?" . http_build_query($data) );
}

$response = str_replace('/hundo', '', $text);


$parameters = array('chat_id' => $chatId, "text" => $response, "parse_mode" => "markdown");
$parameters["method"] = "sendMessage";
echo json_encode($parameters);
