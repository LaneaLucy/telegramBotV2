<html>
<body>



<?php

/*
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

*/

if ($_SERVER["REQUEST_METHOD"] == "POST") {

	
	$webhookUrl = $_POST['webhookUrl'];
	if (empty($webhookUrl)) {
		echo "webhookUrl is empty";
		exit();
	}
	
	$webhookUrl = rawurlencode($webhookUrl);
	
	include 'include.php';
	$URL = $apiURL. 'setWebhook?url='.$webhookUrl.'';
	//syslog(LOG_INFO, 'setWebhook_URL: ' .$URL);
	$antwort = file_get_contents($URL);
	syslog(LOG_DEBUG, 'antwort setWebhook: ' .$antwort);
	
	echo 'antwort setWebhook: ' .$antwort;
	
} else {
	
	echo '<form method="post" action="'.$_SERVER["PHP_SELF"].'">
  webhookUrl: <input type="text" name="webhookUrl">
  <input type="submit">
</form>';
	
}
?>




</body>
</html>