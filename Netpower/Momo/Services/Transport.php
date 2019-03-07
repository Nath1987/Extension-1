<?php

namespace Netpower\Momo\Services;

use \Netpower\Momo\Api\Services\TransportInterface;

use Netpower\Momo\Helper\CatchErrorCodeRequest;
use Netpower\Momo\Helper\CatchStatusCheck;

class Transport implements TransportInterface 
{

	/**
	 * @param String $api : is URL of Endpoint API
	 * @param Json $data : contain value format json send to API.
	 * @return Json Response value or String Error.
	 */
    public function post($api, $data = null)
	{	
			//$this->log($data);
			
			$result = $this->call("POST", $api, $data);
			$result = json_decode($result, true);
			$message = $result['message'];

			if(isset($result['errorCode'])){
				$errorCode = $result['errorCode'];
				if($errorCode == 0) {
					return $result;
				}
				else {
					return $message;
				}
			}
			else {
				$status = $result['status'];
				if($status == 0) {
					return $result;
				}
				else {
					return $message;
				}
			}
			
	}

	/**
	 * @param String $method : maybe POST, GET, PUT, DELETE ...
	 * @param String $api : URL API.
	 * @param Json $data : Value request send to API.
	 * @return Json Response value
	 */
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

	/**
	 * @param Simple Value.
	 * @return File Log.
	 */
	public function log($variable)
	{
		$writer = new \Zend\Log\Writer\Stream(BP . '/var/log/test.log');
		$logger = new \Zend\Log\Logger();
		$logger->addWriter($writer);
		return $logger->info($variable);
	}
}