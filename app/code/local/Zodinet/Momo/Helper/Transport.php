<?php

class Zodinet_Momo_Helper_Transport extends Mage_Core_Helper_Abstract{

	const MAX_TIMEOUT = 5;

	protected function getHelper(){
		return Mage::helper('momo');
	}

	public function call($data, $ignoreRawHash = array()){
		$url = $this->getHelper()->getGateway();
		$partnerCode = $this->getHelper()->getPartnerCode();
		$accessKey = $this->getHelper()->getAccessKey();
		$serectKey = $this->getHelper()->getSerectKey();

		$data = array_merge( 
			array(
				'partnerCode' => $partnerCode , 
				'accessKey' => $accessKey
			),
			$data
		);

		$params = array();
		foreach ($data as $key => $value) {
			if(!in_array($key,$ignoreRawHash)){
				$params[]= "{$key}={$value}";
			}
		}

		$rawHash = implode("&", $params);
		$signature = hash_hmac("sha256", $rawHash, $serectKey);
		$data['signature'] = $signature;

		$this->getHelper()->log("Request: {$url}");
		$this->getHelper()->log("RawHash: {$rawHash}");
		$this->getHelper()->log(json_encode($data));
		
		try{
			$response =  $this->exec($url, json_encode($data));
			$this->getHelper()->log("Response:");
			$this->getHelper()->log($response);
		}
		catch(Exception $e){
			$this->getHelper()->log($e->getMessage());
			$response = false;
		}
		
		
		if($result = json_decode($response)){
			return $result;
		}
		else{
			return false;
		}
	}

	public function exec($url, $data){
		$ch = curl_init($url);
	    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
	    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
	        'Content-Type: application/json',
	        'Content-Length: ' . strlen($data))
	    );
		//curl_setopt($ch, CURLOPT_TIMEOUT, self::MAX_TIMEOUT);
	    //curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, self::MAX_TIMEOUT);
	    //execute post
	    $result = curl_exec($ch);
		if(curl_error($ch))
		{
			throw new Exception(curl_error($ch));
		}
	    //close connection
	    curl_close($ch);
	    return $result;
	}	
}