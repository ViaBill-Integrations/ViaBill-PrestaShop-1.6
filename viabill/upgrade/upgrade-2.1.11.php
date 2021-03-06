<?php
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

use ViaBill\Config\Config;

if (!defined('_PS_VERSION_')) {
    exit;
}

/**
 *
 * @param Viabill $module
 * @return bool
 */
function upgrade_module_2_1_11($module)
{
    return $module->registerHook('actionOrderHistoryAddAfter') &&
        $module->unregisterHook('actionOrderStatusPostUpdate');
}
