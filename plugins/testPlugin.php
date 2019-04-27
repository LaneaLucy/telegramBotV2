<?php

class testPlugin
{
	
	function command1()
		{
		// Do something
		}
	
	function command2()
		{
		// Do something
		}
	
	function __construct() //init
		{
		$data = "{'name' : 'testPlugin', 'description' : 'A simple Demonstration', 'commandDescriptions' : [{'command1' : 'This is command1'}, {'command2' : 'Thist is command2'}]}";
		return $data;
		}
	
	function handelEvent($event, $data)
	{
		switch ($event) {
		case "messageRecived":
			// Do something with the Data
			break;
		case "join":
			// Do something with the Data
			break;
		case "leave":
			// Do something with the Data
			break;
		case "photo":
			// Do something with the Data
			break;
		default:
			// Don't handle the event Type
		}
	$answer = 'replay|pingas';
	//return $answer;
	}
}



?>