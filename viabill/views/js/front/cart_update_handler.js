/**
* NOTICE OF LICENSE
*
* @author    Written for or by ViaBill
* @copyright Copyright (c) Viabill
* @license   Addons PrestaShop license limitation
* @see       /LICENSE
*
*
*/

$(document).ready(function () {
    appendPriceTagToExistingContainer();

    var $changeTrigger = $('#'+dynamicPriceTagTrigger);
    $changeTrigger.on('change', function () {
        // this is only the trigger which is being called by price tags after ajax calls
    });

    $(document).ajaxComplete(function(event, xhr, settings) {
        if (typeof settings == 'undefined') {
            return;
        }

        if (typeof settings.data === 'string') {
            var action = getUrlParam('method', settings.data);

            if (action === 'getproductprice') {
                $changeTrigger.trigger('change');
                $changeTrigger.trigger('click');
            }

            if (action === 'updateTOSStatusAndGetPayments' ||
                action === 'updateCarrierAndGetPayments' ||
                action === 'updateAddressesSelected'
                && controller === 'order-opc') {
                vb.buildTags();
            }
        }
    });

    function appendPriceTagToExistingContainer() {
        if (typeof priceTagCartBodyHolder === 'undefined') {
            return;
        }

        $('#order-detail-content').after(priceTagCartBodyHolder.substr(1).slice(0, -1));
    }

    /**
     *  gets parameters from string which is identical to the url parameter
     *
     * @param sParam
     * @param string
     *
     * @returns {boolean}
     * @constructor
     */
    function getUrlParam(sParam, string)
    {
        var sPageURL = decodeURIComponent(string),
            sURLVariables = sPageURL.split('&'),
            sParameterName,
            i;

        for (i = 0; i < sURLVariables.length; i++) {
            sParameterName = sURLVariables[i].split('=');
            if (sParameterName[0] === 'getproductprice') {
                return sParameterName[0];
            }

            if (sParameterName[0] === sParam) {
                return sParameterName[1] === undefined ? true : sParameterName[1];
            }
        }
    }
});