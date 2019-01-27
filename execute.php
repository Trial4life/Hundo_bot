<?php
spl_autoload_register(
    function ($class) {
        $path = __DIR__ . "/classes/$class.php";
        if (file_exists($path)) require_once $path;
    }
);

// FUNZIONI
include "functions.php";
//	$BREAKPOINT = microtime(true);	// TIMESTAMP INIZIALE PER I DEBUG

date_default_timezone_set('Europe/London');
$today = date('Y-m-d');
$today2 = date('d/m/y');

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

$callback_query = isset($update["callback_query"]) ? $update["callback_query"] : "";
$callbackId = isset($callback_query['message']['chat']['id']) ? $callback_query['message']['chat']['id'] : "";
$callbackData = isset($callback_query['data']) ? $callback_query['data'] : "";

$callback = isset($update['callback_query']['id']) ? $update['callback_query']['id'] : "";

header("Content-Type: application/json");
$response = '';
$apiToken = "689487990:AAGhqhcsalt0mXYRnUqFro9ECNxPuOOVPZc";
$channel = '@PokeradarRoma';

// EMOJIS
$EMO_100 = "\xF0\x9F\x92\xAF";
$EMO_PIN = "\xF0\x9F\x93\x8C";
$EMO_zZz = "\xF0\x9F\x92\xA4";
$EMO_GLO = "\xF0\x9F\x8C\x90";
$EMO_EXE = "\xF0\x9F\x8C\xB4";
$EMO_TRI = "\xE2\x96\xB6";
$EMO_ON = "\xF0\x9F\x94\x94";
$EMO_OFF = "\xF0\x9F\x94\x95";
$EMO_TREE = "\xF0\x9F\x8C\xB3";
$EMO_TREE2 = "\xF0\x9F\x8C\xB2";
$EMO_LEAF = "\xF0\x9F\x8D\x83";
$EMO_NUM = "\xF0\x9F\x94\xA2";

$EMO_C = "\x30\xE2\x83\xA3" ;
$EMO_E = "\xE2\x9E\xA1" ;
$EMO_NE = "\xE2\x86\x97" ;
$EMO_N = "\xE2\xAC\x86" ;
$EMO_NO = "\xE2\x86\x96" ;
$EMO_O = "\xE2\xAC\x85" ;
$EMO_SO = "\xE2\x86\x99" ;
$EMO_S = "\xE2\xAC\x87" ;
$EMO_SE = "\xE2\x86\x98" ;

$EMO_v = json_decode('"'."\u2705".'"');
$EMO_x = json_decode('"'."\u274c".'"');
$EMO_ALR = json_decode('"'."\u203c".'"');
$EMO_ERR = json_decode('"'."\u26d4".'"');

// MySQL -> Create connection
$conn = new mysqli("db4free.net", "trial4life", "16021993", "tradepkmn");
// $conn = new mysqli("2.227.251.71:3306", "root", "", "tradepkmn");
// $conn = new mysqli("sql7.freemysqlhosting.net:3306/sql7243921", "sql7243921", "4ezgelH6xq", "sql7243921");   [OLD freemysqlhosting account]
// Check connection

// AUTORIZZAZIONI
include "authorizations.php";

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
	   'text' =>
	   	$EMO_TRI . " *Per segnalare un pokémon selvatico*, utilizzare il comando `/100 <segnalazione>` (sia in chat privata, sia nel gruppo) e, dopo aver inviato il comando, condivedere la posizione dell'avvistamento direttamente tramite telegram.\n\n_Esempio:_\n`/100 Dratini 100% appena spawnato al Pincio`\n\n" .
	   	$EMO_TRI . " *Per segnalare una quest*, utilizzare in chat privata con il bot il comando\n `/q <inserire-la-ricompensa>` e, dopo aver inviato il comando, condividere la posizione del pokéstop tramite @ingressportalbot (da utilizzare in modalità inline: dopo aver digitato `@ingressportalbot`, iniziare a digitare il nome del portale e selezionarlo dal menu a tendina una volta comparso - si consiglia di condividere la posizione con il bot in modo da rilevare i portali più vicini).\n\n_Esempio:_\n`/q Dratini`\n`/q 1 Caramella Rara, vinci 3 sfide`\n\n".
	   	$EMO_TRI . " *Per attivare/disattivare le notifiche per una quest* utilizzare i comandi `/addalert <inserire-la-ricompensa>` e `/delalert <inserire-la-ricompensa>` \n\n_Esempio_:\n`/addalert Larvitar`\n`/delalert Dratini`\n\n" .
	   	$EMO_TRI ." *Per mostrare le notifiche attive*, utilizzare il comando `/alerts`.\n\n".
	   	$EMO_TRI ." *Per mostrare le quest segnalate entro il raggio impostato*, inviare la posizione in privato al bot.\n\n".
	   	$EMO_TRI ." *Per impostare il raggio entro il quale mostrare le quest segnalate*, usare il comando `/radius <chilometri>`. \n\n_Esempio_:\n`/radius 0.5`\n\n".
	   	$EMO_TRI ." *Per consultare il meteo*, usare il comando `/meteo`\n\n".
	   	$EMO_TRI ." *Per segnalare un nuovo nido*, usare il comando `/nest <pokemon>, <nido>`. \n\n_Esempio_:\n`/nest Squirtle, Villa Borghese`\n\n".
	   	$EMO_TRI ." *Per segnalare un nuovo spawn frequente*, usare il comando `/spawn <pokemon>, <spawn>`. \n\n_Esempio_:\n`/spawn Squirtle, Parchetto dei Galli`\n\n".
	   	$EMO_TRI ." *Per elencare i nidi correnti*, usare il comando `/nidi`\n\n".
	   	$EMO_TRI ." *Per elencare i parchi nel database*, usare il comando `/parks`".
	   	$EMO_TRI ." *Per registrare il proprio codice amico*, usare il comando `/addcode <nick-in-game>, <#### #### ####>`\n\n_Esempio_:\n`/addcode Ash, 2142 8421 4284`\n\n".
	   	$EMO_TRI ." *Per rimuovere il proprio codice amico*, usare il comando `/delcode`\n\n".
	   	$EMO_TRI ." *Per mostrare l'elenco dei codici amico*, usare il comando `/codici`\n\n",
	   'parse_mode' => 'markdown',
	];
	$response = file_get_contents("https://api.telegram.org/bot$apiToken/sendMessage?" . http_build_query($data) );
}

