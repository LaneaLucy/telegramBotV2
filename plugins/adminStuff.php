<?php

class adminStuff
{
	var $warnsForBan = 3;
	var $globalBans = true;
	
	var $pluginName = 'adminStuff';
	var $description = '';
	var $commandDescriptions = "[{'/selectOrgan' : 'Select a Organisation'}, {'/ban <reason>' : 'ban the User from the replayed message'}]";
	
	function setData($file, $name, $value)
	{
		$root = filter_input(INPUT_SERVER, 'DOCUMENT_ROOT');
		
		if(!is_dir('./plugins/pluginDatas/'.$this->pluginName.'/'))	{ mkdir('./plugins/pluginDatas/'.$this->pluginName.'/', 0755, true); }
		if(!is_file('./plugins/pluginDatas/'.$this->pluginName.'/'.$file))	{ file_put_contents('./plugins/pluginDatas/'.$this->pluginName.'/'.$file, ''); }
		$rawData = file_get_contents('./plugins/pluginDatas/'.$this->pluginName.'/'.$file);
		$jsonData = json_decode($rawData, true);
		
		ob_start();
		var_dump($jsonData);
		$result = ob_get_clean();
		
		$jsonData[$name] = $value;
		
		$rawData = json_encode($jsonData);
		
		if (!file_put_contents('./plugins/pluginDatas/'.$this->pluginName.'/'.$file, $rawData) == false) {return true; }
		
		return false;
	}
	
	function getData($file, $name)
	{
		if(!is_file('./plugins/pluginDatas/'.$this->pluginName.'/'.$file))	{ file_put_contents('./plugins/pluginDatas/'.$this->pluginName.'/'.$file, ''); }
		$rawData = file_get_contents('./plugins/pluginDatas/'.$this->pluginName.'/'.$file);
		$jsonData = json_decode($rawData);
		
		ob_start();
		var_dump($jsonData);
		$result = ob_get_clean();
		
		error_log("rawData: ".$rawData, 0);
		error_log("Result: ".$result, 0);
		error_log("Name: ".$name, 0);
		error_log("strpos: ".strpos($result, $name), 0);
		
		/**
		if (strpos($result, $name) === false) {
			return false;
		}
		**/
		//error_log("test", 0);
		
		//return false;
		
		try {
			$value = $jsonData->{$name};
		} catch (Exception $e) {
			$value =  false;
		}
		
		return $value;
	}
	
	function addOrganAdmin($userID, $organ)
	{
		$rawData = $this->getData('organList_'.$organ.'.txt', 'admins');
		
		$jsonData = json_decode($rawData, true);
		if ($rawData == false) { $jsonData = array(); }
		array_push($jsonData, $userID);
		$rawData = json_encode($jsonData);
		//$rawData .= '|'.$userID;
		
		$this->setData('organList_'.$organ.'.txt', 'admins', $rawData);
	}
	
	function createOrgan($userID, $organName)
	{
		$result = $this->getData('organNameList.txt', $organName);
		if ($result === false) {
			//Frei
again:
			$newOrgan = rand(0, 184467440737095516);
			$result = $this->getData('organList_'.$newOrgan.'.txt', 'admins');
			if (!$result === false) { goto again; }
			$this->addOrganAdmin($userID, $newOrgan);
			$this->setData('organNameList.txt', $organName, $newOrgan);
			return 'replay|Organisation sucessfully created';
		} else { 
			return 'replay|Organisation Name already exist';
		}
	}
	
	function getOrganID($organName)
	{
		$result = $this->getData('organNameList.txt', $organName);
		if ($result === false) {
			return 0;
		} else { 
			return $result;
		}
	}
	
	function getOrgan($data, $processer)
	{
		$chat_ID = $data->{'message'}->{'chat'}->{'id'};
		$chat_type = $data->{'message'}->{'chat'}->{'type'};
		$from = $data->{'message'}->{'from'}->{'id'};
		if ($chat_type == 'private')
		{
			$result = $this->getData('selectedOrganList.txt', '' .$from. '');
			if ($result == false) {
				$processer("replay|Please select a Organisation with /selectOrgan <organisation_name>", $data);
				return 0;
			} else { 
				$processer('replay|Organisation "'.$result.'" is selected', $data);
				return $result;
			}
		} else {
			$result = $this->getData('groupOrganList.txt', $chat_ID);
			$processer("replay|chat_ID " .$chat_ID, $data);
			$processer("replay|result " .$result, $data);
			if ($result == false) {
				$processer("replay|This Group or SuperGroup is in no Organisation!", $data);
				return 0;
			} else {
				return $result;
			}
		}
		return false;
	}
	
	function setGroupOrgan($organ, $chat_ID)
	{	
		if ($organ == 0) return false;
		
		return $this->setData('groupOrganList.txt', $chat_ID, $organ);
	}
	
