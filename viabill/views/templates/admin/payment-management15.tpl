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
<br>
<fieldset>
    <legend><img src="../img/admin/tab-customers.gif" /> {l s = 'ViaBill payment actions' mod='viabill'}</legend>
    <div class="panel-body">
        {if $paymentManagement.isCancelled || $paymentManagement.isFullRefund || $paymentManagement.currencyError}

            {$message = {l s='Payment is cancelled' mod='viabill'}}

            {if $paymentManagement.isFullRefund}
                {$message = {l s='Payment is refunded' mod='viabill'}}
            {/if}

            {if $paymentManagement.currencyError}
                {$message = $paymentManagement.currencyError}
            {/if}

            {include file='./partials/info-message.tpl' message=$message}
        {else}
            {include file='./partials/capture-container.tpl' captureFormGroup=$paymentManagement.captureFormGroup}

            {if $paymentManagement.refundFormGroup.isVisible || $paymentManagement.cancelFormGroup.isVisible}
                <div class="panel">
                    <div class="panel-heading">
                        <i class="icon icon-circle-arrow-down"></i> {l s='Return' mod='viabill'}
                    </div>
                    <div class="panel-body return-action-container">
                        {include file='./partials/refund-container.tpl' refundFormGroup=$paymentManagement.refundFormGroup}
                        {include file='./partials/cancel-container.tpl' cancelFormGroup=$paymentManagement.cancelFormGroup}
                    </div>
                </div>
            {/if}

            {include file='./partials/renew-container.tpl' renewFormGroup=$paymentManagement.renewFormGroup}
        {/if}
    </div>
</fieldset>
