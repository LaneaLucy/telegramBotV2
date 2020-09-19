<?php

class yauw
{
	
	function __construct() //init
		{
		$data = "{'name' : 'yauw', 'description' : 'Programmer's personal Plugin.', 'commandDescriptions' : []}";
		return $data;
		}
	
	function handelEvent($event, $data, $obj)
	{
		switch ($event) {
		case "messageRecived":
			// Do something with the Data
			$received_message = $data->{'message'}->{'text'};
			$fromID = $data->{'message'}->{'from'}->{'id'};
			if ((preg_match('/^((.)* )?y.?a.?u.?w.?( (.)*)?$/i', $received_message)) == 1) {
				if ($fromID == 25737932) {
					$answer = "replay|yauw";
				} else {
					$answer = "replay|DON'T SAY THIS AGAIN!!! You have not the Permission to do that!";
				}
				return $answer;
			}
			break;
		default:
			// Don't handle the event Type
		}
	
	}
}



?>