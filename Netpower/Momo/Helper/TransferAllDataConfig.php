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

        $requestUrl = $this->_config->getBaseUrl() . Cons::RETURN_URL_PATH;
        $notifyUrl = $this->_config->getBaseUrl() . Cons::NOTIFY_URL_PATH;

        $res['partnerCode'] = $dataConfigs['partnerCode'];
        $res['accessKey'] = $dataConfigs['accessKey'];
        $res['requestId'] = $dataArray['requestId'];
        $res['amount'] = $dataArray['amount'];
        $res['orderId'] = $dataArray['orderId'];    
        $res['orderInfo'] = $dataConfigs['title'];
        $res['returnUrl'] = $requestUrl; 
        $res['notifyUrl'] = $notifyUrl;
        $res['extraData'] = $dataArray['extraData'];

        $dataCalculateSignature = $res;
        $res['requestType'] = Cons::REQUEST_TYPE_CAPTURE; 
        $signature = $this->calculateSignature($dataCalculateSignature, $dataConfigs['secretKey']);
        $res['signature'] = $signature;
        
        return $res;
    }

    public function convertStringRequiredByMoMo($dataArray)
    {
        $dataRequest = [];
        foreach($dataArray as $key => $value) {
            $dataRequest[] = "{$key}={$value}";
        }
        $rawHash = implode('&', $dataRequest);

        return $rawHash;
    }

    /** 
     * @param array     : all data is required by API.
     * @return string   : signature
     */
    public function calculateSignature($dataArray, $secretKey = null)
    {
        $rawHash = $this->convertStringRequiredByMoMo($dataArray);
        $signature = hash_hmac("sha256", $rawHash, $secretKey);

        return $signature;
    }


    public function calHashValue($dataArray)
    {   
        $publicKey = $this->_config->getPublicKey();
        $rowJSON =  json_encode($dataArray);
        openssl_public_encrypt($rowJSON, $encrypted, $publicKey, OPENSSL_PKCS1_PADDING);
        $hashValue =  base64_encode($encrypted);

        return $hashValue;
    }


    /** 
     * @return string   : Total prices include shipping...
     */
    public function getOrderTotal()
    {
        $objectManager = ObjectManager::getInstance();
        $cart = $objectManager->get('\Magento\Checkout\Model\Cart'); 
        $grandTotal = $cart->getQuote()->getGrandTotal();
        return $grandTotal;
    }   
}