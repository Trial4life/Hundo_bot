<?php

// PARAMETRI DA MODIFICARE
// $WEBHOOK_URL = 'https://hundo.herokuapp.com/execute.php';
$WEBHOOK_URL = 'http://2.227.251.71:80/_DEV_/[HEROKU]/Exeggutor_bot/hundo_bot/execute.php';
$BOT_TOKEN = '689487990:AAGhqhcsalt0mXYRnUqFro9ECNxPuOOVPZc';

// NON APPORTARE MODIFICHE NEL CODICE SEGUENTE
$API_URL = 'https://api.telegram.org/bot' . $BOT_TOKEN .'/';
$method = 'setWebhook';
$parameters = array('url' => $WEBHOOK_URL);
$url = $API_URL . $method. '?' . http_build_query($parameters);
$handle = curl_init($url);
curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
curl_setopt($handle, CURLOPT_CONNECTTIMEOUT, 0);
curl_setopt($handle, CURLOPT_TIMEOUT, 120);
$result = curl_exec($handle);
print_r($result);
