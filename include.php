<?php

include  'config.php';


//global $token;
//global $language_code;
//global $apiURL;
//global $botId;
//global $botName;
//global $botUsername;
//global $user_language;

//$ttoken = '';



$apiURL = 'https://api.telegram.org/bot'.$token.'/';

$URL = 'https://api.telegram.org/bot'.$token.'/getMe';
//syslog(LOG_INFO, 'getMe_URL: ' .$URL);
$antwort = file_get_contents($URL);
//error_log('antwort getMe: ' .$antwort);
$jsonData = json_decode($antwort);

$me = $jsonData->{'result'};

$botId = $me->{'id'};
$botName = $me->{'first_name'};
$botUsername = $me->{'username'};
//$user_language = $me->{'result'};


?>