	function checkPermission($userID, $organ)
	{
		if ($organ == 0) return false;
		
		$rawData = $this->getData('organList_'.$organ.'.txt', 'admins');
		
		$jsonData = json_decode($rawData, true);
		
		foreach ($jsonData as &$admin) {
			if ($userID == $admin) {
				return true;
				break;
			}
		}
		
		return false;
	}
	
	function ban($userID, $group, $reason, $processer)
	{
		$name = $userID;
		
		$result = $this->getData('banlist.txt', $name);
		$result .= $reason."|";
		
		$this->setData('banlist.txt', $name, $result);
		
		return 'kickChatMember|'.$userID.'|0';
		return 'kickChatMember|'.$userID.'|'.time() + (60); //60 seconds for testing
	}
	
	function unban($userID, $group, $processer)
	{
		$name = $userID;
		
		$result = getData('banlist.txt', $name);
		$result .= "UnBaned\r\n";
		
		setData('banlist.txt', $name, $result);
		
		return 'unbanChatMember|'.$userID;
	}
	
	function warn($userID, $group, $reason, $processer)
	{
		$name = $group;
		
		$result = getData('groups.txt', $name);
		$result .= $userID."\r\n";
		
		setData('groups.txt', $name, $result);
	}
	
	function unwarn($userID, $group, $processer)
	{
		$name = $group;
		
		$result = getData('groups.txt', $name);
		$result .= $userID."\r\n";
		
		setData('groups.txt', $name, $result);
	}
	
	function noWarns($userID, $group, $processer)
	{
		$name = $group;
		
		$result = getData('groups.txt', $name);
		$result .= $userID."\r\n";
		
		setData('groups.txt', $name, $result);
	}
	
	function admin($userID, $organ, $processer)
	{
		$name = $group;
		
		$result = getData('groups.txt', $name);
		$result .= $userID."\r\n";
		
		setData('groups.txt', $name, $result);
	}
	
	function unadmin($userID, $group, $processer)
	{
		$name = $group;
		
		$result = getData('groups.txt', $name);
		$result .= $userID."\r\n";
		
		setData('groups.txt', $name, $result);
	}
	
