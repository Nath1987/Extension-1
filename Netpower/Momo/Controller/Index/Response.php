<?php

namespace Netpower\Momo\Controller\Index;

use \Magento\Framework\App\Action\Context;
use \Magento\Framework\Controller\ResultFactory;

use \Magento\Sales\Model\OrderFactory;

use \Magento\Checkout\Model\Session;

use Netpower\Momo\Services\Cons;
use Netpower\Momo\Services\Config;
use Netpower\Momo\Services\Transport;

use Netpower\Momo\Model\MomoOrderFactory;
use Netpower\Momo\Model\MomoOrderQueueFactory;

use Netpower\Momo\Helper\TransferAllDataConfig;
use Netpower\Momo\Helper\QueryStatusOrder;
use Netpower\Momo\Helper\RefundOrder;
use Netpower\Momo\Helper\ConfirmOrder;

class Response extends \Magento\Framework\App\Action\Action 
{
    /** 
     * @var \Magento\Checkout\Model\Session;
     */
    protected $_session;

    /** 
     * @var Netpower\Momo\Model\MomoOrderFactory;
     */
    protected $_momoOrderCollect;
    
    /** 
     * @var Netpower\Momo\Helper\TransferAllDataConfig;
     */
    protected $_transfer;

    /** 
     * @var Netpower\Momo\Services\Config;
     */
    protected $_config;

    /** 
     * @var Netpower\Momo\Services\Transport;
     */
    protected $_transport;

    /** 
     * @var \Magento\Sales\Model\OrderFactory;
     */
    protected $_orderFactory;

    /**
     * @var \Magento\Framework\Controller\ResultFactory;
     */
    protected $_resultFactory;

    /**
     * @var Netpower\Momo\Helper\QueryStatusOrder;
     */
    protected $_queryStatus;

    /**
     * @var Netpower\Momo\Helper\RefundOrder;
     */
    protected $_refund;

    /**
     * @var Netpower\Momo\Helper\ConfirmOrder;
     */
    protected $_confirm;

    /**
     * @var Netpower\Momo\Helper\MomoOrderQueueFactory;
     */
    protected $_momoOrderQueueFactory;

    /** CONSTRUCTOR
     * @param \Magento\Framework\App\Action\Context $context,
     * @param \Magento\Checkout\Model\Session $session ,
     * @param Netpower\Momo\Helper\TransferAllDataConfig $transfer,
     * @param Netpower\Momo\Services\Config $config,
     * @param Netpower\Momo\Services\Transport $transport,
     * @param Netpower\Momo\Model\MomoOrderFactory $momoOrderCollect,
     * @param \Magento\Sales\Model\OrderFactory $orderFactory,
     * @param \Magento\Framework\Controller\ResultFactory $resultFactory
     * @param Netpower\Momo\Helper\QueryStatusOrder $queryStatus;
     * @param Netpower\Momo\Helper\RefundOrder $refund;
     * @param Netpower\Momo\Helper\ConfirmOrder $confirm;
     * @param Netpower\Momo\Model\MomoOrderQueueFactory $momoOrderQueueFactory;
     */
    public function __construct(
        Context $context,
        Session $session,
        TransferAllDataConfig $transfer,
        Config $config,
        Transport $transport,
        MomoOrderFactory $momoOrderCollect,
        OrderFactory $orderFactory,
        ResultFactory $resultFactory,
        QueryStatusOrder $queryStatus,
        RefundOrder $refund,
        ConfirmOrder $confirm,
        MomoOrderQueueFactory $momoOrderQueueFactory
        )
    {
        $this->_session = $session;
        $this->_momoOrderCollect = $momoOrderCollect;
        $this->_transfer = $transfer;
        $this->_config = $config;
        $this->_transport = $transport;
        $this->_orderFactory = $orderFactory;
        $this->_resultFactory = $resultFactory;
        $this->_queryStatus = $queryStatus;
        $this->_refund = $refund;
        $this->_confirm = $confirm;
        $this->_momoOrderQueueFactory = $momoOrderQueueFactory;
        return parent::__construct($context);
    }

    public function execute()
    {
        $valueResponse = $this->getRequest()->getParams();
        $pathUrlController = $this->checkTransactionOrder($valueResponse);
       
        $resultRedirect = $this->resultRedirectFactory->create();
        $resultRedirect->setPath($pathUrlController);
        return $resultRedirect;
    }

