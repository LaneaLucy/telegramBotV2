<?php

class verification
{
	var $pluginName = 'verificationPlugin';
	var $description = 'Verify Users';
	var $commandDescriptions = "[{'verify' : 'verify a user'}]";
	
	
	function setData($file, $name, $value)
	{
		$root = filter_input(INPUT_SERVER, 'DOCUMENT_ROOT');
		
		
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
	
	function decryptData($data_encrypted, $data_secret, $data_hash) {
		$data_secret_hash = hash('sha512', $data_secret.$data_hash, true);
		$data_key         = substr($data_secret_hash, 0, 32);
		$data_iv          = substr($data_secret_hash, 32, 16);
		$options          = OPENSSL_RAW_DATA | OPENSSL_ZERO_PADDING;
		$data_decrypted   = openssl_decrypt($data_encrypted, 'aes-256-cbc', $data_key, $options, $data_iv);
		if (!$data_decrypted) {
			throw new Exception('DECRYPT_FAILED');
		}
		$data_decrypted_hash = hash('sha256', $data_decrypted, true);
		if (strcmp($data_hash, $data_decrypted_hash)) {
			throw new Exception('HASH_INVALID');
		}
		$padding_len    = ord($data_decrypted[0]);
		$data_decrypted = substr($data_decrypted, $padding_len);
		return $data_decrypted;
	}
	
	function passportGetFile($itemName, $passport_data_item, $secure_data_field_name, $processer, $data) {
		$file_data      = $passport_data_item->{$itemName};
		$file_id        = $file_data->{'file_id'};
		$file_path	= $processer('getFileById|'.$file_id, $data);
		$file_encrypted = $processer('getFile|'.$file_path, $data);
		//error_log('file_id: '.$file_id, 0);
		//error_log('file_path: '.$file_path, 0);
		//error_log('file_encrypted: '.$file_encrypted, 0);
		if ($file_encrypted !== false) {
			$file_credentials = $secure_data_field_name[$itemName];
			$file_hash        = base64_decode($file_credentials['file_hash']);
			$file_secret      = base64_decode($file_credentials['secret']);
			$file_content     = $this->decryptData($file_encrypted, $file_secret, $file_hash);
			//File is Here
			//$file_local_path  = md5($file_id).'.jpg';
			//file_put_contents('./plugins/pluginDatas/'.$this->pluginName.'/'.$file_local_path, $file_content);
			
		}
	}
	
	function __construct() //init
	{
		
		/*if(!is_file('./plugins/pluginDatas/'.$this->pluginName.'/private.key') {
			
			//$configargs = array(
			//	//"config" => "./../openssl.cnf",
			//	'private_key_bits' => 2048,
			//	'private_key_type' => OPENSSL_KEYTYPE_RSA,
			//);
			
			$config = array(
			    "digest_alg" => "sha512",
			    "private_key_bits" => 2048,
			    "private_key_type" => OPENSSL_KEYTYPE_RSA,
			);
			
			// Create the keypair
			//$res = openssl_pkey_new($configargs);
			$res = openssl_pkey_new($config);
			openssl_pkey_export($res, $privKey);
			$pubKey = openssl_pkey_get_details($res);
			$pubKey = $pubKey["key"];
			
			file_put_contents('./plugins/pluginDatas/'.$this->pluginName.'/private.key', $privKey);
			file_put_contents('./plugins/pluginDatas/'.$this->pluginName.'/public.key', $pubKey);
		}*/
		
		if(!is_dir('./plugins/pluginDatas/'.$this->pluginName.'/'))	{ mkdir('./plugins/pluginDatas/'.$this->pluginName.'/', 0755, true); }
		
		$data = "{'pluginName' : '".$this->pluginName."', 'description' : '".$this->description."', 'commandDescriptions' : ".$this->commandDescriptions."}";
		return $data;
	}
	
	function handelEvent($event, $data, $obj)
	{
		$processer = $obj->process;
		
		if(!is_file('./private.key')) { 
			error_log('Private Key is missing!', 0);
			return '';
		}
		
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
				case "verify":
					// Do something with the Data
					break;
				case "command2":
					// Do something with the Data
					break;
				default:
					// Don't handle the event Type
			}
			break;
		case "passport":
			//file_put_contents('./out', $data);
			$passportDatas = $data->{'message'}->{'passport_data'}->{'data'};
			$passportCredentials = $data->{'message'}->{'passport_data'}->{'credentials'};
			
			$passportCredentialsSecret = base64_decode($passportCredentials->{'secret'});
			$passportCredentialsHash = base64_decode($passportCredentials->{'hash'});
			$passportCredentialsData = base64_decode($passportCredentials->{'data'});
			
			$result = openssl_private_decrypt($passportCredentialsSecret, $credentials_secret_decrypted, file_get_contents('./private.key'), OPENSSL_PKCS1_OAEP_PADDING);
			if (!$result) {
			    // Credential secret decryption failed
			    error_log('Credential secret decryption failed', 0);
			    exit;
			}
			
			$credentials_data_json = $this->decryptData($passportCredentialsData, $credentials_secret_decrypted, $passportCredentialsHash);
			//return 'send|'.$credentials_data_json;
			
			$credentials_data = json_decode($credentials_data_json, true);
			
			
			if (!isset($credentials_data['secure_data']) || !isset($credentials_data['nonce'])) {
				throw new Exception('CREDENTIALS_FORMAT_INVALID');
			}
			
			$secure_data = $credentials_data['secure_data'];
			$nonce       = $credentials_data['nonce'];
			//if (!preg_match('/^[0-9a-f]{64}$/', $nonce)) {
			//	throw new Exception('NONCE_INVALID');
			//}
			
			foreach ($passportDatas as &$passport_data_item) {
				$field_name = $passport_data_item->{'type'};
				if (isset($secure_data[$field_name])) {
					$secure_data_item = $secure_data[$field_name];
					if (isset($secure_data_item['data']) && isset($passport_data_item->{'data'})) {
						$value_credentials = $secure_data_item['data'];
						$value_hash        = base64_decode($value_credentials['data_hash']);
						$value_secret      = base64_decode($value_credentials['secret']);
						$data_encrypted    = base64_decode($passport_data_item->{'data'});
						$value_data        = $this->decryptData($data_encrypted, $value_secret, $value_hash);
						$value_data_json   = json_decode($value_data, true);
						$passport_data_item->{'data'} = $value_data_json;
						
						$processer('send|'.$value_data, $data);
					}
					if (isset($secure_data[$field_name]['front_side']) && isset($passport_data_item->{'front_side'})) {
						$front_side_file = $this->passportGetFile('front_side', $passport_data_item, $secure_data[$field_name], $processer, $data);
						
					}
					if (isset($secure_data[$field_name]['reverse_side']) && isset($passport_data_item->{'reverse_side'})) {
						$reverse_side_file = $this->passportGetFile('reverse_side', $passport_data_item, $secure_data[$field_name], $processer, $data);
						
					}
					if (isset($secure_data[$field_name]['selfie']) && isset($passport_data_item->{'selfie'})) {
						$selfie_file = $this->passportGetFile('selfie', $passport_data_item, $secure_data[$field_name], $processer, $data);
						
					}
				}
				
			}
			
			
			if (isset($front_side_file) && isset($selfie_file)) {
				//Visual AI
			}
			
			//return 'send|'.$credentials_data_json;
			return 'send|Data Received';
			break;
		default:
			$answer = 'replay|pingas';
			return $answer;
		}
	
	}
}



?>
