define(
    [
        'uiComponent',
        'Magento_Checkout/js/model/payment/renderer-list'
    ],
    function (
        Component,
        rendererList
    ) {
        'use strict';
        rendererList.push(
            {
                type: 'netpowermomo',
                component: 'Netpower_Momo/js/view/payment/method-renderer/netpowermomo'
            }
        );
        return Component.extend({});
    }
);