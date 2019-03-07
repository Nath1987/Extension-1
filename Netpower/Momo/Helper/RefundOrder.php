<?php

namespace Netpower\Momo\Helper;

use Netpower\Momo\Services\Config;
use Netpower\Momo\Services\Cons;

use Netpower\Momo\Services\Transport;

class RefundOrder 
{
    /**
     * @var Netpower\Momo\Services\Config
     */
    protected $_config;

    /**
     * @var Netpower\Momo\Helper\TransferAllDataConfig
     */
    protected $_transfer;

    /**
     * @var Netpower\Momo\Services\Transport
     */
    protected $_transport;

    /**
     * @param Netpower\Momo\Services\Config $config;
     * @param Netpower\Momo\Helper\TransferAllDataConfig $transfer;
     * @param Netpower\Momo\Services\Transport $transport;
     */
    public function __construct(
        Config $config,
        TransferAllDataConfig $transfer,
        Transport $transport
    )
    {
        $this->_config = $config;
        $this->_transfer = $transfer;
        $this->_transport = $transport;
    }

    /**
     * @param $dataArray : [ 
     *  requestId or orderId,
     *  transactionId, 
     *  amount 
     * ]
     * @return Array contain response value from API refund /pay/refund
     */
    public function refund($dataArray)
    {   
        // $dataHash = [
        //     "partnerCode"=> $this->_config->getMerchantnentId(),
        //     "partnerRefId"=> $dataArray['orderId'],
        //     "momoTransId"=> $dataArray['transId'],
        //     "amount"=> $dataArray['amount']
        // ]; 
        
        //$dataArray["partnerCode"] = $this->_config->getMerchantnentId();

        $hashValue = $this->_transfer->calHashValue($dataArray);

        $dataRequest = [
            "partnerCode" => $dataArray["partnerCode"],
            "requestId" => $dataArray["partnerRefId"],
            "hash" => $hashValue,
            "version" => Cons::VERSION
        ];  

        $dataJSON = json_encode($dataRequest);

        $urlEndpoint = $this->_config->getUrlEndPoint() . Cons::REFUND_ORDER_PATH;
      
        try {
            $result = $this->_transport->post($urlEndpoint, $dataJSON);
        } catch(Exception $e) {
            // REQUEST API FAIL.
        }
        return $result;
    }   

    /** IN MODEL PaymentMethod.php
     * @param $dataArray : [ 
     *  partnerCode,
     *  accessKey, 
     *  requestId,
     *  amount,
     *  orderId,
     *  transId,
     *  requestType,
     * ]
     * @return Array contain response value from API refund /gw_payment/transactionProcessor
     */
    public function refundOrder($dataArray)
    {
        $secretKey = $this->_config->getSecretKey();
        
        $signature = $this->_transfer->calculateSignature($dataArray, $secretKey);

        $dataArray['signature'] = $signature;

        $dataJSON = json_encode($dataArray);
       
        $urlEndpoint = $this->_config->getUrlEndPoint() . Cons::GATEWAY_PAYMENT;

        try {
            $result = $this->_transport->post($urlEndpoint, $dataJSON);
        } catch(Exception $e) {
            // FAIL REQUEST API
        }
        return $result;
    }
}