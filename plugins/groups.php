<?php

class groups
{
	var $pluginName = 'groups';
	var $description = '';
	var $commandDescriptions = '[]';
	
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
	
	function checkGroups($userID, $group)
	{
		$name = $group;
		$result = getData('groups.txt', $name);
		
		if ($result) {
			if (!strpos($rawData, $name) === false) { return true; }
		}
		
		return false;
	}
	
	function delUserfromGroup($userID, $group)
	{
		$name = $group;
		
		$result = getData('groups.txt', $name);
		$newResult = str_replace($userID."\r\n", "", $result);
		
		setData('groups.txt', $name, $newResult);
	}
	
	function addUserToGroup($userID, $group)
	{
		$name = $group;
		
		$result = getData('groups.txt', $name);
		$result .= $userID."\r\n";
		
		setData('groups.txt', $name, $result);
	}
	
	function __construct() //init
	{
		$data = "{'pluginName' : '".$this->pluginName."', 'description' : '".$this->description."', 'commandDescriptions' : ".$this->commandDescriptions."}";
		return $data;
	}
	
	function handelEvent($event, $data, $obj)
	{
		switch ($event) {
		case "customeEvent":
			$dataArray = explode('|', $data);
			$eventType = $dataArray[0];
			switch ($eventType) {
				case 'checkGroups':
					$userID = $dataArray[1];
					$group = $dataArray[2];
					$result = checkGroups($userID, $group);
					return 'answer|'.$result;
					break;
				case 'addUserToGroup':
					$userID = $dataArray[1];
					$group = $dataArray[2];
					$result = addUserToGroup($userID, $group);
					return 'answer|'.$result;
					break;
				case 'delUserfromGroup':
					$userID = $dataArray[1];
					$group = $dataArray[2];
					$result = delUserfromGroup($userID, $group);
					return 'answer|'.$result;
					break;
				default:
					// Don't handle the event Type
			}
			break;
		default:
			// Don't handle the event Type
		}
	
	}
}



?>