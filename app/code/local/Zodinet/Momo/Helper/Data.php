<?php

class Zodinet_Momo_Helper_Data extends Mage_Core_Helper_Abstract
{
	const XML_MOMO_IS_TEST_MODE = 'payment/momo/is_test_mode';
	const XML_MOMO_TEST_ACCESS_KEY = 'payment/momo/test_access_key';
	const XML_MOMO_TEST_SERECT_KEY = 'payment/momo/test_serect_key';
	const XML_MOMO_TEST_PARTNER_CODE = 'payment/momo/test_partner_code';
	const XML_MOMO_TEST_GATEWAY = 'payment/momo/test_gateway';

	const XML_MOMO_ACCESS_KEY = 'payment/momo/access_key';
	const XML_MOMO_SERECT_KEY = 'payment/momo/serect_key';
	const XML_MOMO_PARTNER_CODE = 'payment/momo/partner_code';
	const XML_MOMO_GATEWAY = 'payment/momo/gateway';

	const XML_MOMO_DEFAULT_DESCRIPTION = 'payment/momo/description';

	private $_isDebug;
	private $_logPath;

	public function __construct(){
		$this->_logPath = "momo.log";
		$this->_isDebug = true;
	}

	public function isDebug($storeId = null){
		return true;
	}

	public function isTestMode($storeId = null){
		return Mage::getStoreConfig(self::XML_MOMO_IS_TEST_MODE , $storeId);
	}

	public function getAccessKey($storeId = null){
		$isTestMode = $this->isTestMode($storeId);
		if($isTestMode){
			return Mage::getStoreConfig(self::XML_MOMO_TEST_ACCESS_KEY, $storeId);
		}
		else{
			return Mage::getStoreConfig(self::XML_MOMO_ACCESS_KEY, $storeId);
		}
	}

	public function getSerectKey($storeId = null){
		$isTestMode = $this->isTestMode($storeId);
		if($isTestMode){
			return Mage::getStoreConfig(self::XML_MOMO_TEST_SERECT_KEY, $storeId);
		}
		else{
			return Mage::getStoreConfig(self::XML_MOMO_SERECT_KEY, $storeId);
		}
	}

	public function getPartnerCode($storeId = null){
		$isTestMode = $this->isTestMode($storeId);
		if($isTestMode){
			return Mage::getStoreConfig(self::XML_MOMO_TEST_PARTNER_CODE, $storeId);
		}
		else{
			return Mage::getStoreConfig(self::XML_MOMO_PARTNER_CODE, $storeId);
		}
	}

	public function getGateway($storeId = null){
		$isTestMode = $this->isTestMode($storeId);
		if($isTestMode){
			return Mage::getStoreConfig(self::XML_MOMO_TEST_GATEWAY, $storeId);
		}
		else{
			return Mage::getStoreConfig(self::XML_MOMO_GATEWAY, $storeId);
		}
	}

	public function getDefaultDescription($storeId = null){
		return Mage::getStoreConfig(self::XML_MOMO_DEFAULT_DESCRIPTION, $storeId);
	}

	public function log($msg){
		if($this->_isDebug){
			Mage::log($msg,null,$this->_logPath);
		}
	}
}