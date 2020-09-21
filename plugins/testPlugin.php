<?php

class testPlugin
{
	var $pluginName = 'testPlugin';
	var $description = 'A simple Demonstration';
	var $commandDescriptions = "[{'command1' : 'This is command1'}, {'command2' : 'Thist is command2'}]";
	
	
	function setData($file, $name, $value)
	{
		$root = filter_input(INPUT_SERVER, 'DOCUMENT_ROOT');
		
		if(!is_dir('./plugins/pluginDatas/'.$this->pluginName.'/'))	{ mkdir('./plugins/pluginDatas/'.$this->pluginName.'/', 0755, true); }
		if(!is_file('./plugins/pluginDatas/'.$this->pluginName.'/'.$file))	{ file_put_contents('./plugins/pluginDatas/'.$this->pluginName.'/'.$file, ''); }
		$rawData = file_get_contents('./plugins/pluginDatas/'.$this->pluginName.'/'.$file);
		$jsonData = json_decode($rawData);
		
		ob_start();
		var_dump($jsonData);
		$result = ob_get_clean();
		
		$jsonData[$name] = $value;
		
		$rawData = json_encode($jsonData);
		
		file_put_contents('./plugins/pluginDatas/'.$this->pluginName.'/'.$file, $rawData);
	}
	
	function getData($file, $name)
	{
		$rawData = file_get_contents('./plugins/pluginDatas/'.$this->pluginName.'/'.$file);
		$jsonData = json_decode($rawData);
		
		ob_start();
		var_dump($jsonData);
		$result = ob_get_clean();
		
		if (strpos($rawData, $name) === false) {
			return false;
		}
		$value = $jsonData->{$name};
		
		return $value;
	}
	
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
		$data = "{'pluginName' : '".$this->pluginName."', 'description' : '".$this->description."', 'commandDescriptions' : ".$this->commandDescriptions."}";
		return $data;
	}
	
	function handelEvent($event, $data, $obj)
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
		case "command":
			$dataArray = explode(' ', $data->{'command'});
			$command = $dataArray[0];
			// Do something with the Data
			switch ($command) {
				case "command1":
					// Do something with the Data
					break;
				case "command2":
					// Do something with the Data
					break;
				default:
					// Don't handle the event Type
			}
		default:
			// Don't handle the event Type
		}
	$answer = 'replay|pingas';
	//return $answer;
	}
}



?>