    /** 
     * @param Array [
     * partnerCode,
     * accessKey,
     * requestId,
     * amount,
     * orderId,
     * orderInfo,
     * orderType,
     * transId,
     * message,
     * localMessage,
     * responseTime,
     * errorCode,
     * payType,
     * extraData,
     * signature, ]
     *
     * @return String Path URL PAGE
     */
    public function checkTransactionOrder($valueResponse)
    {
        $allMomoId = $this->_momoOrderCollect->create()->getCollection()->getColumnValues('momo_id');

        $entityId = $this->_session->getLastRealOrder()->getEntityId();

        $requestId = $valueResponse['requestId'];
        $payType = $valueResponse['payType'];
        $transactionId = $valueResponse['transId'];
        $status = $valueResponse['message'];
        $orderId = $valueResponse['orderId'];

        $dataRequest = [
            'partnerCode' => $valueResponse['partnerCode'],
            'accessKey' => $valueResponse['accessKey'],
            'requestId' => $requestId,
            'orderId' => $orderId,
            'requestType' => Cons::REQUEST_TYPE_TRANSACTION,
        ];
        $secretKey = $this->_config->getSecretKey();

        $signature = $this->_transfer->calculateSignature($dataRequest, $secretKey);

        $dataRequest['signature'] = $signature;

        $dataSendRequest = json_encode($dataRequest);
        $url = $this->_config->getUrlEndPoint() . Cons::GATEWAY_PAYMENT;

        try {
            $dataResponse =  $this->_transport->post($url, $dataSendRequest);
        } catch(\Exception $e) {
            $this->messageManager->addError(__($e->getMessage()));
            return Cons::CHECKOUT_FAIL_PATH_MAGENTO;
        }

        if($dataResponse == NULL) {
            $this->messageManager->addError(__("Cound not Parse data from MoMo"));
            return Cons::CHECKOUT_FAIL_PATH_MAGENTO;;
        }
        elseif(!is_array($dataResponse)) { // Cancel request or call transaction API fail
            $orderIncrementId = $this->_session->getLastRealOrderId();
            $order = $this->_orderFactory->create();
            $order->loadByIncrementId($orderIncrementId);

            if($dataResponse == Cons::CANCEL_ORDER) { // When Use Cancel
                $order->getPayment()->cancel();
                $order->addStatusHistoryComment("MOMO Queue changes from pending to canceled");
                $order->registerCancellation("Cancel from MOMO with Error Code : {$dataResponse}");
                $order->save();
            }
            else { // Transaction API fail 
                $dataMomoQueue = [
                    'request_id' => $requestId,
                    'order_id' => $orderId,
                    'status' => "fail",
                    'error' => $dataResponse,
                    'sales_order_id' => $entityId
                ];

                // Set status for order if order has status different pending
                if($order->getData('status') != 'pending' && $order) {
                    $order->setState("pending");
                    $order->setStatus("pending");
                }   

                // Check id of order in momo_queue, if the same order id we don't save new record.
                $momoOrderQueue = $this->_momoOrderQueueFactory->create();
                $allMomoQueueId = $momoOrderQueue->getCollection()->getColumnValues('sales_order_id');
                $checkExistMoMoQueueId = $this->checkExistMoMoId($allMomoQueueId, $requestId);
                if($checkExistMoMoQueueId) { // NOTE : opposite , $checkExistMoMoQueueId == true when don't same value
                    $momoOrderQueue->setData($dataMomoQueue);
                    $momoOrderQueue->save();
                }

                $this->storeDataMomoOrderQueue($dataMomoQueue);
            }

            $this->messageManager->addError(__($dataResponse));
            return Cons::CHECKOUT_FAIL_PATH_MAGENTO;;
        }
        else {
            $orderIncrementId = $this->_session->getLastRealOrderId();
            $order = $this->_orderFactory->create();
            $order->loadByIncrementId($orderIncrementId);
            
            $check = $this->checkExistMoMoId($allMomoId, $requestId);

            $payment = $order->getPayment();
			$payment->setTransactionId($transactionId)
			            ->setCurrencyCode($order->getBaseCurrencyCode())
                        ->registerCaptureNotification($valueResponse['amount']/Cons::MULTI);
		    if (!empty($dataResponse)) {
		        $payment->setAdditionalData(serialize($dataResponse));
            }

            $orderId = $this->_session->getOrderId();
            $checkExistMoMoId = $this->checkExistMoMoId($allMomoId, $requestId);
            if($checkExistMoMoId){
                $dataStored = [
                    'momo_id' => $requestId,
                    'status' => $status,
                    'pay_type' => $payType,
                    'transaction_id' => $transactionId,
                    'order_id' => (int)$orderId
                ];
                $this->storeDataMomoOrder($dataStored);
            }

            if($order->getData('status') == 'pending' && $order) {
                $order->setState("processing");
                $order->setStatus("processing");
            }
            try {
                $order->save();
            } catch(\Exception $e) {

            }
            return Cons::CHECKOUT_SUCCESS_PATH_MAGENTO;
        }
    }

    /** 
     * @param Array
     * @return [Store data in database. sales_order_momo]
     */
    public function storeDataMomoOrder($dataStored)
    {
        $momoOrder = $this->_momoOrderCollect->create();
        $momoOrder->setData($dataStored);
        $momoOrder->save();
    }

    /** 
     * @param Array
     * @return [Store data in database. sales_order_momo_queue]
     */
    public function storeDataMomoOrderQueue($dataStored)
    {
        $momoOrderQueue = $this->_momoOrderQueueFactory->create();
        $momoOrderQueue->setData($dataStored);
        $momoOrderQueue->save();
    }

    /** 
     * @param Array [list id of sales_order_momo] $listId, 
     * @param String $currentMomoId 
     * @return [Store data in database. sales_order_momo]
     */
    public function checkExistMoMoId($listId, $currentMomoId)
    {
        $checkExist = 0;
        foreach($listId as $momoId) {
            if($momoId === $currentMomoId) {
                $checkExist = 1;
                break;
            }
            else {
                
            }
        }
        if($checkExist === 1) {
            return false;
        }
        else {
            return true;
        }
    }
}