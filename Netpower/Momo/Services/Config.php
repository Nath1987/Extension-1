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
     * @return array : all config switch mode. 
     */
    public function getConfigValues()
    {
        $configValues = [];
        $mode = $this->getMode();
        if($mode === "production") {
            $configValues = [
                'title' => $this->_configValue->getValue(Cons::TITLE_PATH,ScopeInterface::SCOPE_STORE),
                'partnerCode' => $this->_configValue->getValue(Cons::MERCHANTNENT_ID_PATH,ScopeInterface::SCOPE_STORE),
                'accessKey' => $this->_configValue->getValue(Cons::ACCESS_KEY_PATH,ScopeInterface::SCOPE_STORE),
                'secretKey' => $this->_configValue->getValue(Cons::SECRECT_KEY_PATH,ScopeInterface::SCOPE_STORE),
                'apiEndpoint' => $this->_configValue->getValue(Cons::API_ENDPOINT_PATH,ScopeInterface::SCOPE_STORE)
            ];
        }
        else {
            $configValues = [
                'title' => $this->_configValue->getValue(Cons::TITLE_PATH,ScopeInterface::SCOPE_STORE),
                'partnerCode' => $this->_configValue->getValue(Cons::MERCHANTNENT_ID_TEST_PATH,ScopeInterface::SCOPE_STORE),
                'accessKey' => $this->_configValue->getValue(Cons::ACCESS_KEY_TEST_PATH,ScopeInterface::SCOPE_STORE),
                'secretKey' => $this->_configValue->getValue(Cons::SECRECT_KEY_TEST_PATH,ScopeInterface::SCOPE_STORE),
                'apiEndpoint' => $this->_configValue->getValue(Cons::API_ENDPOINT_TEST_PATH,ScopeInterface::SCOPE_STORE)
            ];
        }
        return $configValues;
    }
}