<?php

class rip
{
	
	function __construct() //init
		{
		$data = "{'name' : 'rip', 'description' : 'Rest in Peace!', 'commandDescriptions' : []}";
		return $data;
		}
	
	function handelEvent($event, $data, $obj)
	{
		switch ($event) {
		case "messageRecived":
			// Do something with the Data
			$received_message = $data->{'message'}->{'text'};
			if ((preg_match('/^((.)* )?r.?i.?p.?( (.)*)?$/i', $received_message)) == 1) {
				$answer = "replay|❀◟(ó ̯ ò, )";
				return $answer;
			}
			break;
		default:
			// Don't handle the event Type
		}
	
	}
}



?>