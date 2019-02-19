<?php

class Zodinet_Momo_IndexController extends Mage_Core_Controller_Front_Action
{
	public function successAction(){
		$storeId = Mage::app()->getStore()->getStoreId();
		$requestParams = $this->getRequest()->getParams();
		
		$this->getHelper()->log("Return URL with data:");
		$this->getHelper()->log($requestParams);

		$orderId = $this->getRequest()->getParam('orderId');
		$requestId = $this->getRequest()->getParam('requestId');
		$errorCode = $this->getRequest()->getParam('errorCode');

		$data = array(
			"requestId" => $requestId,
			"orderId" => $orderId,
			"requestType" => "transactionStatus",
		);

		try{
			$transport = Mage::helper('momo/transport');
			$result = $transport->call($data);
		}
		catch(Exception $e){
			$session = Mage::getSingleton('checkout/session');
	                $orderIncrementId = $session->getLastRealOrderId();
			if($orderIncrementId){
				$order = Mage::getModel('sales/order')->loadByIncrementId($orderIncrementId);
				$payment = $order->getPayment();
				$payment->setAdditionalData(serialize($requestParams));
				$order->addStatusHistoryComment($e->getMessage());
				$order->save();
			}

			$this->getHelper()->log($e->getMessage());
			Mage::getSingleton('core/session')->addError($e->getMessage()); 
			$redirectUrl = Mage::getUrl('momo/index/cancel', array('_query' => http_build_query(array('status' => 'FAIL_AUTH_TRANSACTION', 'momo' => $requestParams)) , '_secure' => true));
			return $this->_redirectUrl($redirectUrl);
		}

		if($result){
			if($result->errorCode != 0){
				Mage::getSingleton('core/session')->addError($result->localMessage);
				$redirectUrl = Mage::getUrl('momo/index/cancel', array('_secure' => true, '_query' => http_build_query(array('momo' => $requestParams))));
				return $this->_redirectUrl($redirectUrl);
			}

			// Do something here
			$order = Mage::getModel('sales/order')->loadByIncrementId($orderId);
			if($order  && $order->getStatus() == 'pending'){
			    $checkoutSession = Mage::getSingleton('checkout/session');
		            $checkoutSession
					->setLastQuoteId($order->getQuoteId())
					->setLastOrderId($order->getId())
					->setLastSuccessQuoteId($order->getQuoteId());
			
			    $order->setData('state', "processing");
			    $order->setStatus("processing");       

			    $payment = $order->getPayment();
			    $payment->setTransactionId($requestId)
				            ->setCurrencyCode($order->getBaseCurrencyCode())
				            ->registerCaptureNotification($result->amount);

		        $details = (array)$result;

		        if (!empty($details)) {
		            $payment->setAdditionalData(serialize($details));
		        }

			$order->save();	
			if ($order->getId()) {
                            $order->queueNewOrderEmail();
                        }
	
			$redirectUrl = Mage::getUrl('checkout/onepage/success', array('_secure' => true));
			$this->getHelper()->log("===============================");
			 	$this->_redirectUrl($redirectUrl);   
			}
		    else{
			$redirectUrl = Mage::getUrl('checkout/cart', array('_secure' => true));
			$this->_redirectUrl($redirectUrl);   
		    }
		}
		else{
			Mage::getSingleton('core/session')->addError("Could not parse data from MOMO"); 
			$redirectUrl = Mage::getUrl('checkout/onepage/failure', array('_secure' => true));
			$this->getHelper()->log("===============================");
			$this->_redirectUrl($redirectUrl);   
		}
	}

	public function notifyAction(){
		$storeId = Mage::app()->getStore()->getStoreId();
		$data = $this->getRequest()->getParams();
		
		$this->getHelper()->log("Notify URL with data:");
		$this->getHelper()->log($data);

		$queue = Mage::getModel('momo/queue');
		$queue->setExtraData(serialize($data));
		$queue->setOrderId($data['orderId']);
		$queue->setRequestId($data['requestId']);
		$queue->setStatus('new');
		$queue->setCreatedTime(date('Y-m-d H:i:s'));
		$queue->save();
	}

	public function cancelAction(){
		$session = Mage::getSingleton('checkout/session');
		$orderIncrementId = $session->getLastRealOrderId();
		$status = $this->getRequest()->getParam('status');
		$requestParams = $this->getRequest()->getParam('momo');
		if($status == 'FAIL_AUTH_TRANSACTION'){
		    Mage::getSingleton('core/session')->addError("Fail to authentication transaction from MOMO");
		    $redirectUrl = Mage::getUrl('checkout/onepage/failure', array('_secure' => true));
                    $this->_redirectUrl($redirectUrl);
		    return $this;
		}

		if($orderIncrementId){
			$order = Mage::getModel('sales/order')->loadByIncrementId($orderIncrementId);
			if(true){// || $order->getStatus() == 'pending'){
				if($requestParams){
				    $payment = $order->getPayment();
				    $payment->setAdditionalData(serialize($requestParams));
				}

				$order->getPayment()->cancel();
	                        $order->registerCancellation("Customer cancel order from Momo");
			        $order->save();	

				$redirectUrl = Mage::getUrl('checkout/onepage/failure', array('_secure' => true));
				$this->_redirectUrl($redirectUrl);
				return $this;
			}	
		}
		$redirect = Mage::getUrl('checkout/cart', array('_secure' => true));
		$this->_redirect($redirect);
		return $this;
	}

	public function redirectAction()
	{
		$session = Mage::getSingleton('checkout/session');
		$orderIncrementId = $session->getLastRealOrderId();
		$order = Mage::getModel('sales/order')->loadByIncrementId($orderIncrementId);
		if(!$order->getId()){
			$redirect = Mage::getUrl('checkout/cart', array('_secure' => true));
			$this->_redirectUrl($redirect);
			return $this;
		}
		$storeId = $order->getStoreId();
		$transport = Mage::helper('momo/transport');
		$storeInfo = Mage::getStoreConfig('general/store_information/name',$storeId);

		$this->getHelper()->log("===============================");
		$this->getHelper()->log("Start MOMO transaction with order {$orderIncrementId}");

		$data = array(
			'requestId' => time() . "",
 			'amount' => intval($order->getGrandTotal()) . "",
			'orderId' => $order->getIncrementId() . "",
			'orderInfo' => $storeInfo . " - " . $order->getIncrementId(),
			'returnUrl' => Mage::getUrl('*/*/success', array('_secure' => true)),
			'notifyUrl' => Mage::getUrl('*/*/notify', array('_secure' => true)),
			'extraData' => '',
			'requestType' => 'captureMoMoWallet',
		);

		try{
			$result = $transport->call($data,array('requestType'));
		}
		catch(Exception $e){
			Mage::getSingleton('core/session')->addError($e->getMessage()); 
			$redirect = Mage::getUrl('checkout/cart', array('_secure' => true));
			$this->_redirectUrl($redirect);
		}
		if($result && $result->errorCode == 0){
			return $this->_redirectUrl($result->payUrl);
		}
		else{
			if($result && $result->localMessage){
				Mage::getSingleton('core/session')->addError($result->localMessage); 
			}
			else{
				Mage::getSingleton('core/session')->addError("Could not fetch order from MOMO"); 
			}
			$redirect = Mage::getUrl('checkout/cart', array('_secure' => true));
			return $this->_redirectUrl($redirect);
		}
		
        return $this;
	}

	protected function getHelper(){
		return Mage::helper('momo');
	}
}
