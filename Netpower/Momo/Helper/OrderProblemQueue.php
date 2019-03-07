<?php 

namespace Netpower\Momo\Helper;

use Netpower\Momo\Services\Transport;
use Netpower\Momo\Services\Config;
use Netpower\Momo\Services\Cons;

use Netpower\Momo\Model\MomoOrderQueueFactory;

use \Magento\Sales\Model\OrderFactory;

class OrderProblemQueue
{
    /**
     * @var Netpower\Momo\Services\Transport
     */
    protected $_transport;

    /**
     * @var Netpower\Momo\Services\Config
     */
    protected $_config;

    /**
     * @var Netpower\Momo\Model\MomoOrderQueueFactory
     */
    protected $_momoOrderQueueFactory;

    /**
     * @var Netpower\Momo\Helper\TransferAllDataConfig
     */   
    protected $_transfer;

    /**
     * @var \Magento\Sales\Model\OrderFactory
     */  
    protected $_orderFactory;

    /**
     * @param Netpower\Momo\Services\Transport $transport;
     * @param Netpower\Momo\Services\Config $config;
     * @param Netpower\Momo\Model\MomoOrderQueueFactory $momoOrderQueueFactory;
     * @param Netpower\Momo\Helper\TransferAllDataConfig $transfer;
     * @param \Magento\Sales\Model\OrderFactory $orderFactory;
     */
    public function __construct(
        Transport $transport,
        Config $config,
        MomoOrderQueueFactory $momoOrderQueueFactory,
        TransferAllDataConfig $transfer,
        OrderFactory $orderFactory
    )
    {
        $this->_transport = $transport;
        $this->_config = $config;
        $this->_momoOrderQueueFactory = $momoOrderQueueFactory;
        $this->_transfer = $transfer;
        $this->_orderFactory = $orderFactory;
    }

    /**
     * @param
     * @return 
     */
    public function checkOrderStatus()
    {
        $ordersInQueue = $this->_momoOrderQueueFactory->create()->getCollection()->getData();
        
        $this->_transport->log("CRON JOB");

        if($ordersInQueue == NULL) {
            exit;
        }

        $partnerCode = $this->_config->getMerchantnentId();
        $accessKey = $this->_config->getAccessKey();

        foreach($ordersInQueue as $order) {
            $orderInfor= $this->_orderFactory->create()->load($order['sales_order_id']);

            $dataParam = [
                'partnerCode' => $partnerCode,
                'accessKey' => $accessKey,
                'requestId' => $order['request_id'],
                'orderId' => $order['order_id'],
                'requestType' => Cons::REQUEST_TYPE_TRANSACTION
            ];
         
            $secretKey = $this->_config->getSecretKey();

            $signature = $this->_transfer->calculateSignature($dataParam, $secretKey);

            $dataParam['signature'] = $signature;

            $dataJSON = json_encode($dataParam);

            $urlEndpoint = $this->_config->getUrlEndPoint() . Cons::GATEWAY_PAYMENT;
            try {
                $result = $this->_transport->post($urlEndpoint, $dataJSON);
                

                if(is_array($result)) { // When not error
                    if($orderInfor && $orderInfor['status'] == 'pending') {
                        $orderInfor->setState("processing");
		                $orderInfor->setStatus("processing");       
		                $payment = $orderInfor->getPayment();
		                $payment->setTransactionId($result['transId'])
			                    ->setCurrencyCode($orderInfor->getBaseCurrencyCode())
                                ->registerCaptureNotification($result['amount']);
                                
                        $orderInfor->save();
                        
                        $this->deleteRecordQueue($order['id']);
                    }
                }
                else { // When error
                    if($orderInfor) {
                        $orderInfor->getPayment()->cancel();
                        $orderInfor->addStatusHistoryComment("MOMO Queue changes from pending to canceled");
                        $orderInfor->registerCancellation("Cancel from MOMO with Error Code {$result}");
                        $orderInfor->save();	

                        $this->_transport->log($order['id']);

                        $this->deleteRecordQueue($order['id']);
                    }
                }
            } catch(\Exeption $e) {
                $this->_transport->log('Exception');
                $this->messageManager->addError(__($e->getMessage()));
            }
        }
    }

    public function deleteRecordQueue($id)
    {
        $model = $this->_momoOrderQueueFactory->create();
        $model->load($id);
        $model->delete();
    }
}