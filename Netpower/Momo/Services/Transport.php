<?php

namespace Netpower\Momo\Services;

use \Netpower\Momo\Api\Services\TransportInterface;

class Transport implements TransportInterface 
{
    public function post($api, $data = null)
	{
		$result = $this->call("POST", $api, $data);
		$result = json_decode($result, true);
		if($result['errorCode'] == "0") {
			$result = json_encode($result);
			return $result;
		}
    	else {
			return "0";
    	}
	}

	public function call($method, $api, $data = null)
	{	
		if($method == "POST") {
		 	$ch = curl_init();
         	curl_setopt($ch, CURLOPT_URL, $api);
         	curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
         	curl_setopt($ch, CURLOPT_POST, 1);
         	curl_setopt($ch, CURLOPT_POSTFIELDS,$data);
         	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
         	$response  = curl_exec($ch);
         	return $response;
     	}
	}

	public function log($variable)
	{
		$writer = new \Zend\Log\Writer\Stream(BP . '/var/log/test.log');
		$logger = new \Zend\Log\Logger();
		$logger->addWriter($writer);
		return $logger->info($variable);
	}
}