<?php

namespace Netpower\Momo\Services;

class Cons 
{
    // PATH ON SYSTEM.XML
    const MODE_PATH = 'payment/netpowermomo/mode';
    const TITLE_PATH = 'payment/netpowermomo/title';

    const MERCHANTNENT_ID_PATH = 'payment/netpowermomo/merchantnent_id';
    const ACCESS_KEY_PATH = 'payment/netpowermomo/access_key';
    const SECRECT_KEY_PATH = 'payment/netpowermomo/secret_key';
    const API_ENDPOINT_PATH = 'payment/netpowermomo/api_endpoint';
    const PUBLIC_KEY_PATH = 'payment/netpowermomo/public_key';

    const MERCHANTNENT_ID_TEST_PATH = 'payment/netpowermomo/merchantnent_id_test';
    const ACCESS_KEY_TEST_PATH = 'payment/netpowermomo/access_key_test';
    const SECRECT_KEY_TEST_PATH = 'payment/netpowermomo/secret_key_test';
    const API_ENDPOINT_TEST_PATH = 'payment/netpowermomo/api_endpoint_test';
    const PUBLIC_KEY_TEST_PATH = 'payment/netpowermomo/public_key_test';


    // URL RETURN and NOTIFY
    const RETURN_URL_PATH = "momo/index/response";
    const NOTIFY_URL_PATH = "momo/index/response";   

    // REQUEST TYPE 
    const REQUEST_TYPE_CAPTURE = "captureMoMoWallet";
    const REQUEST_TYPE_REFUND = "refundMoMoWallet";
    const REQUEST_TYPE_REFUND_STATUS = "refundStatus";
    const REQUEST_TYPE_TRANSACTION = "transactionStatus";

    const REQUEST_TYPE_CONFIRM_ORDER = "capture";
    const REQUEST_TYPE_CANCEL_ORDER = "revertAuthorize";
    
    // VERSION MOMO
    const VERSION = 2;

    // API URL PATH

    const INFOR_ORDER_PATH = "/pay/query-status";
    const REFUND_ORDER_PATH = "/pay/refund";
    const CONFIRM_ORDER_PATH = "/pay/confirm";
    const GATEWAY_PAYMENT = "/gw_payment/transactionProcessor";

    // PATH INSIDE PAGE
    const CHECKOUT_SUCCESS_PATH = "momo/index/success";
    const CHECKOUT_FAIL_PATH = "momo/index/fail"; 
    const CHECKOUT_SUCCESS_PATH_MAGENTO = "checkout/onepage/success";
    const CHECKOUT_FAIL_PATH_MAGENTO ="checkout/onepage/failure";

    // TEST GRAND TOTAL multiplication
    const MULTI = 10000;


    // ERORR STATUS
    const CANCEL_ORDER = "Order was canceled by user";
}