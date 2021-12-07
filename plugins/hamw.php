<?php

class hamw
{
  
  const urlBase = "http://example.com/plugins/temp/";
	
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
			$dataArray = explode(' ', $data->{'command'});
			$command = $dataArray[0];
			// Do something with the Data
			$from = $data->{'message'}->{'from'}->{'id'};
			if ($command == '/hamw') {
			  $url = 'http://www.hamqsl.com/solar101vhf.php';
			  $file_name = date("d.m.Y-H:i").".jpg";

        if(file_exists($file_name) && is_file($file_name))
        {
          
        } else {
          if(file_put_contents( $file_name,file_get_contents($url))) { 
            $answer = "photo|".self::urlBase."/".$file_name;
            return $answer;
          } else { 
            return "reply|File downloading failed."; 
          } 
        }
				//$answer = "photo|http://www.hamqsl.com/solar101vhf.php";
				//$answer = "replay|hamw";
				//return $answer;
			}
			break;
		default:
			// Don't handle the event Type
		}
	
	}
}



?>