	function report($userID, $group, $reason, $processer)
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
		$processer = $obj->process;
		switch ($event) {
		case "customeEvent":
			$dataArray = explode('|', $data);
			$eventType = $dataArray[0];
			switch ($eventType) {
				case 'ban':
					$userID = $dataArray[1];
					$group = $dataArray[2];
					$reason = $dataArray[3];
					$result = ban($userID, $group, $reason, $processer);
					return 'answer|'.$result;
					break;
				case 'unban':
					$userID = $dataArray[1];
					$group = $dataArray[2];
					$result = unban($userID, $group, $processer);
					return 'answer|'.$result;
					break;
				case 'warn':
					$userID = $dataArray[1];
					$group = $dataArray[2];
					$reason = $dataArray[3];
					$result = warn($userID, $group, $reason, $processer);
					return 'answer|'.$result;
					break;
				case 'unwarn':
					$userID = $dataArray[1];
					$group = $dataArray[2];
					$result = addUserToGroup($userID, $group, $processer);
					return 'answer|'.$result;
					break;
				case 'noWarns':
					$userID = $dataArray[1];
					$group = $dataArray[2];
					$result = addUserToGroup($userID, $group, $processer);
					return 'answer|'.$result;
					break;
				case 'admin':
					$userID = $dataArray[1];
					$group = $dataArray[2];
					$result = delUserfromGroup($userID, $group, $processer);
					return 'answer|'.$result;
					break;
				case 'unadmin':
					$userID = $dataArray[1];
					$group = $dataArray[2];
					$result = delUserfromGroup($userID, $group, $processer);
					return 'answer|'.$result;
					break;
				case 'report':
					$userID = $dataArray[1];
					$group = $dataArray[2];
					$result = delUserfromGroup($userID, $group, $reason, $processer);
					return 'answer|'.$result;
					break;
				default:
					// Don't handle the event Type
			}
			break;
		case "command":
			$chat_ID = $data->{'message'}->{'chat'}->{'id'};
			$received_message = $data->{'message'}->{'text'};
			$dataArray = explode(' ', $data->{'command'});
			$command = $dataArray[0];
			$from = $data->{'message'}->{'from'}->{'id'};
			//if (!strpos($data, 'reply_to_message') === false) 
			try
			{
				$reply_to_message_from = $data->{'message'}->{'reply_to_message'}->{'from'}->{'id'};
			} catch (Exception $e) {
				//nothing
			}
			if (!strpos($command, '@') === false) 
			{
				$commandArray = explode('@', $command);
				$command = $commandArray[0];
			}
			switch ($command) {
				case '/ban':
					$organ = $this->getOrgan($data, $processer);
					if ($organ == 0)
					{
						return 'replay|You have not set a Organisation!';
						break 2;
					}
					if (!$this->checkPermission($from, $organ))
					{
						return 'replay|You have not the permissions to do that';
						break 2;
					}
					$aruments = count($dataArray);
					if ($aruments < 2) { 
						return 'replay|Not enough Arguments'; 
					} else if ($aruments = 2) {
						$reason = $dataArray[1];
						if (!isset($reply_to_message_from)) { return 'replay|ERROR! You have not mention a user or replyed to a message!'; }
						$userID = $reply_to_message_from;
					} else if ($aruments >= 3) {
						$reason = $dataArray[1];
						$userID = $dataArray[2];  //benutzername auflösen
					}
					return $this->ban($userID, "platzhalter", $reason, $processer);
					break;
				case 'unban':
					$userID = $dataArray[1];
					$group = $dataArray[2];
					return unban($userID, $group, $processer);
					return 'answer|'.$result;
					break;
				case 'warn':
					$userID = $reply_to_message_from;
					$group = $dataArray[2];
					$reason = $dataArray[3];
					$result = warn($userID, $group, $reason, $processer);
					return 'answer|'.$result;
					break;
				case 'unwarn':
					$userID = $dataArray[1];
					$group = $dataArray[2];
					$result = unwarn($userID, $group, $processer);
					return 'answer|'.$result;
					break;
				case 'noWarns':
					$userID = $dataArray[1];
					$group = $dataArray[2];
					$result = noWarns($userID, $group, $processer);
					return 'answer|'.$result;
					break;
				case 'admin':
					$userID = $dataArray[1];
					$group = $dataArray[2];
					$result = admin($userID, $group, $processer);
					return 'answer|'.$result;
					break;
				case 'unadmin':
					$userID = $dataArray[1];
					$group = $dataArray[2];
					$result = unadmin($userID, $group, $processer);
					return 'answer|'.$result;
					break;
				case 'report':
					$replyed_message = $data->{'message'}->{'reply_to_message'};
					$userID = $reply_to_message_from;
					$group = $dataArray[2];
					$result = report($userID, $group, $replyed_message->{'text'}, $processer);
					return 'answer|'.$result;
					break;
				case '/selectOrgan':
					$organName = $dataArray[1];
					$organ = $this->getOrganID($organName);
					if ($organ == 0) 
					{ 
						$processer('replay|Can\'t find Organisation "'.$organName.'"', $data);
						break;
					}
					$this->setData('selectedOrganList.txt', $from, $organ);
					return 'replay|Organisation "'.$organ.'" aka "'.$organName.'" is selected now';
					break;
				case '/setOrgan':
					$chat_type = $data->{'message'}->{'chat'}->{'type'};
					if ($chat_type == 'group' OR $chat_type == 'supergroup') 
					{
						$ChatAdministrators = $processer('getChatAdministrators|', $data);
						foreach ($ChatAdministrators as &$admin) {
							if ($from == $admin->{'user'}->{'id'}) {
								$organName = $dataArray[1];
								$organ = $this->getOrganID($organName);
								//$processer('replay|organ "'.$organ.'"', $data);
								//$processer('replay|from "'.$from.'"', $data);
								if ($organ == 0)
								{
									return 'replay|Organisation "'.$organName.'" dont exist!';
									break;
								}
								if ($this->checkPermission($from, $organ))
								{
									//return 'replay|acces granted but function is work in progress';
									if (!$this->setGroupOrgan($organ, $chat_ID)) 
									{ 
										return 'replay|Internal ERROR!'; 
									} else {
										return 'replay|Successfully selected organ "'.$organ.'"';
									}
									
									break;
								} else {
									return 'replay|You have for "'.$organName.'" not the organisation permissions to do that';
									break;
								}
								
							}
						}
						return 'replay|You have not the groupPermissions to do that';
						break;
					} else {
						return 'replay|Please do this in a group or supergroup';
					}
					break;
				case '/createOrgan':
					$chat_type = $data->{'message'}->{'chat'}->{'type'};
					if ($chat_type == 'private') 
					{
						$organName = $dataArray[1];
						return $this->createOrgan($from, $organName);
						
					} else { return "replay|Please message me directly to do that."; }
					break;
				case '/addOrganAdmin':
					$chat_type = $data->{'message'}->{'chat'}->{'type'};
					if ($chat_type == 'private') 
					{
						$organName = $dataArray[1];
						return $this->createOrgan($from, $organName);
						
					} else { return "replay|Please message me directly to do that."; }
					break;
				case '/test':
					$processer("send|debugshit", $data);
					break;
				default:
					// Don't handle the event Type
					//return "replay|Command not reconized";
					break;
			}
			break;
		default:
			// Don't handle the event Type
		}
	
	}
}



?>