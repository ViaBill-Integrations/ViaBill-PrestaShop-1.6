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
    updateProductVariantsEventHandler();

    function updateProductVariantsEventHandler() {
        var $changeTrigger = $('#'+dynamicPriceTagTrigger);
        $changeTrigger.on('change', function () {
            // this is only the trigger which is being called by price tags after ajax calls
        });

        $('#our_price_display').on('change', function () {
            var productPrice = $('#our_price_display').text().replace(/[^\d,.-]/g,'');
            $('.dynamic-price-tag-selector').text(productPrice);

            $changeTrigger.trigger('change');
            $changeTrigger.trigger('click');
        });
    }
});