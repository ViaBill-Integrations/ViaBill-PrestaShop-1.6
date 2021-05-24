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

{if $refundFormGroup.isVisible}
    <form
            method="post"
            class="form-inline"
            action="{$paymentManagement.formAction|escape:'htmlall':'UTF-8'}"
            data-id_order="{$paymentManagement.orderId|intval}"
    >
        <div class="form-group">
            <div class="input-group fixed-width-xl">
                <input
                        type="text"
                        inputmode="numeric"
                        pattern="[0-9]+([,\.][0-9]+)?"
                        name="refund_amount"
                        class="capture-amount form-control"
                        {if $refundFormGroup.remainingToRefund}
                            value="{$refundFormGroup.remainingToRefund|floatval}"
                        {/if}
                >
                <div class="input-group-addon">
                    {$paymentManagement.currencySign|escape:'htmlall':'UTF-8'}
                </div>
            </div>
        </div>

        <input type="hidden" name="refundPayment" value="1">
        <button
                type="submit"
                class="btn btn-default"
                name="refundPayment"
                {if $refundFormGroup.refundConfirmation}
                    data-ajax_capture="true"
                {/if}
        >
            <i class="icon icon-circle-arrow-down"></i> {l s='Refund' mod='viabill'}
        </button>
    </form>
{/if}
