<?php


//include 'config.php';
include 'telegramFunctions.php';
include 'include.php';

//global $apiURL;


//$token = '23456:ABC-DEF1234ghIkl-zyx57W2v1u123ew11';
//$apiURL = 'https://api.telegram.org/bot'.$token.'/';
//$botName = getMe()->{'username'};


//global $plugins;
$plugins = [];


/**
spl_autoload_register(function ($class_name) {
    include 'plugins/'.$class_name . '.php';
});
*/

########################### Load Plugins ###########################

$path    = './plugins';
$files = scandir($path);
print_r($files);

foreach ($files as &$file) {
	if($file != '.' && $file != '..' && !is_dir('./plugins/'.	$file)){
		echo $file;
		$pluginName = substr($file, 0, -4);
		include './plugins/'.$pluginName . '.php';
		array_push($plugins, new $pluginName);
	}
}
print_r($plugins);
//bla

####################################################################


function processPluginResponse($pluginResponse, $data)
{
	$pluginResponseArray = explode('|', $pluginResponse);
	switch ($pluginResponseArray[0]) {
		case "replay":
			$message_ID = $data->{'message'}->{'message_id'};
			$chat_ID = $data->{'message'}->{'chat'}->{'id'};
			$message = $pluginResponseArray[1];
			replayMessage($chat_ID, $message, $message_ID);
			break;
		case "join":
			// Do something with the Data
			break;
		case "leave":
			// Do something with the Data
			break;
		case "photo":
			// Do something with the Data
			break;
		default:
			// Don't handle the event Type
		}
	//Code
}

function triggerEvent($eventType, $data, $plugins)
{
	foreach ($plugins as &$plugin) {
		$response = $plugin->handelEvent($eventType, $data);
		$responseArray = explode('|+|', $response);
		foreach ($responseArray as &$pluginResponse) {
			processPluginResponse($pluginResponse, $data);
		}
	}
	//Code
}



############################# Get Data #############################

$rawData = file_get_contents("php://input");
$jsonData = json_decode($rawData);

ob_start();
var_dump($jsonData);
$result = ob_get_clean();

syslog(LOG_DEBUG, 'rawData: ' .$rawData);

########################### process Data ###########################

$update_id = $jsonData->{'update_id'};

if (!strpos($rawData, 'new_chat_member') === false) {
	// join Message
	goto not_for_me;
	$message_ID = $jsonData->{'message'}->{'message_id'};
	$chat_ID = $jsonData->{'message'}->{'chat'}->{'id'};
	$user = $jsonData->{'message'}->{'new_chat_member'};
	$user_id = $user->{'id'};
	$user_is_bot = $user->{'is_bot'};
	$user_first_name = $user->{'first_name'};
	if (!strpos($user, 'username') === false) {
		$user_username = $user->{'username'};
	}
	if (!strpos($user, 'language_code') === false) {
		$user_language_code = $user->{'language_code'};
	} else { $user_language_code = $language_code;}
	triggerEvent('join', $jsonData);
	//code
	
}elseif (!strpos($rawData, 'left_chat_member') === false) {
	// leave Message
	goto not_for_me;
	$message_ID = $jsonData->{'message'}->{'message_id'};
	$chat_ID = $jsonData->{'message'}->{'chat'}->{'id'};
	$user = $jsonData->{'message'}->{'left_chat_member'};
	$user_id = $user->{'id'};
	$user_is_bot = $user->{'is_bot'};
	$user_first_name = $user->{'first_name'};
	if (!strpos($user, 'username') === false) {
		$user_username = $user->{'username'};
	}
	if (!strpos($user, 'language_code') === false) {
		$user_language_code = $user->{'language_code'};
	} else { $user_language_code = $language_code; }
	triggerEvent('leave', $jsonData);
	//code
	
}elseif (!strpos($rawData, 'photo') === false) {
	// Photo
	
	goto not_for_me;
	$message_ID = $jsonData->{'message'}->{'message_id'};
	$chat_ID = $jsonData->{'message'}->{'chat'}->{'id'};	
	$file_id = $jsonData->{'message'}->{'photo'}[0]->{'file_id'};
	$caption = $jsonData->{'message'}->{'caption'};
	$file_path = getFile($file_id);
	syslog(LOG_INFO, 'antwort file_path: ' .$file_path);
	syslog(LOG_INFO, 'antwort file_id: ' .$file_id);
	$pic = $apiURL. ''.$file_path;
	
	triggerEvent('photo', $jsonData);
	//Code
	
	goto not_for_me;
}elseif (!strpos($rawData, 'text') === false) {
	// text Message
	$message_ID = $jsonData->{'message'}->{'message_id'};
	$chat_ID = $jsonData->{'message'}->{'chat'}->{'id'};
	$received_message = $jsonData->{'message'}->{'text'};
	syslog(LOG_DEBUG, 'Nachricht von: "' .$chat_ID. '" Text: "'.$received_message. '"');
	triggerEvent('messageRecived', $jsonData, $plugins);
}else{
	// Wat auch immer
	goto not_for_me;
}

not_for_me:




?>