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

<div>
  <select class="{if $isPs16}chosen {/if}searchable-multiselect" name="order_status_multiselect[]" multiple>
      {foreach $multiselectOrderStatuses as $orderStatus}
        <option value="{$orderStatus['id_order_state']|escape:'htmlall':'UTF-8'}"{if $orderStatus['selected']} selected="selected"{/if}>
            {$orderStatus['name']|escape:'htmlall':'UTF-8'}
        </option>
      {/foreach}
  </select>
</div>
{if $isPs16}
  <div>
    <p class="help-block">{l s='Start typing to see suggestions' mod='viabill'}</p>
  </div>
{/if}