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
    updateProductVariantsEventHandler();

    $(window).load(function () {
        $('.attribute_select').trigger('change');
        $('.color_pick').trigger('change');
        $('.attribute_radio').trigger('change');
    })

    function updateProductVariantsEventHandler() {

        var $changeTrigger = $('#'+dynamicPriceTagTrigger);
        $changeTrigger.on('change', function () {
            // this is only the trigger which is being called by price tags after ajax calls
        });

        $('.attribute_select').on('change', updateDynamicPriceTagPrice);
        $('.color_pick').on('click', updateDynamicPriceTagPrice);
        $('.attribute_radio').on('click', updateDynamicPriceTagPrice);

        function updateDynamicPriceTagPrice() {
            var productPrice = $('#our_price_display').text().replace(/[^\d,.-]/g,'');

            $('.dynamic-price-tag-selector').text(productPrice);

            $changeTrigger.trigger('change');
            $changeTrigger.trigger('click');
        }
    }

    function appendPriceTagToExistingContainer() {
        if (typeof priceTagCartBodyHolder === 'undefined') {
            return;
        }

        $('#our_price_display').after((priceTagCartBodyHolder.substr(1).slice(0, -1)));
    }
});