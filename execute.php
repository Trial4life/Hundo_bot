<?php
$content = file_get_contents("php://input");
$update = json_decode($content, true);

if($text == "/hundo")
{
	$response = "100."; }
}



$parameters = array('chat_id' => $chatId, "text" => $response, "parse_mode" => "markdown");
$parameters["method"] = "sendMessage";
echo json_encode($parameters);
