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
        return $this->_configValue->getValue('payment/netpowermomo/mode',ScopeInterface::SCOPE_STORE);
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
                'title' => $this->_configValue->getValue('payment/netpowermomo/title',ScopeInterface::SCOPE_STORE),
                'partnerCode' => $this->_configValue->getValue('payment/netpowermomo/merchantnent_id',ScopeInterface::SCOPE_STORE),
                'accessKey' => $this->_configValue->getValue('payment/netpowermomo/access_key',ScopeInterface::SCOPE_STORE),
                'requestId' => $this->_configValue->getValue('payment/netpowermomo/secret_key',ScopeInterface::SCOPE_STORE),
                'apiEndpoint' => $this->_configValue->getValue('payment/netpowermomo/api_endpoint',ScopeInterface::SCOPE_STORE)
            ];
        }
        else {
            $configValues = [
                'title' => $this->_configValue->getValue('payment/netpowermomo/title',ScopeInterface::SCOPE_STORE),
                'partnerCode' => $this->_configValue->getValue('payment/netpowermomo/merchantnent_id_test',ScopeInterface::SCOPE_STORE),
                'accessKey' => $this->_configValue->getValue('payment/netpowermomo/access_key_test',ScopeInterface::SCOPE_STORE),
                'accessKey' => $this->_configValue->getValue('payment/netpowermomo/secret_key_test',ScopeInterface::SCOPE_STORE),
                'apiEndpoint' => $this->_configValue->getValue('payment/netpowermomo/api_endpoint_test',ScopeInterface::SCOPE_STORE)
            ];
        }
        return $configValues;
    }
}