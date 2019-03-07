<?php

namespace Netpower\Momo\Services;

use \Magento\Framework\App\Config\ScopeConfigInterface;
use \Magento\Store\Model\ScopeInterface;


class Config 
{
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface;
     */
    protected $_configValue;

    protected $_logger;

    /** CONSTRUCTOR
     * @param \Magento\Framework\App\Config\ScopeConfigInterface;
     * @param \Netpower\Momo\Services\Transport;
     */
    public function __construct(
        ScopeConfigInterface $configValue,
        Transport $logger)
    {
        $this->_configValue = $configValue;
        $this->_logger = $logger;
    }

    /** 
     * @return string : test or production
     */
    public function getMode()
    {   
        return $this->_configValue->getValue(Cons::MODE_PATH,ScopeInterface::SCOPE_STORE);
    }

     /** 
     * @return string : Title or Title-Test
     */
    public function getTitle()
    {
        $mode = $this->getMode();
        if($mode === "production") {
            return $this->_configValue->getValue(Cons::TITLE_PATH,ScopeInterface::SCOPE_STORE);
        }
        else {
            return $this->_configValue->getValue(Cons::TITLE_PATH,ScopeInterface::SCOPE_STORE) ."-Test";
        }
    }

    /** 
     * @return string : Partner ID
     */
    public function getMerchantnentId()
    {
        $mode = $this->getMode();
        if($mode === "production") {
            return $this->_configValue->getValue(Cons::MERCHANTNENT_ID_PATH,ScopeInterface::SCOPE_STORE);
        }
        else {
            return $this->_configValue->getValue(Cons::MERCHANTNENT_ID_TEST_PATH,ScopeInterface::SCOPE_STORE);
        }
    }

    /** 
    * @return string : Access Key 
    */
    public function getAccessKey()
    {
        $mode = $this->getMode();
        if($mode === "production") {
            return $this->_configValue->getValue(Cons::ACCESS_KEY_PATH,ScopeInterface::SCOPE_STORE);
        }
        else {
            return $this->_configValue->getValue(Cons::ACCESS_KEY_TEST_PATH,ScopeInterface::SCOPE_STORE);
        }
    }

    /** 
    * @return string : Secret Key 
    */
    public function getSecretKey()
    {
        $mode = $this->getMode();
        if($mode === "production") {
            return $this->_configValue->getValue(Cons::SECRECT_KEY_PATH,ScopeInterface::SCOPE_STORE);
        }
        else {
            return $this->_configValue->getValue(Cons::SECRECT_KEY_TEST_PATH,ScopeInterface::SCOPE_STORE);
        }
    }

    /** 
    * @return string : Url endpoint 
    */
    public function getUrlEndPoint()
    {
        $mode = $this->getMode();
        if($mode === "production") {
            return $this->_configValue->getValue(Cons::API_ENDPOINT_PATH,ScopeInterface::SCOPE_STORE);
        }
        else {
            return $this->_configValue->getValue(Cons::API_ENDPOINT_TEST_PATH,ScopeInterface::SCOPE_STORE);
        }
    }

    public function getPublicKey()
    {
        $mode = $this->getMode();
        if($mode === "production") {
            $publicKeyConfig = $this->_configValue->getValue(Cons::PUBLIC_KEY_PATH,ScopeInterface::SCOPE_STORE);
            $publicKeyFormat = "";
            return $publicKeyFormat;
        }
        else {
            $publicKeyConfig = $this->_configValue->getValue(Cons::PUBLIC_KEY_TEST_PATH,ScopeInterface::SCOPE_STORE);
            $publicKeyFormat = "-----BEGIN PUBLIC KEY-----\r\n" . $publicKeyConfig . "\r\n-----END PUBLIC KEY-----";
            return $publicKeyFormat;
        }
    }

    public function getBaseUrl()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $storeManager = $objectManager->get('\Magento\Store\Model\StoreManagerInterface');
        $baseUrl =  $storeManager->getStore()->getBaseUrl();
        return $baseUrl;
    }

    /** 
     * @return array : all config switch mode. 
     */
    public function getConfigValues()
    {
        $configValues = [
            'title' => $this->getTitle(),
            'partnerCode' => $this->getMerchantnentId(),
            'accessKey' => $this->getAccessKey(),
            'secretKey' => $this->getSecretKey(),
            'apiEndpoint' => $this->getUrlEndPoint()
        ];

        return $configValues;
    }
}