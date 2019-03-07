<?php

namespace Netpower\Momo\Controller\Index;

use Netpower\Momo\Services\Config;
use Netpower\Momo\Services\Transport;
use Netpower\Momo\Services\Cons;

use Netpower\Momo\Helper\TransferAllDataConfig;

use \Magento\Framework\App\Action\Context; 

use \Magento\Checkout\Model\Session;

use \Magento\Sales\Model\OrderFactory;

use Netpower\Momo\Model\MomoOrderQueueFactory;

use Netpower\Momo\Helper\OrderProblemQueue;

class Redirect extends \Magento\Framework\App\Action\Action
{
    /** 
     * @var Netpower\Momo\Helper\TransferAllDataConfig;
     */
    protected $_helper;

    /** 
     * @var Netpower\Momo\Services\Transport;
     */
    protected $_transport;

    /** 
     * @var Netpower\Momo\Services\Config;
     */
    protected $_config;

    /** 
     * @var \Magento\Checkout\Model\Session;
     */
    protected $_checkoutSession;

    /** 
     * @var \Magento\Sales\Model\OrderFactory;
     */
    protected $_orderFactory;

    /** 
     * @var Netpower\Momo\Model\MomoOrderQueueFactory;
     */
    protected $_momoOrderQueueFactory;

    /**
     * @var Netpower\Momo\Helper\OrderProblemQueue;
     */
    protected $_orderProblemQueue;

    /** CONSTRUCTOR
     * @param Netpower\Momo\Services\Config,
     * @param Netpower\Momo\Helper\TransferAllDataConfig,
     * @param Netpower\Momo\Services\Transport,
     * @param \Magento\Framework\App\Action\Context,
     * @param \Magento\Checkout\Model\Session,
     * @param \Magento\Sales\Model\OrderFactory,
     * @param Netpower\Momo\Model\MomoOrderQueueFactory,
     * @param Netpower\Momo\Helper\OrderProblemQueue
     */
    public function __construct(
        Config $config,
        TransferAllDataConfig $helper,
        Transport $transport,
        Context $context,
        Session $checkoutSession,
        OrderFactory $orderFactory,
        MomoOrderQueueFactory $momoOrderQueueFactory,
        OrderProblemQueue $orderProblemQueue
        )
    {
        $this->_config = $config;
        $this->_helper = $helper;
        $this->_transport = $transport;
        $this->_checkoutSession = $checkoutSession;
        $this->_orderFactory = $orderFactory;
        $this->_momoOrderQueueFactory = $momoOrderQueueFactory;
        $this->_orderProblemQueue = $orderProblemQueue;
        return parent::__construct($context);
    }
    
    /** EXECUTE
     * @return [Redirect to third party page in this case, redirect to MoMo PayUrl].
     */
    public function execute()
    {       
        //$this->_orderProblemQueue->checkOrderStatus();

        $orderIncrementId = $this->_checkoutSession->getLastRealOrderId();

        $entityId = $this->_checkoutSession->getLastRealOrder()->getEntityId();
        
        if($entityId == NULL) {
            $resultRedirect = $this->resultRedirectFactory->create();
            $resultRedirect->setPath(Cons::CHECKOUT_FAIL_PATH_MAGENTO);
            return $resultRedirect;
        }

        $this->_checkoutSession->setOrderId($entityId);

        $order = $this->_orderFactory->create();
        $order->loadByIncrementId($orderIncrementId);

        $grandTotal = $order->getGrandTotal();

        $amount = $grandTotal*Cons::MULTI;
        $requestId = $orderId = time()."";
        $currency = $order->getBaseCurrencyCode();
        $storeName = $order->getData('store_name');

        $dataArray = [
            'amount' => (string)$amount,
            'requestId' => $requestId,
            'orderId' => $orderId,
            'extraData' => "currency=$currency;storename=Momo"
        ];

        $configValues = $this->_helper->allDataApiRequire($dataArray);
        
        $dataSend = json_encode($configValues);

        $urlEndPoint = $this->_config->getUrlEndPoint();
        $urlEndPoint = $urlEndPoint . Cons::GATEWAY_PAYMENT;
        try {
            $result =  $this->_transport->post($urlEndPoint,$dataSend);
        } catch(\Exception $e) {
            $this->messageManager->addError(__($e->getMessage()));
        }
        if(is_array($result)) { // After Place Order, send request API success
            $payUrl = $result['payUrl'];
            return $this->resultRedirectFactory->create()->setUrl($payUrl);
        }
        else { // After Place Order, Send request Fail. Save Order to Queue. 
            $pathUrlController = Cons::CHECKOUT_FAIL_PATH_MAGENTO;
            $this->messageManager->addError(__($result));
            if($order->getData('status') == 'processing' && $order) {
                $order->setState("pending");
                $order->setStatus("pending");
                $order->save();
            }   

            $this->_transport->log($entityId);
            
            $dataStoreQueue = [
                'request_id' => $requestId,
                'order_id' => $orderId,
                'status' => 'fail',
                'error' => $result,
                'sales_order_id' => $entityId
            ];

            try {
                $this->storeData($dataStoreQueue);
            } catch(\Exeption $e) {
                $this->messageManager->addError(__($e->getMessage()));
            }

            $resultRedirect = $this->resultRedirectFactory->create();
            $resultRedirect->setPath($pathUrlController);
            return $resultRedirect;
        }
    }

    public function storeData($dataStored)
    {
        $momoOrder = $this->_momoOrderQueueFactory->create();
        $momoOrder->setData($dataStored);
        $momoOrder->save();
    }
}