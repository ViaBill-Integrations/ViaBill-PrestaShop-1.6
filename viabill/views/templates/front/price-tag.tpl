{**
* NOTICE OF LICENSE
*
* @author    Written for or by ViaBill
* @copyright Copyright (c) Viabill
* @license   Addons PrestaShop license limitation
* @see       /LICENSE
*
*
*}

{if $dynamicPriceTrigger}
    <input type="hidden" id="{$dynamicPriceTrigger|escape:'htmlall':'UTF-8'}">
{/if}

<div
        class="viabill-pricetag"
        data-view="{$dataView|escape:'htmlall':'UTF-8'}"
        {if $dynamicPriceSelector && !$dataPrice}
            data-dynamic-price="{$dynamicPriceSelector|escape:'htmlall':'UTF-8'}"
            data-dynamic-price-triggers="#{$dynamicPriceTrigger|escape:'htmlall':'UTF-8'}"
        {elseif $dataPrice}
            data-price="{$dataPrice|escape:'htmlall':'UTF-8'}"
        {/if}
        data-language="{$dataLanguageIso|escape:'htmlall':'UTF-8'}"
        data-currency="{$dataCurrencyIso|escape:'htmlall':'UTF-8'}"
        data-country-code="{$dataCountryCodeIso|escape:'htmlall':'UTF-8'}"
></div>