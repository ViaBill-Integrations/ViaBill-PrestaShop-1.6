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

<div class="margin-form">
    <a type="button"
            target="_blank"
            name="goToMyViaBill"
            class="vd-auth-additional-button myviabill-button {if !$myViaBillLink}disabled{/if}"
            id="configuration_form_submit_btn"
            href="{$myViaBillLink|escape:'htmlall':'UTF-8'}"
    >
        {l s='Go to MyViaBill' mod='viabill'}
    </a>
</div>