<?php

namespace Netpower\Momo\Helper;

use Netpower\Momo\Services\Config;
use Netpower\Momo\Services\Transport;

use Netpower\Momo\Services\Cons;

use \Magento\Framework\App\ObjectManager;
class TransferAllDataConfig 
{

    /** 
     * @var Netpower\Momo\Services\Config;
     */
    protected $_config;

    /** 
     * @param Netpower\Momo\Services\Config;
     */
    public function __construct(
        Config $config
    ) {
        $this->_config = $config;
    }

     /** 
     * @param array     : include [amount, orderInfo, extraData]
     * @return array    : all add that needed to send to API require [partnerCode, accessKey, requestId, amount, orderId, orderInfo, returnUrl, notifyUrl, requestType, extraData, signature]
     */
    public function allDataApiRequire($dataArray)
    {   
        $res = [];

        $dataConfigs = $this->_config->getConfigValues();
        
        $res['partnerCode'] = $dataConfigs['partnerCode'];
        $res['accessKey'] = $dataConfigs['accessKey'];
        $res['requestId'] = $dataArray['requestId'];
        $res['amount'] = $dataArray['amount'];
        $res['orderId'] = $dataArray['orderId'];    
        $res['orderInfo'] = $dataConfigs['title'];
        $res['returnUrl'] = Cons::RETURN_URL; 
        $res['notifyUrl'] = Cons::NOTIFY_URL;
        $res['requestType'] = Cons::REQUEST_TYPE; 
        $res['extraData'] = $dataArray['extraData'];

        $dataCalculateSignature = $res;
        $dataCalculateSignature['secretKey'] = $dataConfigs['secretKey'];

        $signature = $this->calculateSignature($dataCalculateSignature);

        $res['signature'] = $signature;

        return $res;
    }

    /** 
     * @param array     : all data is required by API.
     * @return string   : signature
     */
    public function calculateSignature($dataArray)
    {
        $rawHash = "partnerCode=".$dataArray['partnerCode']."&accessKey=".$dataArray['accessKey']."&requestId=".$dataArray['requestId']."&amount=".$dataArray['amount']."&orderId=".$dataArray['orderId']."&orderInfo=".$dataArray['orderInfo']."&returnUrl=".$dataArray['returnUrl']."&notifyUrl=".$dataArray['notifyUrl']."&extraData=".$dataArray['extraData'];

        $signature = hash_hmac("sha256", $rawHash, $dataArray['secretKey']);

        return $signature;
    }

    public function getOrderTotal()
    {
        $objectManager = ObjectManager::getInstance();
        $cart = $objectManager->get('\Magento\Checkout\Model\Cart'); 
        $grandTotal = $cart->getQuote()->getGrandTotal();
        return $grandTotal;
    }   
}