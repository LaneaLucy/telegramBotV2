<?php

class ping
{
	
	function __construct() //init
		{
		$data = "{'name' : 'ping', 'description' : 'Ping? PONG!', 'commandDescriptions' : []}";
		return $data;
		}
	
	function handelEvent($event, $data, $obj)
	{
		switch ($event) {
		case "messageRecived":
			// Do something with the Data
			$received_message = $data->{'message'}->{'text'};
			if ($received_message == 'ping') {
				$answer = "replay|PONG!";
				return $answer;
			}
			break;
		default:
			// Don't handle the event Type
		}
	
	}
}



?>