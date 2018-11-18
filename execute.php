<?php
$content = file_get_contents("php://input");
$update = json_decode($content, true);

$apiToken = "721221790:AAFvEHkZQUVU3S9SeNaYoKIHPvvVojnCq6Q";

$data = [
    'chat_id' => '@cento42',
    'text' => 'Hello world!'
];

$response = file_get_contents("https://api.telegram.org/bot$apiToken/sendMessage?" . http_build_query($data) );

// Do what you want with result

if($text == "/hundo")
{
	$response = "100."; }
}



$parameters = array('chat_id' => $chatId, "text" => $response, "parse_mode" => "markdown");
$parameters["method"] = "sendMessage";
echo json_encode($parameters);
>