if(strpos($text, "/adminhelp") === 0 ) {
	if ((in_array($username, $admins)) and ($chatId == $userId)) {
		$data = [
		   'chat_id' => $chatId,
		   'text' =>
		   	$EMO_TRI . " *Per aggiungere una nuova quest*, utilizzare il comando `/newquest <ricompensa>, <task>, <flag>` (il flag può essere 0, se non si vogliono le notifiche nel canale @PokeradarRoma, o 1, se si desiderano le notifiche per la quest).\n\n_Esempio:_\n`/newquest Nincada, Cattura 5 Pokémon di tipo Coleottero, 1`\n`/newquest Gastly, Effettua 3 tiri ottimi, 0`\n\n" .
		   	$EMO_TRI . " *Per rimuovere una quest*, utilizzare il comando `/delquest <ricompensa>`.\n\n_Esempio:_\n`/delquest Nincada`\n\n".
		   	$EMO_TRI	. " *Per visualizzare tutte le quest disponibili* per il mese corrente, utilizzare il comando `/listquests`\n\n" .
		   	$EMO_TRI . " *Per aggiungere una cella S2 al bot*, utilizzare il comando `/addcell <ID-cella>, <nome-cella>`.\n\n_Esempio:_\n`/addcell 132f61, Roma-Centro`.\n\n" .
		   	$EMO_TRI . " *Per rimuovere una cella S2 dal bot*, utilizzare il comando `/delcell <nome-cella>`.\n\n_Esempio:_\n`/delcell Roma-Centro`.\n\n" .
		   	$EMO_TRI . " *Per elencare le celle S2 disponibili*, utilizzare il comando `/cells`.\n\n" .
		   	$EMO_TRI . " *Per registrare un gruppo ad una cella S2*, utilizzare il comando `/register <nome-cella>` all'interno della chat del gruppo.\n\n_Esempio:_\n`/register Roma-Centro`.\n\n" .
		   	$EMO_TRI . " *Per rimuovere un gruppo da una cella S2*, utilizzare il comando `/unregister <nome-cella>` all'interno della chat del gruppo.\n\n_Esempio:_\n`/unregister Roma-Centro`.\n\n" .
		   	$EMO_TRI . " *Per elencare le celle S2 associate ad un gruppo*, utilizzare il comando `/groupcells` all'interno della chat del gruppo.\n\n" .
		   	$EMO_TRI . " *Per elencare gli admin del bot*, utilizzare il comando `/admins`.\n\n".
		   	$EMO_TRI . " *Per rimuovere un singolo nido*, utilizzare il comando `/delnest <nido>`.\n\n".
		   	$EMO_TRI . " *Per resettare i nidi correnti*, utilizzare il comando `/resetnests`.".
		   	$EMO_TRI . " *Per aggiungere un parco*, utilizzare il comando `/newpark <parco>, <latitudine>, <longitudine>`.\n\n_Esempio:_\n`/newpark Villa Borghese, 41.913567, 12.484158`\n\n" .
		   	$EMO_TRI . " *Per rimuovere un parco*, utilizzare il comando `/delpark <parco>`.\n\n_Esempio:_\n`/delpark Villa Borghese`\n\n" ,
		   	//$EMO_TRI . " *Per aggiungere un admin al bot*, utilizzare il comando `/addadmin <username>`.\n\n_Esempio:_\n`/addadmin Exeggutor`.\n\n" .
		   	//$EMO_TRI . " *Per rimuovere un admin dal bot*, utilizzare il comando `/deladmin <username>`.\n\n_Esempio:_\n`/deladmin Exeggutor`.\n\n" .
		   	//$EMO_TRI . " *Per aggiungere un gruppo al bot*, utilizzare il comando `/addgroup <ID-gruppo>, <nome-gruppo>`.\n\n_Esempio:_\n`/addgroup -123456, Exeggutor-group`.\n\n" .
		   	//$EMO_TRI . " *Per rimuovere un gruppo dal bot*, utilizzare il comando `/delgroup <nome-gruppo>`.\n\n_Esempio:_\n`/delgroup Exeggutor-group`.\n\n" .
		   	//$EMO_TRI . " *Per elencare i gruppi autorizzati dal bot*, utilizzare il comando `/groups`.\n\n",
		   'parse_mode' => 'markdown',
		];
		$response = file_get_contents("https://api.telegram.org/bot$apiToken/sendMessage?" . http_build_query($data) );
	}
	else {
		$data = [
		  	'chat_id' => $chatId,
		  	'text' => $EMO_ERR.' Questo comando può essere usato solo dagli admin e solo in chat privata. '.$EMO_ERR,
		];
		$response = file_get_contents("https://api.telegram.org/bot$apiToken/sendMessage?" . http_build_query($data) );
	}
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
elseif($status == 0 and strpos($text, "/100") === 0 ) {
	if (!in_array($username, $bannedUsers)) {
		if(isset($message['reply_to_message']['text']))	{
			$reply = str_replace("'","\'",$reply);
			$data = [
   	 		'chat_id' => $chatId,
   	 		'text' => $EMO_PIN.' Mandami la posizione di *'.$reply.'*.',
   	 		'parse_mode' => 'markdown',
			];
			$response = file_get_contents("https://api.telegram.org/bot$apiToken/sendMessage?" . http_build_query($data) );
			mysqli_query($conn,"INSERT INTO `sessions` (userID, username, status, alert) VALUES ($userId, '$username', 1, '$reply')");
		}
		else {
			$text = str_replace('/100 ', '', $text);
			$text = str_replace("'","\'",$text);
			$data = [
   		 	'chat_id' => $chatId,
   		 	'text' => $EMO_PIN.' Mandami la posizione di *'.$text.'*.',
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
	$alert = str_replace("\'","'",$alert);
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

elseif ($conn->connect_error and (strpos($text, "/annulla") === 0 or strpos($text, "/cancella") === 0 or strpos($text, "/quests") === 0 or strpos($text, "/quest") === 0  or strpos($text, "/q") === 0 or strpos($text, "/100") === 0 or strpos($text, "/mappaquest") === 0)) {
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

	$query = "SELECT * FROM `zones` WHERE `name` = '$zona'";
	$result_quest = mysqli_query($conn,$query);
	$row = mysqli_fetch_assoc($result_quest);
	$cell = $row['cellId'];

	list($latC, $lngC, $zoom) = getCellData(hexdec($row['cellId64']), 2);
	$link = "https://s2.sidewalklabs.com/regioncoverer/?center=". $latC ."%2C". $lngC . "&zoom=" . $zoom . "&cells=" . $cell;

	if (sizeof($quest)==0) {
		$response = 'Non è stata segnalata nessuna quest nella cella ['.$zona.']('.$link.').';
		$parameters = array('chat_id' => $chatId, "text" => $response, "parse_mode" => "markdown", "disable_web_page_preview" => TRUE);
		$parameters["method"] = "sendMessage";
		echo json_encode($parameters);
	}
	else {
		// $response = 'Elenco delle quest nella cella ['.$zona.']('.$link.'):';  	// MARKDOWN
		$response = 'Elenco delle quest nella cella <a href="'.$link.'">'.$zona.'</a>:';

		/* MARKDOWN
		for ($i = 0; $i <= sizeof($quest)-1; $i++){
			$link = 'https://maps.google.com/?q='.$lat[$i].','.$lng[$i];
			$response = $response . "\n*" . ucfirst($quest[$i]) . "* − [" . $pokestop[$i] . "](" . $link . ")";
		}
		*/
		for ($i = 0; $i <= sizeof($quest)-1; $i++){
			$link = "https://maps.google.com/?q=".$lat[$i].",".$lng[$i]."(".str_replace(" ","+",str_replace("\'","'",str_replace("\"","''",$pokestop[$i]))).")";
			// $link = 'https://maps.google.com/?q='.$lat[$i].','.$lng[$i];
			$response = $response . "\n<b>" . ucfirst($quest[$i]) . '</b> − <a href = "' .$link.'">'.str_replace("\'","'",$pokestop[$i]).'</a>';
		}
		$parameters = array('chat_id' => $chatId, "text" => $response, "parse_mode" => "HTML", "disable_web_page_preview" => TRUE);
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
		/* MARKDOWN
		for ($i = 0; $i <= sizeof($quest)-1; $i++){
			$link = urldecode('https://maps.google.com/?q='.$lat[$i].','.$lng[$i].'%28'.$pokestop[$i].'%29');			// BETA
			// $link = 'https://maps.google.com/?q='.$lat[$i].','.$lng[$i];
			$response = $response . "\n*" . ucfirst($quest[$i]) . "* − [" . $pokestop[$i] . "](" . $link . ")";
		}
		*/
		for ($i = 0; $i <= sizeof($quest)-1; $i++){
			$link = "https://maps.google.com/?q=".$lat[$i].",".$lng[$i]."(".str_replace(" ","+",str_replace("\'","'",str_replace("\"","''",$pokestop[$i]))).")";
			// $link = 'https://maps.google.com/?q='.$lat[$i].','.$lng[$i];
			$response = $response . "\n<b>" . ucfirst($quest[$i]) . '</b> − <a href = "' .$link.'">'.str_replace("\'","'",$pokestop[$i]).'</a>';
		}
		$parameters = array('chat_id' => $chatId, "text" => $response, "parse_mode" => "HTML", "disable_web_page_preview" => TRUE);
		$parameters["method"] = "sendMessage";
		echo json_encode($parameters);
	}
}

elseif(strpos($text, "/mappaquest") === 0 ) {
	$response = $EMO_GLO . ' Mappa delle quest ' . $EMO_GLO;
	$link = 'http://pogocasts.com/questmap/questmap.php';
	$response = "[" . $response . "](" . $link . ")";				$response = '. . . Under maintenance. . .';
	$parameters = array('chat_id' => $chatId, "text" => $response, "parse_mode" => "markdown", "disable_web_page_preview" => TRUE);
	$parameters["method"] = "sendMessage";
	echo json_encode($parameters);
}

elseif(strpos($text, "/mappanidi") === 0 ) {
	$response = $EMO_TREE . ' Mappa dei nidi ' . $EMO_TREE;
	$link = 'http://pogocasts.com/questmap/nestmap.php';
	$response = "[" . $response . "](" . $link . ")";				$response = '. . . Under maintenance. . .';
	$parameters = array('chat_id' => $chatId, "text" => $response, "parse_mode" => "markdown", "disable_web_page_preview" => TRUE);
	$parameters["method"] = "sendMessage";
	echo json_encode($parameters);
}

elseif($status == 0) {

####################
###### QUESTS ######
####################

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
				$data = [
		   	 	'chat_id' => $chatId,
		   	 	'text' => "Il comando per segnalare le quest è stato abbreviato in: \n`\q <nome-quest>`.",
		   	 	'parse_mode' => 'markdown',
				];
				$response = file_get_contents("https://api.telegram.org/bot$apiToken/sendMessage?" . http_build_query($data) );
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

	if(strpos($text, "/q ") === 0 )	{
		mysqli_query($conn,"DELETE FROM `quests` WHERE giorno < '$today'");		// RIMUOVE LE QUEST DEL GIORNO PRECEDENTE
		//if (in_array($chatId, $authorizedChats)) {
		if ($chatType == 'group' or $chatType == 'supergroup') {
			$response = $EMO_EXE." Per segnalare le quest, utilizza il comando /quest in chat privata con il bot.";
			$parameters = array('chat_id' => $chatId, "text" => $response, "parse_mode" => "markdown",);
			$parameters["method"] = "sendMessage";
			echo json_encode($parameters);
		}
		else {
			if (!in_array($username, $bannedUsers)) {
				$quest = ucfirst(str_replace('/q ', '', $text));
				$data = [
		   	 	'chat_id' => $chatId,
		   	 	'text' => $EMO_PIN.' Mandami la posizione della quest *'.$quest.'* tramite @ingressportalbot.',
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

	///////////////////
	/// RESETQUESTS ///
	///////////////////
	elseif(strpos($text, "/resetquests") === 0 ) {
		if (in_array($username, $admins)) {
			mysqli_query($conn,"TRUNCATE `quests`");
			$data = [
		  		'chat_id' => $chatId,
		  		'text' => $EMO_x.' Quest resettate.',
			];
			$response = file_get_contents("https://api.telegram.org/bot$apiToken/sendMessage?" . http_build_query($data) );
		}

		else {
			$data = [
		  		'chat_id' => $chatId,
		  		'text' => $EMO_ERR.' Solo gli admin possono utilizzare questo comando. '.$EMO_ERR,
			];
			$response = file_get_contents("https://api.telegram.org/bot$apiToken/sendMessage?" . http_build_query($data) );
		}

		$parameters = array('chat_id' => $chatId, "text" => $response, "parse_mode" => "markdown", "disable_web_page_preview" => TRUE);
		$parameters["method"] = "sendMessage";
		echo json_encode($parameters);
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
			mysqli_query($conn,"INSERT INTO `tasks` (reward, task, flag) VALUES ('$reward', '$task', $flag)");

			$response = $EMO_v.' Quest aggiunta.';
			$parameters = array('chat_id' => $chatId, "text" => $response, "parse_mode" => "markdown", "disable_web_page_preview" => TRUE);
			$parameters["method"] = "sendMessage";
			echo json_encode($parameters);
		}
		else {
			$data = [
		  		'chat_id' => $chatId,
		  		'text' => $EMO_ERR.' Solo gli admin possono utilizzare questo comando. '.$EMO_ERR,
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
		  		'text' => $EMO_ERR.' Solo gli admin possono utilizzare questo comando. '.$EMO_ERR,
			];
			$response = file_get_contents("https://api.telegram.org/bot$apiToken/sendMessage?" . http_build_query($data) );
		}
	}

	/////////////////
	/// QUESTLIST ///
	/////////////////
	elseif(strpos($text, "/listquests") === 0 ) {
		//if (in_array($username, $admins)) {
			$query = "SELECT * FROM `tasks`";
			$result = mysqli_query($conn,$query);
			$reward = $task = array();
			while ($row = mysqli_fetch_assoc($result)) {
				array_push($reward, $row['reward']);
				array_push($task, $row['task']);
			}

			$response = "Lista delle quest di questo mese:";
			for ($i = 0; $i <= sizeof($reward)-1; $i++){
				$response = $response."\n*".$reward[$i]."* − ".$task[$i];
			}

			$parameters = array('chat_id' => $chatId, "text" => $response, "parse_mode" => "markdown", "disable_web_page_preview" => TRUE);
			$parameters["method"] = "sendMessage";
			echo json_encode($parameters);
		//}
		/*else {
			$data = [
		  		'chat_id' => $chatId,
		  		'text' => $EMO_ERR.' Solo gli admin possono utilizzare questo comando. '.$EMO_ERR,
			];
			$response = file_get_contents("https://api.telegram.org/bot$apiToken/sendMessage?" . http_build_query($data) );
		}*/
	}

	///////////////
	/// LAT-LNG ///
	///////////////
	elseif ($lat and $lng and !$text and $chatId == $userId) {
		$query = "SELECT * FROM `usersettings` WHERE `username` = '$username'";
		$result = mysqli_query($conn,$query);
		$row = mysqli_fetch_assoc($result);
		$row == NULL ? $rad = 1 : $rad = $row['radius'];

		$query = "SELECT * FROM `quests` ORDER BY quest ASC";
		$result = mysqli_query($conn,$query);
		$quest = $pokestop = $questLat = $questLng = array();
		while ($row = mysqli_fetch_assoc($result)) {
			array_push($quest, $row['quest']);
			array_push($pokestop, $row['pokestop']);
			array_push($questLat, $row['lat']);
			array_push($questLng, $row['lng']);
		}

		if (sizeof($quest)==0) {
			$response = 'Non è stata segnalata nessuna quest per oggi.';
			$parameters = array('chat_id' => $chatId, "text" => $response, "parse_mode" => "markdown", "disable_web_page_preview" => TRUE);
			$parameters["method"] = "sendMessage";
			echo json_encode($parameters);
		}
		else {
			$response = 'Elenco delle quest nel raggio di <b>'.$rad.' km</b>:';
			$check = FALSE;

			for ($i = 0; $i <= sizeof($quest)-1; $i++){
				$distance = computeDistance($lat,$lng,$questLat[$i],$questLng[$i]);
				/* MARKDOWN
				if ($distance <= $rad) {
					$link = 'https://maps.google.com/?q='.$questLat[$i].','.$questLng[$i];
					$response = $response . "\n*" . ucfirst($quest[$i]) . "* − [" . $pokestop[$i] . "](" . $link . ")";
					$check = TRUE;
				}
				*/
				if ($distance <= $rad) {
					$link = 'https://maps.google.com/?q='.$questLat[$i].','.$questLng[$i]."(".str_replace(" ","+",str_replace("\'","'",str_replace("\"","''",$pokestop[$i]))).")";
					$response = $response . "\n<b>" . ucfirst($quest[$i]) . '</b> − <a href = "' .$link.'">'.str_replace("\'","'",$pokestop[$i]).'</a>';
					$check = TRUE;
				}
			}

			if ($check == FALSE) { $response = 'Nessuna quest segnalata nel raggio di <b>'.$rad.' km</b>.'; }

			$parameters = array('chat_id' => $chatId, "text" => $response, "parse_mode" => "HTML", "disable_web_page_preview" => TRUE);
			$parameters["method"] = "sendMessage";
			echo json_encode($parameters);
		}
	}

	//////////////
	/// RADIUS ///
	//////////////
	elseif(strpos($text, "/radius") === 0 ){
		$str = explode(' ', $text);
		$rad = $str[1];

		mysqli_query($conn,"DELETE FROM `usersettings` WHERE username = '$username'");
		mysqli_query($conn,"INSERT INTO `usersettings` (`username`,`radius`) VALUES ('$username',$rad)");

		$response = $EMO_GLO." Raggio delle quest impostato a *".$rad." km*.";
		$parameters = array('chat_id' => $chatId, "text" => $response, "parse_mode" => "markdown", "disable_web_page_preview" => TRUE);
		$parameters["method"] = "sendMessage";
		echo json_encode($parameters);
	}

#####################
###### WEATHER ######
#####################

	/////////////
	/// METEO ///
	/////////////
	elseif(strpos($text, "/meteo") === 0 ){
   	$response = $EMO_GLO." Selezionare la cella meteo ".$EMO_GLO;

		$keyboard = array(
		    "inline_keyboard" => array(
		    	array(
		    		array(
		    			"text" => $EMO_NO,
		    			"callback_data" => 4
		    		),
		    		array(
		    			"text" => $EMO_N,
		    			"callback_data" => 3
		    		),
		    		array(
		    			"text" => $EMO_NE,
		    			"callback_data" => 2
		    		),
		    	),
		    	array(
		    		array(
		    			"text" => $EMO_O,
		    			"callback_data" => 5
		    		),
		    		array(
		    			"text" => $EMO_C,
		    			"callback_data" => 0
		    		),
		    		array(
		    			"text" => $EMO_E,
		    			"callback_data" => 1
		    		),
		    	),
		    	array(
		    		array(
		    			"text" => $EMO_SO,
		    			"callback_data" => 6
		    		),
		    		array(
		    			"text" => $EMO_S,
		    			"callback_data" => 7
		    		),
		    		array(
		    			"text" => $EMO_SE,
		    			"callback_data" => 8
		    		),
		    	),
		   ),
		);

    	$encodedKeyboard = json_encode($keyboard, true);
    	$parameters = array(
    		'chat_id' => $chatId,
    		'text' => $response,
    		'reply_markup' => $encodedKeyboard
    	);
    	$parameters["method"] = "sendMessage";
    	echo json_encode($parameters);
	}

	elseif (isset($update["callback_query"])) {
		include_once "weather.php";

    	$parameters = array(
    		'chat_id' => $callbackId,
    		'text' => $response,
    		'parse_mode' => 'HTML',
   		'disable_web_page_preview' => TRUE,
    	);
    	$parameters["method"] = "sendMessage";
    	echo json_encode($parameters);
	}

	///////////
	/// FC0 ///
	///////////
	elseif(strpos($text, "/fc0") === 0 ){
		if (in_array($username, $admins)) {
			$query = "SELECT * FROM `update_times` WHERE `cell` = 'NEW_FC'";
			$result = mysqli_query($conn,$query);
			$row = mysqli_fetch_assoc($result);

			mysqli_query($conn,"UPDATE `update_times` SET `update_time` = '0' WHERE `cell` = 'NEW_FC'");
			$response = $EMO_v.' Previsioni meteo ripristinate.';
		}
		else {
			$data = [
		  		'chat_id' => $chatId,
		  		'text' => $EMO_ERR.' Solo gli admin possono utilizzare questo comando. '.$EMO_ERR,
			];
			$response = file_get_contents("https://api.telegram.org/bot$apiToken/sendMessage?" . http_build_query($data) );
		}

		$parameters = array('chat_id' => $chatId, "text" => $response, "parse_mode" => "HTML", "disable_web_page_preview" => TRUE);
		$parameters["method"] = "sendMessage";
		echo json_encode($parameters);
	}

	/////////////
	/// NEWFC ///
	/////////////
	elseif(strpos($text, "/fc") === 0 ){
		if (in_array($username, $admins)) {
			$query = "SELECT * FROM `update_times` WHERE `cell` = 'NEW_FC'";
			$result = mysqli_query($conn,$query);
			$row = mysqli_fetch_assoc($result);

			if ($row['update_time'] == '0') {
				mysqli_query($conn,"UPDATE `update_times` SET `update_time` = '1' WHERE `cell` = 'NEW_FC'");
				$response = $EMO_v.' Previsioni meteo aggiornate.';
			}
			else {
				$response = 'Le previsioni meteo sono già state aggiornate per oggi.';
			}
		}
		else {
			$data = [
		  		'chat_id' => $chatId,
		  		'text' => $EMO_ERR.' Solo gli admin possono utilizzare questo comando. '.$EMO_ERR,
			];
			$response = file_get_contents("https://api.telegram.org/bot$apiToken/sendMessage?" . http_build_query($data) );
		}

		$parameters = array('chat_id' => $chatId, "text" => $response, "parse_mode" => "HTML", "disable_web_page_preview" => TRUE);
		$parameters["method"] = "sendMessage";
		echo json_encode($parameters);
	}

###################
###### NESTS ######
###################

	//////////////////
	////// NEST //////
	//////////////////
	elseif(strpos($text, "/nest ") === 0 ) {
		$str = str_replace('/nest ', '', str_replace("'","\'",$text));

		$query = "SELECT * FROM `nestEnd`";
		$result = mysqli_query($conn,$query);
		$row = mysqli_fetch_assoc($result);
		$endDate = $row['endDate'];
		if ($today >= $endDate) {
			mysqli_query($conn,"TRUNCATE `nests`");
			$newEnd = date('Y-m-d', strtotime($endDate. ' + 14 days'));
			mysqli_query($conn,"UPDATE `nestEnd` SET `endDate` = '$newEnd' WHERE `endDate` = '$endDate'");
			$endDate = $newEnd;
		}

		$strArr = explode(", ",$str);
		$pkmn = ucfirst($strArr[0]);
		$nest = ucwords($strArr[1]);
		$query = "SELECT * FROM `nests` WHERE `nido` = '$nest'";
		$result = mysqli_query($conn,$query);
		$row = mysqli_fetch_assoc($result);
		$currNest = $row['nido'];

		$query2 = "SELECT * FROM `parks` WHERE `park` = '$nest'";
		$result2 = mysqli_query($conn,$query2);
		$row2 = mysqli_fetch_assoc($result2);
		$row2['lat'] != '' ? $latN = $row2['lat'] : $latN = '0';
		$row2['lng'] != '' ? $lngN = $row2['lng'] : $lngN = '0';
		$latN != '0' ? $link = "https://maps.google.com/?q=".$latN.",".$lngN."(".str_replace(" ","+",str_replace("\'","'",str_replace("\"","''",$nest))).")" : $link = "";

		// setlocale(LC_ALL, "ita");
		$endDate = str_replace(" ","",date("j/m", strtotime(str_replace('-','/', $endDate))));

		if ($currNest == $nest) {
			$response = 'Il nido di <a href="'.$link.'">'.str_replace("\'","'",$nest).'</a> è stato già registrato fino al <b>'.$endDate.'</b>.';
		}
		else {
			mysqli_query($conn,"INSERT INTO `nests` VALUES ('$nest','$pkmn',1)");
			$response = $EMO_v.' Nido di <b>'.$pkmn.'</b> a <a href="'.$link.'">'.str_replace("\'","'",$nest).'</a> registrato fino al <b>'.$endDate.'</b>.';
		}

		$parameters = array('chat_id' => $chatId, "text" => $response, "parse_mode" => "HTML", "disable_web_page_preview" => TRUE);
		$parameters["method"] = "sendMessage";
		echo json_encode($parameters);
	}

	//////////////////
	////// SPAWN /////
	//////////////////
	elseif(strpos($text, "/spawn ") === 0 ) {
		$str = str_replace('/spawn ', '', str_replace("'","\'",$text));

		$query = "SELECT * FROM `nestEnd`";
		$result = mysqli_query($conn,$query);
		$row = mysqli_fetch_assoc($result);
		$endDate = $row['endDate'];
		if ($today >= $endDate) {
			mysqli_query($conn,"TRUNCATE `nests`");
			$newEnd = date('Y-m-d', strtotime($endDate. ' + 14 days'));
			mysqli_query($conn,"UPDATE `nestEnd` SET `endDate` = '$newEnd' WHERE `endDate` = '$endDate'");
			$endDate = $newEnd;
		}

		$strArr = explode(", ",$str);
		$pkmn = ucfirst($strArr[0]);
		$nest = ucwords($strArr[1]);
		$query = "SELECT * FROM `nests` WHERE `nido` = '$nest'";
		$result = mysqli_query($conn,$query);
		$row = mysqli_fetch_assoc($result);
		$currNest = $row['nido'];

		$query2 = "SELECT * FROM `parks` WHERE `park` = '$nest'";
		$result2 = mysqli_query($conn,$query2);
		$row2 = mysqli_fetch_assoc($result2);
		$row2['lat'] != '' ? $latS = $row2['lat'] : $latS = '0';
		$row2['lng'] != '' ? $lngS = $row2['lng'] : $lngS = '0';
		$latS != '0' ? $link = "https://maps.google.com/?q=".$latS.",".$lngS."(".str_replace(" ","+",str_replace("\'","'",str_replace("\"","''",$nest))).")" : $link = "";

		// setlocale(LC_ALL, "ita");
		$endDate = str_replace(" ","",date("j/m", strtotime(str_replace('-','/', $endDate))));

		if ($currNest == $nest) {
			$response = 'Lo spawn frequente di <a href="'.$link.'">'.str_replace("\'","'",$nest).'</a> è stato già registrato fino al <b>'.$endDate.'</b>.';
		}
		else {
			mysqli_query($conn,"INSERT INTO `nests` VALUES ('$nest','$pkmn',2)");
			$response = $EMO_v.' Spawn frequente di <b>'.$pkmn.'</b> a <a href="'.$link.'">'.str_replace("\'","'",$nest).'</a> registrato fino al <b>'.$endDate.'</b>.';
		}

		$parameters = array('chat_id' => $chatId, "text" => $response, "parse_mode" => "HTML", "disable_web_page_preview" => TRUE);
		$parameters["method"] = "sendMessage";
		echo json_encode($parameters);
	}

	/////////////////
	//// DELNEST ////
	/////////////////
	elseif(strpos($text, "/delnest") === 0 ) {
		$nest = ucfirst(str_replace('/delnest ', '', str_replace("'","\'",$text)));
		$query = "SELECT * FROM `nests` WHERE `nido` = '$nest'";
		$result = mysqli_query($conn,$query);
		$row = mysqli_fetch_assoc($result);
		$currNest = $row['nido'];
		if (!$row) {
			$response = $EMO_ERR.' Nido di *'.str_replace("\'","'",$nest).'* non trovato.';
		}
		else {
			$response = $EMO_x.' Nido di *'.str_replace("\'","'",$nest).'* cancellato.';
			mysqli_query($conn,"DELETE FROM `nests` WHERE `nido` = '$nest'");
		}
		$parameters = array('chat_id' => $chatId, "text" => $response, "parse_mode" => "markdown", "disable_web_page_preview" => TRUE);
		$parameters["method"] = "sendMessage";
		echo json_encode($parameters);
	}

	//////////////////
	////// NIDI //////
	//////////////////
	elseif(strpos($text, "/nidi") === 0 ) {
		$query = "SELECT * FROM `nestEnd`";
		$result = mysqli_query($conn,$query);
		$row = mysqli_fetch_assoc($result);
		$endDate = $row['endDate'];
		if ($today >= $endDate) {
			mysqli_query($conn,"TRUNCATE `nests`");
			$newEnd = date('Y-m-d', strtotime($endDate. ' + 14 days'));
			mysqli_query($conn,"UPDATE `nestEnd` SET `endDate` = '$newEnd' WHERE `endDate` = '$endDate'");
			$endDate = $newEnd;
		}

		$query = "SELECT * FROM `nests` WHERE `type` = 1 ORDER BY `pokemon` ASC";
		$result = mysqli_query($conn,$query);
		$nest = $pkmnN = $spawn = $pkmnS = $latN = $lngN = $latS = $lngS = array();
		while ($row = mysqli_fetch_assoc($result)) {
			array_push($nest, $row['nido']);
			array_push($pkmnN, $row['pokemon']);

			$parkTMP = $row['nido'];
			$query2 = "SELECT * FROM `parks` WHERE `park` = '$parkTMP'";
			$result2 = mysqli_query($conn,$query2);
			$row2 = mysqli_fetch_assoc($result2);
			$row2['lat'] != '' ? array_push($latN, $row2['lat']) : array_push($latN, '0');
			$row2['lng'] != '' ? array_push($lngN, $row2['lng']) : array_push($lngN, '0');
		}

		$query = "SELECT * FROM `nests` WHERE `type` = 2 ORDER BY `pokemon` ASC";
		$result = mysqli_query($conn,$query);
		while ($row = mysqli_fetch_assoc($result)) {
			array_push($spawn, $row['nido']);
			array_push($pkmnS, $row['pokemon']);

			$parkTMP = $row['nido'];
			$query2 = "SELECT * FROM `parks` WHERE `park` = '$parkTMP'";
			$result2 = mysqli_query($conn,$query2);
			$row2 = mysqli_fetch_assoc($result2);
			$row2['lat'] != '' ? array_push($latS, $row2['lat']) : array_push($latS, '0');
			$row2['lng'] != '' ? array_push($lngS, $row2['lng']) : array_push($lngS, '0');
		}

		// setlocale(LC_ALL, "ita");
		$endDate = str_replace(" ","",date("j/m", strtotime(str_replace('-','/', $endDate))));

		if (!$nest and !$spawn) {
			$response = 'Nessun nido segnalato fino al <b>'.$endDate.'</b>.';
		}
		else {
			$response = "";
			if ($nest) {
				$response = $EMO_TREE .' Nidi fino al <b>'.$endDate.'</b>:';
				for ($i = 0; $i <= sizeof($nest)-1; $i++){
					$latN[$i] != '0' ? $link = "https://maps.google.com/?q=".$latN[$i].",".$lngN[$i]."(".str_replace(" ","+",str_replace("\'","'",str_replace("\"","''",$nest[$i]))).")" : $link = "";
					$response = $response."\n<b>".$pkmnN[$i]."</b> − ".'<a href="'.$link.'">'.$nest[$i].'</a>';
				}
			}
			if ($spawn) {
				$response = $response."\n\n".$EMO_LEAF .' Spawn frequenti fino al <b>'.$endDate.'</b>:';
				for ($i = 0; $i <= sizeof($spawn)-1; $i++){
					$latS[$i] != '0' ? $link = "https://maps.google.com/?q=".$latS[$i].",".$lngS[$i]."(".str_replace(" ","+",str_replace("\'","'",str_replace("\"","''",$spawn[$i]))).")" : $link = "";
					$response = $response."\n<b>".$pkmnS[$i]."</b> − ".'<a href="'.$link.'">'.$spawn[$i].'</a>';
				}
			}
		}
		$parameters = array('chat_id' => $chatId, "text" => $response, "parse_mode" => "HTML", "disable_web_page_preview" => TRUE);
		$parameters["method"] = "sendMessage";
		echo json_encode($parameters);
	}

	/////////////////
	/// RESETNEST ///
	/////////////////
	elseif(strpos($text, "/resetnests") === 0 ) {
		if (in_array($username, $admins)) {
			mysqli_query($conn,"TRUNCATE `nests`");
			$data = [
		  		'chat_id' => $chatId,
		  		'text' => $EMO_x.' Nidi resettati.',
			];
			$response = file_get_contents("https://api.telegram.org/bot$apiToken/sendMessage?" . http_build_query($data) );
		}
		else {
			$data = [
		  		'chat_id' => $chatId,
		  		'text' => $EMO_ERR.' Solo gli admin possono utilizzare questo comando. '.$EMO_ERR,
			];
			$response = file_get_contents("https://api.telegram.org/bot$apiToken/sendMessage?" . http_build_query($data) );
		}
	}

	//////////////////
	//// NEWPARK /////
	//////////////////
	elseif(strpos($text, "/newpark") === 0 ) {
		if (in_array($username, $admins)) {
			$str = str_replace('/newpark ', '', str_replace("'","\'",$text));

			$strArr = explode(", ",$str);
			$park = ucfirst($strArr[0]);
			$lat = ucwords($strArr[1]);
			$lng = ucwords($strArr[2]);
			$link = "https://maps.google.com/?q=".$lat.",".$lng."(".str_replace(" ","+",str_replace("\'","'",str_replace("\"","''",$park))).")";

			$query = "SELECT * FROM `parks` WHERE `park` = '$park'";
			$result = mysqli_query($conn,$query);
			$row = mysqli_fetch_assoc($result);

			if (!$row) {
				mysqli_query($conn,"INSERT INTO `parks` VALUES ('$park','$lat','$lng')");
				$response = $EMO_v.' <a href="'.$link.'">'.str_replace("\'","'",$park).'</a> aggiunto/a al database dei parchi.';
			}
			else {
				$response = '<a href="'.$link.'">'.str_replace("\'","'",$park).'</a> è già presente nel database.';
			}
		}
		else {
			$data = [
		  		'chat_id' => $chatId,
		  		'text' => $EMO_ERR.' Solo gli admin possono utilizzare questo comando. '.$EMO_ERR,
			];
			$response = file_get_contents("https://api.telegram.org/bot$apiToken/sendMessage?" . http_build_query($data) );
		}

		$parameters = array('chat_id' => $chatId, "text" => $response, "parse_mode" => "HTML", "disable_web_page_preview" => TRUE);
		$parameters["method"] = "sendMessage";
		echo json_encode($parameters);
	}

	/////////////////
	//// DELPARK ////
	/////////////////
	elseif(strpos($text, "/delpark") === 0 ) {
		if (in_array($username, $admins)) {
			$park = ucwords(str_replace('/delpark ', '', str_replace("'","\'",$text)));
			$query = "SELECT * FROM `parks` WHERE `park` = '$park'";
			$result = mysqli_query($conn,$query);
			$row = mysqli_fetch_assoc($result);
			if (!$row) {
				$response = $EMO_ERR.' *'.str_replace("\'","'",$park).'* non trovato/a.';
			}
			else {
				$response = $EMO_x.' *'.str_replace("\'","'",$park).'* rimosso/a dal database dei parchi.';
				mysqli_query($conn,"DELETE FROM `parks` WHERE `park` = '$park'");
			}
			$parameters = array('chat_id' => $chatId, "text" => $response, "parse_mode" => "markdown", "disable_web_page_preview" => TRUE);
			$parameters["method"] = "sendMessage";
			echo json_encode($parameters);
		}
		else {
			$data = [
		  		'chat_id' => $chatId,
		  		'text' => $EMO_ERR.' Solo gli admin possono utilizzare questo comando. '.$EMO_ERR,
			];
			$response = file_get_contents("https://api.telegram.org/bot$apiToken/sendMessage?" . http_build_query($data) );
		}
	}

	///////////////
	//// PARKS ////
	///////////////
	elseif(strpos($text, "/parks") === 0 ) {
		$query = "SELECT * FROM `parks` ORDER BY `park` ASC";
		$result = mysqli_query($conn,$query);
		$park = $lat = $lng = array();
		while ($row = mysqli_fetch_assoc($result)) {
			array_push($park, $row['park']);
			array_push($lat, $row['lat']);
			array_push($lng, $row['lng']);
		}

		$response = $EMO_TREE.$EMO_TREE2.' <b>Elenco dei parchi </b>'.$EMO_TREE2.$EMO_TREE ;
		for ($i = 0; $i <= sizeof($park)-1; $i++){
			$link = "https://maps.google.com/?q=".$lat[$i].",".$lng[$i]."(".str_replace(" ","+",str_replace("\'","'",str_replace("\"","''",$park[$i]))).")";
			$response = $response. "\n− ".'<a href="'.$link.'">'.$park[$i].'</a>';
		}

		$parameters = array('chat_id' => $chatId, "text" => $response, "parse_mode" => "HTML", "disable_web_page_preview" => TRUE);
		$parameters["method"] = "sendMessage";
		echo json_encode($parameters);
	}

###############################
###### CODICI ALLENATORE ######
###############################

	///////////////////
	///// ADDCODE /////
	///////////////////
	elseif(strpos($text, "/addcode ") === 0 ) {
		$str = str_replace('/addcode ', '', str_replace("'","\'",$text));

		$strArr = explode(", ",$str);
		$trainer = $strArr[0];
		$code = $strArr[1];
		$query = "SELECT * FROM `codes` WHERE `userId` = '$userId'";
		$result = mysqli_query($conn,$query);
		$row = mysqli_fetch_assoc($result);
		$currUserId = $row['userId'];

		if ($currUserId == $userId) {
			$response = 'Hai già registrato il tuo codice amico.';
		}
		else {
			mysqli_query($conn,"INSERT INTO `codes` VALUES ('$trainer','$username','$firstname','$userId','$code')");
			$response = $EMO_v.' Codice amico registrato.';
		}

		$parameters = array('chat_id' => $chatId, "text" => $response, "parse_mode" => "markdown", "disable_web_page_preview" => TRUE);
		$parameters["method"] = "sendMessage";
		echo json_encode($parameters);
	}

	/////////////////
	//// DELCODE ////
	/////////////////
	elseif(strpos($text, "/delcode") === 0 ) {
		mysqli_query($conn,"DELETE FROM `codes` WHERE `telegram` = '$username'");
		$response = $EMO_x.' Codice amico rimosso.';

		$parameters = array('chat_id' => $chatId, "text" => $response, "parse_mode" => "markdown", "disable_web_page_preview" => TRUE);
		$parameters["method"] = "sendMessage";
		echo json_encode($parameters);
	}

	//////////////////
	///// CODICI /////
	//////////////////
	elseif(strpos($text, "/codici") === 0 ) {
		$query = "SELECT * FROM `codes` ORDER BY `trainer` ASC";
		$result = mysqli_query($conn,$query);

		$trainer = $telegram = $telegramName = $telegramId = $code = array();
		while ($row = mysqli_fetch_assoc($result)) {
			array_push($trainer, $row['trainer']);
			array_push($telegram, $row['telegram']);
			array_push($telegramName, $row['telegramName']);
			array_push($telegramId, $row['userId']);
			array_push($code, $row['code']);
		}

		$response = $EMO_NUM .' Lista dei codici amico:';
		for ($i = 0; $i <= sizeof($trainer)-1; $i++){
			$telegram[$i] != '' ? $telegramDispName = $telegram[$i] : $telegramDispName = $telegramName[$i];
			// $tgLink = "[".$telegram[$i]."](https://t.me/".$telegramId[$i].")";
			$tgLink = "[".$telegramDispName."](tg://user?id=".$telegramId[$i].")";

			$response = $response."\n*".$trainer[$i]."* − ".$tgLink."\n`".$code[$i].'`';
		}
		$parameters = array('chat_id' => $chatId, "text" => $response, "parse_mode" => "markdown", "disable_web_page_preview" => TRUE);
		$parameters["method"] = "sendMessage";
		echo json_encode($parameters);
	}

####################
###### ALERTS ######
####################

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

	//////////////
	/// ALERTS ///
	//////////////
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

###################
###### CELLS ######
###################

	////////////////
	/// ADD CELL ///
	////////////////
	elseif(strpos($text, "/addcell") === 0 ) {
		if (in_array($username, $admins)) {
			$data = explode(', ', str_replace('/addcell ', '', $text));
			$cellId = $data[0];
			$name = ucfirst(str_replace("'","\'",$data[1]));
			$cellId64 = $cellId . str_repeat("0",16-strlen($cellId));

			$query = "SELECT * FROM `zones` WHERE cellId = '$cellId'";
			$result = mysqli_query($conn,$query);
			$row = mysqli_fetch_assoc($result);
			if (!$row) {
				$response = $EMO_v." La cella *".$cellId."* è stata registrata come *\"".str_replace("\'","'",$name)."\"*.";
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
		else {
			$data = [
		  		'chat_id' => $chatId,
		  		'text' => $EMO_ERR.' Solo gli admin possono utilizzare questo comando. '.$EMO_ERR,
			];
			$response = file_get_contents("https://api.telegram.org/bot$apiToken/sendMessage?" . http_build_query($data) );
		}
	}

	////////////////
	/// DEL CELL ///
	////////////////
	elseif(strpos($text, "/delcell") === 0) {
		if (in_array($username, $admins)) {
			$name = str_replace('/delcell ', '', str_replace("'","\'",$text));

			$query = "SELECT * FROM `zones` WHERE name = '$name'";
			$result = mysqli_query($conn,$query);
			$row = mysqli_fetch_assoc($result);
			if (!$row) {
				$response = $EMO_ERR.' Cella *'.str_replace("\'","'",$name).'* non trovata.';
			}
			else {
				$response = $EMO_x.' La cella *'.str_replace("\'","'",$name).'* è stata rimossa.';
				mysqli_query($conn,"DELETE FROM `zones` WHERE name = '$name'");
			}
			$parameters = array('chat_id' => $chatId, "text" => $response, "parse_mode" => "markdown", "disable_web_page_preview" => TRUE);
			$parameters["method"] = "sendMessage";
			echo json_encode($parameters);
		}
		else {
			$data = [
		  		'chat_id' => $chatId,
		  		'text' => $EMO_ERR.' Solo gli admin possono utilizzare questo comando. '.$EMO_ERR,
			];
			$response = file_get_contents("https://api.telegram.org/bot$apiToken/sendMessage?" . http_build_query($data) );
		}
	}

	/////////////
	/// CELLS ///
	/////////////
	elseif(strpos($text, "/cells") === 0) {
		$query = "SELECT * FROM `zones` ORDER BY name ASC";
		$result = mysqli_query($conn,$query);
		$cell = $name = $lat = $lng = $zoom = array();
		while ($row = mysqli_fetch_assoc($result)) {
			array_push($cell, $row['cellId']);
			array_push($name, str_replace("\'","'",$row['name']));

			list($_lat, $_lng, $_zoom) = getCellData(hexdec($row['cellId64']), 2);
			array_push($zoom, $_zoom);
			array_push($lat, $_lat);
			array_push($lng, $_lng);
		}

		if (!$cell) {
			$response = 'Non è stata attivata ancora nessuna cella per le notifiche quest. Aggiungine una con il comando `/register <ID-cella>`.';
		}
		else {
			$link_all = "https://s2.sidewalklabs.com/regioncoverer/?center=41.891165%2C12.492826&zoom=12&cells=";
			for ($i = 0; $i <= sizeof($cell)-1; $i++){
				$link_all = $link_all . "%2C" . $cell[$i];
			}
			$response = $EMO_GLO." Lista delle [celle attive](".$link_all."): ".$EMO_GLO;
			for ($i = 0; $i <= sizeof($cell)-1; $i++){
				$link = "https://s2.sidewalklabs.com/regioncoverer/?center=". $lat[$i] ."%2C". $lng[$i] . "&zoom=" . $zoom[$i] . "&cells=" . $cell[$i];
				$response = $response."\n*".$name[$i]."* − [".$cell[$i]."](".$link.")";
			}
		}
		$parameters = array('chat_id' => $chatId, "text" => $response, "parse_mode" => "markdown", "disable_web_page_preview" => TRUE);
		$parameters["method"] = "sendMessage";
		echo json_encode($parameters);
	}

	//////////////////
	/// GROUPCELLS ///
	//////////////////
	elseif(strpos($text, "/groupcells") === 0) {
		$query = "SELECT * FROM `zones` WHERE `groups` LIKE CONCAT('%','$chatId','%') ORDER BY name ASC";
		$result = mysqli_query($conn,$query);
		$cell = $name = $lat = $lng = $zoom = array();
		while ($row = mysqli_fetch_assoc($result)) {
			array_push($cell, $row['cellId']);
			array_push($name, str_replace("\'","'",$row['name']));

			list($_lat, $_lng, $_zoom) = getCellData(hexdec($row['cellId64']), 2);
			array_push($zoom, $_zoom);
			array_push($lat, $_lat);
			array_push($lng, $_lng);
		}

		if (!$cell) {
			$response = 'Nessuna cella è attiva per le notifiche quest in questo gruppo. Aggiungine una con il comando `/register <nome-cella>`.';
		}
		else {
			$link_all = "https://s2.sidewalklabs.com/regioncoverer/?center=41.891165%2C12.492826&zoom=12&cells=";
			for ($i = 0; $i <= sizeof($cell)-1; $i++){
				$link_all = $link_all . "%2C" . $cell[$i];
			}
			$response = $EMO_GLO." Lista delle [celle attive](".$link_all.") per le quest del gruppo: ".$EMO_GLO;
			for ($i = 0; $i <= sizeof($cell)-1; $i++){
				$link = "https://s2.sidewalklabs.com/regioncoverer/?center=". $lat[$i] ."%2C". $lng[$i] . "&zoom=" . $zoom[$i] . "&cells=" . $cell[$i];
				$response = $response."\n*".$name[$i]."* − [".$cell[$i]."](".$link.")";
			}
		}
		$parameters = array('chat_id' => $chatId, "text" => $response, "parse_mode" => "markdown", "disable_web_page_preview" => TRUE);
		$parameters["method"] = "sendMessage";
		echo json_encode($parameters);
	}

	////////////////
	/// REGISTER ///
	////////////////
	elseif(strpos($text, "/register") === 0) {
		if (in_array($username, $admins)) {
			$cell = ucfirst(str_replace('/register ', '', $text));
			$query = "SELECT * FROM `zones` WHERE name = '$cell'";
			$result = mysqli_query($conn,$query);
			$row = mysqli_fetch_assoc($result);
			$currGropus = $row['groups'];
			$cellId = $row['cellId'];
			$zona = str_replace("'","\'",$row['name']);
			list($lat, $lng, $zoom) = getCellData(hexdec($row['cellId64']), 2);

			if (!$row) {
				$response = $EMO_ERR.' Cella *'.$cell.'* non trovata. Registrala prima con il comando `/addcell <IDcella>`.';
			}
			else {
				$link = "https://s2.sidewalklabs.com/regioncoverer/?center=". $lat ."%2C". $lng . "&zoom=" . $zoom . "&cells=" . $cellId;
				if (stristr($currGropus,strval($chatId))) {
					$response = "Questo gruppo è già associato alla cella [".$cell."](".$link.") − \"".$zona."\".";
				}
				else {
					$response = $EMO_GLO." Il gruppo è stato associato alla cella [".$cell."](".$link.").";
					mysqli_query($conn,"UPDATE `zones` SET `groups` = concat('$currGropus', '$chatId', ',') WHERE `name` = '$cell'");
				}
			}
			$parameters = array('chat_id' => $chatId, "text" => $response, "parse_mode" => "markdown", "disable_web_page_preview" => TRUE);
			$parameters["method"] = "sendMessage";
			echo json_encode($parameters);
		}
		else {
			$data = [
		  		'chat_id' => $chatId,
		  		'text' => $EMO_ERR.' Solo gli admin possono utilizzare questo comando. '.$EMO_ERR,
			];
			$response = file_get_contents("https://api.telegram.org/bot$apiToken/sendMessage?" . http_build_query($data) );
		}
	}

	//////////////////
	/// UNREGISTER ///
	//////////////////
	elseif(strpos($text, "/unregister") === 0 ) {
		if (in_array($username, $admins)) {
			$cell = ucfirst(str_replace('/unregister ', '', $text));
			$query = "SELECT * FROM `zones` WHERE name = '$cell'";
			$result = mysqli_query($conn,$query);
			$row = mysqli_fetch_assoc($result);
			$currGropus = $row['groups'];
			$cellId = $row['cellId'];
			list($lat, $lng, $zoom) = getCellData(hexdec($row['cellId64']), 2);

			if (!$row) {
				$response = $EMO_ERR.' Cella *'.$cell.'* non trovata.';
			}
			else {
				$link = "https://s2.sidewalklabs.com/regioncoverer/?center=". $lat ."%2C". $lng . "&zoom=" . $zoom . "&cells=" . $cellId;
				$response = $EMO_x." Il gruppo è stato rimosso dalla cella [".$cell."](".$link.").";
			}
			$parameters = array('chat_id' => $chatId, "text" => $response, "parse_mode" => "markdown", "disable_web_page_preview" => TRUE);
			$parameters["method"] = "sendMessage";
			echo json_encode($parameters);
			mysqli_query($conn,"UPDATE `zones` SET `groups` = replace('$currGropus',concat('$chatId', ','), '') WHERE `name` = '$cell'");
		}
		else {
			$data = [
		  		'chat_id' => $chatId,
		  		'text' => $EMO_ERR.' Solo gli admin possono utilizzare questo comando. '.$EMO_ERR,
			];
			$response = file_get_contents("https://api.telegram.org/bot$apiToken/sendMessage?" . http_build_query($data) );
		}
	}

###################
###### ADMIN ######
###################

	//////////////////
	/// ADD ADMINS ///
	//////////////////
	elseif(strpos($text, "/addadmin") === 0 ) {
		if (in_array($username, $admins)) {
			$admin = str_replace('/addadmin ', '', $text);
			mysqli_query($conn,"INSERT INTO `admins` VALUES ('$admin')");

			$response = $EMO_v.' *'.$admin.'* aggiunto come amministratore del bot.';
			$parameters = array('chat_id' => $chatId, "text" => $response, "parse_mode" => "markdown", "disable_web_page_preview" => TRUE);
			$parameters["method"] = "sendMessage";
			echo json_encode($parameters);
		}
		else {
			$data = [
		  		'chat_id' => $chatId,
		  		'text' => $EMO_ERR.' Solo gli admin possono utilizzare questo comando. '.$EMO_ERR,
			];
			$response = file_get_contents("https://api.telegram.org/bot$apiToken/sendMessage?" . http_build_query($data) );
		}
	}

	//////////////////
	/// DEL ADMINS ///
	//////////////////
	elseif(strpos($text, "/deladmin") === 0 ) {
		if (in_array($username, $admins)) {
			$admin = str_replace('/deladmin ', '', $text);
			mysqli_query($conn,"DELETE FROM `admins` WHERE `username` = '$admin'");

			$response = $EMO_x.' *'.$admin.'* rimosso dagli amministratori del bot.';
			$parameters = array('chat_id' => $chatId, "text" => $response, "parse_mode" => "markdown", "disable_web_page_preview" => TRUE);
			$parameters["method"] = "sendMessage";
			echo json_encode($parameters);
		}
		else {
			$data = [
		  		'chat_id' => $chatId,
		  		'text' => $EMO_ERR.' Solo gli admin possono utilizzare questo comando. '.$EMO_ERR,
			];
			$response = file_get_contents("https://api.telegram.org/bot$apiToken/sendMessage?" . http_build_query($data) );
		}
	}

	//////////////
	/// ADMINS ///
	//////////////
	elseif(strpos($text, "/admins") === 0 ) {
		$response = "Elenco degli amministratori del bot:\n";
		foreach ($admins as $key => $value) {
			$response = $response . "− @" . $value . "\n";
		}
		$data = [
		  	'chat_id' => $chatId,
		  	'text' => $response,
		];
		$response = file_get_contents("https://api.telegram.org/bot$apiToken/sendMessage?" . http_build_query($data) );
	}

	//////////////////
	/// ADD GROUPS ///
	//////////////////
	elseif(strpos($text, "/addgroup") === 0 ) {
		if (in_array($username, $admins)) {
			$group = explode(', ', str_replace('/addgroup ', '', $text));
			$groupName = $group[0];
			$groupID = $group[1];
			mysqli_query($conn,"INSERT INTO `auth_groups` VALUES ('$groupName', '$groupID')");

			$response = $EMO_v.' *'.$groupName.'* aggiunto ai gruppi di competenza del bot.';
			$parameters = array('chat_id' => $chatId, "text" => $response, "parse_mode" => "markdown", "disable_web_page_preview" => TRUE);
			$parameters["method"] = "sendMessage";
			echo json_encode($parameters);
		}
		else {
			$data = [
		  		'chat_id' => $chatId,
		  		'text' => $EMO_ERR.' Solo gli admin possono utilizzare questo comando. '.$EMO_ERR.json_encode($admins),
			];
			$response = file_get_contents("https://api.telegram.org/bot$apiToken/sendMessage?" . http_build_query($data) );
		}
	}

	//////////////////
	/// DEL GROUPS ///
	//////////////////
	elseif(strpos($text, "/delgroup") === 0 ) {
		if (in_array($username, $admins)) {
			$group = str_replace('/delgroup ', '', $text);
			mysqli_query($conn,"DELETE FROM `auth_groups` WHERE `groupName` = '$group'");

			$response = $EMO_x.' *'.$group.'* rimosso dai gruppi di competenza del bot.';
			$parameters = array('chat_id' => $chatId, "text" => $response, "parse_mode" => "markdown", "disable_web_page_preview" => TRUE);
			$parameters["method"] = "sendMessage";
			echo json_encode($parameters);
		}
		else {
			$data = [
		  		'chat_id' => $chatId,
		  		'text' => $EMO_ERR.' Solo gli admin possono utilizzare questo comando. '.$EMO_ERR.json_encode($admins),
			];
			$response = file_get_contents("https://api.telegram.org/bot$apiToken/sendMessage?" . http_build_query($data) );
		}
	}

	//////////////
	/// GROUPS ///
	//////////////
	elseif(strpos($text, "/groups") === 0 ) {
		$response = "Elenco dei gruppi attivi:\n";
		foreach ($authorizedChatsNames as $key => $value) {
			$response = $response . "− " . $value . "\n";
		}
		$data = [
		  	'chat_id' => $chatId,
		  	'text' => $response,
		];
		$response = file_get_contents("https://api.telegram.org/bot$apiToken/sendMessage?" . http_build_query($data) );
	}
}

elseif($status == 2) {
	$quest = $alert;

	list($pkst, $lat, $lng) = getPortalData($text, $URLs[1]['url']);

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
		$link = "https://maps.google.com/?q=".$lat.",".$lng."(".str_replace(" ","+",str_replace("\'","'",str_replace("\"","''",$pkst))).")";		// BETA
		// $link = 'https://maps.google.com/?q='.$lat.','.$lng;
		if ($om_pkst == str_replace("\'","'",$pkst) and $om_lat == $lat and $om_lng == $lng) {				// IN REALTÀ BISOGNEREBBE FAR EIL CONFRONTO CON TUTTI GLI OMONINI! CI VUOLE while
			// AVVISO DI QUEST GIÀ SEGNALATA
			$response = $EMO_v.' La quest di questo pokéstop è stata già segnalata per oggi.';
			$parameters = array('chat_id' => $userId, "text" => $response, "parse_mode" => "markdown");
			$parameters["method"] = "sendMessage";
			echo json_encode($parameters);
			mysqli_query($conn,"DELETE FROM `sessions` WHERE userID = $userId");
		}
		else {
			$query = "SELECT * FROM `tasks` WHERE '$quest' LIKE CONCAT('%',`reward`,'%')";
			$result = mysqli_query($conn,$query);
			$row2 = mysqli_fetch_assoc($result);
			$flag = $row2['flag'];
			$task = $row2['task'];

			// NOTIFICA UTENTI CON NOTIFICHE ATTIVE NELLA CHAT DEL BOT
			// $query = "SELECT * FROM `pokeid` WHERE `pokemon` = '$quest'"; 		// Old query
			$query = "SELECT * FROM `pokeid` WHERE '$quest' LIKE CONCAT('%',`pokemon`,'%')";
			$result = mysqli_query($conn,$query);
			$row3 = mysqli_fetch_assoc($result);
			$userAlerts = $row3['userAlerts'];
			$userAlertsIDs = explode(',', $userAlerts);
			foreach ($userAlertsIDs as $userAlertsID) {
				if ($userAlertsID != $userId) {
					$data = [
			  			'chat_id' => $userAlertsID,
			  			 //'text' => "`Quest:   ` *". $quest . "*\n`Pokéstop:` [" . str_replace("\'","'",$pkst) . "](" . $link . ")\n`Giorno:  ` ".$today2."\n`Task:    ` ". $task,
			  			'text' => "<code>Quest:    </code><b>". $quest ."</b>\n<code>Pokéstop: </code>".'<a href="'.$link.'">'.str_replace("\'","'",$pkst)."</a>\n<code>Giorno:   </code>".$today2."\n<code>Task:     </code>".$task,
			  			//'text' => "`Quest:   ` *". $quest . "*\n`Pokéstop:` [" . $pkst . "](" . $link . ")\n`Giorno:  ` ".$today2,
			  			'parse_mode' => 'HTML',
			  			// 'parse_mode' => 'markdown',
			  			'disable_web_page_preview' => TRUE,
					];
					$response = file_get_contents("https://api.telegram.org/bot$apiToken/sendMessage?" . http_build_query($data) );
				}
			}

			// SEGNALA LA QUEST NEL CANALE - CONTROLLO FLAG MISSIONI RARE
			if ($flag == 1) {
				$data = [
			  		'chat_id' => $channel,
			  		// 'text' => "`Quest:   ` *". $quest . "*\n`Pokéstop:` [" . str_replace("\'","'",$pkst) . "](" . $link . ")\n`Giorno:  ` ".$today2."\n`Task:    ` ". $task,
			  		'text' => "<code>Quest:    </code><b>". $quest ."</b>\n<code>Pokéstop: </code>".'<a href="'.$link.'">'.str_replace("\'","'",$pkst)."</a>\n<code>Giorno:   </code>".$today2."\n<code>Task:     </code>".$task,
			  		//'text' => "`Quest:   ` *". $quest . "*\n`Pokéstop:` [" . $pkst . "](" . $link . ")\n`Giorno:  ` ".$today2,
			  		'parse_mode' => 'HTML',
			  		'disable_web_page_preview' => TRUE,
				];
				$response = file_get_contents("https://api.telegram.org/bot$apiToken/sendMessage?" . http_build_query($data) );
			}

			// INVIA MESSAGGIO NEL GRUPPO SE REGISTRATO ALLE CELLE CHE CONTENGONO LA QUEST
			$minlevel = 10;
			$maxlevel = 13;
			$zone = '';
			for ($i = $minlevel; $i <=$maxlevel; $i++) {
				$tmp = getPortalZone($i, $lat, $lng, $conn);
				if ($tmp != NULL) {
					$zone = $zone . $tmp . ', ';
				}
			}

			$query = "SELECT * FROM `zones` WHERE '$zone' LIKE CONCAT('%', name, '%')";
			$result = mysqli_query($conn,$query);
			$groupsIDs = array();
			$strTMP = '';
			while ($row = mysqli_fetch_assoc($result)) {
				$groupSTR = $row['groups'];
				$strTMP = $strTMP . $groupSTR;
				$groupsIDs = explode(',', $strTMP);
			}
			$groupsIDs = array_unique($groupsIDs);

			for ($i = 0; $i <= sizeof($groupsIDs)-2; $i++) {
				$grp = intval($groupsIDs[$i]);
				$data = [
				  	'chat_id' => $grp,
				  	//'text' => $firstname . " ha segnalato una quest *" . $quest . "* presso [" . str_replace("\'","'",$pkst) . "](" . $link . ").",
				  	'text' => $firstname . " ha segnalato una quest <b>" . $quest . "</b> presso ".'<a href = "'. $link .'">' . str_replace("\'","'",$pkst)."</a>.",
				  	'parse_mode' => 'HTML',
				  	'disable_web_page_preview' => TRUE,
				];
				$response = file_get_contents("https://api.telegram.org/bot$apiToken/sendMessage?" . http_build_query($data) );
			}

			$response = $EMO_v.' La quest è stata registrata.';
			$parameters = array('chat_id' => $userId, "text" => $response, "parse_mode" => "markdown");
			$parameters["method"] = "sendMessage";
			echo json_encode($parameters);

			// REGISTRA LA QUEST NEL DATABASE E RESETTA LA SESSIONE DELL'UTENTE
			mysqli_query($conn,"INSERT INTO `quests` (quest, pokestop, lat, lng, zona, giorno) VALUES ('$quest', '$pkst', '$lat', '$lng', '$zone', '$today')");
			mysqli_query($conn,"DELETE FROM `sessions` WHERE `userID` = $userId");
		}
	}
}

//close the mySQL connection
$conn->close();


