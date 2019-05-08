<?php

class adminStuff
{
	var $warnsForBan = 3;
	var $globalBans = true;
	
	var $pluginName = 'adminStuff';
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
	
	function getOrgan($data, $processer)
	{
		$chat_ID = $data->{'message'}->{'chat'}->{'id'};
		$chat_type = $data->{'message'}->{'chat'}->{'type'};
		$from = $data->{'message'}->{'from'}->{'id'};
		if ($chat_type == 'private')
		{
			$result = $this->getData('selectedOrganList.txt', $from);
			if ($result === false) {
				$processer("replay|Please select a Organisation with /selectOrgan <your_Organisation_ID>", $data);
				return 0;
			} else { 
				$processer('replay|Organisation "'.$result.'" is selected', $data);
				return $result;
			}
		} else {
			return $chat_ID;
		}
		return false;
	}
	
	function checkPermission($userID, $organ)
	{
		//include 'groups.php';
		
		if ($organ == 0) return false;
		
		$result = $this->getData('organList_'.$organ.'.txt', 'admins');
		
		if (strpos($result, $userID) === false) {
			return false;
		} else { return true; }
		
		return false;
	}
	
	function ban($userID, $group, $reason, $processer)
	{
		$name = $userID;
		
		$result = $this->getData('banlist.txt', $name);
		$result .= $reason."\r\n";
		
		$this->setData('banlist.txt', $name, $result);
		
		return 'kickChatMember|'.$userID.'|0';
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
	
	function admin($userID, $group, $processer)
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
			$dataArray = explode(' ', $received_message);
			$command = $dataArray[0];
			$from = $data->{'message'}->{'from'}->{'id'};
			if (!strpos($data, 'reply_to_message') === false) 
			{
				$reply_to_message_from = $data->{'message'}->{'reply_to_message'}->{'from'}->{'id'};
			}
			switch ($command) {
				case '/ban':
					$organ = $this->getOrgan($data, $processer);
					if (!$this->checkPermission($from, $organ))
					{
						return 'replay|You have not the permissions to do that';
						break;
					}
					$userID = $reply_to_message_from;
					$reason = $dataArray[1];
					return $this->ban($userID, $group, $reason, $processer);
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
					$organ = $dataArray[1];
					$this->setData('selectedOrganList.txt', $from, $organ);
					return 'replay|Organisation "'.$organ.'" is selected now';
				case '/test':
					$processer("send|debugshit", $data);
					break;
				default:
					// Don't handle the event Type
					return "replay|Command not reconized";
			}
			break;
		default:
			// Don't handle the event Type
		}
	
	}
}



?>