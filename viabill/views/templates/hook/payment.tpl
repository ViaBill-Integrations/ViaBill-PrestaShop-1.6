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

<div class="row">
    <div class="col-xs-12">
        <div class="payment_module payment-price-tag-container">
            <a
                href="{$viabill_payment_url|escape:'htmlall':'UTF-8'}"
                title="{l s='Pay with ViaBill' mod='viabill'}"
                class="viabill clearfix{if !$vb_payment_logo_display} no-logo{/if}"
            >
                <div class="row">
                    <div class="no-padding col-md-12">
                        {l s='Pay with ViaBill' mod='viabill'}
                        <span>{l s='(order processing will be faster)' mod='viabill'}</span>
                    </div>
                    <div class="payment-price-tag col-md-12">
                        {include file='../front/tag-body.tpl'}
                    </div>
                </div>
            </a>
        </div>
    </div>
</div>