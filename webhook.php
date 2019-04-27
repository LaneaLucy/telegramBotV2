<?php


$rawData = file_get_contents("php://input");

syslog(LOG_DEBUG, 'rawData: ' .$rawData);



$url = 'core.php';
$data = $rawData;
$options = array(
        'http' => array(
        'header'  => "Content-type: application/json\r\n",
        'method'  => 'POST',
        'content' => $data,
    )
);

$context  = stream_context_create($options);
$result = file_get_contents($url, false, $context);
var_dump($result);




?>