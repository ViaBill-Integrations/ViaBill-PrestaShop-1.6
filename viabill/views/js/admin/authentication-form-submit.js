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

$(document).ready(function() {
    $(document).on('submit', '#configuration_form.AdminViaBillAuthentication', function (e) {
        if ($('input.js-terms-checkbox').is(':checked') != true) {
            e.preventDefault();
            submited = false;
            $(".js-terms-error").removeClass('hidden');
        } else {
            $(".js-terms-error").not('hidden').addClass('hidden');
        }
    });
});