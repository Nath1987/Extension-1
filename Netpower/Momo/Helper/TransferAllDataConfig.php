<?php

namespace Netpower\Momo\Helper;

use Netpower\Momo\Services\Config;
use Netpower\Momo\Services\Transport;

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
        $res['requestId'] = time()."";
        $res['amount'] = $dataArray['amount'];
        $res['orderId'] = time()."";
        $res['orderInfo'] = $dataArray['orderInfo'];
        $res['returnUrl'] = "https://momo.vn/return"; 
        $res['notifyUrl'] = "https://dummy-url.vn/notify";
        $res['requestType'] = "captureMoMoWallet"; 
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
}