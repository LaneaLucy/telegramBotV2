<?php

class hamw
{
	
	function __construct() //init
		{
		$data = "{'name' : 'hamw', 'description' : 'give you ham weather', 'commandDescriptions' : []}";
		return $data;
		}
	
	function handelEvent($event, $data, $obj)
	{
		switch ($event) {
		case "command":
			$chat_ID = $data->{'message'}->{'chat'}->{'id'};
			$received_message = $data->{'message'}->{'text'};
			$dataArray = explode(' ', $received_message);
			$command = $dataArray[0];
			// Do something with the Data
			$from = $data->{'message'}->{'from'}->{'id'};
			if ($received_message == '/hamw') {
				$answer = "photo|http://www.hamqsl.com/solar101vhf.php";
				//$answer = "replay|hamw";
				return $answer;
			}
			break;
		default:
			// Don't handle the event Type
		}
	
	}
}



?>