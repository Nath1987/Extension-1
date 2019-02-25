define(
    [
        'Magento_Checkout/js/view/payment/default'
    ],
    function (Component) {
        'use strict';

        return Component.extend({
            defaults: {
                template: 'Netpower_Momo/payment/netpowermomo'
            }
        });
    }
);

// define(
//     [
//         'ko',
//         'jquery',
//         'Magento_Checkout/js/view/payment/default',
//         'Netpower_Momo/js/action/set-payment-method-action'
//     ],
//     function (ko, $, Component, setPaymentMethodAction) {
//         'use strict';
//         return Component.extend({
//             defaults: {
//                 redirectAfterPlaceOrder: false,
//                 template: 'Netpower_Momo/payment/netpowermomo'
//             },
//             afterPlaceOrder: function () {
//                 setPaymentMethodAction(this.messageContainer);
//                 return false;
//             }
//         });
//     }
// );

