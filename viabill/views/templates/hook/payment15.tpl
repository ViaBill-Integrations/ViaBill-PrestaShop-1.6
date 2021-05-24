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


<div class="payment_module payment-price-tag-container">
    <a href="{$viabill_payment_url|escape:'htmlall':'UTF-8'}" title="{l s='Pay with ViaBill' mod='viabill'}" class="viabill clearfix">
        <img
            src="{$vb_payment_logo_url|escape:'htmlall':'UTF-8'}"
            alt="Pay with ViaBill" width="86" height="49"
            {if !$vb_payment_logo_display}style="visibility: hidden"{/if}
        >
        {l s='Pay with ViaBill (order processing will be faster)' mod='viabill'}
    </a>
    {include file='../front/tag-body.tpl'}
</div>