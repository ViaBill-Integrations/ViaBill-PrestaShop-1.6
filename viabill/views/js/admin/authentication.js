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
    setAuthButtonTargetBlank();

    $(document).on('change', '.js-country-select', changeTermsLink);

    function setAuthButtonTargetBlank() {
        $('.vd-auth-additional-button').attr('target', '_blank');
    }

    function changeTermsLink() {
        var selectedCountryISO = $(this).val();

        if (selectedCountryISO) {
            $(".terms-and-conditions-link").attr("href", termsLink + '#' + selectedCountryISO)
        } else {
            $(".terms-and-conditions-link").attr("href", termsLink)
        }
    }
});