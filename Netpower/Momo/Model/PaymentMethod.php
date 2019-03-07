<?php

namespace Netpower\Momo\Model;

use \Magento\Framework\UrlInterface;

use \Magento\Framework\App\ResponseFactory;

use Netpower\Momo\Helper\RefundOrder;

use Netpower\Momo\Services\Transport;

use Netpower\Momo\Model\MomoOrderFactory;

use Netpower\Momo\Services\Config;

use Netpower\Momo\Services\Cons;
/**
 * Pay In Store payment method model
 */

class PaymentMethod extends \Magento\Payment\Model\Method\AbstractMethod
{

    /**
     * Payment code
     *
     * @var string
     */
    protected $_code = 'netpowermomo';

    /**
     * @var \Netpower\Momo\Services\UrlInterface
     */
    protected $_url;

    /**
     * @var \Magento\Framework\App\ResponseFactory
     */
    protected $_responseFactory;

    /**
     * @var Netpower\Momo\Helper\RefundOrder
     */
    protected $_refund;

    /**
     * @var Netpower\Momo\Services\Transport
     */
    protected $_transport;

    protected $_canAuthorize                = false;
    protected $_canCapture                  = false;
    protected $_canCapturePartial           = false;
    protected $_canCaptureOnce              = false;
    protected $_canRefund                   = true;

    /**
     * @var Netpower\Momo\Model\MomoOrderFactory
     */
    protected $_momoOrderFactory;

    /**
     * @var Netpower\Momo\Services\Config
     */
    protected $_config;

    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Api\ExtensionAttributesFactory $extensionFactory,
        \Magento\Framework\Api\AttributeValueFactory $customAttributeFactory,
        \Magento\Payment\Helper\Data $paymentData,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Payment\Model\Method\Logger $logger,
        \Magento\Directory\Helper\Data $directory,
        RefundOrder $refund,
        Transport $transport,
        MomoOrderFactory $momoOrderFactory,
        Config $config,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $registry,
            $extensionFactory,
            $customAttributeFactory,
            $paymentData,
            $scopeConfig,
            $logger,
            $resource,
            $resourceCollection,
            $data,
            $directory
        );
        $this->_refund = $refund;
        $this->_transport = $transport;
        $this->_momoOrderFactory = $momoOrderFactory;
        $this->_config = $config;
    }
    
   
	/**
     * Authorize payment abstract method
     * 
     * @param \Magento\Framework\DataObject|InfoInterface $payment
     * @param float $amount
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     * @api
     */
    public function authorize(\Magento\Payment\Model\InfoInterface $payment, $amount)
    {   
        $this->_canAuthorize = true;
        if (!$this->canAuthorize()) {
            throw new \Magento\Framework\Exception\LocalizedException(__('The authorize action is not available.'));
        }
        return $this;
    }
    /**
     * Capture payment abstract method
     *
     * @param \Magento\Framework\DataObject|InfoInterface $payment
     * @param float $amount
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     * @api
     */
    public function capture(\Magento\Payment\Model\InfoInterface $payment, $amount)
    {
        $this->_transport->log("Capture at");

        if (!$this->canCapture()) {
            throw new \Magento\Framework\Exception\LocalizedException(__('The capture action is not available.'));
        }
        return $this;
    }
    /**
     * Refund specified amount for payment
     *
     * @param \Magento\Framework\DataObject|InfoInterface $payment
     * @param float $amount
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     * @api
     */
    public function refund(\Magento\Payment\Model\InfoInterface $payment, $amount)
    {
        $order = $payment->getOrder();
        $orderId = $order->getEntityId();
        $amount = $order->getData('grand_total')*Cons::MULTI;
        $orderMoMos = $this->_momoOrderFactory->create()->getCollection()->getData();
        $orderTest = $this->_momoOrderFactory->create()->load($orderId);

        $orderMomoRefund = [];
        foreach($orderMoMos as $orderMoMo) {
            if($orderMoMo['order_id'] == $orderId) {
                $orderMomoRefund = $orderMoMo; 
                break;
            }
        }
        $dataArray = [
            "partnerCode" => $this->_config->getMerchantnentId(),
            "accessKey" => $this->_config->getAccessKey(),
            "requestId" => $orderMomoRefund['momo_id'],
            "amount" => strval($amount),
            "orderId" => time().'',
            "transId" => $orderMomoRefund['transaction_id'],
            "requestType" => Cons::REQUEST_TYPE_REFUND
        ];

        $result = $this->_refund->refundOrder($dataArray);
        if(is_array($result)) {
            $this->_transport->log($result);
        } 
        else {
            $this->_transport->log($result);
            // REFUND FAIL
            $this->messageManager->addError(__($result));
            exit;
        }
        if (!$this->canRefund()) {
            throw new \Magento\Framework\Exception\LocalizedException(__('The refund action is not available.'));
        }
        return parent::refund($payment, $amount);
    } 
}

