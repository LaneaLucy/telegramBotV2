<?php

global $token = '123456:ABC-DEF1234ghIkl-zyx57W2v1u123ew11';
global $language_code = 'en';


include 'telegramFunctions.php';


$apiURL = 'https://api.telegram.org/bot'.token.'/';
$botName = getMe()->{'username'}


########################### Load Plugins ###########################

//bla

####################################################################


function triggerEvent($eventType, $data)
{
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
	$user_id = $user->{'id'}};
	$user_is_bot = $user->{'is_bot'};
	$user_first_name = $user->{'first_name'};
	if (!strpos($user, 'username') === false) {
		$user_username = $user->{'username'};
	}
	if (!strpos($user, 'language_code') === false) {
		$user_language_code = $user->{'language_code'};
	} else { $user_language_code = $language_code;}
	//code
	
}elseif (!strpos($rawData, 'left_chat_member') === false) {
	// leave Message
	goto not_for_me;
	$message_ID = $jsonData->{'message'}->{'message_id'};
	$chat_ID = $jsonData->{'message'}->{'chat'}->{'id'};
	$user = $jsonData->{'message'}->{'left_chat_member'};
	$user_id = $user->{'id'}};
	$user_is_bot = $user->{'is_bot'};
	$user_first_name = $user->{'first_name'};
	if (!strpos($user, 'username') === false) {
		$user_username = $user->{'username'};
	}
	if (!strpos($user, 'language_code') === false) {
		$user_language_code = $user->{'language_code'};
	} else { $user_language_code = $language_code;}
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
	
	//Code
	
	goto not_for_me;
}elseif (!strpos($rawData, 'text') === false) {
	// text Message
	$message_ID = $jsonData->{'message'}->{'message_id'};
	$chat_ID = $jsonData->{'message'}->{'chat'}->{'id'};
	$received_message = $jsonData->{'message'}->{'text'};
	syslog(LOG_DEBUG, 'Nachricht von: "' .$chat_ID. '" Text: "'.$received_message. '"');
}else{
	// Wat auch immer
	goto not_for_me;
}






?>