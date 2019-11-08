
//################################################# JSON Objekt auslesen #######################################################

$chat_ID = $jsonData->{'message'}->{'chat'}->{'id'};
$message_ID = $jsonData->{'message'}->{'message_id'};




//###############################################################################################################################


//#################################################### Commands erkennen ########################################################


// ist es ein command?
if (strpos($received_message, '/') === 0) {
	// command
}else{
	// kein command
	goto not_for_me;
}

if (strpos($received_message, '@') === false) {
// for everyone
//goto not_for_me;
}else{
	// for one persone
	if (strpos($received_message, '@'.$botName) === false) {
		// for someone other
		goto not_for_me;
	}else{
		// for me
		$received_message = str_replace('@'.$botName, '', $received_message);
	}
}

// argumente rausfiltern
if (strpos($received_message, ' ') === false) {
	// kein argument
}else{
	// mit argument
	$array = explode(" ", $received_message, 2);
	$received_message = $array[0];
	$message = 'Uhhh, with arguments! Arguments: '.$array[1];
	//replayMessage($chat_ID, $message, $message_ID);
}

switch ($received_message) {
	case "/start":
		$message = 'You don\'t need start , I always run!';
		replayMessage($chat_ID, $message, $message_ID);
		break;
	case "/help":
		$message = 'Help yourself!';
		replayMessage($chat_ID, $message, $message_ID);
		break;
	case "/stop":
		$message = 'Why? Am I really scripted so badly? When you have ideas just write a message to my creator @LaneaLucy';
		replayMessage($chat_ID, $message, $message_ID);
		break;
	case "/apfelkuchen":
		$message = 'Apple Pie';
		replayMessage($chat_ID, $message, $message_ID);
		sendPhoto($chat_ID, 'apfelkuchen.jpg', $message_ID);
		break;
	case "/debug":
		$message = 'Ach ja, debuging ist schwer...';
		replayMessage($chat_ID, $message, $message_ID);
		sendSticker($chat_ID, 'debug.gif', $message_ID);
		break;
	case "/cake":
		$message = 'The Cake is a Lie!';
		sendPhoto($chat_ID, 'cake.jpg', $message_ID);
		replayMessage($chat_ID, $message, $message_ID);
		break;
	default:
		$message = 'Sorry, but i don\'t now what "'.$received_message. '" mean!';
		//replayMessage($chat_ID, $message, $message_ID);
}

not_for_me:



//###############################################################################################################################


foreach ($ChatAdministrators as &$admin) {
							if ($from == $admin->{'user'}->{'id'}) {
								$organName = $dataArray[1];
								$organ = $this->getOrganID($organName);
								$processer('replay|organ "'.$organ.'"', $data);
								$processer('replay|from "'.$from.'"', $data);
								if ($this->checkPermission($from, $organ))
								{
									//$this->setGroupOrgan($organ, $chat_ID);
									break;
								} else {
									return 'replay|You have for "'.$organName.'" not the organisation permissions to do that';
									break;
								}
								
							}
						}


//###############################################################################################################################










