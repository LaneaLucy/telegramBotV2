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
//print_r($files);


foreach ($files as &$file) {
	$pluginNameEnding = substr($file, -4, 4);
	//print "PluginNameEnding: ".$pluginNameEnding."\n<br>";
	if($file != '.' && $file != '..' && !is_dir('./plugins/'.	$file) && ($pluginNameEnding == ".php")){
		//echo $file."\n<br>";
		$pluginName = substr($file, 0, -4);
		include './plugins/'.$pluginName . '.php';
		array_push($plugins, new $pluginName);
	}
}
//print_r($plugins);
//bla

####################################################################


function processPluginResponse($pluginResponse, $data)
{
	$pluginResponseArray = explode('|', $pluginResponse);
	switch ($pluginResponseArray[0]) {
		case "send":
			$chat_ID = $data->{'message'}->{'chat'}->{'id'};
			$message = $pluginResponseArray[1];
			sendMessage($chat_ID, $message);
			break;
		case "replay":
			$message_ID = $data->{'message'}->{'message_id'};
			$chat_ID = $data->{'message'}->{'chat'}->{'id'};
			$message = $pluginResponseArray[1];
			replayMessage($chat_ID, $message, $message_ID);
			break;
		case "answer":
			// Do something with the Data
			break;
		case "sticker":
			// Do something with the Data
			break;
		case "photo":
			// TODO: replay
			// Do something with the Data
			$chat_ID = $data->{'message'}->{'chat'}->{'id'};
			$photo = $pluginResponseArray[1];
			$file = $photo;
			//sendPhoto($chat_ID, $file, $message_ID = "NULL");
			sendPhoto($chat_ID, $file);
			break;
		case "sendChatAction":
			$chat_ID = $data->{'message'}->{'chat'}->{'id'};
			$action = $pluginResponseArray[1];
			sendChatAction($chat_ID, $action);
			break;
		case "kickChatMember":
			$chat_ID = $data->{'message'}->{'chat'}->{'id'};
			$user_ID = $pluginResponseArray[1];
			$until_date = $pluginResponseArray[2];
			kickChatMember($chat_ID, $user_ID, $until_date);
			break;
		case "unbanChatMember":
			$chat_ID = $data->{'message'}->{'chat'}->{'id'};
			$user_ID = $pluginResponseArray[1];
			unbanChatMember($chat_ID, $user_ID);
			break;
		case "restrictChatMember":
			// Do something
			break;
		case "promoteChatMember":
			// Do something
			break;
		case "pinChatMessage":
			// Do something
			break;
		case "unpinChatMessage":
			// Do something
			break;
		case "leaveChat":
			$chat_ID = $data->{'message'}->{'chat'}->{'id'};
			leaveChat($chat_ID);
			break;
		case "getChatAdministrators":
			$chat_ID = $data->{'message'}->{'chat'}->{'id'};
			$result = getChatAdministrators($chat_ID);
			return $result;
			break;
		case "getMe":
			return getMe();
			break;
		case "customeEvent":
			// Do something
			return false;
			break;
		default:
			// Don't handle the event Type
		}
	//Code
}

function triggerEvent($eventType, $data, $plugins)
{
	$obj = new stdClass();
	$obj->process = function ($param, $data) {
		return processPluginResponse($param, $data);
	};
	
	foreach ($plugins as &$plugin) {
		$response = $plugin->handelEvent($eventType, $data, $obj);
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
//error_log('rawData: ' .$rawData, 0);

########################### process Data ###########################

$update_id = $jsonData->{'update_id'};

if (!strpos($rawData, 'new_chat_member') === false) {
	// join Message
	goto not_for_me;
	$message_ID = $jsonData->{'message'}->{'message_id'};
	$chat_ID = $jsonData->{'message'}->{'chat'}->{'id'};
	if (!strpos($user, 'username') === false) {
		$FromUser = $jsonData->{'message'}->{'from'};
	}
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
	if ($user_id == getMe()->{'id'}) 
	{
		triggerEvent('botAdded', $jsonData);
	} else {
		triggerEvent('join', $jsonData);
	}
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
	
	if (!strpos($rawData, '"entities":[') === false)
	{ //entities in message
		$entities = $jsonData->{'message'}->{'entities'};
		//error_log('entities: ', 0);
		foreach ($entities as &$entitie)
		{
			$entitie_type = $entitie->{'type'};
			//error_log('entitie_type: ' .$entitie_type, 0);
			if ($entitie_type == 'bot_command')
			{ //entitie is command
				$entitie_offset = $entitie->{'offset'};
				$entitie_length = $entitie->{'length'};
				$entitie_content = substr($received_message, $entitie_offset, $entitie_length);
				//error_log('entitie_content: ' .$entitie_content, 0);
				if (!strpos($entitie_content, '@') === false) 
				{
					//error_log('Bot specific entitie', 0);
					$username = getMe()->{'username'};
					//error_log('Bot username: '.$username, 0);
					$commandPos = 0;
					$usernamePos = strpos($entitie_content, $username);
					$command = substr($entitie_content, 0, $usernamePos - 1);
					error_log("command: ".$command, 0);
					if (!strpos($entitie_content, $username) === false) 
					{
						error_log('entitie is for me', 0);
						$jsonData->{'command'} = $command;
						triggerEvent('command', $jsonData, $plugins);
						break;
					}
				} else {
					$jsonData->{'command'} = $entitie_content;
					triggerEvent('command', $jsonData, $plugins);
					break;
				}
			}
			triggerEvent('messageRecived', $jsonData, $plugins);
		}
	} else {
		triggerEvent('messageRecived', $jsonData, $plugins);
	}

}else{
	// Wat auch immer
	goto not_for_me;
}

not_for_me:




?>