<?php


$rawData = file_get_contents("php://input");

syslog(LOG_DEBUG, 'rawData: ' .$rawData);



$url = 'core.php';
$data = $rawData;
$options = array(
        'http' => array(
        'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
        'method'  => 'POST',
        'content' => http_build_query($data),
    )
);

$context  = stream_context_create($options);
$result = file_get_contents($url, false, $context);
var_dump($result);




?>