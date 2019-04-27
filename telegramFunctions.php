<?php

include 'config.php';

//global $apiURL;
//$token = '23456:ABC-DEF1234ghIkl-zyx57W2v1u123ew11';
//$apiURL = 'https://api.telegram.org/bot'.$token.'/';
$a = 'https://api.telegram.org/bot/';


function sendMessage($chat_ID, $message, $message_ID = "NULL")
	{
	include 'include.php';
	$message = rawurlencode($message);
	
	if ($message_ID = "NULL"){
		$URL = $apiURL. 'sendMessage?chat_id='.$chat_ID.'&text='.$message.'';
	}else{
		$URL = $apiURL. 'sendMessage?chat_id='.$chat_ID.'&text='.$message.'&reply_to_message_id='.$message_ID.'';
	}
	
	$URL = $apiURL. 'sendMessage?chat_id='.$chat_ID.'&text='.$message.'';
	
	syslog(LOG_DEBUG, 'replay_URL: ' .$URL);
	$antwort = file_get_contents($URL);
	syslog(LOG_INFO, 'antwort sendMessage: ' .$antwort);
	}

function replayMessage($chat_ID, $message, $message_ID)
	{
	include 'include.php';
	$message = rawurlencode($message);
	$URL = $apiURL. 'sendMessage?chat_id='.$chat_ID.'&text='.$message.'&reply_to_message_id='.$message_ID.'';
	//syslog(LOG_INFO, 'replay_URL: ' .$URL);
	$antwort = file_get_contents($URL);
	syslog(LOG_DEBUG, 'antwort sendMessage: ' .$antwort);
	}

function sendPhoto($chat_ID, $file, $message_ID = "NULL")
	{
	include 'include.php';
		$url = $apiURL. 'sendPhoto';
		$header = array('Content-Type: multipart/form-data');
		
		if ($message_ID = "NULL"){
			$fields = array('photo' => '@'.$file , 'chat_id' => $chat_ID);
		}else{
			$fields = array('photo' => $file , 'chat_id' => $chat_ID , 'reply_to_message_id' => $message_ID);
		}
	
		//$fields = array('file' => '@' . $file , 'reply_to_message_id' => $message_ID);
		
		$resource = curl_init();
		curl_setopt($resource, CURLOPT_URL, $url);
		curl_setopt($resource, CURLOPT_HTTPHEADER, $header);
		curl_setopt($resource, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($resource, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($resource, CURLOPT_POST, 1);
		curl_setopt($resource, CURLOPT_POSTFIELDS, $fields);
		//curl_setopt($resource, CURLOPT_TIMEOUT, 10);
		//$antwort = json_decode(curl_exec($resource));
		$antwort = curl_exec($resource);
		curl_close($resource);
		
		syslog(LOG_DEBUG, 'antwort sendPhoto: ' .$antwort);
		
	}


function sendSticker($chat_ID, $file, $message_ID = "NULL")
	{
	include 'include.php';
	$url = $apiURL. 'sendSticker';
	$header = array('Content-Type: multipart/form-data');
	
	if ($message_ID = "NULL"){
		$fields = array('sticker' => '@'.$file , 'chat_id' => $chat_ID);
	}else{
		$fields = array('sticker' => $file , 'chat_id' => $chat_ID , 'reply_to_message_id' => $message_ID);
	}
	
	//$fields = array('file' => '@' . $file , 'reply_to_message_id' => $message_ID);
	
	$resource = curl_init();
	curl_setopt($resource, CURLOPT_URL, $url);
	curl_setopt($resource, CURLOPT_HTTPHEADER, $header);
	curl_setopt($resource, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($resource, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($resource, CURLOPT_POST, 1);
	curl_setopt($resource, CURLOPT_POSTFIELDS, $fields);
	//curl_setopt($resource, CURLOPT_TIMEOUT, 10);
	//$antwort = json_decode(curl_exec($resource));
	$antwort = curl_exec($resource);
	curl_close($resource);
	
	syslog(LOG_DEBUG, 'antwort sendSticker: ' .$antwort);
	
	}


function getFile($file_id)
	{
	include 'include.php';
	syslog(LOG_INFO, 'getFile file_id: ' .$file_id);
	$URL = $apiURL. 'getFile?file_id='.$file_id.'';
	//syslog(LOG_INFO, 'replay_URL: ' .$URL);
	$antwort = file_get_contents($URL);
	syslog(LOG_DEBUG, 'getFile antwort: ' .$antwort);
	$jsonData = json_decode($antwort);
	return $jsonData->{'result'}->{'file_path'};
	}

function getMe()
	{
	include 'include.php';
	$URL = $apiURL. 'getMe';
	//syslog(LOG_INFO, 'getMe_URL: ' .$URL);
	$antwort = file_get_contents($URL);
	syslog(LOG_DEBUG, 'antwort getMe: ' .$antwort);
	$jsonData = json_decode($antwort);
	return $jsonData->{'result'};
	}




?>