{*
 * NOTICE OF LICENSE
 *
 * @author    Written for or by ViaBill
 * @copyright Copyright (c) Viabill
 * @license   Addons PrestaShop license limitation
 * @see       /LICENSE
 *
 *
 *
 *}

<script type="text/javascript">
    {foreach from=$js_def key=k item=def}
    {if !empty($k) && is_string($k)}
    {if is_bool($def)}
    var {$k|escape:'htmlall':'UTF-8'} = {$def|var_export:true};
    {elseif is_int($def)}
    var {$k|escape:'htmlall':'UTF-8'} = {$def|intval};
    {elseif is_float($def)}
    var {$k|escape:'htmlall':'UTF-8'} = {$def|floatval|replace:',':'.'};
    {elseif is_string($def)}
    var {$k|escape:'htmlall':'UTF-8'} = '{$def|strval}';
    {elseif is_array($def) || is_object($def)}
    var {$k|escape:'htmlall':'UTF-8'} = {$def|json_encode};
    {elseif is_null($def)}
    var {$k|escape:'htmlall':'UTF-8'} = null;
    {else}
    var {$k|escape:'htmlall':'UTF-8'} = '{$def|@addcslashes:'\''}';
    {/if}
    {/if}
    {/foreach}
</script>